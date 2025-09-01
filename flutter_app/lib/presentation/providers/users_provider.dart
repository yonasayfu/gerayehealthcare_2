import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:riverpod_annotation/riverpod_annotation.dart';

import '../../core/di/injection.dart';
import '../../domain/entities/user.dart';
import '../../domain/usecases/user_usecases.dart';

part 'users_provider.g.dart';

// Users state class
class UsersState {
  final List<User> users;
  final bool isLoading;
  final bool isLoadingMore;
  final String? error;
  final bool hasMore;
  final int currentPage;
  final String? searchQuery;
  final String? selectedRole;
  final String? selectedDepartment;

  const UsersState({
    this.users = const [],
    this.isLoading = false,
    this.isLoadingMore = false,
    this.error,
    this.hasMore = true,
    this.currentPage = 1,
    this.searchQuery,
    this.selectedRole,
    this.selectedDepartment,
  });

  UsersState copyWith({
    List<User>? users,
    bool? isLoading,
    bool? isLoadingMore,
    String? error,
    bool? hasMore,
    int? currentPage,
    String? searchQuery,
    String? selectedRole,
    String? selectedDepartment,
  }) {
    return UsersState(
      users: users ?? this.users,
      isLoading: isLoading ?? this.isLoading,
      isLoadingMore: isLoadingMore ?? this.isLoadingMore,
      error: error ?? this.error,
      hasMore: hasMore ?? this.hasMore,
      currentPage: currentPage ?? this.currentPage,
      searchQuery: searchQuery ?? this.searchQuery,
      selectedRole: selectedRole ?? this.selectedRole,
      selectedDepartment: selectedDepartment ?? this.selectedDepartment,
    );
  }
}

// Users provider
@riverpod
class Users extends _$Users {
  late final GetUsersUseCase _getUsersUseCase;
  late final SearchUsersUseCase _searchUsersUseCase;
  late final GetStaffUsersUseCase _getStaffUsersUseCase;
  late final UpdateUserUseCase _updateUserUseCase;
  late final DeleteUserUseCase _deleteUserUseCase;
  late final UpdateUserRoleUseCase _updateUserRoleUseCase;
  late final UpdateUserStatusUseCase _updateUserStatusUseCase;

  @override
  UsersState build() {
    _getUsersUseCase = getIt<GetUsersUseCase>();
    _searchUsersUseCase = getIt<SearchUsersUseCase>();
    _getStaffUsersUseCase = getIt<GetStaffUsersUseCase>();
    _updateUserUseCase = getIt<UpdateUserUseCase>();
    _deleteUserUseCase = getIt<DeleteUserUseCase>();
    _updateUserRoleUseCase = getIt<UpdateUserRoleUseCase>();
    _updateUserStatusUseCase = getIt<UpdateUserStatusUseCase>();

    loadUsers();
    return const UsersState();
  }

  Future<void> loadUsers({bool refresh = false}) async {
    if (refresh) {
      state = state.copyWith(
        isLoading: true,
        error: null,
        currentPage: 1,
        hasMore: true,
      );
    } else if (state.isLoading || state.isLoadingMore) {
      return;
    } else {
      state = state.copyWith(isLoading: true, error: null);
    }

    final result = await _getUsersUseCase(
      page: refresh ? 1 : state.currentPage,
      search: state.searchQuery,
      role: state.selectedRole,
      department: state.selectedDepartment,
    );

    result.fold(
      (failure) => state = state.copyWith(
        isLoading: false,
        error: failure.message,
      ),
      (users) => state = state.copyWith(
        isLoading: false,
        users: refresh ? users : [...state.users, ...users],
        hasMore: users.length >= 20, // Assuming 20 is the page size
        currentPage: refresh ? 2 : state.currentPage + 1,
      ),
    );
  }

