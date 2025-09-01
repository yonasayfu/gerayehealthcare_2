import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../providers/sync_provider.dart';

class SyncStatusWidget extends ConsumerWidget {
  final bool showDetails;
  final VoidCallback? onTap;

  const SyncStatusWidget({
    super.key,
    this.showDetails = false,
    this.onTap,
  });

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final syncState = ref.watch(syncProvider);
    final theme = Theme.of(context);

    return GestureDetector(
      onTap: onTap ?? () => _showSyncDialog(context, ref),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        decoration: BoxDecoration(
          color: _getStatusColor(syncState, theme).withOpacity(0.1),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(
            color: _getStatusColor(syncState, theme).withOpacity(0.3),
            width: 1,
          ),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            _buildStatusIcon(syncState, theme),
            const SizedBox(width: 8),
            if (showDetails) ...[
              Flexible(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      ref.read(syncProvider.notifier).getSyncStatusText(),
                      style: theme.textTheme.bodySmall?.copyWith(
                        color: _getStatusColor(syncState, theme),
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    if (syncState.lastSyncTime != null)
                      Text(
                        'Last sync: ${ref.read(syncProvider.notifier).getLastSyncText()}',
                        style: theme.textTheme.bodySmall?.copyWith(
                          color: theme.colorScheme.onSurfaceVariant,
                          fontSize: 10,
                        ),
                      ),
                  ],
                ),
              ),
            ] else ...[
              Text(
                ref.read(syncProvider.notifier).getSyncStatusText(),
                style: theme.textTheme.bodySmall?.copyWith(
                  color: _getStatusColor(syncState, theme),
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }

  Widget _buildStatusIcon(SyncState syncState, ThemeData theme) {
    if (syncState.isSyncing) {
      return SizedBox(
        width: 16,
        height: 16,
        child: CircularProgressIndicator(
          strokeWidth: 2,
          valueColor: AlwaysStoppedAnimation<Color>(
            _getStatusColor(syncState, theme),
          ),
        ),
      );
    }

    IconData iconData;
    if (!syncState.isOnline) {
      iconData = Icons.cloud_off;
    } else if (syncState.error != null) {
      iconData = Icons.error_outline;
    } else if (syncState.pendingOperations > 0) {
      iconData = Icons.cloud_upload;
    } else {
      iconData = Icons.cloud_done;
    }

    return Icon(
      iconData,
      size: 16,
      color: _getStatusColor(syncState, theme),
    );
  }

  Color _getStatusColor(SyncState syncState, ThemeData theme) {
    if (syncState.isSyncing) {
      return theme.colorScheme.primary;
    } else if (!syncState.isOnline) {
      return Colors.grey;
    } else if (syncState.error != null) {
      return theme.colorScheme.error;
    } else if (syncState.pendingOperations > 0) {
      return Colors.orange;
    } else {
      return Colors.green;
    }
  }

  void _showSyncDialog(BuildContext context, WidgetRef ref) {
    showDialog(
      context: context,
      builder: (context) => SyncStatusDialog(),
    );
  }
}

class SyncStatusDialog extends ConsumerWidget {
  const SyncStatusDialog({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final syncState = ref.watch(syncProvider);
    final theme = Theme.of(context);

    return AlertDialog(
      title: const Text('Sync Status'),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Connection Status
          _buildStatusRow(
            context,
            'Connection',
            syncState.isOnline ? 'Online' : 'Offline',
            syncState.isOnline ? Icons.wifi : Icons.wifi_off,
            syncState.isOnline ? Colors.green : Colors.grey,
          ),

          const SizedBox(height: 16),

          // Sync Status
          _buildStatusRow(
            context,
            'Sync Status',
            ref.read(syncProvider.notifier).getSyncStatusText(),
            syncState.isSyncing ? Icons.sync : Icons.cloud_done,
            syncState.error != null ? theme.colorScheme.error : Colors.green,
          ),

          if (syncState.isSyncing) ...[
            const SizedBox(height: 16),
            LinearProgressIndicator(value: syncState.syncProgress),
            const SizedBox(height: 8),
            if (syncState.currentOperation != null)
              Text(
                syncState.currentOperation!,
                style: theme.textTheme.bodySmall,
              ),
          ],

          const SizedBox(height: 16),

          // Last Sync
          _buildStatusRow(
            context,
            'Last Sync',
            ref.read(syncProvider.notifier).getLastSyncText(),
            Icons.schedule,
            theme.colorScheme.onSurfaceVariant,
          ),

          if (syncState.pendingOperations > 0) ...[
            const SizedBox(height: 16),
            _buildStatusRow(
              context,
              'Pending Operations',
              '${syncState.pendingOperations}',
              Icons.cloud_upload,
              Colors.orange,
            ),
          ],

          if (syncState.error != null) ...[
            const SizedBox(height: 16),
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: theme.colorScheme.errorContainer,
                borderRadius: BorderRadius.circular(8),
              ),
              child: Row(
                children: [
                  Icon(
                    Icons.error_outline,
                    color: theme.colorScheme.error,
                    size: 20,
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      syncState.error!,
                      style: theme.textTheme.bodySmall?.copyWith(
                        color: theme.colorScheme.onErrorContainer,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ],
      ),
      actions: [
        if (syncState.error != null)
          TextButton(
            onPressed: () {
              ref.read(syncProvider.notifier).clearError();
              Navigator.of(context).pop();
            },
            child: const Text('Clear Error'),
          ),
        if (syncState.pendingOperations > 0 && syncState.isOnline)
          TextButton(
            onPressed: () {
              ref.read(syncProvider.notifier).retryFailedOperations();
              Navigator.of(context).pop();
            },
            child: const Text('Retry'),
          ),
        if (syncState.isOnline && !syncState.isSyncing)
          TextButton(
            onPressed: () {
              ref.read(syncProvider.notifier).syncAll(force: true);
              Navigator.of(context).pop();
            },
            child: const Text('Sync Now'),
          ),
        TextButton(
          onPressed: () => Navigator.of(context).pop(),
          child: const Text('Close'),
        ),
      ],
    );
  }

  Widget _buildStatusRow(
    BuildContext context,
    String label,
    String value,
    IconData icon,
    Color color,
  ) {
    final theme = Theme.of(context);
    
    return Row(
      children: [
        Icon(icon, size: 20, color: color),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                label,
                style: theme.textTheme.bodySmall?.copyWith(
                  color: theme.colorScheme.onSurfaceVariant,
                ),
              ),
              Text(
                value,
                style: theme.textTheme.bodyMedium?.copyWith(
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}

// Compact sync indicator for app bars
class SyncIndicator extends ConsumerWidget {
  const SyncIndicator({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final syncState = ref.watch(syncProvider);
    final theme = Theme.of(context);

    if (syncState.isSyncing) {
      return Padding(
        padding: const EdgeInsets.all(8.0),
        child: SizedBox(
          width: 20,
          height: 20,
          child: CircularProgressIndicator(
            strokeWidth: 2,
            valueColor: AlwaysStoppedAnimation<Color>(
              theme.colorScheme.onSurface,
            ),
          ),
        ),
      );
    }

    if (!syncState.isOnline) {
      return IconButton(
        icon: const Icon(Icons.cloud_off),
        onPressed: () => _showSyncDialog(context, ref),
        tooltip: 'Offline',
      );
    }

    if (syncState.error != null) {
      return IconButton(
        icon: Icon(Icons.error_outline, color: theme.colorScheme.error),
        onPressed: () => _showSyncDialog(context, ref),
        tooltip: 'Sync Error',
      );
    }

    if (syncState.pendingOperations > 0) {
      return IconButton(
        icon: const Icon(Icons.cloud_upload, color: Colors.orange),
        onPressed: () => _showSyncDialog(context, ref),
        tooltip: '${syncState.pendingOperations} pending operations',
      );
    }

    return IconButton(
      icon: const Icon(Icons.cloud_done, color: Colors.green),
      onPressed: () => _showSyncDialog(context, ref),
      tooltip: 'Synced',
    );
  }

  void _showSyncDialog(BuildContext context, WidgetRef ref) {
    showDialog(
      context: context,
      builder: (context) => const SyncStatusDialog(),
    );
  }
}
