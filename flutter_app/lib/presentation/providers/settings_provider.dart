import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:riverpod_annotation/riverpod_annotation.dart';

import '../../core/di/injection.dart';
import '../../domain/entities/app_settings.dart';
import '../../domain/usecases/settings_usecases.dart';

part 'settings_provider.g.dart';

// Settings state class
class SettingsState {
  final AppSettings settings;
  final bool isLoading;
  final String? error;

  const SettingsState({
    required this.settings,
    this.isLoading = false,
    this.error,
  });

  SettingsState copyWith({
    AppSettings? settings,
    bool? isLoading,
    String? error,
  }) {
    return SettingsState(
      settings: settings ?? this.settings,
      isLoading: isLoading ?? this.isLoading,
      error: error ?? this.error,
    );
  }
}

// Settings provider
@riverpod
class Settings extends _$Settings {
  late final GetSettingsUseCase _getSettingsUseCase;
  late final SaveSettingsUseCase _saveSettingsUseCase;
  late final UpdateThemeModeUseCase _updateThemeModeUseCase;
  late final UpdateLanguageUseCase _updateLanguageUseCase;
  late final UpdateFontSizeUseCase _updateFontSizeUseCase;
  late final UpdateNotificationSettingsUseCase _updateNotificationSettingsUseCase;
  late final UpdateSecuritySettingsUseCase _updateSecuritySettingsUseCase;
  late final UpdatePrivacySettingsUseCase _updatePrivacySettingsUseCase;
  late final UpdateDataSettingsUseCase _updateDataSettingsUseCase;
  late final ResetSettingsUseCase _resetSettingsUseCase;
  late final ExportSettingsUseCase _exportSettingsUseCase;
  late final ImportSettingsUseCase _importSettingsUseCase;

  @override
  SettingsState build() {
    _getSettingsUseCase = getIt<GetSettingsUseCase>();
    _saveSettingsUseCase = getIt<SaveSettingsUseCase>();
    _updateThemeModeUseCase = getIt<UpdateThemeModeUseCase>();
    _updateLanguageUseCase = getIt<UpdateLanguageUseCase>();
    _updateFontSizeUseCase = getIt<UpdateFontSizeUseCase>();
    _updateNotificationSettingsUseCase = getIt<UpdateNotificationSettingsUseCase>();
    _updateSecuritySettingsUseCase = getIt<UpdateSecuritySettingsUseCase>();
    _updatePrivacySettingsUseCase = getIt<UpdatePrivacySettingsUseCase>();
    _updateDataSettingsUseCase = getIt<UpdateDataSettingsUseCase>();
    _resetSettingsUseCase = getIt<ResetSettingsUseCase>();
    _exportSettingsUseCase = getIt<ExportSettingsUseCase>();
    _importSettingsUseCase = getIt<ImportSettingsUseCase>();

    loadSettings();
    return SettingsState(settings: const AppSettings(), isLoading: true);
  }

  Future<void> loadSettings() async {
    state = state.copyWith(isLoading: true, error: null);

    final result = await _getSettingsUseCase();

    result.fold(
      (failure) => state = state.copyWith(
        isLoading: false,
        error: failure.message,
      ),
      (settings) => state = state.copyWith(
        settings: settings,
        isLoading: false,
      ),
    );
  }

  Future<void> updateThemeMode(AppThemeMode themeMode) async {
    final result = await _updateThemeModeUseCase(themeMode);

    result.fold(
      (failure) => state = state.copyWith(error: failure.message),
      (_) => state = state.copyWith(
        settings: state.settings.copyWith(themeMode: themeMode),
      ),
    );
  }

  Future<void> updateLanguage(AppLanguage language) async {
    final result = await _updateLanguageUseCase(language);

    result.fold(
      (failure) => state = state.copyWith(error: failure.message),
      (_) => state = state.copyWith(
        settings: state.settings.copyWith(language: language),
      ),
    );
  }

  Future<void> updateFontSize(FontSize fontSize) async {
    final result = await _updateFontSizeUseCase(fontSize);

    result.fold(
      (failure) => state = state.copyWith(error: failure.message),
      (_) => state = state.copyWith(
        settings: state.settings.copyWith(fontSize: fontSize),
      ),
    );
  }

