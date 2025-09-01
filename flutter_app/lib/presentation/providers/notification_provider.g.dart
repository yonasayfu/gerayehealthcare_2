// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'notification_provider.dart';

// **************************************************************************
// RiverpodGenerator
// **************************************************************************

String _$fcmTokenHash() => r'a75f4b01938cd97f352293575b006b3acdf49698';

/// See also [fcmToken].
@ProviderFor(fcmToken)
final fcmTokenProvider = AutoDisposeFutureProvider<String?>.internal(
  fcmToken,
  name: r'fcmTokenProvider',
  debugGetCreateSourceHash:
      const bool.fromEnvironment('dart.vm.product') ? null : _$fcmTokenHash,
  dependencies: null,
  allTransitiveDependencies: null,
);

typedef FcmTokenRef = AutoDisposeFutureProviderRef<String?>;
String _$notificationPermissionHash() =>
    r'b3f7f07cc7a98220fd61e4a058d7fdad230808e6';

/// See also [notificationPermission].
@ProviderFor(notificationPermission)
final notificationPermissionProvider = AutoDisposeFutureProvider<bool>.internal(
  notificationPermission,
  name: r'notificationPermissionProvider',
  debugGetCreateSourceHash: const bool.fromEnvironment('dart.vm.product')
      ? null
      : _$notificationPermissionHash,
  dependencies: null,
  allTransitiveDependencies: null,
);

typedef NotificationPermissionRef = AutoDisposeFutureProviderRef<bool>;
String _$unreadNotificationsCountHash() =>
    r'96842506274736cc98e7952a86d6f21c61ee8720';

/// See also [unreadNotificationsCount].
@ProviderFor(unreadNotificationsCount)
final unreadNotificationsCountProvider = AutoDisposeProvider<int>.internal(
  unreadNotificationsCount,
  name: r'unreadNotificationsCountProvider',
  debugGetCreateSourceHash: const bool.fromEnvironment('dart.vm.product')
      ? null
      : _$unreadNotificationsCountHash,
  dependencies: null,
  allTransitiveDependencies: null,
);

typedef UnreadNotificationsCountRef = AutoDisposeProviderRef<int>;
String _$recentNotificationsHash() =>
    r'7ba030b9e61e0286a08614da0c7c4b7f7f861126';

/// See also [recentNotifications].
@ProviderFor(recentNotifications)
final recentNotificationsProvider =
    AutoDisposeProvider<List<NotificationPayload>>.internal(
  recentNotifications,
  name: r'recentNotificationsProvider',
  debugGetCreateSourceHash: const bool.fromEnvironment('dart.vm.product')
      ? null
      : _$recentNotificationsHash,
  dependencies: null,
  allTransitiveDependencies: null,
);

typedef RecentNotificationsRef
    = AutoDisposeProviderRef<List<NotificationPayload>>;
String _$notificationHash() => r'7053d3a6df643e1f670de7475a170898862fe61e';

/// See also [Notification].
@ProviderFor(Notification)
final notificationProvider =
    AutoDisposeNotifierProvider<Notification, NotificationState>.internal(
  Notification.new,
  name: r'notificationProvider',
  debugGetCreateSourceHash:
      const bool.fromEnvironment('dart.vm.product') ? null : _$notificationHash,
  dependencies: null,
  allTransitiveDependencies: null,
);

typedef _$Notification = AutoDisposeNotifier<NotificationState>;
// ignore_for_file: type=lint
// ignore_for_file: subtype_of_sealed_class, invalid_use_of_internal_member, invalid_use_of_visible_for_testing_member
