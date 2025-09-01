import 'package:dartz/dartz.dart';

import '../../core/errors/failures.dart';
import '../entities/user.dart';

abstract class AuthRepository {
  Future<Either<Failure, User>> login(String email, String password, {bool rememberMe = false});
  Future<Either<Failure, User>> register(String name, String email, String password);
  Future<Either<Failure, void>> logout();
  Future<Either<Failure, User>> getCurrentUser();
  Future<Either<Failure, void>> forgotPassword(String email);
  Future<Either<Failure, void>> resetPassword(String email, String token, String password);
  Future<Either<Failure, void>> updateProfile(Map<String, dynamic> data);
  Future<Either<Failure, void>> changePassword(String currentPassword, String newPassword);
  Future<Either<Failure, bool>> refreshToken();
  Future<bool> isAuthenticated();
  Future<void> clearAuthData();
}
