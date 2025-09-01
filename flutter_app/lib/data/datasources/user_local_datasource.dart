import 'dart:convert';
import 'package:injectable/injectable.dart';

import '../../core/database/database_service.dart';
import '../../core/sync/sync_service.dart';
import '../models/user_model.dart';

abstract class UserLocalDataSource {
  Future<List<UserModel>> getUsers({
    int page = 1,
    int limit = 20,
    String? search,
    String? role,
    String? department,
  });
  Future<UserModel?> getUserById(int id);
  Future<UserModel> insertUser(UserModel user);
  Future<UserModel> updateUser(UserModel user);
  Future<void> deleteUser(int id);
  Future<List<UserModel>> searchUsers(String query);
  Future<List<UserModel>> getStaffUsers();
  Future<void> clearUsers();
  Future<int> getUserCount();
  Future<List<UserModel>> getUnsyncedUsers();
}

@LazySingleton(as: UserLocalDataSource)
class UserLocalDataSourceImpl implements UserLocalDataSource {
  final DatabaseService _databaseService;
  final SyncService _syncService;

  UserLocalDataSourceImpl(this._databaseService, this._syncService);

  @override
  Future<List<UserModel>> getUsers({
    int page = 1,
    int limit = 20,
    String? search,
    String? role,
    String? department,
  }) async {
    final offset = (page - 1) * limit;
    String whereClause = '1=1';
    List<dynamic> whereArgs = [];

    if (search != null && search.isNotEmpty) {
      whereClause += ' AND (name LIKE ? OR email LIKE ?)';
      whereArgs.addAll(['%$search%', '%$search%']);
    }

    if (role != null && role.isNotEmpty) {
      if (role == 'staff') {
        whereClause += ' AND is_staff = 1';
      } else {
        whereClause += ' AND is_staff = 0';
      }
    }

    final results = await _databaseService.query(
      'users',
      where: whereClause.isNotEmpty ? whereClause : null,
      whereArgs: whereArgs.isNotEmpty ? whereArgs : null,
      orderBy: 'name ASC',
      limit: limit,
      offset: offset,
    );

    final users = <UserModel>[];
    for (final userMap in results) {
      final user = await _mapToUserModel(userMap);
      users.add(user);
    }

    return users;
  }

  @override
  Future<UserModel?> getUserById(int id) async {
    final results = await _databaseService.query(
      'users',
      where: 'id = ?',
      whereArgs: [id],
    );

    if (results.isEmpty) return null;

    return await _mapToUserModel(results.first);
  }

  @override
  Future<UserModel> insertUser(UserModel user) async {
    final userMap = _userModelToMap(user);
    userMap['sync_status'] = SyncStatus.pending.value;
    userMap['last_synced'] = null;

    await _databaseService.transaction((txn) async {
      // Insert user
      final userId = await txn.insert('users', userMap);

      // Insert staff data if exists
      if (user.staff != null) {
        final staffMap = _staffModelToMap(user.staff!, userId);
        staffMap['sync_status'] = SyncStatus.pending.value;
        await txn.insert('staff', staffMap);
      }

      // Queue for sync
      await _syncService.queueOperation('users', SyncOperation.create, userMap);
    });

    return user;
  }

  @override
  Future<UserModel> updateUser(UserModel user) async {
    final userMap = _userModelToMap(user);
    userMap['sync_status'] = SyncStatus.pending.value;
    userMap['updated_at'] = DateTime.now().toIso8601String();

    await _databaseService.transaction((txn) async {
      // Update user
      await txn.update('users', userMap, where: 'id = ?', whereArgs: [user.id]);

      // Update or insert staff data
      if (user.staff != null) {
        final staffMap = _staffModelToMap(user.staff!, user.id);
        staffMap['sync_status'] = SyncStatus.pending.value;
        staffMap['updated_at'] = DateTime.now().toIso8601String();

        final existingStaff = await _databaseService.query(
          'staff',
          where: 'user_id = ?',
          whereArgs: [user.id],
        );

        if (existingStaff.isEmpty) {
          await txn.insert('staff', staffMap);
        } else {
          await txn.update('staff', staffMap, where: 'user_id = ?', whereArgs: [user.id]);
        }
      }

      // Queue for sync
      await _syncService.queueOperation('users', SyncOperation.update, userMap);
    });

    return user;
  }

