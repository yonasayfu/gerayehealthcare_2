// GENERATED CODE - DO NOT MODIFY BY HAND

// **************************************************************************
// InjectableConfigGenerator
// **************************************************************************

// ignore_for_file: type=lint
// coverage:ignore-file

// ignore_for_file: no_leading_underscores_for_library_prefixes
import 'package:connectivity_plus/connectivity_plus.dart' as _i895;
import 'package:dio/dio.dart' as _i361;
import 'package:flutter_secure_storage/flutter_secure_storage.dart' as _i558;
import 'package:get_it/get_it.dart' as _i174;
import 'package:injectable/injectable.dart' as _i526;
import 'package:laravel_boilerplate_mobile/core/database/database_service.dart'
    as _i817;
import 'package:laravel_boilerplate_mobile/core/di/injection.dart' as _i557;
import 'package:laravel_boilerplate_mobile/core/network/dio_client.dart'
    as _i132;
import 'package:laravel_boilerplate_mobile/core/notifications/notification_service.dart'
    as _i359;
import 'package:laravel_boilerplate_mobile/core/storage/local_storage_service.dart'
    as _i464;
import 'package:laravel_boilerplate_mobile/core/storage/secure_storage_service.dart'
    as _i887;
import 'package:laravel_boilerplate_mobile/core/sync/background_sync_service.dart'
    as _i526;
import 'package:laravel_boilerplate_mobile/core/sync/sync_service.dart'
    as _i816;
import 'package:laravel_boilerplate_mobile/data/datasources/auth_remote_datasource.dart'
    as _i36;
import 'package:laravel_boilerplate_mobile/data/datasources/message_remote_datasource.dart'
    as _i438;
import 'package:laravel_boilerplate_mobile/data/datasources/settings_local_datasource.dart'
    as _i129;
import 'package:laravel_boilerplate_mobile/data/datasources/user_local_datasource.dart'
    as _i647;
import 'package:laravel_boilerplate_mobile/data/datasources/user_remote_datasource.dart'
    as _i284;
import 'package:laravel_boilerplate_mobile/data/repositories/auth_repository_impl.dart'
    as _i609;
import 'package:laravel_boilerplate_mobile/data/repositories/message_repository_impl.dart'
    as _i1040;
import 'package:laravel_boilerplate_mobile/data/repositories/settings_repository_impl.dart'
    as _i998;
import 'package:laravel_boilerplate_mobile/data/repositories/user_repository_impl.dart'
    as _i630;
import 'package:laravel_boilerplate_mobile/domain/repositories/auth_repository.dart'
    as _i915;
import 'package:laravel_boilerplate_mobile/domain/repositories/message_repository.dart'
    as _i423;
import 'package:laravel_boilerplate_mobile/domain/repositories/settings_repository.dart'
    as _i953;
import 'package:laravel_boilerplate_mobile/domain/repositories/user_repository.dart'
    as _i826;
import 'package:laravel_boilerplate_mobile/domain/usecases/auth_usecases.dart'
    as _i950;
import 'package:laravel_boilerplate_mobile/domain/usecases/settings_usecases.dart'
    as _i592;
import 'package:laravel_boilerplate_mobile/domain/usecases/user_usecases.dart'
    as _i1024;
import 'package:shared_preferences/shared_preferences.dart' as _i460;

