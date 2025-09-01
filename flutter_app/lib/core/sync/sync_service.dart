import 'dart:async';
import 'dart:convert';
import 'package:injectable/injectable.dart';
import 'package:connectivity_plus/connectivity_plus.dart';

import '../database/database_service.dart';
import '../network/dio_client.dart';
import '../storage/local_storage_service.dart';

enum SyncStatus {
  pending(0),
  synced(1),
  failed(2),
  conflict(3);

  const SyncStatus(this.value);
  final int value;

  static SyncStatus fromValue(int value) {
    return SyncStatus.values.firstWhere((e) => e.value == value);
  }
}

enum SyncOperation {
  create,
  update,
  delete,
}

@singleton
class SyncService {
  final DatabaseService _databaseService;
  final DioClient _dioClient;
  final LocalStorageService _localStorage;
  final Connectivity _connectivity;

  SyncService(
    this._databaseService,
    this._dioClient,
    this._localStorage,
    this._connectivity,
  );

  static const String _lastSyncKey = 'last_sync_timestamp';
  static const String _syncInProgressKey = 'sync_in_progress';

  // Sync status stream
  final _syncStatusController = StreamController<SyncProgress>.broadcast();
  Stream<SyncProgress> get syncStatusStream => _syncStatusController.stream;

  Future<bool> get isOnline async {
    final connectivityResult = await _connectivity.checkConnectivity();
    return connectivityResult != ConnectivityResult.none;
  }

  Future<bool> get isSyncInProgress async {
    return _localStorage.getBool(_syncInProgressKey) ?? false;
  }

  Future<DateTime?> get lastSyncTime async {
    final timestamp = _localStorage.getString(_lastSyncKey);
    return timestamp != null ? DateTime.parse(timestamp) : null;
  }

  // Main sync method
  Future<SyncResult> syncAll({bool force = false}) async {
    if (await isSyncInProgress && !force) {
      return SyncResult(success: false, message: 'Sync already in progress');
    }

    if (!await isOnline) {
      return SyncResult(success: false, message: 'No internet connection');
    }

    await _localStorage.setBool(_syncInProgressKey, true);
    _syncStatusController.add(SyncProgress(isInProgress: true, progress: 0.0));

    try {
      final result = await _performSync();
      await _localStorage.setString(_lastSyncKey, DateTime.now().toIso8601String());
      return result;
    } catch (e) {
      return SyncResult(success: false, message: e.toString());
    } finally {
      await _localStorage.setBool(_syncInProgressKey, false);
      _syncStatusController.add(SyncProgress(isInProgress: false, progress: 1.0));
    }
  }

  Future<SyncResult> _performSync() async {
    final results = <String, bool>{};
    double progress = 0.0;
    const totalSteps = 6;

    try {
      // Step 1: Sync pending local changes to server
      _syncStatusController.add(SyncProgress(
        isInProgress: true,
        progress: progress,
        currentOperation: 'Uploading local changes...',
      ));
      results['upload'] = await _syncLocalChangesToServer();
      progress += 1 / totalSteps;

      // Step 2: Download server changes
      _syncStatusController.add(SyncProgress(
        isInProgress: true,
        progress: progress,
        currentOperation: 'Downloading server changes...',
      ));
      results['download'] = await _downloadServerChanges();
      progress += 1 / totalSteps;

      // Step 3: Sync users
      _syncStatusController.add(SyncProgress(
        isInProgress: true,
        progress: progress,
        currentOperation: 'Syncing users...',
      ));
      results['users'] = await _syncUsers();
      progress += 1 / totalSteps;

      // Step 4: Sync conversations
      _syncStatusController.add(SyncProgress(
        isInProgress: true,
        progress: progress,
        currentOperation: 'Syncing conversations...',
      ));
      results['conversations'] = await _syncConversations();
      progress += 1 / totalSteps;

      // Step 5: Sync messages
      _syncStatusController.add(SyncProgress(
        isInProgress: true,
        progress: progress,
        currentOperation: 'Syncing messages...',
      ));
      results['messages'] = await _syncMessages();
      progress += 1 / totalSteps;

      // Step 6: Clean up sync queue
      _syncStatusController.add(SyncProgress(
        isInProgress: true,
        progress: progress,
        currentOperation: 'Cleaning up...',
      ));
      await _cleanupSyncQueue();
      progress = 1.0;

      final allSuccessful = results.values.every((success) => success);
      return SyncResult(
        success: allSuccessful,
        message: allSuccessful ? 'Sync completed successfully' : 'Sync completed with some errors',
        details: results,
      );
    } catch (e) {
      return SyncResult(success: false, message: e.toString());
    }
  }

