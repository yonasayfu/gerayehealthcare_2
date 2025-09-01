import 'dart:async';
import 'dart:isolate';
import 'package:injectable/injectable.dart';
import 'package:workmanager/workmanager.dart';
import 'package:connectivity_plus/connectivity_plus.dart';

import '../database/database_service.dart';
import '../di/injection.dart';
import 'sync_service.dart';

@singleton
class BackgroundSyncService {
  static const String _syncTaskName = 'background_sync_task';
  static const String _periodicSyncTaskName = 'periodic_sync_task';
  
  final SyncService _syncService;
  final Connectivity _connectivity;

  BackgroundSyncService(this._syncService, this._connectivity);

  Future<void> initialize() async {
    await Workmanager().initialize(
      callbackDispatcher,
      isInDebugMode: false, // Set to true for debugging
    );

    // Listen to connectivity changes
    _connectivity.onConnectivityChanged.listen(_onConnectivityChanged);
  }

  void _onConnectivityChanged(ConnectivityResult result) {
    if (result != ConnectivityResult.none) {
      // Device came online, trigger sync
      scheduleImmediateSync();
    }
  }

  Future<void> scheduleImmediateSync() async {
    await Workmanager().registerOneOffTask(
      'immediate_sync_${DateTime.now().millisecondsSinceEpoch}',
      _syncTaskName,
      constraints: Constraints(
        networkType: NetworkType.connected,
        requiresBatteryNotLow: false,
        requiresCharging: false,
        requiresDeviceIdle: false,
        requiresStorageNotLow: false,
      ),
    );
  }

  Future<void> schedulePeriodicSync() async {
    await Workmanager().registerPeriodicTask(
      'periodic_sync',
      _periodicSyncTaskName,
      frequency: const Duration(hours: 1), // Sync every hour
      constraints: Constraints(
        networkType: NetworkType.connected,
        requiresBatteryNotLow: true,
        requiresCharging: false,
        requiresDeviceIdle: false,
        requiresStorageNotLow: true,
      ),
    );
  }

  Future<void> cancelAllSyncTasks() async {
    await Workmanager().cancelAll();
  }

  Future<void> cancelPeriodicSync() async {
    await Workmanager().cancelByUniqueName('periodic_sync');
  }
}

// Top-level function for background execution
@pragma('vm:entry-point')
void callbackDispatcher() {
  Workmanager().executeTask((task, inputData) async {
    try {
      // Initialize dependency injection for background isolate
      await configureDependencies();
      
      final syncService = getIt<SyncService>();
      
      switch (task) {
        case BackgroundSyncService._syncTaskName:
        case BackgroundSyncService._periodicSyncTaskName:
          final result = await syncService.syncAll();
          print('Background sync completed: ${result.success}');
          return result.success;
        default:
          return false;
      }
    } catch (e) {
      print('Background sync error: $e');
      return false;
    }
  });
}

// Connectivity service for monitoring network status
@singleton
class ConnectivityService {
  final Connectivity _connectivity;
  final StreamController<bool> _connectionStatusController = StreamController<bool>.broadcast();

  ConnectivityService(this._connectivity) {
    _initConnectivity();
    _connectivity.onConnectivityChanged.listen(_updateConnectionStatus);
  }

  Stream<bool> get connectionStatusStream => _connectionStatusController.stream;

  bool _isConnected = false;
  bool get isConnected => _isConnected;

  Future<void> _initConnectivity() async {
    final result = await _connectivity.checkConnectivity();
    _updateConnectionStatus(result);
  }

  void _updateConnectionStatus(ConnectivityResult result) {
    final wasConnected = _isConnected;
    _isConnected = result != ConnectivityResult.none;
    
    if (_isConnected != wasConnected) {
      _connectionStatusController.add(_isConnected);
    }
  }

  void dispose() {
    _connectionStatusController.close();
  }
}

// Offline queue manager
@singleton
class OfflineQueueManager {
  final SyncService _syncService;
  final ConnectivityService _connectivityService;
  final BackgroundSyncService _backgroundSyncService;

  OfflineQueueManager(
    this._syncService,
    this._connectivityService,
    this._backgroundSyncService,
  ) {
    _connectivityService.connectionStatusStream.listen(_onConnectivityChanged);
  }

  void _onConnectivityChanged(bool isConnected) {
    if (isConnected) {
      // Device came online, process offline queue
      _processOfflineQueue();
    }
  }

  Future<void> _processOfflineQueue() async {
    try {
      // Schedule immediate sync when coming online
      await _backgroundSyncService.scheduleImmediateSync();
    } catch (e) {
      print('Error processing offline queue: $e');
    }
  }

