import 'dart:convert';
import 'package:injectable/injectable.dart';

import '../../core/storage/local_storage_service.dart';
import '../../core/storage/secure_storage_service.dart';
import '../../domain/entities/app_settings.dart';

abstract class SettingsLocalDataSource {
  Future<AppSettings> getSettings();
  Future<void> saveSettings(AppSettings settings);
  Future<void> resetSettings();
  Future<void> exportSettings();
  Future<void> importSettings(String settingsJson);
}

@LazySingleton(as: SettingsLocalDataSource)
class SettingsLocalDataSourceImpl implements SettingsLocalDataSource {
  final LocalStorageService _localStorage;
  final SecureStorageService _secureStorage;

  static const String _settingsKey = 'app_settings';
  static const String _secureSettingsKey = 'secure_app_settings';

  SettingsLocalDataSourceImpl(this._localStorage, this._secureStorage);

  @override
  Future<AppSettings> getSettings() async {
    try {
      // Get regular settings
      final settingsJson = _localStorage.getString(_settingsKey);
      
      // Get secure settings
      final secureSettingsJson = await _secureStorage.read(_secureSettingsKey);
      
      if (settingsJson != null) {
        final settingsMap = jsonDecode(settingsJson) as Map<String, dynamic>;
        
        // Merge secure settings if available
        if (secureSettingsJson != null) {
          final secureSettingsMap = jsonDecode(secureSettingsJson) as Map<String, dynamic>;
          settingsMap.addAll(secureSettingsMap);
        }
        
        return _mapToAppSettings(settingsMap);
      }
      
      return const AppSettings(); // Return default settings
    } catch (e) {
      return const AppSettings(); // Return default settings on error
    }
  }

  @override
  Future<void> saveSettings(AppSettings settings) async {
    try {
      final settingsMap = _appSettingsToMap(settings);
      
      // Separate secure and regular settings
      final secureSettings = <String, dynamic>{};
      final regularSettings = <String, dynamic>{};
      
      // Define which settings are secure
      final secureKeys = {
        'isBiometricEnabled',
        'isAutoLockEnabled',
        'autoLockDuration',
        'isLocationEnabled',
        'isAnalyticsEnabled',
        'isCrashReportingEnabled',
      };
      
      settingsMap.forEach((key, value) {
        if (secureKeys.contains(key)) {
          secureSettings[key] = value;
        } else {
          regularSettings[key] = value;
        }
      });
      
      // Save regular settings
      await _localStorage.setString(_settingsKey, jsonEncode(regularSettings));
      
      // Save secure settings
      if (secureSettings.isNotEmpty) {
        await _secureStorage.write(_secureSettingsKey, jsonEncode(secureSettings));
      }
    } catch (e) {
      throw Exception('Failed to save settings: $e');
    }
  }

  @override
  Future<void> resetSettings() async {
    try {
      await _localStorage.remove(_settingsKey);
      await _secureStorage.delete(_secureSettingsKey);
    } catch (e) {
      throw Exception('Failed to reset settings: $e');
    }
  }

  @override
  Future<void> exportSettings() async {
    try {
      final settings = await getSettings();
      final settingsMap = _appSettingsToMap(settings);
      
      // Remove sensitive data from export
      settingsMap.remove('isBiometricEnabled');
      settingsMap.remove('isAutoLockEnabled');
      settingsMap.remove('autoLockDuration');
      
      final exportJson = jsonEncode(settingsMap);
      
      // TODO: Implement file export functionality
      // This could save to device storage or share via platform channels
      print('Settings export: $exportJson');
    } catch (e) {
      throw Exception('Failed to export settings: $e');
    }
  }

