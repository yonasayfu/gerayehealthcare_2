import 'package:dartz/dartz.dart';

import '../../core/errors/failures.dart';
import '../entities/user.dart';

abstract class UserRepository {
  Future<Either<Failure, List<User>>> getUsers({
    int page = 1,
    int limit = 20,
    String? search,
    String? role,
    String? department,
  });
  
  Future<Either<Failure, User>> getUserById(int id);
  Future<Either<Failure, User>> updateUser(int id, Map<String, dynamic> data);
  Future<Either<Failure, void>> deleteUser(int id);
  Future<Either<Failure, List<User>>> searchUsers(String query);
  Future<Either<Failure, List<User>>> getStaffUsers();
  Future<Either<Failure, User>> updateUserRole(int id, String role);
  Future<Either<Failure, User>> updateUserStatus(int id, bool isActive);
}
