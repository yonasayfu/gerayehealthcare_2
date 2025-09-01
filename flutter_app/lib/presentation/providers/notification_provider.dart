import 'dart:async';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:riverpod_annotation/riverpod_annotation.dart';

import '../../core/di/injection.dart';
import '../../core/notifications/notification_service.dart';

part 'notification_provider.g.dart';

// Notification state class
class NotificationState {
  final List<NotificationPayload> notifications;
  final bool hasPermission;
  final String? fcmToken;
  final bool isInitialized;
  final int unreadCount;

  const NotificationState({
    this.notifications = const [],
    this.hasPermission = false,
    this.fcmToken,
    this.isInitialized = false,
    this.unreadCount = 0,
  });

  NotificationState copyWith({
    List<NotificationPayload>? notifications,
    bool? hasPermission,
    String? fcmToken,
    bool? isInitialized,
    int? unreadCount,
  }) {
    return NotificationState(
      notifications: notifications ?? this.notifications,
      hasPermission: hasPermission ?? this.hasPermission,
      fcmToken: fcmToken ?? this.fcmToken,
      isInitialized: isInitialized ?? this.isInitialized,
      unreadCount: unreadCount ?? this.unreadCount,
    );
  }
}

// Notification provider
@riverpod
class Notification extends _$Notification {
  late final NotificationService _notificationService;
  StreamSubscription? _notificationSubscription;
  StreamSubscription? _tokenSubscription;

  @override
  NotificationState build() {
    _notificationService = getIt<NotificationService>();
    _initializeNotifications();
    return const NotificationState();
  }

  Future<void> _initializeNotifications() async {
    try {
      // Initialize notification service
      await _notificationService.initialize();

      // Get initial state
      final hasPermission = await _notificationService.hasNotificationPermission();
      final fcmToken = await _notificationService.getFCMToken();
      final storedNotifications = await _notificationService.getStoredNotifications();

      state = state.copyWith(
        hasPermission: hasPermission,
        fcmToken: fcmToken,
        notifications: storedNotifications,
        unreadCount: storedNotifications.length,
        isInitialized: true,
      );

      // Listen to new notifications
      _notificationSubscription = _notificationService.notificationStream.listen(
        _handleNewNotification,
      );

      // Listen to token updates
      _tokenSubscription = _notificationService.tokenStream.listen(
        (token) {
          state = state.copyWith(fcmToken: token);
        },
      );
    } catch (e) {
      print('Error initializing notifications: $e');
      state = state.copyWith(isInitialized: true);
    }
  }

  void _handleNewNotification(NotificationPayload notification) {
    final updatedNotifications = [notification, ...state.notifications];
    state = state.copyWith(
      notifications: updatedNotifications,
      unreadCount: state.unreadCount + 1,
    );
  }

  Future<void> markAsRead(String notificationId) async {
    final updatedNotifications = state.notifications.map((notification) {
      if (notification.id == notificationId) {
        // Mark as read (you might want to add a 'read' field to NotificationPayload)
        return notification;
      }
      return notification;
    }).toList();

    state = state.copyWith(
      notifications: updatedNotifications,
      unreadCount: state.unreadCount > 0 ? state.unreadCount - 1 : 0,
    );
  }

  Future<void> markAllAsRead() async {
    state = state.copyWith(unreadCount: 0);
  }

  Future<void> clearNotification(String notificationId) async {
    final updatedNotifications = state.notifications
        .where((notification) => notification.id != notificationId)
        .toList();

    state = state.copyWith(
      notifications: updatedNotifications,
      unreadCount: state.unreadCount > 0 ? state.unreadCount - 1 : 0,
    );
  }

  Future<void> clearAllNotifications() async {
    await _notificationService.clearStoredNotifications();
    await _notificationService.cancelAllNotifications();
    
    state = state.copyWith(
      notifications: [],
      unreadCount: 0,
    );
  }

  Future<void> showLocalNotification({
    required String title,
    required String body,
    String? payload,
    String type = 'general',
  }) async {
    await _notificationService.showLocalNotification(
      title: title,
      body: body,
      payload: payload,
      type: type,
    );
  }

  Future<void> requestPermission() async {
    // Re-initialize to request permissions
    await _initializeNotifications();
  }

  @override
  void dispose() {
    _notificationSubscription?.cancel();
    _tokenSubscription?.cancel();
    super.dispose();
  }
}

// FCM Token provider
@riverpod
Future<String?> fcmToken(FcmTokenRef ref) async {
  final notificationService = getIt<NotificationService>();
  return await notificationService.getFCMToken();
}

// Notification permission provider
@riverpod
Future<bool> notificationPermission(NotificationPermissionRef ref) async {
  final notificationService = getIt<NotificationService>();
  return await notificationService.hasNotificationPermission();
}

// Unread notifications count provider
@riverpod
int unreadNotificationsCount(UnreadNotificationsCountRef ref) {
  final notificationState = ref.watch(notificationProvider);
  return notificationState.unreadCount;
}

// Recent notifications provider (last 10)
@riverpod
List<NotificationPayload> recentNotifications(RecentNotificationsRef ref) {
  final notificationState = ref.watch(notificationProvider);
  return notificationState.notifications.take(10).toList();
}
