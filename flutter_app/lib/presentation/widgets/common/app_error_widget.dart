import 'package:flutter/material.dart';

import '../../../core/errors/failures.dart';

class AppErrorWidget extends StatelessWidget {
  final String? title;
  final String message;
  final String? actionText;
  final VoidCallback? onRetry;
  final bool showRetry;
  final IconData? icon;

  const AppErrorWidget({
    super.key,
    this.title,
    required this.message,
    this.actionText = 'Retry',
    this.onRetry,
    this.showRetry = true,
    this.icon,
  });

  factory AppErrorWidget.fromFailure(
    Failure failure, {
    VoidCallback? onRetry,
    String? actionText = 'Retry',
    bool showRetry = true,
  }) {
    String title;
    IconData icon;

    switch (failure.runtimeType) {
      case NetworkFailure:
        title = 'Connection Error';
        icon = Icons.wifi_off_outlined;
        break;
      case ServerFailure:
        title = 'Server Error';
        icon = Icons.error_outline;
        break;
      case AuthFailure:
        title = 'Authentication Error';
        icon = Icons.lock_outline;
        break;
      case ValidationFailure:
        title = 'Validation Error';
        icon = Icons.warning_outlined;
        break;
      case PermissionFailure:
        title = 'Permission Denied';
        icon = Icons.block_outlined;
        break;
      case TimeoutFailure:
        title = 'Request Timeout';
        icon = Icons.access_time_outlined;
        break;
      default:
        title = 'Error';
        icon = Icons.error_outline;
    }

    return AppErrorWidget(
      title: title,
      message: failure.message,
      actionText: actionText,
      onRetry: onRetry,
      showRetry: showRetry,
      icon: icon,
    );
  }

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
            // Error Icon
            Container(
              width: 120,
              height: 120,
              decoration: BoxDecoration(
                color: colorScheme.errorContainer.withOpacity(0.3),
                shape: BoxShape.circle,
              ),
              child: Icon(
                icon ?? Icons.error_outline,
                size: 64,
                color: colorScheme.error,
              ),
            ),
            
            const SizedBox(height: 24),
            
            // Title
            if (title != null) ...[
              Text(
                title!,
                style: theme.textTheme.headlineSmall?.copyWith(
                  fontWeight: FontWeight.w600,
                  color: colorScheme.onSurface,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 8),
            ],
            
            // Error Message
            Text(
              message,
              style: theme.textTheme.bodyLarge?.copyWith(
                color: colorScheme.onSurfaceVariant,
              ),
              textAlign: TextAlign.center,
            ),
            
            if (showRetry && onRetry != null) ...[
              const SizedBox(height: 32),
              ElevatedButton.icon(
                onPressed: onRetry,
                icon: const Icon(Icons.refresh),
                label: Text(actionText ?? 'Retry'),
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 32,
                    vertical: 16,
                  ),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}

// Specialized error widgets
class NetworkErrorWidget extends StatelessWidget {
  final VoidCallback? onRetry;

  const NetworkErrorWidget({super.key, this.onRetry});

  @override
  Widget build(BuildContext context) {
    return AppErrorWidget(
      title: 'No Internet Connection',
      message: 'Please check your internet connection and try again.',
      icon: Icons.wifi_off_outlined,
      onRetry: onRetry,
    );
  }
}

class ServerErrorWidget extends StatelessWidget {
  final VoidCallback? onRetry;
  final String? message;

  const ServerErrorWidget({super.key, this.onRetry, this.message});

  @override
  Widget build(BuildContext context) {
    return AppErrorWidget(
      title: 'Server Error',
      message: message ?? 'Something went wrong on our end. Please try again later.',
      icon: Icons.dns_outlined,
      onRetry: onRetry,
    );
  }
}

class AuthErrorWidget extends StatelessWidget {
  final VoidCallback? onRetry;

  const AuthErrorWidget({super.key, this.onRetry});

  @override
  Widget build(BuildContext context) {
    return AppErrorWidget(
      title: 'Authentication Required',
      message: 'Please sign in to continue.',
      icon: Icons.lock_outline,
      actionText: 'Sign In',
      onRetry: onRetry,
    );
  }
}

class PermissionErrorWidget extends StatelessWidget {
  final VoidCallback? onRetry;
  final String? message;

  const PermissionErrorWidget({super.key, this.onRetry, this.message});

  @override
  Widget build(BuildContext context) {
    return AppErrorWidget(
      title: 'Permission Denied',
      message: message ?? 'You don\'t have permission to access this resource.',
      icon: Icons.block_outlined,
      actionText: 'Request Access',
      onRetry: onRetry,
    );
  }
}

// Error snackbar helper
class ErrorSnackBar {
  static void show(BuildContext context, String message, {VoidCallback? onRetry}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: Theme.of(context).colorScheme.error,
        action: onRetry != null
            ? SnackBarAction(
                label: 'Retry',
                textColor: Theme.of(context).colorScheme.onError,
                onPressed: onRetry,
              )
            : null,
        behavior: SnackBarBehavior.floating,
        margin: const EdgeInsets.all(16),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(8),
        ),
      ),
    );
  }

  static void showFromFailure(BuildContext context, Failure failure, {VoidCallback? onRetry}) {
    show(context, failure.message, onRetry: onRetry);
  }
}
