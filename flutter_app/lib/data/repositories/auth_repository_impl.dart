import 'dart:convert';
import 'package:dartz/dartz.dart';
import 'package:injectable/injectable.dart';

import '../../core/errors/exceptions.dart';
import '../../core/errors/failures.dart';
import '../../core/storage/secure_storage_service.dart';
import '../../domain/entities/user.dart';
import '../../domain/repositories/auth_repository.dart';
import '../datasources/auth_remote_datasource.dart';
import '../models/user_model.dart';

@LazySingleton(as: AuthRepository)
class AuthRepositoryImpl implements AuthRepository {
  final AuthRemoteDataSource _remoteDataSource;
  final SecureStorageService _secureStorage;

  AuthRepositoryImpl(this._remoteDataSource, this._secureStorage);

  @override
  Future<Either<Failure, User>> login(String email, String password, {bool rememberMe = false}) async {
    try {
      final request = LoginRequest(
        email: email,
        password: password,
        rememberMe: rememberMe,
      );
      
      final response = await _remoteDataSource.login(request);
      
      // Store tokens and user data
      await _secureStorage.saveToken(response.accessToken);
      if (response.refreshToken != null) {
        await _secureStorage.saveRefreshToken(response.refreshToken!);
      }
      await _secureStorage.saveUserData(jsonEncode(response.user.toJson()));
      
      return Right(response.user);
    } on AuthException catch (e) {
      return Left(AuthFailure(message: e.message, code: e.code));
    } on ValidationException catch (e) {
      return Left(ValidationFailure(message: e.message, code: e.code, errors: e.errors));
    } on NetworkException catch (e) {
      return Left(NetworkFailure(message: e.message, code: e.code));
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message, code: e.code));
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, User>> register(String name, String email, String password) async {
    try {
      final request = RegisterRequest(
        name: name,
        email: email,
        password: password,
        passwordConfirmation: password,
      );
      
      final response = await _remoteDataSource.register(request);
      
      // Store tokens and user data
      await _secureStorage.saveToken(response.accessToken);
      if (response.refreshToken != null) {
        await _secureStorage.saveRefreshToken(response.refreshToken!);
      }
      await _secureStorage.saveUserData(jsonEncode(response.user.toJson()));
      
      return Right(response.user);
    } on AuthException catch (e) {
      return Left(AuthFailure(message: e.message, code: e.code));
    } on ValidationException catch (e) {
      return Left(ValidationFailure(message: e.message, code: e.code, errors: e.errors));
    } on NetworkException catch (e) {
      return Left(NetworkFailure(message: e.message, code: e.code));
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message, code: e.code));
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> logout() async {
    try {
      await _remoteDataSource.logout();
      await clearAuthData();
      return const Right(null);
    } on NetworkException catch (e) {
      // Even if network fails, clear local data
      await clearAuthData();
      return Left(NetworkFailure(message: e.message, code: e.code));
    } on ServerException catch (e) {
      // Even if server fails, clear local data
      await clearAuthData();
      return Left(ServerFailure(message: e.message, code: e.code));
    } catch (e) {
      await clearAuthData();
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, User>> getCurrentUser() async {
    try {
      // First try to get from local storage
      final userData = await _secureStorage.getUserData();
      if (userData != null) {
        final userJson = jsonDecode(userData);
        final user = UserModel.fromJson(userJson);
        return Right(user);
      }
      
      // If not in local storage, fetch from server
      final user = await _remoteDataSource.getCurrentUser();
      await _secureStorage.saveUserData(jsonEncode(user.toJson()));
      return Right(user);
    } on AuthException catch (e) {
      return Left(AuthFailure(message: e.message, code: e.code));
    } on NetworkException catch (e) {
      return Left(NetworkFailure(message: e.message, code: e.code));
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message, code: e.code));
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> forgotPassword(String email) async {
    try {
      final request = ForgotPasswordRequest(email: email);
      await _remoteDataSource.forgotPassword(request);
      return const Right(null);
    } on ValidationException catch (e) {
      return Left(ValidationFailure(message: e.message, code: e.code, errors: e.errors));
    } on NetworkException catch (e) {
      return Left(NetworkFailure(message: e.message, code: e.code));
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message, code: e.code));
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> resetPassword(String email, String token, String password) async {
    try {
      final request = ResetPasswordRequest(
        email: email,
        token: token,
        password: password,
        passwordConfirmation: password,
      );
      await _remoteDataSource.resetPassword(request);
      return const Right(null);
    } on ValidationException catch (e) {
      return Left(ValidationFailure(message: e.message, code: e.code, errors: e.errors));
    } on NetworkException catch (e) {
      return Left(NetworkFailure(message: e.message, code: e.code));
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message, code: e.code));
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> updateProfile(Map<String, dynamic> data) async {
    try {
      await _remoteDataSource.updateProfile(data);
      
      // Update local user data
      final currentUser = await getCurrentUser();
      return currentUser.fold(
        (failure) => Left(failure),
        (user) async {
          // Refresh user data from server
          final updatedUser = await _remoteDataSource.getCurrentUser();
          await _secureStorage.saveUserData(jsonEncode(updatedUser.toJson()));
          return const Right(null);
        },
      );
    } on ValidationException catch (e) {
      return Left(ValidationFailure(message: e.message, code: e.code, errors: e.errors));
    } on NetworkException catch (e) {
      return Left(NetworkFailure(message: e.message, code: e.code));
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message, code: e.code));
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> changePassword(String currentPassword, String newPassword) async {
    try {
      await _remoteDataSource.changePassword(currentPassword, newPassword);
      return const Right(null);
    } on ValidationException catch (e) {
      return Left(ValidationFailure(message: e.message, code: e.code, errors: e.errors));
    } on NetworkException catch (e) {
      return Left(NetworkFailure(message: e.message, code: e.code));
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message, code: e.code));
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, bool>> refreshToken() async {
    try {
      final refreshToken = await _secureStorage.getRefreshToken();
      if (refreshToken == null) {
        return const Left(AuthFailure(message: 'No refresh token available'));
      }
      
      final response = await _remoteDataSource.refreshToken(refreshToken);
      
      // Update stored tokens
      await _secureStorage.saveToken(response.accessToken);
      if (response.refreshToken != null) {
        await _secureStorage.saveRefreshToken(response.refreshToken!);
      }
      
      return const Right(true);
    } on AuthException catch (e) {
      await clearAuthData();
      return Left(AuthFailure(message: e.message, code: e.code));
    } catch (e) {
      await clearAuthData();
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<bool> isAuthenticated() async {
    final token = await _secureStorage.getToken();
    return token != null;
  }

  @override
  Future<void> clearAuthData() async {
    await _secureStorage.clearAuthData();
  }
}
