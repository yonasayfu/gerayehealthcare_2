import 'package:dartz/dartz.dart';

import '../../core/errors/failures.dart';
import '../entities/app_settings.dart';

abstract class SettingsRepository {
  Future<Either<Failure, AppSettings>> getSettings();
  Future<Either<Failure, void>> saveSettings(AppSettings settings);
  Future<Either<Failure, void>> updateThemeMode(AppThemeMode themeMode);
  Future<Either<Failure, void>> updateLanguage(AppLanguage language);
  Future<Either<Failure, void>> updateFontSize(FontSize fontSize);
  Future<Either<Failure, void>> updateNotificationSettings({
    bool? isNotificationsEnabled,
    bool? isPushNotificationsEnabled,
    bool? isEmailNotificationsEnabled,
    NotificationFrequency? frequency,
  });
  Future<Either<Failure, void>> updateSecuritySettings({
    bool? isBiometricEnabled,
    bool? isAutoLockEnabled,
    int? autoLockDuration,
  });
  Future<Either<Failure, void>> updatePrivacySettings({
    bool? isLocationEnabled,
    bool? isAnalyticsEnabled,
    bool? isCrashReportingEnabled,
  });
  Future<Either<Failure, void>> updateDataSettings({
    bool? isDataSaverEnabled,
    bool? isAutoDownloadEnabled,
  });
  Future<Either<Failure, void>> resetSettings();
  Future<Either<Failure, void>> exportSettings();
  Future<Either<Failure, void>> importSettings(String settingsJson);
}
