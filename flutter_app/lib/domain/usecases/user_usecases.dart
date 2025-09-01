import 'package:dartz/dartz.dart';
import 'package:injectable/injectable.dart';

import '../../core/errors/failures.dart';
import '../entities/user.dart';
import '../repositories/user_repository.dart';

@injectable
class GetUsersUseCase {
  final UserRepository _repository;

  GetUsersUseCase(this._repository);

  Future<Either<Failure, List<User>>> call({
    int page = 1,
    int limit = 20,
    String? search,
    String? role,
    String? department,
  }) {
    return _repository.getUsers(
      page: page,
      limit: limit,
      search: search,
      role: role,
      department: department,
    );
  }
}

@injectable
class GetUserByIdUseCase {
  final UserRepository _repository;

  GetUserByIdUseCase(this._repository);

  Future<Either<Failure, User>> call(int id) {
    return _repository.getUserById(id);
  }
}

@injectable
class UpdateUserUseCase {
  final UserRepository _repository;

  UpdateUserUseCase(this._repository);

  Future<Either<Failure, User>> call(int id, Map<String, dynamic> data) {
    return _repository.updateUser(id, data);
  }
}

@injectable
class DeleteUserUseCase {
  final UserRepository _repository;

  DeleteUserUseCase(this._repository);

  Future<Either<Failure, void>> call(int id) {
    return _repository.deleteUser(id);
  }
}

@injectable
class SearchUsersUseCase {
  final UserRepository _repository;

  SearchUsersUseCase(this._repository);

  Future<Either<Failure, List<User>>> call(String query) {
    return _repository.searchUsers(query);
  }
}

@injectable
class GetStaffUsersUseCase {
  final UserRepository _repository;

  GetStaffUsersUseCase(this._repository);

  Future<Either<Failure, List<User>>> call() {
    return _repository.getStaffUsers();
  }
}

@injectable
class UpdateUserRoleUseCase {
  final UserRepository _repository;

  UpdateUserRoleUseCase(this._repository);

  Future<Either<Failure, User>> call(int id, String role) {
    return _repository.updateUserRole(id, role);
  }
}

@injectable
class UpdateUserStatusUseCase {
  final UserRepository _repository;

  UpdateUserStatusUseCase(this._repository);

  Future<Either<Failure, User>> call(int id, bool isActive) {
    return _repository.updateUserStatus(id, isActive);
  }
}
