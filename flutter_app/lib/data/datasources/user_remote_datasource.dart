import 'package:injectable/injectable.dart';

import '../../core/constants/api_endpoints.dart';
import '../../core/errors/exceptions.dart';
import '../../core/network/dio_client.dart';
import '../models/user_model.dart';

abstract class UserRemoteDataSource {
  Future<List<UserModel>> getUsers({
    int page = 1,
    int limit = 20,
    String? search,
    String? role,
    String? department,
  });
  Future<UserModel> getUserById(int id);
  Future<UserModel> updateUser(int id, Map<String, dynamic> data);
  Future<void> deleteUser(int id);
  Future<List<UserModel>> searchUsers(String query);
  Future<List<UserModel>> getStaffUsers();
  Future<UserModel> updateUserRole(int id, String role);
  Future<UserModel> updateUserStatus(int id, bool isActive);
}

@LazySingleton(as: UserRemoteDataSource)
class UserRemoteDataSourceImpl implements UserRemoteDataSource {
  final DioClient _dioClient;

  UserRemoteDataSourceImpl(this._dioClient);

  @override
  Future<List<UserModel>> getUsers({
    int page = 1,
    int limit = 20,
    String? search,
    String? role,
    String? department,
  }) async {
    try {
      final queryParams = <String, dynamic>{
        'page': page,
        'limit': limit,
      };

      if (search != null && search.isNotEmpty) {
        queryParams['search'] = search;
      }
      if (role != null && role.isNotEmpty) {
        queryParams['role'] = role;
      }
      if (department != null && department.isNotEmpty) {
        queryParams['department'] = department;
      }

      final response = await _dioClient.get(
        ApiEndpoints.users,
        queryParameters: queryParams,
      );

      if (response.data['success'] == true) {
        final List<dynamic> usersJson = response.data['data']['users'];
        return usersJson.map((json) => UserModel.fromJson(json)).toList();
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to fetch users',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<UserModel> getUserById(int id) async {
    try {
      final response = await _dioClient.get('${ApiEndpoints.users}/$id');

      if (response.data['success'] == true) {
        return UserModel.fromJson(response.data['data']);
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'User not found',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<UserModel> updateUser(int id, Map<String, dynamic> data) async {
    try {
      final response = await _dioClient.put(
        '${ApiEndpoints.users}/$id',
        data: data,
      );

      if (response.data['success'] == true) {
        return UserModel.fromJson(response.data['data']);
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to update user',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<void> deleteUser(int id) async {
    try {
      final response = await _dioClient.delete('${ApiEndpoints.users}/$id');

      if (response.data['success'] != true) {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to delete user',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<List<UserModel>> searchUsers(String query) async {
    try {
      final response = await _dioClient.get(
        '${ApiEndpoints.users}/search',
        queryParameters: {'q': query},
      );

      if (response.data['success'] == true) {
        final List<dynamic> usersJson = response.data['data'];
        return usersJson.map((json) => UserModel.fromJson(json)).toList();
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Search failed',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<List<UserModel>> getStaffUsers() async {
    try {
      final response = await _dioClient.get('${ApiEndpoints.users}/staff');

      if (response.data['success'] == true) {
        final List<dynamic> usersJson = response.data['data'];
        return usersJson.map((json) => UserModel.fromJson(json)).toList();
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to fetch staff users',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<UserModel> updateUserRole(int id, String role) async {
    try {
      final response = await _dioClient.put(
        '${ApiEndpoints.users}/$id/role',
        data: {'role': role},
      );

      if (response.data['success'] == true) {
        return UserModel.fromJson(response.data['data']);
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to update user role',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<UserModel> updateUserStatus(int id, bool isActive) async {
    try {
      final response = await _dioClient.put(
        '${ApiEndpoints.users}/$id/status',
        data: {'is_active': isActive},
      );

      if (response.data['success'] == true) {
        return UserModel.fromJson(response.data['data']);
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to update user status',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }
}
