import 'package:flutter/material.dart';

class AppEmptyState extends StatelessWidget {
  final IconData? icon;
  final Widget? customIcon;
  final String title;
  final String? subtitle;
  final String? actionText;
  final VoidCallback? onActionPressed;
  final bool showAction;

  const AppEmptyState({
    super.key,
    this.icon,
    this.customIcon,
    required this.title,
    this.subtitle,
    this.actionText,
    this.onActionPressed,
    this.showAction = true,
  });

  const AppEmptyState.noData({
    super.key,
    this.title = 'No Data Available',
    this.subtitle = 'There\'s nothing to show here yet.',
    this.actionText = 'Refresh',
    this.onActionPressed,
    this.showAction = true,
  }) : icon = Icons.inbox_outlined,
       customIcon = null;

  const AppEmptyState.noResults({
    super.key,
    this.title = 'No Results Found',
    this.subtitle = 'Try adjusting your search or filters.',
    this.actionText = 'Clear Filters',
    this.onActionPressed,
    this.showAction = true,
  }) : icon = Icons.search_off_outlined,
       customIcon = null;

  const AppEmptyState.noConnection({
    super.key,
    this.title = 'No Internet Connection',
    this.subtitle = 'Please check your connection and try again.',
    this.actionText = 'Retry',
    this.onActionPressed,
    this.showAction = true,
  }) : icon = Icons.wifi_off_outlined,
       customIcon = null;

  const AppEmptyState.error({
    super.key,
    this.title = 'Something Went Wrong',
    this.subtitle = 'We encountered an error. Please try again.',
    this.actionText = 'Retry',
    this.onActionPressed,
    this.showAction = true,
  }) : icon = Icons.error_outline,
       customIcon = null;

  const AppEmptyState.maintenance({
    super.key,
    this.title = 'Under Maintenance',
    this.subtitle = 'We\'re working to improve your experience. Please check back later.',
    this.actionText,
    this.onActionPressed,
    this.showAction = false,
  }) : icon = Icons.build_outlined,
       customIcon = null;

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final colorScheme = theme.colorScheme;

    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Icon
            Container(
              width: 120,
              height: 120,
              decoration: BoxDecoration(
                color: colorScheme.surfaceVariant.withOpacity(0.3),
                shape: BoxShape.circle,
              ),
              child: customIcon ??
                  Icon(
                    icon ?? Icons.inbox_outlined,
                    size: 64,
                    color: colorScheme.onSurfaceVariant,
                  ),
            ),
            
            const SizedBox(height: 24),
            
            // Title
            Text(
              title,
              style: theme.textTheme.headlineSmall?.copyWith(
                fontWeight: FontWeight.w600,
                color: colorScheme.onSurface,
              ),
              textAlign: TextAlign.center,
            ),
            
            if (subtitle != null) ...[
              const SizedBox(height: 8),
              Text(
                subtitle!,
                style: theme.textTheme.bodyLarge?.copyWith(
                  color: colorScheme.onSurfaceVariant,
                ),
                textAlign: TextAlign.center,
              ),
            ],
            
            if (showAction && actionText != null && onActionPressed != null) ...[
              const SizedBox(height: 32),
              ElevatedButton(
                onPressed: onActionPressed,
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 32,
                    vertical: 16,
                  ),
                ),
                child: Text(actionText!),
              ),
            ],
          ],
        ),
      ),
    );
  }
}

// Specialized empty states for different scenarios
class EmptyConversations extends StatelessWidget {
  final VoidCallback? onStartChat;

  const EmptyConversations({super.key, this.onStartChat});

  @override
  Widget build(BuildContext context) {
    return AppEmptyState(
      icon: Icons.chat_bubble_outline,
      title: 'No Conversations Yet',
      subtitle: 'Start a conversation to connect with others.',
      actionText: 'Start Chat',
      onActionPressed: onStartChat,
    );
  }
}

class EmptyMessages extends StatelessWidget {
  final VoidCallback? onSendMessage;

  const EmptyMessages({super.key, this.onSendMessage});

  @override
  Widget build(BuildContext context) {
    return AppEmptyState(
      icon: Icons.message_outlined,
      title: 'No Messages',
      subtitle: 'Send your first message to start the conversation.',
      actionText: 'Send Message',
      onActionPressed: onSendMessage,
    );
  }
}

class EmptyNotifications extends StatelessWidget {
  final VoidCallback? onRefresh;

  const EmptyNotifications({super.key, this.onRefresh});

  @override
  Widget build(BuildContext context) {
    return AppEmptyState(
      icon: Icons.notifications_none_outlined,
      title: 'No Notifications',
      subtitle: 'You\'re all caught up! No new notifications.',
      actionText: 'Refresh',
      onActionPressed: onRefresh,
    );
  }
}

class EmptyUsers extends StatelessWidget {
  final VoidCallback? onRefresh;

  const EmptyUsers({super.key, this.onRefresh});

  @override
  Widget build(BuildContext context) {
    return AppEmptyState(
      icon: Icons.people_outline,
      title: 'No Users Found',
      subtitle: 'No users match your current search or filters.',
      actionText: 'Refresh',
      onActionPressed: onRefresh,
    );
  }
}