extension GetItInjectableX on _i174.GetIt {
// initializes the registration of main-scope dependencies inside of GetIt
  Future<_i174.GetIt> init({
    String? environment,
    _i526.EnvironmentFilter? environmentFilter,
  }) async {
    final gh = _i526.GetItHelper(
      this,
      environment,
      environmentFilter,
    );
    final registerModule = _$RegisterModule();
    await gh.factoryAsync<_i460.SharedPreferences>(
      () => registerModule.prefs,
      preResolve: true,
    );
    gh.singleton<_i817.DatabaseService>(() => _i817.DatabaseService());
    gh.singleton<_i526.ConflictResolutionService>(
        () => _i526.ConflictResolutionService());
    gh.lazySingleton<_i558.FlutterSecureStorage>(
        () => registerModule.secureStorage);
    gh.lazySingleton<_i361.Dio>(() => registerModule.dio);
    gh.lazySingleton<_i895.Connectivity>(() => registerModule.connectivity);
    gh.lazySingleton<_i464.LocalStorageService>(() =>
        registerModule.localStorageService(gh<_i460.SharedPreferences>()));
    gh.singleton<_i526.DataIntegrityService>(
        () => _i526.DataIntegrityService(gh<_i817.DatabaseService>()));
    gh.singleton<_i359.NotificationService>(() =>
        _i359.NotificationService.create(gh<_i464.LocalStorageService>()));
    gh.lazySingleton<_i887.SecureStorageService>(() =>
        registerModule.secureStorageService(gh<_i558.FlutterSecureStorage>()));
    gh.singleton<_i526.ConnectivityService>(
        () => _i526.ConnectivityService(gh<_i895.Connectivity>()));
    gh.lazySingleton<_i129.SettingsLocalDataSource>(
        () => _i129.SettingsLocalDataSourceImpl(
              gh<_i464.LocalStorageService>(),
              gh<_i887.SecureStorageService>(),
            ));
    gh.lazySingleton<_i953.SettingsRepository>(() =>
        _i998.SettingsRepositoryImpl(gh<_i129.SettingsLocalDataSource>()));
    gh.lazySingleton<_i132.DioClient>(() => registerModule.dioClient(
          gh<_i361.Dio>(),
          gh<_i887.SecureStorageService>(),
        ));
    gh.factory<_i592.GetSettingsUseCase>(
        () => _i592.GetSettingsUseCase(gh<_i953.SettingsRepository>()));
    gh.factory<_i592.SaveSettingsUseCase>(
        () => _i592.SaveSettingsUseCase(gh<_i953.SettingsRepository>()));
    gh.factory<_i592.UpdateThemeModeUseCase>(
        () => _i592.UpdateThemeModeUseCase(gh<_i953.SettingsRepository>()));
    gh.factory<_i592.UpdateLanguageUseCase>(
        () => _i592.UpdateLanguageUseCase(gh<_i953.SettingsRepository>()));
    gh.factory<_i592.UpdateFontSizeUseCase>(
        () => _i592.UpdateFontSizeUseCase(gh<_i953.SettingsRepository>()));
    gh.factory<_i592.UpdateNotificationSettingsUseCase>(() =>
        _i592.UpdateNotificationSettingsUseCase(
            gh<_i953.SettingsRepository>()));
    gh.factory<_i592.UpdateSecuritySettingsUseCase>(() =>
        _i592.UpdateSecuritySettingsUseCase(gh<_i953.SettingsRepository>()));
    gh.factory<_i592.UpdatePrivacySettingsUseCase>(() =>
        _i592.UpdatePrivacySettingsUseCase(gh<_i953.SettingsRepository>()));
    gh.factory<_i592.UpdateDataSettingsUseCase>(
        () => _i592.UpdateDataSettingsUseCase(gh<_i953.SettingsRepository>()));
    gh.factory<_i592.ResetSettingsUseCase>(
        () => _i592.ResetSettingsUseCase(gh<_i953.SettingsRepository>()));
    gh.factory<_i592.ExportSettingsUseCase>(
        () => _i592.ExportSettingsUseCase(gh<_i953.SettingsRepository>()));
    gh.factory<_i592.ImportSettingsUseCase>(
        () => _i592.ImportSettingsUseCase(gh<_i953.SettingsRepository>()));
    gh.singleton<_i816.SyncService>(() => _i816.SyncService(
          gh<_i817.DatabaseService>(),
          gh<_i132.DioClient>(),
          gh<_i464.LocalStorageService>(),
          gh<_i895.Connectivity>(),
        ));
    gh.lazySingleton<_i647.UserLocalDataSource>(
        () => _i647.UserLocalDataSourceImpl(
              gh<_i817.DatabaseService>(),
              gh<_i816.SyncService>(),
            ));
    gh.lazySingleton<_i36.AuthRemoteDataSource>(
        () => _i36.AuthRemoteDataSourceImpl(gh<_i132.DioClient>()));
    gh.lazySingleton<_i438.MessageRemoteDataSource>(
        () => _i438.MessageRemoteDataSourceImpl(gh<_i132.DioClient>()));
    gh.singleton<_i526.BackgroundSyncService>(() => _i526.BackgroundSyncService(
          gh<_i816.SyncService>(),
          gh<_i895.Connectivity>(),
        ));
    gh.lazySingleton<_i284.UserRemoteDataSource>(
        () => _i284.UserRemoteDataSourceImpl(gh<_i132.DioClient>()));
    gh.lazySingleton<_i915.AuthRepository>(() => _i609.AuthRepositoryImpl(
          gh<_i36.AuthRemoteDataSource>(),
          gh<_i887.SecureStorageService>(),
        ));
    gh.lazySingleton<_i423.MessageRepository>(() =>
        _i1040.MessageRepositoryImpl(gh<_i438.MessageRemoteDataSource>()));
    gh.singleton<_i526.OfflineQueueManager>(() => _i526.OfflineQueueManager(
          gh<_i816.SyncService>(),
          gh<_i526.ConnectivityService>(),
          gh<_i526.BackgroundSyncService>(),
        ));
    gh.lazySingleton<_i826.UserRepository>(() => _i630.UserRepositoryImpl(
          gh<_i284.UserRemoteDataSource>(),
          gh<_i647.UserLocalDataSource>(),
          gh<_i526.ConnectivityService>(),
          gh<_i526.OfflineQueueManager>(),
        ));
    gh.factory<_i1024.GetUsersUseCase>(
        () => _i1024.GetUsersUseCase(gh<_i826.UserRepository>()));
    gh.factory<_i1024.GetUserByIdUseCase>(
        () => _i1024.GetUserByIdUseCase(gh<_i826.UserRepository>()));
    gh.factory<_i1024.UpdateUserUseCase>(
        () => _i1024.UpdateUserUseCase(gh<_i826.UserRepository>()));
    gh.factory<_i1024.DeleteUserUseCase>(
        () => _i1024.DeleteUserUseCase(gh<_i826.UserRepository>()));
    gh.factory<_i1024.SearchUsersUseCase>(
        () => _i1024.SearchUsersUseCase(gh<_i826.UserRepository>()));
    gh.factory<_i1024.GetStaffUsersUseCase>(
        () => _i1024.GetStaffUsersUseCase(gh<_i826.UserRepository>()));
    gh.factory<_i1024.UpdateUserRoleUseCase>(
        () => _i1024.UpdateUserRoleUseCase(gh<_i826.UserRepository>()));
    gh.factory<_i1024.UpdateUserStatusUseCase>(
        () => _i1024.UpdateUserStatusUseCase(gh<_i826.UserRepository>()));
    gh.factory<_i950.LoginUseCase>(
        () => _i950.LoginUseCase(gh<_i915.AuthRepository>()));
    gh.factory<_i950.RegisterUseCase>(
        () => _i950.RegisterUseCase(gh<_i915.AuthRepository>()));
    gh.factory<_i950.LogoutUseCase>(
        () => _i950.LogoutUseCase(gh<_i915.AuthRepository>()));
    gh.factory<_i950.GetCurrentUserUseCase>(
        () => _i950.GetCurrentUserUseCase(gh<_i915.AuthRepository>()));
    gh.factory<_i950.ForgotPasswordUseCase>(
        () => _i950.ForgotPasswordUseCase(gh<_i915.AuthRepository>()));
    gh.factory<_i950.ResetPasswordUseCase>(
        () => _i950.ResetPasswordUseCase(gh<_i915.AuthRepository>()));
    gh.factory<_i950.UpdateProfileUseCase>(
        () => _i950.UpdateProfileUseCase(gh<_i915.AuthRepository>()));
    gh.factory<_i950.ChangePasswordUseCase>(
        () => _i950.ChangePasswordUseCase(gh<_i915.AuthRepository>()));
    gh.factory<_i950.CheckAuthStatusUseCase>(
        () => _i950.CheckAuthStatusUseCase(gh<_i915.AuthRepository>()));
    gh.factory<_i950.RefreshTokenUseCase>(
        () => _i950.RefreshTokenUseCase(gh<_i915.AuthRepository>()));
    return this;
  }
}

class _$RegisterModule extends _i557.RegisterModule {}
