import 'package:injectable/injectable.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../constants/app_constants.dart';

class LocalStorageService {
  final SharedPreferences _prefs;

  LocalStorageService(this._prefs);

  // Theme settings
  Future<void> setThemeMode(String themeMode) async {
    await _prefs.setString(AppConstants.themeKey, themeMode);
  }

  String getThemeMode() {
    return _prefs.getString(AppConstants.themeKey) ?? 'system';
  }

  // Language settings
  Future<void> setLanguage(String languageCode) async {
    await _prefs.setString(AppConstants.languageKey, languageCode);
  }

  String getLanguage() {
    return _prefs.getString(AppConstants.languageKey) ?? 'en';
  }

  // Generic methods for different data types
  
  // String
  Future<void> setString(String key, String value) async {
    await _prefs.setString(key, value);
  }

  String? getString(String key) {
    return _prefs.getString(key);
  }

  // Integer
  Future<void> setInt(String key, int value) async {
    await _prefs.setInt(key, value);
  }

  int? getInt(String key) {
    return _prefs.getInt(key);
  }

  // Double
  Future<void> setDouble(String key, double value) async {
    await _prefs.setDouble(key, value);
  }

  double? getDouble(String key) {
    return _prefs.getDouble(key);
  }

  // Boolean
  Future<void> setBool(String key, bool value) async {
    await _prefs.setBool(key, value);
  }

  bool? getBool(String key) {
    return _prefs.getBool(key);
  }

  // String List
  Future<void> setStringList(String key, List<String> value) async {
    await _prefs.setStringList(key, value);
  }

  List<String>? getStringList(String key) {
    return _prefs.getStringList(key);
  }

  // Remove key
  Future<void> remove(String key) async {
    await _prefs.remove(key);
  }

  // Check if key exists
  bool containsKey(String key) {
    return _prefs.containsKey(key);
  }

  // Get all keys
  Set<String> getKeys() {
    return _prefs.getKeys();
  }

  // Clear all data
  Future<void> clear() async {
    await _prefs.clear();
  }

  // Cache management methods
  Future<void> setCacheTimestamp(String key) async {
    await _prefs.setInt('${key}_timestamp', DateTime.now().millisecondsSinceEpoch);
  }

  bool isCacheValid(String key, Duration maxAge) {
    final timestamp = _prefs.getInt('${key}_timestamp');
    if (timestamp == null) return false;
    
    final cacheTime = DateTime.fromMillisecondsSinceEpoch(timestamp);
    final now = DateTime.now();
    
    return now.difference(cacheTime) < maxAge;
  }

  Future<void> clearCache(String key) async {
    await Future.wait([
      remove(key),
      remove('${key}_timestamp'),
    ]);
  }

  // App-specific cache methods
  Future<void> cacheConversations(String data) async {
    await setString(AppConstants.conversationsCacheKey, data);
    await setCacheTimestamp(AppConstants.conversationsCacheKey);
  }

  String? getCachedConversations() {
    if (isCacheValid(AppConstants.conversationsCacheKey, AppConstants.mediumCacheDuration)) {
      return getString(AppConstants.conversationsCacheKey);
    }
    return null;
  }

  Future<void> cacheMessages(String conversationId, String data) async {
    final key = '${AppConstants.messagesCacheKey}_$conversationId';
    await setString(key, data);
    await setCacheTimestamp(key);
  }

  String? getCachedMessages(String conversationId) {
    final key = '${AppConstants.messagesCacheKey}_$conversationId';
    if (isCacheValid(key, AppConstants.shortCacheDuration)) {
      return getString(key);
    }
    return null;
  }

  Future<void> cacheNotifications(String data) async {
    await setString(AppConstants.notificationsCacheKey, data);
    await setCacheTimestamp(AppConstants.notificationsCacheKey);
  }

  String? getCachedNotifications() {
    if (isCacheValid(AppConstants.notificationsCacheKey, AppConstants.shortCacheDuration)) {
      return getString(AppConstants.notificationsCacheKey);
    }
    return null;
  }

  // Clear all app caches
  Future<void> clearAllCaches() async {
    await Future.wait([
      clearCache(AppConstants.conversationsCacheKey),
      clearCache(AppConstants.notificationsCacheKey),
      // Clear all message caches
      ...getKeys()
          .where((key) => key.startsWith(AppConstants.messagesCacheKey))
          .map((key) => remove(key)),
    ]);
  }
}
