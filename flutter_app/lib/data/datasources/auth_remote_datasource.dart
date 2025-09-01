import 'package:injectable/injectable.dart';

import '../../core/constants/api_endpoints.dart';
import '../../core/errors/exceptions.dart';
import '../../core/network/dio_client.dart';
import '../models/user_model.dart';

abstract class AuthRemoteDataSource {
  Future<AuthResponse> login(LoginRequest request);
  Future<AuthResponse> register(RegisterRequest request);
  Future<void> logout();
  Future<AuthResponse> refreshToken(String refreshToken);
  Future<void> forgotPassword(ForgotPasswordRequest request);
  Future<void> resetPassword(ResetPasswordRequest request);
  Future<UserModel> getCurrentUser();
  Future<void> updateProfile(Map<String, dynamic> data);
  Future<void> changePassword(String currentPassword, String newPassword);
}

@LazySingleton(as: AuthRemoteDataSource)
class AuthRemoteDataSourceImpl implements AuthRemoteDataSource {
  final DioClient _dioClient;

  AuthRemoteDataSourceImpl(this._dioClient);

  @override
  Future<AuthResponse> login(LoginRequest request) async {
    try {
      final response = await _dioClient.post(
        ApiEndpoints.login,
        data: request.toJson(),
      );

      if (response.data['success'] == true) {
        return AuthResponse.fromJson(response.data['data']);
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Login failed',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<AuthResponse> register(RegisterRequest request) async {
    try {
      final response = await _dioClient.post(
        ApiEndpoints.register,
        data: request.toJson(),
      );

      if (response.data['success'] == true) {
        return AuthResponse.fromJson(response.data['data']);
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Registration failed',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<void> logout() async {
    try {
      await _dioClient.post(ApiEndpoints.logout);
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<AuthResponse> refreshToken(String refreshToken) async {
    try {
      final response = await _dioClient.post(
        ApiEndpoints.refresh,
        data: {'refresh_token': refreshToken},
      );

      if (response.data['success'] == true) {
        return AuthResponse.fromJson(response.data['data']);
      } else {
        throw AuthException(
          message: response.data['message'] ?? 'Token refresh failed',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw AuthException(message: e.toString());
    }
  }

  @override
  Future<void> forgotPassword(ForgotPasswordRequest request) async {
    try {
      final response = await _dioClient.post(
        ApiEndpoints.forgotPassword,
        data: request.toJson(),
      );

      if (response.data['success'] != true) {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to send reset email',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<void> resetPassword(ResetPasswordRequest request) async {
    try {
      final response = await _dioClient.post(
        ApiEndpoints.resetPassword,
        data: request.toJson(),
      );

      if (response.data['success'] != true) {
        throw ServerException(
          message: response.data['message'] ?? 'Password reset failed',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<UserModel> getCurrentUser() async {
    try {
      final response = await _dioClient.get(ApiEndpoints.user);

      if (response.data['success'] == true) {
        return UserModel.fromJson(response.data['data']);
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to get user data',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<void> updateProfile(Map<String, dynamic> data) async {
    try {
      final response = await _dioClient.put(
        ApiEndpoints.user,
        data: data,
      );

      if (response.data['success'] != true) {
        throw ServerException(
          message: response.data['message'] ?? 'Profile update failed',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<void> changePassword(String currentPassword, String newPassword) async {
    try {
      final response = await _dioClient.put(
        '${ApiEndpoints.user}/password',
        data: {
          'current_password': currentPassword,
          'password': newPassword,
          'password_confirmation': newPassword,
        },
      );

      if (response.data['success'] != true) {
        throw ServerException(
          message: response.data['message'] ?? 'Password change failed',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }
}
