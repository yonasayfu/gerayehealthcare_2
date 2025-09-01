import 'package:equatable/equatable.dart';
import 'package:flutter/material.dart';

enum AppThemeMode {
  system,
  light,
  dark,
}

enum AppLanguage {
  english('en', 'English'),
  spanish('es', 'Español'),
  french('fr', 'Français'),
  german('de', 'Deutsch'),
  italian('it', 'Italiano'),
  portuguese('pt', 'Português'),
  russian('ru', 'Русский'),
  chinese('zh', '中文'),
  japanese('ja', '日本語'),
  korean('ko', '한국어'),
  arabic('ar', 'العربية');

  const AppLanguage(this.code, this.displayName);
  final String code;
  final String displayName;

  static AppLanguage fromCode(String code) {
    return AppLanguage.values.firstWhere(
      (lang) => lang.code == code,
      orElse: () => AppLanguage.english,
    );
  }
}

enum NotificationFrequency {
  immediately,
  every15Minutes,
  hourly,
  daily,
  never,
}

enum FontSize {
  small,
  medium,
  large,
  extraLarge,
}

class AppSettings extends Equatable {
  final AppThemeMode themeMode;
  final AppLanguage language;
  final bool isDarkMode;
  final FontSize fontSize;
  final bool isNotificationsEnabled;
  final bool isPushNotificationsEnabled;
  final bool isEmailNotificationsEnabled;
  final NotificationFrequency notificationFrequency;
  final bool isSoundEnabled;
  final bool isVibrationEnabled;
  final bool isBiometricEnabled;
  final bool isAutoLockEnabled;
  final int autoLockDuration; // in minutes
  final bool isDataSaverEnabled;
  final bool isAutoDownloadEnabled;
  final bool isLocationEnabled;
  final bool isAnalyticsEnabled;
  final bool isCrashReportingEnabled;
  final String dateFormat;
  final String timeFormat;
  final String currency;
  final String timezone;

  const AppSettings({
    this.themeMode = AppThemeMode.system,
    this.language = AppLanguage.english,
    this.isDarkMode = false,
    this.fontSize = FontSize.medium,
    this.isNotificationsEnabled = true,
    this.isPushNotificationsEnabled = true,
    this.isEmailNotificationsEnabled = true,
    this.notificationFrequency = NotificationFrequency.immediately,
    this.isSoundEnabled = true,
    this.isVibrationEnabled = true,
    this.isBiometricEnabled = false,
    this.isAutoLockEnabled = false,
    this.autoLockDuration = 5,
    this.isDataSaverEnabled = false,
    this.isAutoDownloadEnabled = true,
    this.isLocationEnabled = false,
    this.isAnalyticsEnabled = true,
    this.isCrashReportingEnabled = true,
    this.dateFormat = 'MM/dd/yyyy',
    this.timeFormat = '12h',
    this.currency = 'USD',
    this.timezone = 'UTC',
  });

  @override
  List<Object?> get props => [
        themeMode,
        language,
        isDarkMode,
        fontSize,
        isNotificationsEnabled,
        isPushNotificationsEnabled,
        isEmailNotificationsEnabled,
        notificationFrequency,
        isSoundEnabled,
        isVibrationEnabled,
        isBiometricEnabled,
        isAutoLockEnabled,
        autoLockDuration,
        isDataSaverEnabled,
        isAutoDownloadEnabled,
        isLocationEnabled,
        isAnalyticsEnabled,
        isCrashReportingEnabled,
        dateFormat,
        timeFormat,
        currency,
        timezone,
      ];

  ThemeMode get materialThemeMode {
    switch (themeMode) {
      case AppThemeMode.light:
        return ThemeMode.light;
      case AppThemeMode.dark:
        return ThemeMode.dark;
      case AppThemeMode.system:
        return ThemeMode.system;
    }
  }

  Locale get locale => Locale(language.code);

  double get fontSizeMultiplier {
    switch (fontSize) {
      case FontSize.small:
        return 0.85;
      case FontSize.medium:
        return 1.0;
      case FontSize.large:
        return 1.15;
      case FontSize.extraLarge:
        return 1.3;
    }
  }

  AppSettings copyWith({
    AppThemeMode? themeMode,
    AppLanguage? language,
    bool? isDarkMode,
    FontSize? fontSize,
    bool? isNotificationsEnabled,
    bool? isPushNotificationsEnabled,
    bool? isEmailNotificationsEnabled,
    NotificationFrequency? notificationFrequency,
    bool? isSoundEnabled,
    bool? isVibrationEnabled,
    bool? isBiometricEnabled,
    bool? isAutoLockEnabled,
    int? autoLockDuration,
    bool? isDataSaverEnabled,
    bool? isAutoDownloadEnabled,
    bool? isLocationEnabled,
    bool? isAnalyticsEnabled,
    bool? isCrashReportingEnabled,
    String? dateFormat,
    String? timeFormat,
    String? currency,
    String? timezone,
  }) {
    return AppSettings(
      themeMode: themeMode ?? this.themeMode,
      language: language ?? this.language,
      isDarkMode: isDarkMode ?? this.isDarkMode,
      fontSize: fontSize ?? this.fontSize,
      isNotificationsEnabled: isNotificationsEnabled ?? this.isNotificationsEnabled,
      isPushNotificationsEnabled: isPushNotificationsEnabled ?? this.isPushNotificationsEnabled,
      isEmailNotificationsEnabled: isEmailNotificationsEnabled ?? this.isEmailNotificationsEnabled,
      notificationFrequency: notificationFrequency ?? this.notificationFrequency,
      isSoundEnabled: isSoundEnabled ?? this.isSoundEnabled,
      isVibrationEnabled: isVibrationEnabled ?? this.isVibrationEnabled,
      isBiometricEnabled: isBiometricEnabled ?? this.isBiometricEnabled,
      isAutoLockEnabled: isAutoLockEnabled ?? this.isAutoLockEnabled,
      autoLockDuration: autoLockDuration ?? this.autoLockDuration,
      isDataSaverEnabled: isDataSaverEnabled ?? this.isDataSaverEnabled,
      isAutoDownloadEnabled: isAutoDownloadEnabled ?? this.isAutoDownloadEnabled,
      isLocationEnabled: isLocationEnabled ?? this.isLocationEnabled,
      isAnalyticsEnabled: isAnalyticsEnabled ?? this.isAnalyticsEnabled,
      isCrashReportingEnabled: isCrashReportingEnabled ?? this.isCrashReportingEnabled,
      dateFormat: dateFormat ?? this.dateFormat,
      timeFormat: timeFormat ?? this.timeFormat,
      currency: currency ?? this.currency,
      timezone: timezone ?? this.timezone,
    );
  }
}