  @override
  Future<void> importSettings(String settingsJson) async {
    try {
      final settingsMap = jsonDecode(settingsJson) as Map<String, dynamic>;
      final currentSettings = await getSettings();
      
      // Merge imported settings with current settings, preserving secure settings
      final mergedSettings = _appSettingsToMap(currentSettings);
      
      // Only import non-sensitive settings
      final allowedKeys = {
        'themeMode',
        'language',
        'fontSize',
        'isNotificationsEnabled',
        'isPushNotificationsEnabled',
        'isEmailNotificationsEnabled',
        'notificationFrequency',
        'isSoundEnabled',
        'isVibrationEnabled',
        'isDataSaverEnabled',
        'isAutoDownloadEnabled',
        'dateFormat',
        'timeFormat',
        'currency',
        'timezone',
      };
      
      settingsMap.forEach((key, value) {
        if (allowedKeys.contains(key)) {
          mergedSettings[key] = value;
        }
      });
      
      final newSettings = _mapToAppSettings(mergedSettings);
      await saveSettings(newSettings);
    } catch (e) {
      throw Exception('Failed to import settings: $e');
    }
  }

  AppSettings _mapToAppSettings(Map<String, dynamic> map) {
    return AppSettings(
      themeMode: AppThemeMode.values.firstWhere(
        (e) => e.name == map['themeMode'],
        orElse: () => AppThemeMode.system,
      ),
      language: AppLanguage.fromCode(map['language'] ?? 'en'),
      isDarkMode: map['isDarkMode'] ?? false,
      fontSize: FontSize.values.firstWhere(
        (e) => e.name == map['fontSize'],
        orElse: () => FontSize.medium,
      ),
      isNotificationsEnabled: map['isNotificationsEnabled'] ?? true,
      isPushNotificationsEnabled: map['isPushNotificationsEnabled'] ?? true,
      isEmailNotificationsEnabled: map['isEmailNotificationsEnabled'] ?? true,
      notificationFrequency: NotificationFrequency.values.firstWhere(
        (e) => e.name == map['notificationFrequency'],
        orElse: () => NotificationFrequency.immediately,
      ),
      isSoundEnabled: map['isSoundEnabled'] ?? true,
      isVibrationEnabled: map['isVibrationEnabled'] ?? true,
      isBiometricEnabled: map['isBiometricEnabled'] ?? false,
      isAutoLockEnabled: map['isAutoLockEnabled'] ?? false,
      autoLockDuration: map['autoLockDuration'] ?? 5,
      isDataSaverEnabled: map['isDataSaverEnabled'] ?? false,
      isAutoDownloadEnabled: map['isAutoDownloadEnabled'] ?? true,
      isLocationEnabled: map['isLocationEnabled'] ?? false,
      isAnalyticsEnabled: map['isAnalyticsEnabled'] ?? true,
      isCrashReportingEnabled: map['isCrashReportingEnabled'] ?? true,
      dateFormat: map['dateFormat'] ?? 'MM/dd/yyyy',
      timeFormat: map['timeFormat'] ?? '12h',
      currency: map['currency'] ?? 'USD',
      timezone: map['timezone'] ?? 'UTC',
    );
  }

  Map<String, dynamic> _appSettingsToMap(AppSettings settings) {
    return {
      'themeMode': settings.themeMode.name,
      'language': settings.language.code,
      'isDarkMode': settings.isDarkMode,
      'fontSize': settings.fontSize.name,
      'isNotificationsEnabled': settings.isNotificationsEnabled,
      'isPushNotificationsEnabled': settings.isPushNotificationsEnabled,
      'isEmailNotificationsEnabled': settings.isEmailNotificationsEnabled,
      'notificationFrequency': settings.notificationFrequency.name,
      'isSoundEnabled': settings.isSoundEnabled,
      'isVibrationEnabled': settings.isVibrationEnabled,
      'isBiometricEnabled': settings.isBiometricEnabled,
      'isAutoLockEnabled': settings.isAutoLockEnabled,
      'autoLockDuration': settings.autoLockDuration,
      'isDataSaverEnabled': settings.isDataSaverEnabled,
      'isAutoDownloadEnabled': settings.isAutoDownloadEnabled,
      'isLocationEnabled': settings.isLocationEnabled,
      'isAnalyticsEnabled': settings.isAnalyticsEnabled,
      'isCrashReportingEnabled': settings.isCrashReportingEnabled,
      'dateFormat': settings.dateFormat,
      'timeFormat': settings.timeFormat,
      'currency': settings.currency,
      'timezone': settings.timezone,
    };
  }
}