  @override
  Future<void> deleteUser(int id) async {
    await _databaseService.transaction((txn) async {
      // Get user data before deletion for sync queue
      final userData = await _databaseService.query(
        'users',
        where: 'id = ?',
        whereArgs: [id],
      );

      if (userData.isNotEmpty) {
        // Delete user (cascade will handle staff)
        await txn.delete('users', where: 'id = ?', whereArgs: [id]);

        // Queue for sync
        await _syncService.queueOperation(
          'users',
          SyncOperation.delete,
          {'id': id},
        );
      }
    });
  }

  @override
  Future<List<UserModel>> searchUsers(String query) async {
    final results = await _databaseService.query(
      'users',
      where: 'name LIKE ? OR email LIKE ?',
      whereArgs: ['%$query%', '%$query%'],
      orderBy: 'name ASC',
      limit: 50,
    );

    final users = <UserModel>[];
    for (final userMap in results) {
      final user = await _mapToUserModel(userMap);
      users.add(user);
    }

    return users;
  }

  @override
  Future<List<UserModel>> getStaffUsers() async {
    final results = await _databaseService.query(
      'users',
      where: 'is_staff = 1',
      orderBy: 'name ASC',
    );

    final users = <UserModel>[];
    for (final userMap in results) {
      final user = await _mapToUserModel(userMap);
      users.add(user);
    }

    return users;
  }

  @override
  Future<void> clearUsers() async {
    await _databaseService.delete('users');
  }

  @override
  Future<int> getUserCount() async {
    final result = await _databaseService.rawQuery('SELECT COUNT(*) as count FROM users');
    return result.first['count'] as int;
  }

  @override
  Future<List<UserModel>> getUnsyncedUsers() async {
    final results = await _databaseService.query(
      'users',
      where: 'sync_status != ?',
      whereArgs: [SyncStatus.synced.value],
    );

    final users = <UserModel>[];
    for (final userMap in results) {
      final user = await _mapToUserModel(userMap);
      users.add(user);
    }

    return users;
  }

  Future<UserModel> _mapToUserModel(Map<String, dynamic> userMap) async {
    // Get staff data if user is staff
    StaffModel? staff;
    if (userMap['is_staff'] == 1) {
      final staffResults = await _databaseService.query(
        'staff',
        where: 'user_id = ?',
        whereArgs: [userMap['id']],
      );

      if (staffResults.isNotEmpty) {
        staff = _mapToStaffModel(staffResults.first);
      }
    }

    return UserModel(
      id: userMap['id'],
      name: userMap['name'],
      email: userMap['email'],
      profilePhotoPath: userMap['profile_photo_path'],
      profilePhotoUrl: userMap['profile_photo_url'],
      emailVerifiedAt: userMap['email_verified_at'] != null
          ? DateTime.parse(userMap['email_verified_at'])
          : null,
      createdAt: DateTime.parse(userMap['created_at']),
      updatedAt: DateTime.parse(userMap['updated_at']),
      staffModel: staff,
    );
  }

  StaffModel _mapToStaffModel(Map<String, dynamic> staffMap) {
    return StaffModel(
      id: staffMap['id'],
      userId: staffMap['user_id'],
      position: staffMap['position'],
      department: staffMap['department'],
      hiredAt: staffMap['hired_at'] != null ? DateTime.parse(staffMap['hired_at']) : null,
      isActive: staffMap['is_active'] == 1,
      createdAt: DateTime.parse(staffMap['created_at']),
      updatedAt: DateTime.parse(staffMap['updated_at']),
    );
  }

  Map<String, dynamic> _userModelToMap(UserModel user) {
    return {
      'id': user.id,
      'name': user.name,
      'email': user.email,
      'profile_photo_path': user.profilePhotoPath,
      'profile_photo_url': user.profilePhotoUrl,
      'email_verified_at': user.emailVerifiedAt?.toIso8601String(),
      'created_at': user.createdAt.toIso8601String(),
      'updated_at': user.updatedAt.toIso8601String(),
      'is_staff': user.staff != null ? 1 : 0,
    };
  }

  Map<String, dynamic> _staffModelToMap(StaffModel staff, int userId) {
    return {
      'id': staff.id,
      'user_id': userId,
      'position': staff.position,
      'department': staff.department,
      'hired_at': staff.hiredAt?.toIso8601String(),
      'is_active': staff.isActive ? 1 : 0,
      'created_at': staff.createdAt.toIso8601String(),
      'updated_at': staff.updatedAt.toIso8601String(),
    };
  }
}
