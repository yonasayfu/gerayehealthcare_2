import 'package:dartz/dartz.dart';
import 'package:injectable/injectable.dart';

import '../../core/errors/failures.dart';
import '../entities/app_settings.dart';
import '../repositories/settings_repository.dart';

@injectable
class GetSettingsUseCase {
  final SettingsRepository _repository;

  GetSettingsUseCase(this._repository);

  Future<Either<Failure, AppSettings>> call() {
    return _repository.getSettings();
  }
}

@injectable
class SaveSettingsUseCase {
  final SettingsRepository _repository;

  SaveSettingsUseCase(this._repository);

  Future<Either<Failure, void>> call(AppSettings settings) {
    return _repository.saveSettings(settings);
  }
}

@injectable
class UpdateThemeModeUseCase {
  final SettingsRepository _repository;

  UpdateThemeModeUseCase(this._repository);

  Future<Either<Failure, void>> call(AppThemeMode themeMode) {
    return _repository.updateThemeMode(themeMode);
  }
}

@injectable
class UpdateLanguageUseCase {
  final SettingsRepository _repository;

  UpdateLanguageUseCase(this._repository);

  Future<Either<Failure, void>> call(AppLanguage language) {
    return _repository.updateLanguage(language);
  }
}

@injectable
class UpdateFontSizeUseCase {
  final SettingsRepository _repository;

  UpdateFontSizeUseCase(this._repository);

  Future<Either<Failure, void>> call(FontSize fontSize) {
    return _repository.updateFontSize(fontSize);
  }
}

@injectable
class UpdateNotificationSettingsUseCase {
  final SettingsRepository _repository;

  UpdateNotificationSettingsUseCase(this._repository);

  Future<Either<Failure, void>> call({
    bool? isNotificationsEnabled,
    bool? isPushNotificationsEnabled,
    bool? isEmailNotificationsEnabled,
    NotificationFrequency? frequency,
  }) {
    return _repository.updateNotificationSettings(
      isNotificationsEnabled: isNotificationsEnabled,
      isPushNotificationsEnabled: isPushNotificationsEnabled,
      isEmailNotificationsEnabled: isEmailNotificationsEnabled,
      frequency: frequency,
    );
  }
}

@injectable
class UpdateSecuritySettingsUseCase {
  final SettingsRepository _repository;

  UpdateSecuritySettingsUseCase(this._repository);

  Future<Either<Failure, void>> call({
    bool? isBiometricEnabled,
    bool? isAutoLockEnabled,
    int? autoLockDuration,
  }) {
    return _repository.updateSecuritySettings(
      isBiometricEnabled: isBiometricEnabled,
      isAutoLockEnabled: isAutoLockEnabled,
      autoLockDuration: autoLockDuration,
    );
  }
}

@injectable
class UpdatePrivacySettingsUseCase {
  final SettingsRepository _repository;

  UpdatePrivacySettingsUseCase(this._repository);

  Future<Either<Failure, void>> call({
    bool? isLocationEnabled,
    bool? isAnalyticsEnabled,
    bool? isCrashReportingEnabled,
  }) {
    return _repository.updatePrivacySettings(
      isLocationEnabled: isLocationEnabled,
      isAnalyticsEnabled: isAnalyticsEnabled,
      isCrashReportingEnabled: isCrashReportingEnabled,
    );
  }
}

@injectable
class UpdateDataSettingsUseCase {
  final SettingsRepository _repository;

  UpdateDataSettingsUseCase(this._repository);

  Future<Either<Failure, void>> call({
    bool? isDataSaverEnabled,
    bool? isAutoDownloadEnabled,
  }) {
    return _repository.updateDataSettings(
      isDataSaverEnabled: isDataSaverEnabled,
      isAutoDownloadEnabled: isAutoDownloadEnabled,
    );
  }
}

@injectable
class ResetSettingsUseCase {
  final SettingsRepository _repository;

  ResetSettingsUseCase(this._repository);

  Future<Either<Failure, void>> call() {
    return _repository.resetSettings();
  }
}

@injectable
class ExportSettingsUseCase {
  final SettingsRepository _repository;

  ExportSettingsUseCase(this._repository);

  Future<Either<Failure, void>> call() {
    return _repository.exportSettings();
  }
}

@injectable
class ImportSettingsUseCase {
  final SettingsRepository _repository;

  ImportSettingsUseCase(this._repository);

  Future<Either<Failure, void>> call(String settingsJson) {
    return _repository.importSettings(settingsJson);
  }
}
