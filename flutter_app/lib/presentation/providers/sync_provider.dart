import 'dart:async';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:riverpod_annotation/riverpod_annotation.dart';

import '../../core/di/injection.dart';
import '../../core/sync/background_sync_service.dart';
import '../../core/sync/sync_service.dart';

part 'sync_provider.g.dart';

// Sync state class
class SyncState {
  final bool isOnline;
  final bool isSyncing;
  final double syncProgress;
  final String? currentOperation;
  final DateTime? lastSyncTime;
  final String? error;
  final int pendingOperations;

  const SyncState({
    this.isOnline = false,
    this.isSyncing = false,
    this.syncProgress = 0.0,
    this.currentOperation,
    this.lastSyncTime,
    this.error,
    this.pendingOperations = 0,
  });

  SyncState copyWith({
    bool? isOnline,
    bool? isSyncing,
    double? syncProgress,
    String? currentOperation,
    DateTime? lastSyncTime,
    String? error,
    int? pendingOperations,
  }) {
    return SyncState(
      isOnline: isOnline ?? this.isOnline,
      isSyncing: isSyncing ?? this.isSyncing,
      syncProgress: syncProgress ?? this.syncProgress,
      currentOperation: currentOperation ?? this.currentOperation,
      lastSyncTime: lastSyncTime ?? this.lastSyncTime,
      error: error ?? this.error,
      pendingOperations: pendingOperations ?? this.pendingOperations,
    );
  }
}

// Sync provider
@riverpod
class Sync extends _$Sync {
  late final SyncService _syncService;
  late final ConnectivityService _connectivityService;
  late final BackgroundSyncService _backgroundSyncService;
  late final OfflineQueueManager _offlineQueueManager;
  
  StreamSubscription? _connectivitySubscription;
  StreamSubscription? _syncProgressSubscription;

  @override
  SyncState build() {
    _syncService = getIt<SyncService>();
    _connectivityService = getIt<ConnectivityService>();
    _backgroundSyncService = getIt<BackgroundSyncService>();
    _offlineQueueManager = getIt<OfflineQueueManager>();

    _initializeSync();
    return SyncState(isOnline: _connectivityService.isConnected);
  }

  void _initializeSync() {
    // Listen to connectivity changes
    _connectivitySubscription = _connectivityService.connectionStatusStream.listen(
      (isOnline) {
        state = state.copyWith(isOnline: isOnline);
        if (isOnline) {
          // Auto-sync when coming online
          syncAll();
        }
      },
    );

    // Listen to sync progress
    _syncProgressSubscription = _syncService.syncStatusStream.listen(
      (progress) {
        state = state.copyWith(
          isSyncing: progress.isInProgress,
          syncProgress: progress.progress,
          currentOperation: progress.currentOperation,
        );
      },
    );

    // Initialize background sync
    _backgroundSyncService.initialize();
    _backgroundSyncService.schedulePeriodicSync();

    // Load initial state
    _loadInitialState();
  }

  Future<void> _loadInitialState() async {
    final lastSync = await _syncService.lastSyncTime;
    final pendingCount = await _getPendingOperationsCount();
    
    state = state.copyWith(
      lastSyncTime: lastSync,
      pendingOperations: pendingCount,
    );
  }

  Future<int> _getPendingOperationsCount() async {
    // This would query the sync_queue table to get pending operations count
    // For now, return 0 as placeholder
    return 0;
  }

  Future<void> syncAll({bool force = false}) async {
    if (state.isSyncing && !force) return;

    state = state.copyWith(
      isSyncing: true,
      error: null,
      syncProgress: 0.0,
    );

    try {
      final result = await _syncService.syncAll(force: force);
      
      if (result.success) {
        final lastSync = await _syncService.lastSyncTime;
        final pendingCount = await _getPendingOperationsCount();
        
        state = state.copyWith(
          isSyncing: false,
          syncProgress: 1.0,
          lastSyncTime: lastSync,
          pendingOperations: pendingCount,
          currentOperation: null,
        );
      } else {
        state = state.copyWith(
          isSyncing: false,
          error: result.message,
          currentOperation: null,
        );
      }
    } catch (e) {
      state = state.copyWith(
        isSyncing: false,
        error: e.toString(),
        currentOperation: null,
      );
    }
  }

