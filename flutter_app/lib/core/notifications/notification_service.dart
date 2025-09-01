import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:injectable/injectable.dart';

import '../storage/local_storage_service.dart';

// Top-level function for background message handling
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  print('Handling a background message: ${message.messageId}');
  
  // Handle background notification
  final notificationService = NotificationService._();
  await notificationService._handleBackgroundMessage(message);
}

@singleton
class NotificationService {
  final FirebaseMessaging _firebaseMessaging = FirebaseMessaging.instance;
  final FlutterLocalNotificationsPlugin _localNotifications = FlutterLocalNotificationsPlugin();
  final LocalStorageService _localStorage;

  NotificationService._(this._localStorage);

  @factoryMethod
  static NotificationService create(LocalStorageService localStorage) {
    return NotificationService._(localStorage);
  }

  static const String _fcmTokenKey = 'fcm_token';
  static const String _notificationPermissionKey = 'notification_permission';

  // Notification channels
  static const AndroidNotificationChannel _messageChannel = AndroidNotificationChannel(
    'messages',
    'Messages',
    description: 'Notifications for new messages',
    importance: Importance.high,
    sound: RawResourceAndroidNotificationSound('notification_sound'),
  );

  static const AndroidNotificationChannel _generalChannel = AndroidNotificationChannel(
    'general',
    'General',
    description: 'General app notifications',
    importance: Importance.defaultImportance,
  );

  // Stream controllers for notification events
  final _notificationStreamController = StreamController<NotificationPayload>.broadcast();
  final _tokenStreamController = StreamController<String>.broadcast();

  Stream<NotificationPayload> get notificationStream => _notificationStreamController.stream;
  Stream<String> get tokenStream => _tokenStreamController.stream;

