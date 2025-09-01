import 'package:dartz/dartz.dart';
import 'package:injectable/injectable.dart';

import '../../core/errors/exceptions.dart';
import '../../core/errors/failures.dart';
import '../../core/sync/background_sync_service.dart';
import '../../domain/entities/user.dart';
import '../../domain/repositories/user_repository.dart';
import '../datasources/user_local_datasource.dart';
import '../datasources/user_remote_datasource.dart';

@LazySingleton(as: UserRepository)
class UserRepositoryImpl implements UserRepository {
  final UserRemoteDataSource _remoteDataSource;
  final UserLocalDataSource _localDataSource;
  final ConnectivityService _connectivityService;
  final OfflineQueueManager _offlineQueueManager;

  UserRepositoryImpl(
    this._remoteDataSource,
    this._localDataSource,
    this._connectivityService,
    this._offlineQueueManager,
  );

  @override
  Future<Either<Failure, List<User>>> getUsers({
    int page = 1,
    int limit = 20,
    String? search,
    String? role,
    String? department,
  }) async {
    try {
      // Always try local first (offline-first approach)
      final localUsers = await _localDataSource.getUsers(
        page: page,
        limit: limit,
        search: search,
        role: role,
        department: department,
      );

      // If we have local data and we're offline, return local data
      if (localUsers.isNotEmpty || !_connectivityService.isConnected) {
        return Right(localUsers);
      }

      // If online and no local data, try remote
      if (_connectivityService.isConnected) {
        try {
          final remoteUsers = await _remoteDataSource.getUsers(
            page: page,
            limit: limit,
            search: search,
            role: role,
            department: department,
          );

          // Cache remote data locally
          for (final user in remoteUsers) {
            await _localDataSource.insertUser(user);
          }

          return Right(remoteUsers);
        } catch (e) {
          // If remote fails but we have local data, return local
          if (localUsers.isNotEmpty) {
            return Right(localUsers);
          }
          rethrow;
        }
      }

      return Right(localUsers);
    } on NetworkException catch (e) {
      // Try to return local data on network error
      try {
        final localUsers = await _localDataSource.getUsers(
          page: page,
          limit: limit,
          search: search,
          role: role,
          department: department,
        );
        return Right(localUsers);
      } catch (_) {
        return Left(NetworkFailure(message: e.message, code: e.code));
      }
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message, code: e.code));
    } on AuthException catch (e) {
      return Left(AuthFailure(message: e.message, code: e.code));
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, User>> getUserById(int id) async {
    try {
      final user = await _remoteDataSource.getUserById(id);
      return Right(user);
    } on NetworkException catch (e) {
      return Left(NetworkFailure(message: e.message, code: e.code));
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message, code: e.code));
    } on AuthException catch (e) {
      return Left(AuthFailure(message: e.message, code: e.code));
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, User>> updateUser(int id, Map<String, dynamic> data) async {
    try {
      // Get current user from local storage
      final currentUser = await _localDataSource.getUserById(id);
      if (currentUser == null) {
        return Left(UnknownFailure(message: 'User not found'));
      }

      // Create updated user model
      final updatedUser = currentUser.copyWith(
        name: data['name'] ?? currentUser.name,
        email: data['email'] ?? currentUser.email,
        profilePhotoPath: data['profile_photo_path'] ?? currentUser.profilePhotoPath,
        profilePhotoUrl: data['profile_photo_url'] ?? currentUser.profilePhotoUrl,
        updatedAt: DateTime.now(),
      );

      // Update locally first (offline-first)
      await _localDataSource.updateUser(updatedUser);

      // If online, try to sync to server
      if (_connectivityService.isConnected) {
        try {
          final serverUser = await _remoteDataSource.updateUser(id, data);
          // Update local with server response
          await _localDataSource.updateUser(serverUser);
          return Right(serverUser);
        } catch (e) {
          // If server update fails, queue for later sync
          await _offlineQueueManager.queueOfflineOperation(
            'users',
            SyncOperation.update,
            updatedUser.toJson(),
          );
        }
      } else {
        // Queue for sync when online
        await _offlineQueueManager.queueOfflineOperation(
          'users',
          SyncOperation.update,
          updatedUser.toJson(),
        );
      }

      return Right(updatedUser);
    } on ValidationException catch (e) {
      return Left(ValidationFailure(message: e.message, code: e.code, errors: e.errors));
    } on NetworkException catch (e) {
      return Left(NetworkFailure(message: e.message, code: e.code));
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message, code: e.code));
    } on AuthException catch (e) {
      return Left(AuthFailure(message: e.message, code: e.code));
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, void>> deleteUser(int id) async {
    try {
      await _remoteDataSource.deleteUser(id);
      return const Right(null);
    } on NetworkException catch (e) {
      return Left(NetworkFailure(message: e.message, code: e.code));
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message, code: e.code));
    } on AuthException catch (e) {
      return Left(AuthFailure(message: e.message, code: e.code));
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, List<User>>> searchUsers(String query) async {
    try {
      final users = await _remoteDataSource.searchUsers(query);
      return Right(users);
    } on NetworkException catch (e) {
      return Left(NetworkFailure(message: e.message, code: e.code));
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message, code: e.code));
    } on AuthException catch (e) {
      return Left(AuthFailure(message: e.message, code: e.code));
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, List<User>>> getStaffUsers() async {
    try {
      final users = await _remoteDataSource.getStaffUsers();
      return Right(users);
    } on NetworkException catch (e) {
      return Left(NetworkFailure(message: e.message, code: e.code));
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message, code: e.code));
    } on AuthException catch (e) {
      return Left(AuthFailure(message: e.message, code: e.code));
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, User>> updateUserRole(int id, String role) async {
    try {
      final user = await _remoteDataSource.updateUserRole(id, role);
      return Right(user);
    } on ValidationException catch (e) {
      return Left(ValidationFailure(message: e.message, code: e.code, errors: e.errors));
    } on NetworkException catch (e) {
      return Left(NetworkFailure(message: e.message, code: e.code));
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message, code: e.code));
    } on AuthException catch (e) {
      return Left(AuthFailure(message: e.message, code: e.code));
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, User>> updateUserStatus(int id, bool isActive) async {
    try {
      final user = await _remoteDataSource.updateUserStatus(id, isActive);
      return Right(user);
    } on NetworkException catch (e) {
      return Left(NetworkFailure(message: e.message, code: e.code));
    } on ServerException catch (e) {
      return Left(ServerFailure(message: e.message, code: e.code));
    } on AuthException catch (e) {
      return Left(AuthFailure(message: e.message, code: e.code));
    } catch (e) {
      return Left(UnknownFailure(message: e.toString()));
    }
  }
}
