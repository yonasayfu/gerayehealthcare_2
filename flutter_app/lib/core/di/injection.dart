import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get_it/get_it.dart';
import 'package:hive/hive.dart';
import 'package:injectable/injectable.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../constants/app_constants.dart';
import '../database/database_service.dart';
import '../network/dio_client.dart';
import '../notifications/notification_service.dart';
import '../storage/secure_storage_service.dart';
import '../storage/local_storage_service.dart';
import '../sync/background_sync_service.dart';
import '../sync/sync_service.dart';

part 'injection.config.dart';

final getIt = GetIt.instance;

@InjectableInit()
Future<void> configureDependencies() async => getIt.init();

@module
abstract class RegisterModule {
  @preResolve
  Future<SharedPreferences> get prefs => SharedPreferences.getInstance();

  @lazySingleton
  FlutterSecureStorage get secureStorage => const FlutterSecureStorage(
        aOptions: AndroidOptions(
          encryptedSharedPreferences: true,
        ),
        iOptions: IOSOptions(
          accessibility: IOSAccessibility.first_unlock_this_device,
        ),
      );

  @lazySingleton
  Dio get dio {
    final dio = Dio(BaseOptions(
      baseUrl: AppConstants.apiBaseUrl,
      connectTimeout: AppConstants.connectTimeout,
      receiveTimeout: AppConstants.receiveTimeout,
      sendTimeout: AppConstants.sendTimeout,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    ));

    // Add logging interceptor in debug mode
    if (!AppConstants.isProduction) {
      dio.interceptors.add(LogInterceptor(
        requestBody: true,
        responseBody: true,
        requestHeader: true,
        responseHeader: false,
        error: true,
        logPrint: (obj) => print(obj),
      ));
    }

    return dio;
  }



  @lazySingleton
  SecureStorageService secureStorageService(FlutterSecureStorage storage) =>
      SecureStorageService(storage);

  @lazySingleton
  LocalStorageService localStorageService(SharedPreferences prefs) =>
      LocalStorageService(prefs);

  @lazySingleton
  DioClient dioClient(Dio dio, SecureStorageService secureStorage) =>
      DioClient(dio, secureStorage);

  @lazySingleton
  Connectivity get connectivity => Connectivity();
}