  Future<void> loadMoreUsers() async {
    if (!state.hasMore || state.isLoadingMore || state.isLoading) return;

    state = state.copyWith(isLoadingMore: true);

    final result = await _getUsersUseCase(
      page: state.currentPage,
      search: state.searchQuery,
      role: state.selectedRole,
      department: state.selectedDepartment,
    );

    result.fold(
      (failure) => state = state.copyWith(
        isLoadingMore: false,
        error: failure.message,
      ),
      (users) => state = state.copyWith(
        isLoadingMore: false,
        users: [...state.users, ...users],
        hasMore: users.length >= 20,
        currentPage: state.currentPage + 1,
      ),
    );
  }

  Future<void> searchUsers(String query) async {
    state = state.copyWith(
      searchQuery: query,
      isLoading: true,
      error: null,
      currentPage: 1,
      hasMore: true,
    );

    if (query.isEmpty) {
      await loadUsers(refresh: true);
      return;
    }

    final result = await _searchUsersUseCase(query);

    result.fold(
      (failure) => state = state.copyWith(
        isLoading: false,
        error: failure.message,
      ),
      (users) => state = state.copyWith(
        isLoading: false,
        users: users,
        hasMore: false, // Search results don't have pagination
      ),
    );
  }

  Future<void> filterByRole(String? role) async {
    state = state.copyWith(
      selectedRole: role,
      isLoading: true,
      error: null,
      currentPage: 1,
      hasMore: true,
    );

    await loadUsers(refresh: true);
  }

  Future<void> filterByDepartment(String? department) async {
    state = state.copyWith(
      selectedDepartment: department,
      isLoading: true,
      error: null,
      currentPage: 1,
      hasMore: true,
    );

    await loadUsers(refresh: true);
  }

  Future<void> clearFilters() async {
    state = state.copyWith(
      searchQuery: null,
      selectedRole: null,
      selectedDepartment: null,
      isLoading: true,
      error: null,
      currentPage: 1,
      hasMore: true,
    );

    await loadUsers(refresh: true);
  }

  Future<void> updateUser(int id, Map<String, dynamic> data) async {
    final result = await _updateUserUseCase(id, data);

    result.fold(
      (failure) => state = state.copyWith(error: failure.message),
      (updatedUser) {
        final updatedUsers = state.users.map((user) {
          return user.id == id ? updatedUser : user;
        }).toList();
        
        state = state.copyWith(users: updatedUsers);
      },
    );
  }

  Future<void> deleteUser(int id) async {
    final result = await _deleteUserUseCase(id);

    result.fold(
      (failure) => state = state.copyWith(error: failure.message),
      (_) {
        final updatedUsers = state.users.where((user) => user.id != id).toList();
        state = state.copyWith(users: updatedUsers);
      },
    );
  }

  Future<void> updateUserRole(int id, String role) async {
    final result = await _updateUserRoleUseCase(id, role);

    result.fold(
      (failure) => state = state.copyWith(error: failure.message),
      (updatedUser) {
        final updatedUsers = state.users.map((user) {
          return user.id == id ? updatedUser : user;
        }).toList();
        
        state = state.copyWith(users: updatedUsers);
      },
    );
  }

  Future<void> updateUserStatus(int id, bool isActive) async {
    final result = await _updateUserStatusUseCase(id, isActive);

    result.fold(
      (failure) => state = state.copyWith(error: failure.message),
      (updatedUser) {
        final updatedUsers = state.users.map((user) {
          return user.id == id ? updatedUser : user;
        }).toList();
        
        state = state.copyWith(users: updatedUsers);
      },
    );
  }

  void clearError() {
    state = state.copyWith(error: null);
  }
}

// Individual user provider
@riverpod
class UserDetail extends _$UserDetail {
  late final GetUserByIdUseCase _getUserByIdUseCase;

  @override
  AsyncValue<User?> build(int userId) {
    _getUserByIdUseCase = getIt<GetUserByIdUseCase>();
    return const AsyncValue.loading();
  }

  Future<void> loadUser() async {
    state = const AsyncValue.loading();
    
    final result = await _getUserByIdUseCase(userId);
    
    result.fold(
      (failure) => state = AsyncValue.error(failure, StackTrace.current),
      (user) => state = AsyncValue.data(user),
    );
  }
}