  Future<void> initialize() async {
    try {
      // Initialize Firebase
      await Firebase.initializeApp();

      // Set background message handler
      FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);

      // Initialize local notifications
      await _initializeLocalNotifications();

      // Request permissions
      await _requestPermissions();

      // Configure FCM
      await _configureFCM();

      // Get and store FCM token
      await _getFCMToken();

      print('NotificationService initialized successfully');
    } catch (e) {
      print('Error initializing NotificationService: $e');
    }
  }

  Future<void> _initializeLocalNotifications() async {
    const androidSettings = AndroidInitializationSettings('@mipmap/ic_launcher');
    const iosSettings = DarwinInitializationSettings(
      requestAlertPermission: false,
      requestBadgePermission: false,
      requestSoundPermission: false,
    );

    const initSettings = InitializationSettings(
      android: androidSettings,
      iOS: iosSettings,
    );

    await _localNotifications.initialize(
      initSettings,
      onDidReceiveNotificationResponse: _onNotificationTapped,
    );

    // Create notification channels for Android
    if (Platform.isAndroid) {
      await _localNotifications
          .resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>()
          ?.createNotificationChannel(_messageChannel);

      await _localNotifications
          .resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>()
          ?.createNotificationChannel(_generalChannel);
    }
  }

  Future<void> _requestPermissions() async {
    // Request FCM permissions
    final settings = await _firebaseMessaging.requestPermission(
      alert: true,
      announcement: false,
      badge: true,
      carPlay: false,
      criticalAlert: false,
      provisional: false,
      sound: true,
    );

    // Store permission status
    await _localStorage.setBool(_notificationPermissionKey, 
        settings.authorizationStatus == AuthorizationStatus.authorized);

    print('Notification permission status: ${settings.authorizationStatus}');

    // Request local notification permissions for iOS
    if (Platform.isIOS) {
      await _localNotifications
          .resolvePlatformSpecificImplementation<IOSFlutterLocalNotificationsPlugin>()
          ?.requestPermissions(
            alert: true,
            badge: true,
            sound: true,
          );
    }
  }

  Future<void> _configureFCM() async {
    // Configure foreground message handling
    FirebaseMessaging.onMessage.listen(_handleForegroundMessage);

    // Configure message opened app handling
    FirebaseMessaging.onMessageOpenedApp.listen(_handleMessageOpenedApp);

    // Handle initial message if app was opened from notification
    final initialMessage = await _firebaseMessaging.getInitialMessage();
    if (initialMessage != null) {
      _handleMessageOpenedApp(initialMessage);
    }
  }

  Future<void> _getFCMToken() async {
    try {
      final token = await _firebaseMessaging.getToken();
      if (token != null) {
        await _localStorage.setString(_fcmTokenKey, token);
        _tokenStreamController.add(token);
        print('FCM Token: $token');
      }

      // Listen for token refresh
      _firebaseMessaging.onTokenRefresh.listen((token) async {
        await _localStorage.setString(_fcmTokenKey, token);
        _tokenStreamController.add(token);
        print('FCM Token refreshed: $token');
      });
    } catch (e) {
      print('Error getting FCM token: $e');
    }
  }

  Future<void> _handleForegroundMessage(RemoteMessage message) async {
    print('Received foreground message: ${message.messageId}');

    // Show local notification when app is in foreground
    await _showLocalNotification(message);

    // Emit notification event
    _emitNotificationEvent(message);
  }

  Future<void> _handleBackgroundMessage(RemoteMessage message) async {
    print('Handling background message: ${message.messageId}');
    
    // Store notification for later processing
    await _storeNotification(message);
  }

  void _handleMessageOpenedApp(RemoteMessage message) {
    print('Message opened app: ${message.messageId}');
    
    // Emit notification event
    _emitNotificationEvent(message);
  }

  void _onNotificationTapped(NotificationResponse response) {
    print('Notification tapped: ${response.payload}');
    
    if (response.payload != null) {
      try {
        final payload = jsonDecode(response.payload!);
        _notificationStreamController.add(NotificationPayload.fromMap(payload));
      } catch (e) {
        print('Error parsing notification payload: $e');
      }
    }
  }

  Future<void> _showLocalNotification(RemoteMessage message) async {
    final notification = message.notification;
    final data = message.data;

    if (notification == null) return;

    final notificationType = data['type'] ?? 'general';
    final channel = notificationType == 'message' ? _messageChannel : _generalChannel;

    final androidDetails = AndroidNotificationDetails(
      channel.id,
      channel.name,
      channelDescription: channel.description,
      importance: channel.importance,
      priority: Priority.high,
      sound: channel.sound,
      icon: '@mipmap/ic_launcher',
    );

    const iosDetails = DarwinNotificationDetails(
      presentAlert: true,
      presentBadge: true,
      presentSound: true,
    );

    final details = NotificationDetails(
      android: androidDetails,
      iOS: iosDetails,
    );

    await _localNotifications.show(
      message.hashCode,
      notification.title,
      notification.body,
      details,
      payload: jsonEncode(data),
    );
  }

  void _emitNotificationEvent(RemoteMessage message) {
    final payload = NotificationPayload(
      id: message.messageId ?? '',
      title: message.notification?.title ?? '',
      body: message.notification?.body ?? '',
      data: message.data,
      receivedAt: DateTime.now(),
    );

    _notificationStreamController.add(payload);
  }

  Future<void> _storeNotification(RemoteMessage message) async {
    // Store notification in local storage for later processing
    final notifications = await getStoredNotifications();
    notifications.add(NotificationPayload(
      id: message.messageId ?? '',
      title: message.notification?.title ?? '',
      body: message.notification?.body ?? '',
      data: message.data,
      receivedAt: DateTime.now(),
    ));

    await _localStorage.setString('stored_notifications', jsonEncode(
      notifications.map((n) => n.toMap()).toList(),
    ));
  }

  // Public methods
  Future<String?> getFCMToken() async {
    return _localStorage.getString(_fcmTokenKey);
  }

  Future<bool> hasNotificationPermission() async {
    return _localStorage.getBool(_notificationPermissionKey) ?? false;
  }

  Future<List<NotificationPayload>> getStoredNotifications() async {
    final stored = _localStorage.getString('stored_notifications');
    if (stored == null) return [];

    try {
      final List<dynamic> decoded = jsonDecode(stored);
      return decoded.map((item) => NotificationPayload.fromMap(item)).toList();
    } catch (e) {
      print('Error parsing stored notifications: $e');
      return [];
    }
  }

  Future<void> clearStoredNotifications() async {
    await _localStorage.remove('stored_notifications');
  }

  Future<void> showLocalNotification({
    required String title,
    required String body,
    String? payload,
    String type = 'general',
  }) async {
    final channel = type == 'message' ? _messageChannel : _generalChannel;

    final androidDetails = AndroidNotificationDetails(
      channel.id,
      channel.name,
      channelDescription: channel.description,
      importance: channel.importance,
      priority: Priority.high,
    );

    const iosDetails = DarwinNotificationDetails();

    final details = NotificationDetails(
      android: androidDetails,
      iOS: iosDetails,
    );

    await _localNotifications.show(
      DateTime.now().millisecondsSinceEpoch ~/ 1000,
      title,
      body,
      details,
      payload: payload,
    );
  }

  Future<void> cancelNotification(int id) async {
    await _localNotifications.cancel(id);
  }

  Future<void> cancelAllNotifications() async {
    await _localNotifications.cancelAll();
  }

  void dispose() {
    _notificationStreamController.close();
    _tokenStreamController.close();
  }
}

class NotificationPayload {
  final String id;
  final String title;
  final String body;
  final Map<String, dynamic> data;
  final DateTime receivedAt;

  NotificationPayload({
    required this.id,
    required this.title,
    required this.body,
    required this.data,
    required this.receivedAt,
  });

  Map<String, dynamic> toMap() {
    return {
      'id': id,
      'title': title,
      'body': body,
      'data': data,
      'receivedAt': receivedAt.toIso8601String(),
    };
  }

  factory NotificationPayload.fromMap(Map<String, dynamic> map) {
    return NotificationPayload(
      id: map['id'] ?? '',
      title: map['title'] ?? '',
      body: map['body'] ?? '',
      data: Map<String, dynamic>.from(map['data'] ?? {}),
      receivedAt: DateTime.parse(map['receivedAt']),
    );
  }
}