  Future<void> syncUsers() async {
    if (!state.isOnline) {
      state = state.copyWith(error: 'No internet connection');
      return;
    }

    // Implement specific user sync logic
    await syncAll();
  }

  Future<void> syncMessages() async {
    if (!state.isOnline) {
      state = state.copyWith(error: 'No internet connection');
      return;
    }

    // Implement specific message sync logic
    await syncAll();
  }

  Future<void> syncConversations() async {
    if (!state.isOnline) {
      state = state.copyWith(error: 'No internet connection');
      return;
    }

    // Implement specific conversation sync logic
    await syncAll();
  }

  Future<void> retryFailedOperations() async {
    if (!state.isOnline) {
      state = state.copyWith(error: 'No internet connection');
      return;
    }

    await syncAll(force: true);
  }

  void clearError() {
    state = state.copyWith(error: null);
  }

  String getLastSyncText() {
    if (state.lastSyncTime == null) {
      return 'Never synced';
    }

    final now = DateTime.now();
    final difference = now.difference(state.lastSyncTime!);

    if (difference.inMinutes < 1) {
      return 'Just now';
    } else if (difference.inMinutes < 60) {
      return '${difference.inMinutes} minutes ago';
    } else if (difference.inHours < 24) {
      return '${difference.inHours} hours ago';
    } else {
      return '${difference.inDays} days ago';
    }
  }

  String getSyncStatusText() {
    if (state.isSyncing) {
      return state.currentOperation ?? 'Syncing...';
    } else if (!state.isOnline) {
      return 'Offline';
    } else if (state.error != null) {
      return 'Sync failed';
    } else if (state.pendingOperations > 0) {
      return '${state.pendingOperations} pending operations';
    } else {
      return 'Up to date';
    }
  }

  @override
  void dispose() {
    _connectivitySubscription?.cancel();
    _syncProgressSubscription?.cancel();
    super.dispose();
  }
}

// Connectivity provider
@riverpod
class Connectivity extends _$Connectivity {
  late final ConnectivityService _connectivityService;
  StreamSubscription? _subscription;

  @override
  bool build() {
    _connectivityService = getIt<ConnectivityService>();
    
    _subscription = _connectivityService.connectionStatusStream.listen(
      (isConnected) {
        state = isConnected;
      },
    );

    return _connectivityService.isConnected;
  }

  @override
  void dispose() {
    _subscription?.cancel();
    super.dispose();
  }
}

// Offline queue provider
@riverpod
class OfflineQueue extends _$OfflineQueue {
  late final OfflineQueueManager _offlineQueueManager;

  @override
  List<Map<String, dynamic>> build() {
    _offlineQueueManager = getIt<OfflineQueueManager>();
    return [];
  }

  Future<void> queueOperation(
    String tableName,
    SyncOperation operation,
    Map<String, dynamic> data,
  ) async {
    await _offlineQueueManager.queueOfflineOperation(tableName, operation, data);
    // Refresh the queue state
    // This would load pending operations from database
    // For now, just trigger a rebuild
    ref.invalidateSelf();
  }

  Future<void> clearQueue() async {
    // Clear all pending operations
    // This would clear the sync_queue table
    state = [];
  }
}

// Data integrity provider
@riverpod
class DataIntegrity extends _$DataIntegrity {
  late final DataIntegrityService _dataIntegrityService;

  @override
  bool build() {
    _dataIntegrityService = getIt<DataIntegrityService>();
    return true; // Assume data is valid initially
  }

  Future<bool> validateIntegrity() async {
    final isValid = await _dataIntegrityService.validateDataIntegrity();
    state = isValid;
    return isValid;
  }

  Future<void> repairDatabase() async {
    await _dataIntegrityService.repairDatabase();
    state = true;
  }
}