  Future<void> queueOfflineOperation(
    String tableName,
    SyncOperation operation,
    Map<String, dynamic> data,
  ) async {
    await _syncService.queueOperation(tableName, operation, data);
    
    // If online, try to sync immediately
    if (_connectivityService.isConnected) {
      await _backgroundSyncService.scheduleImmediateSync();
    }
  }
}

// Conflict resolution strategies
enum ConflictStrategy {
  serverWins,
  clientWins,
  lastWriteWins,
  merge,
  manual,
}

// Conflict resolution service
@singleton
class ConflictResolutionService {

  Future<Map<String, dynamic>> resolveConflict(
    Map<String, dynamic> localData,
    Map<String, dynamic> serverData,
    ConflictStrategy strategy,
  ) async {
    switch (strategy) {
      case ConflictStrategy.serverWins:
        return serverData;
      
      case ConflictStrategy.clientWins:
        return localData;
      
      case ConflictStrategy.lastWriteWins:
        final localUpdated = DateTime.parse(localData['updated_at']);
        final serverUpdated = DateTime.parse(serverData['updated_at']);
        return serverUpdated.isAfter(localUpdated) ? serverData : localData;
      
      case ConflictStrategy.merge:
        return _mergeData(localData, serverData);
      
      case ConflictStrategy.manual:
        // Store conflict for manual resolution
        await _storeConflictForManualResolution(localData, serverData);
        return serverData; // Use server data temporarily
    }
  }

  Map<String, dynamic> _mergeData(
    Map<String, dynamic> localData,
    Map<String, dynamic> serverData,
  ) {
    final merged = Map<String, dynamic>.from(serverData);
    
    // Merge non-conflicting fields
    localData.forEach((key, value) {
      if (!serverData.containsKey(key) || serverData[key] == null) {
        merged[key] = value;
      }
    });
    
    return merged;
  }

  Future<void> _storeConflictForManualResolution(
    Map<String, dynamic> localData,
    Map<String, dynamic> serverData,
  ) async {
    // Store conflict in database for manual resolution
    // This would be implemented based on your UI requirements
  }
}

// Data integrity service
@singleton
class DataIntegrityService {
  final DatabaseService _databaseService;

  DataIntegrityService(this._databaseService);

  Future<bool> validateDataIntegrity() async {
    try {
      // Check for orphaned records
      await _checkOrphanedRecords();
      
      // Check for data consistency
      await _checkDataConsistency();
      
      // Check for corrupted data
      await _checkCorruptedData();
      
      return true;
    } catch (e) {
      print('Data integrity check failed: $e');
      return false;
    }
  }

  Future<void> _checkOrphanedRecords() async {
    // Check for staff records without users
    final orphanedStaff = await _databaseService.rawQuery('''
      SELECT s.* FROM staff s
      LEFT JOIN users u ON s.user_id = u.id
      WHERE u.id IS NULL
    ''');

    if (orphanedStaff.isNotEmpty) {
      // Clean up orphaned staff records
      await _databaseService.rawDelete('''
        DELETE FROM staff 
        WHERE user_id NOT IN (SELECT id FROM users)
      ''');
    }

    // Check for messages without conversations
    final orphanedMessages = await _databaseService.rawQuery('''
      SELECT m.* FROM messages m
      LEFT JOIN conversations c ON m.conversation_id = c.id
      WHERE c.id IS NULL
    ''');

    if (orphanedMessages.isNotEmpty) {
      // Clean up orphaned messages
      await _databaseService.rawDelete('''
        DELETE FROM messages 
        WHERE conversation_id NOT IN (SELECT id FROM conversations)
      ''');
    }
  }

  Future<void> _checkDataConsistency() async {
    // Check if user.is_staff matches existence of staff record
    await _databaseService.rawUpdate('''
      UPDATE users SET is_staff = 0 
      WHERE is_staff = 1 AND id NOT IN (SELECT user_id FROM staff)
    ''');

    await _databaseService.rawUpdate('''
      UPDATE users SET is_staff = 1 
      WHERE is_staff = 0 AND id IN (SELECT user_id FROM staff)
    ''');
  }

  Future<void> _checkCorruptedData() async {
    // Check for invalid dates
    final invalidDates = await _databaseService.rawQuery('''
      SELECT * FROM users 
      WHERE created_at IS NULL OR updated_at IS NULL
      OR created_at = '' OR updated_at = ''
    ''');

    if (invalidDates.isNotEmpty) {
      // Fix invalid dates
      final now = DateTime.now().toIso8601String();
      await _databaseService.rawUpdate('''
        UPDATE users 
        SET created_at = ?, updated_at = ?
        WHERE created_at IS NULL OR updated_at IS NULL
        OR created_at = '' OR updated_at = ''
      ''', [now, now]);
    }
  }

  Future<void> repairDatabase() async {
    await _checkOrphanedRecords();
    await _checkDataConsistency();
    await _checkCorruptedData();
  }
}
