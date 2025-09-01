import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/notifications/notification_service.dart';
import '../../providers/notification_provider.dart';
import '../../widgets/common/app_empty_state.dart';
import '../../widgets/common/sync_status_widget.dart';

class NotificationsPage extends ConsumerStatefulWidget {
  const NotificationsPage({super.key});

  @override
  ConsumerState<NotificationsPage> createState() => _NotificationsPageState();
}

class _NotificationsPageState extends ConsumerState<NotificationsPage> {
  @override
  void initState() {
    super.initState();
    // Mark all notifications as read when page is opened
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(notificationProvider.notifier).markAllAsRead();
    });
  }

  @override
  Widget build(BuildContext context) {
    final notificationState = ref.watch(notificationProvider);
    final theme = Theme.of(context);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Notifications'),
        actions: [
          const SyncIndicator(),
          if (notificationState.notifications.isNotEmpty)
            PopupMenuButton(
              itemBuilder: (context) => [
                const PopupMenuItem(
                  value: 'mark_all_read',
                  child: ListTile(
                    leading: Icon(Icons.mark_email_read),
                    title: Text('Mark all as read'),
                    contentPadding: EdgeInsets.zero,
                  ),
                ),
                const PopupMenuItem(
                  value: 'clear_all',
                  child: ListTile(
                    leading: Icon(Icons.clear_all, color: Colors.red),
                    title: Text('Clear all', style: TextStyle(color: Colors.red)),
                    contentPadding: EdgeInsets.zero,
                  ),
                ),
              ],
              onSelected: _handleMenuAction,
            ),
        ],
      ),
      body: _buildBody(notificationState, theme),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _showTestNotification,
        icon: const Icon(Icons.notifications),
        label: const Text('Test Notification'),
      ),
    );
  }

  Widget _buildBody(NotificationState notificationState, ThemeData theme) {
    if (!notificationState.isInitialized) {
      return const Center(child: CircularProgressIndicator());
    }

    if (!notificationState.hasPermission) {
      return _buildPermissionRequest(theme);
    }

    if (notificationState.notifications.isEmpty) {
      return _buildEmptyState(theme);
    }

    return _buildNotificationsList(notificationState.notifications, theme);
  }

  Widget _buildPermissionRequest(ThemeData theme) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.notifications_off,
              size: 80,
              color: theme.colorScheme.onSurfaceVariant.withOpacity(0.5),
            ),
            const SizedBox(height: 24),
            Text(
              'Notifications Disabled',
              style: theme.textTheme.headlineSmall?.copyWith(
                color: theme.colorScheme.onSurfaceVariant,
              ),
            ),
            const SizedBox(height: 12),
            Text(
              'Enable notifications to receive important updates and messages.',
              style: theme.textTheme.bodyMedium?.copyWith(
                color: theme.colorScheme.onSurfaceVariant.withOpacity(0.7),
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 24),
            ElevatedButton.icon(
              onPressed: () => ref.read(notificationProvider.notifier).requestPermission(),
              icon: const Icon(Icons.notifications),
              label: const Text('Enable Notifications'),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildEmptyState(ThemeData theme) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.notifications_none,
              size: 80,
              color: theme.colorScheme.onSurfaceVariant.withOpacity(0.5),
            ),
            const SizedBox(height: 24),
            Text(
              'No notifications yet',
              style: theme.textTheme.headlineSmall?.copyWith(
                color: theme.colorScheme.onSurfaceVariant,
              ),
            ),
            const SizedBox(height: 12),
            Text(
              'You\'ll see notifications here when you receive them.',
              style: theme.textTheme.bodyMedium?.copyWith(
                color: theme.colorScheme.onSurfaceVariant.withOpacity(0.7),
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildNotificationsList(List<NotificationPayload> notifications, ThemeData theme) {
    return RefreshIndicator(
      onRefresh: () async {
        // Refresh notifications
      },
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: notifications.length,
        itemBuilder: (context, index) {
          final notification = notifications[index];
          return _buildNotificationTile(notification, theme);
        },
      ),
    );
  }

  Widget _buildNotificationTile(NotificationPayload notification, ThemeData theme) {
    final notificationType = notification.data['type'] ?? 'general';
    final isMessage = notificationType == 'message';

    return Card(
      margin: const EdgeInsets.only(bottom: 8),
      child: ListTile(
        leading: CircleAvatar(
          backgroundColor: isMessage
              ? theme.colorScheme.primary.withOpacity(0.1)
              : theme.colorScheme.secondary.withOpacity(0.1),
          child: Icon(
            isMessage ? Icons.message : Icons.notifications,
            color: isMessage ? theme.colorScheme.primary : theme.colorScheme.secondary,
          ),
        ),
        title: Text(
          notification.title,
          style: const TextStyle(fontWeight: FontWeight.w600),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (notification.body.isNotEmpty) ...[
              const SizedBox(height: 4),
              Text(notification.body),
            ],
            const SizedBox(height: 8),
            Text(
              _formatTime(notification.receivedAt),
              style: TextStyle(
                fontSize: 12,
                color: Colors.grey[600],
              ),
            ),
          ],
        ),
        trailing: PopupMenuButton(
          itemBuilder: (context) => [
            PopupMenuItem(
              value: 'mark_read',
              child: const ListTile(
                leading: Icon(Icons.mark_email_read),
                title: Text('Mark as read'),
                contentPadding: EdgeInsets.zero,
              ),
            ),
            PopupMenuItem(
              value: 'delete',
              child: const ListTile(
                leading: Icon(Icons.delete, color: Colors.red),
                title: Text('Delete', style: TextStyle(color: Colors.red)),
                contentPadding: EdgeInsets.zero,
              ),
            ),
          ],
          onSelected: (value) => _handleNotificationAction(value, notification),
        ),
        onTap: () => _handleNotificationTap(notification),
      ),
    );
  }

  String _formatTime(DateTime dateTime) {
    final now = DateTime.now();
    final difference = now.difference(dateTime);

    if (difference.inDays > 0) {
      return '${difference.inDays}d ago';
    } else if (difference.inHours > 0) {
      return '${difference.inHours}h ago';
    } else if (difference.inMinutes > 0) {
      return '${difference.inMinutes}m ago';
    } else {
      return 'Just now';
    }
  }

  void _handleMenuAction(String action) {
    switch (action) {
      case 'mark_all_read':
        ref.read(notificationProvider.notifier).markAllAsRead();
        break;
      case 'clear_all':
        _showClearAllConfirmation();
        break;
    }
  }

  void _handleNotificationAction(String action, NotificationPayload notification) {
    switch (action) {
      case 'mark_read':
        ref.read(notificationProvider.notifier).markAsRead(notification.id);
        break;
      case 'delete':
        ref.read(notificationProvider.notifier).clearNotification(notification.id);
        break;
    }
  }

  void _handleNotificationTap(NotificationPayload notification) {
    // Handle notification tap based on type
    final type = notification.data['type'];
    switch (type) {
      case 'message':
        // Navigate to chat
        final userId = notification.data['user_id'];
        if (userId != null) {
          // Navigate to chat with user
        }
        break;
      case 'user_update':
        // Navigate to users page
        break;
      default:
        // Show notification details
        _showNotificationDetails(notification);
    }
  }

  void _showNotificationDetails(NotificationPayload notification) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text(notification.title),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (notification.body.isNotEmpty) ...[
              Text(notification.body),
              const SizedBox(height: 16),
            ],
            Text(
              'Received: ${_formatTime(notification.receivedAt)}',
              style: TextStyle(
                fontSize: 12,
                color: Colors.grey[600],
              ),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Close'),
          ),
        ],
      ),
    );
  }

  void _showClearAllConfirmation() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Clear All Notifications'),
        content: const Text('Are you sure you want to clear all notifications? This action cannot be undone.'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Cancel'),
          ),
          TextButton(
            onPressed: () {
              Navigator.of(context).pop();
              ref.read(notificationProvider.notifier).clearAllNotifications();
            },
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('Clear All'),
          ),
        ],
      ),
    );
  }

  void _showTestNotification() {
    ref.read(notificationProvider.notifier).showLocalNotification(
      title: 'Test Notification',
      body: 'This is a test notification sent at ${DateTime.now().toString()}',
      type: 'general',
    );

    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Test notification sent!')),
    );
  }
}