  Future<bool> _syncLocalChangesToServer() async {
    try {
      final pendingOperations = await _databaseService.query(
        'sync_queue',
        orderBy: 'created_at ASC',
      );

      for (final operation in pendingOperations) {
        await _processSyncOperation(operation);
      }

      return true;
    } catch (e) {
      print('Error syncing local changes: $e');
      return false;
    }
  }

  Future<void> _processSyncOperation(Map<String, dynamic> operation) async {
    try {
      final tableName = operation['table_name'] as String;
      final operationType = operation['operation'] as String;
      final data = jsonDecode(operation['data'] as String);

      switch (operationType) {
        case 'create':
          await _syncCreateOperation(tableName, data);
          break;
        case 'update':
          await _syncUpdateOperation(tableName, data);
          break;
        case 'delete':
          await _syncDeleteOperation(tableName, data);
          break;
      }

      // Remove from sync queue on success
      await _databaseService.delete(
        'sync_queue',
        where: 'id = ?',
        whereArgs: [operation['id']],
      );
    } catch (e) {
      // Update retry count and error message
      await _databaseService.update(
        'sync_queue',
        {
          'retry_count': (operation['retry_count'] as int) + 1,
          'last_retry_at': DateTime.now().toIso8601String(),
          'error_message': e.toString(),
        },
        where: 'id = ?',
        whereArgs: [operation['id']],
      );
      rethrow;
    }
  }

  Future<void> _syncCreateOperation(String tableName, Map<String, dynamic> data) async {
    switch (tableName) {
      case 'users':
        // Handle user creation sync
        break;
      case 'messages':
        await _dioClient.post('/messages', data: data);
        break;
      case 'conversations':
        await _dioClient.post('/conversations', data: data);
        break;
    }
  }

  Future<void> _syncUpdateOperation(String tableName, Map<String, dynamic> data) async {
    final id = data['id'];
    switch (tableName) {
      case 'users':
        await _dioClient.put('/users/$id', data: data);
        break;
      case 'messages':
        await _dioClient.put('/messages/$id', data: data);
        break;
      case 'conversations':
        await _dioClient.put('/conversations/$id', data: data);
        break;
    }
  }

  Future<void> _syncDeleteOperation(String tableName, Map<String, dynamic> data) async {
    final id = data['id'];
    switch (tableName) {
      case 'users':
        await _dioClient.delete('/users/$id');
        break;
      case 'messages':
        await _dioClient.delete('/messages/$id');
        break;
      case 'conversations':
        await _dioClient.delete('/conversations/$id');
        break;
    }
  }

  Future<bool> _downloadServerChanges() async {
    try {
      final lastSync = await lastSyncTime;
      final timestamp = lastSync?.toIso8601String();

      // Download changes since last sync
      final response = await _dioClient.get(
        '/sync/changes',
        queryParameters: timestamp != null ? {'since': timestamp} : null,
      );

      if (response.data['success'] == true) {
        final changes = response.data['data'] as Map<String, dynamic>;
        await _applyServerChanges(changes);
        return true;
      }

      return false;
    } catch (e) {
      print('Error downloading server changes: $e');
      return false;
    }
  }

