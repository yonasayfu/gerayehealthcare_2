import 'package:dartz/dartz.dart';
import 'package:injectable/injectable.dart';

import '../../core/errors/failures.dart';
import '../../domain/entities/app_settings.dart';
import '../../domain/repositories/settings_repository.dart';
import '../datasources/settings_local_datasource.dart';

@LazySingleton(as: SettingsRepository)
class SettingsRepositoryImpl implements SettingsRepository {
  final SettingsLocalDataSource _localDataSource;

  SettingsRepositoryImpl(this._localDataSource);

  @override
  Future<Either<Failure, AppSettings>> getSettings() async {
    try {
      final settings = await _localDataSource.getSettings();
      return Right(settings);
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> saveSettings(AppSettings settings) async {
    try {
      await _localDataSource.saveSettings(settings);
      return const Right(null);
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> updateThemeMode(AppThemeMode themeMode) async {
    try {
      final currentSettings = await _localDataSource.getSettings();
      final updatedSettings = currentSettings.copyWith(themeMode: themeMode);
      await _localDataSource.saveSettings(updatedSettings);
      return const Right(null);
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> updateLanguage(AppLanguage language) async {
    try {
      final currentSettings = await _localDataSource.getSettings();
      final updatedSettings = currentSettings.copyWith(language: language);
      await _localDataSource.saveSettings(updatedSettings);
      return const Right(null);
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> updateFontSize(FontSize fontSize) async {
    try {
      final currentSettings = await _localDataSource.getSettings();
      final updatedSettings = currentSettings.copyWith(fontSize: fontSize);
      await _localDataSource.saveSettings(updatedSettings);
      return const Right(null);
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> updateNotificationSettings({
    bool? isNotificationsEnabled,
    bool? isPushNotificationsEnabled,
    bool? isEmailNotificationsEnabled,
    NotificationFrequency? frequency,
  }) async {
    try {
      final currentSettings = await _localDataSource.getSettings();
      final updatedSettings = currentSettings.copyWith(
        isNotificationsEnabled: isNotificationsEnabled,
        isPushNotificationsEnabled: isPushNotificationsEnabled,
        isEmailNotificationsEnabled: isEmailNotificationsEnabled,
        notificationFrequency: frequency,
      );
      await _localDataSource.saveSettings(updatedSettings);
      return const Right(null);
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> updateSecuritySettings({
    bool? isBiometricEnabled,
    bool? isAutoLockEnabled,
    int? autoLockDuration,
  }) async {
    try {
      final currentSettings = await _localDataSource.getSettings();
      final updatedSettings = currentSettings.copyWith(
        isBiometricEnabled: isBiometricEnabled,
        isAutoLockEnabled: isAutoLockEnabled,
        autoLockDuration: autoLockDuration,
      );
      await _localDataSource.saveSettings(updatedSettings);
      return const Right(null);
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> updatePrivacySettings({
    bool? isLocationEnabled,
    bool? isAnalyticsEnabled,
    bool? isCrashReportingEnabled,
  }) async {
    try {
      final currentSettings = await _localDataSource.getSettings();
      final updatedSettings = currentSettings.copyWith(
        isLocationEnabled: isLocationEnabled,
        isAnalyticsEnabled: isAnalyticsEnabled,
        isCrashReportingEnabled: isCrashReportingEnabled,
      );
      await _localDataSource.saveSettings(updatedSettings);
      return const Right(null);
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> updateDataSettings({
    bool? isDataSaverEnabled,
    bool? isAutoDownloadEnabled,
  }) async {
    try {
      final currentSettings = await _localDataSource.getSettings();
      final updatedSettings = currentSettings.copyWith(
        isDataSaverEnabled: isDataSaverEnabled,
        isAutoDownloadEnabled: isAutoDownloadEnabled,
      );
      await _localDataSource.saveSettings(updatedSettings);
      return const Right(null);
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> resetSettings() async {
    try {
      await _localDataSource.resetSettings();
      return const Right(null);
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> exportSettings() async {
    try {
      await _localDataSource.exportSettings();
      return const Right(null);
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> importSettings(String settingsJson) async {
    try {
      await _localDataSource.importSettings(settingsJson);
      return const Right(null);
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }
}