  Future<void> updateNotificationSettings({
    bool? isNotificationsEnabled,
    bool? isPushNotificationsEnabled,
    bool? isEmailNotificationsEnabled,
    NotificationFrequency? frequency,
  }) async {
    final result = await _updateNotificationSettingsUseCase(
      isNotificationsEnabled: isNotificationsEnabled,
      isPushNotificationsEnabled: isPushNotificationsEnabled,
      isEmailNotificationsEnabled: isEmailNotificationsEnabled,
      frequency: frequency,
    );

    result.fold(
      (failure) => state = state.copyWith(error: failure.message),
      (_) => state = state.copyWith(
        settings: state.settings.copyWith(
          isNotificationsEnabled: isNotificationsEnabled,
          isPushNotificationsEnabled: isPushNotificationsEnabled,
          isEmailNotificationsEnabled: isEmailNotificationsEnabled,
          notificationFrequency: frequency,
        ),
      ),
    );
  }

  Future<void> updateSecuritySettings({
    bool? isBiometricEnabled,
    bool? isAutoLockEnabled,
    int? autoLockDuration,
  }) async {
    final result = await _updateSecuritySettingsUseCase(
      isBiometricEnabled: isBiometricEnabled,
      isAutoLockEnabled: isAutoLockEnabled,
      autoLockDuration: autoLockDuration,
    );

    result.fold(
      (failure) => state = state.copyWith(error: failure.message),
      (_) => state = state.copyWith(
        settings: state.settings.copyWith(
          isBiometricEnabled: isBiometricEnabled,
          isAutoLockEnabled: isAutoLockEnabled,
          autoLockDuration: autoLockDuration,
        ),
      ),
    );
  }

  Future<void> updatePrivacySettings({
    bool? isLocationEnabled,
    bool? isAnalyticsEnabled,
    bool? isCrashReportingEnabled,
  }) async {
    final result = await _updatePrivacySettingsUseCase(
      isLocationEnabled: isLocationEnabled,
      isAnalyticsEnabled: isAnalyticsEnabled,
      isCrashReportingEnabled: isCrashReportingEnabled,
    );

    result.fold(
      (failure) => state = state.copyWith(error: failure.message),
      (_) => state = state.copyWith(
        settings: state.settings.copyWith(
          isLocationEnabled: isLocationEnabled,
          isAnalyticsEnabled: isAnalyticsEnabled,
          isCrashReportingEnabled: isCrashReportingEnabled,
        ),
      ),
    );
  }

  Future<void> updateDataSettings({
    bool? isDataSaverEnabled,
    bool? isAutoDownloadEnabled,
  }) async {
    final result = await _updateDataSettingsUseCase(
      isDataSaverEnabled: isDataSaverEnabled,
      isAutoDownloadEnabled: isAutoDownloadEnabled,
    );

    result.fold(
      (failure) => state = state.copyWith(error: failure.message),
      (_) => state = state.copyWith(
        settings: state.settings.copyWith(
          isDataSaverEnabled: isDataSaverEnabled,
          isAutoDownloadEnabled: isAutoDownloadEnabled,
        ),
      ),
    );
  }

  Future<void> resetSettings() async {
    state = state.copyWith(isLoading: true);

    final result = await _resetSettingsUseCase();

    result.fold(
      (failure) => state = state.copyWith(
        isLoading: false,
        error: failure.message,
      ),
      (_) {
        state = SettingsState(
          settings: const AppSettings(),
          isLoading: false,
        );
      },
    );
  }

  Future<void> exportSettings() async {
    final result = await _exportSettingsUseCase();

    result.fold(
      (failure) => state = state.copyWith(error: failure.message),
      (_) {
        // Settings exported successfully
        // TODO: Show success message to user
      },
    );
  }

  Future<void> importSettings(String settingsJson) async {
    state = state.copyWith(isLoading: true);

    final result = await _importSettingsUseCase(settingsJson);

    result.fold(
      (failure) => state = state.copyWith(
        isLoading: false,
        error: failure.message,
      ),
      (_) => loadSettings(), // Reload settings after import
    );
  }

  void clearError() {
    state = state.copyWith(error: null);
  }
}