  Future<void> _applyServerChanges(Map<String, dynamic> changes) async {
    await _databaseService.transaction((txn) async {
      // Apply user changes
      if (changes['users'] != null) {
        for (final user in changes['users']) {
          await _applyUserChange(txn, user);
        }
      }

      // Apply conversation changes
      if (changes['conversations'] != null) {
        for (final conversation in changes['conversations']) {
          await _applyConversationChange(txn, conversation);
        }
      }

      // Apply message changes
      if (changes['messages'] != null) {
        for (final message in changes['messages']) {
          await _applyMessageChange(txn, message);
        }
      }
    });
  }

  Future<void> _applyUserChange(dynamic txn, Map<String, dynamic> userData) async {
    final existingUser = await _databaseService.query(
      'users',
      where: 'id = ?',
      whereArgs: [userData['id']],
    );

    if (existingUser.isEmpty) {
      // Insert new user
      await txn.insert('users', {
        ...userData,
        'last_synced': DateTime.now().toIso8601String(),
        'sync_status': SyncStatus.synced.value,
      });
    } else {
      // Check for conflicts and update
      await _handleUserConflict(txn, existingUser.first, userData);
    }
  }

  Future<void> _applyConversationChange(dynamic txn, Map<String, dynamic> conversationData) async {
    // Similar to user change handling
    await txn.insert('conversations', {
      ...conversationData,
      'last_synced': DateTime.now().toIso8601String(),
      'sync_status': SyncStatus.synced.value,
    });
  }

  Future<void> _applyMessageChange(dynamic txn, Map<String, dynamic> messageData) async {
    // Similar to user change handling
    await txn.insert('messages', {
      ...messageData,
      'last_synced': DateTime.now().toIso8601String(),
      'sync_status': SyncStatus.synced.value,
    });
  }

  Future<void> _handleUserConflict(
    dynamic txn,
    Map<String, dynamic> localUser,
    Map<String, dynamic> serverUser,
  ) async {
    final localUpdated = DateTime.parse(localUser['updated_at']);
    final serverUpdated = DateTime.parse(serverUser['updated_at']);

    if (serverUpdated.isAfter(localUpdated)) {
      // Server version is newer, update local
      await txn.update('users', {
        ...serverUser,
        'last_synced': DateTime.now().toIso8601String(),
        'sync_status': SyncStatus.synced.value,
      }, where: 'id = ?', whereArgs: [serverUser['id']]);
    } else if (localUpdated.isAfter(serverUpdated)) {
      // Local version is newer, mark for upload
      await txn.update('users', {
        'sync_status': SyncStatus.pending.value,
      }, where: 'id = ?', whereArgs: [localUser['id']]);
    }
    // If timestamps are equal, keep server version (server wins)
  }

  Future<bool> _syncUsers() async {
    // Implementation for user sync
    return true;
  }

  Future<bool> _syncConversations() async {
    // Implementation for conversation sync
    return true;
  }

  Future<bool> _syncMessages() async {
    // Implementation for message sync
    return true;
  }

  Future<void> _cleanupSyncQueue() async {
    // Remove old failed operations (older than 7 days)
    final cutoffDate = DateTime.now().subtract(const Duration(days: 7));
    await _databaseService.delete(
      'sync_queue',
      where: 'created_at < ? AND retry_count > 3',
      whereArgs: [cutoffDate.toIso8601String()],
    );
  }

  // Queue operations for offline sync
  Future<void> queueOperation(
    String tableName,
    SyncOperation operation,
    Map<String, dynamic> data,
  ) async {
    await _databaseService.insert('sync_queue', {
      'table_name': tableName,
      'record_id': data['id'],
      'operation': operation.name,
      'data': jsonEncode(data),
      'created_at': DateTime.now().toIso8601String(),
      'retry_count': 0,
    });
  }

  void dispose() {
    _syncStatusController.close();
  }
}

class SyncResult {
  final bool success;
  final String message;
  final Map<String, bool>? details;

  SyncResult({
    required this.success,
    required this.message,
    this.details,
  });
}

class SyncProgress {
  final bool isInProgress;
  final double progress;
  final String? currentOperation;

  SyncProgress({
    required this.isInProgress,
    required this.progress,
    this.currentOperation,
  });
}
