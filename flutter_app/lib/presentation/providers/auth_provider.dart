import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:riverpod_annotation/riverpod_annotation.dart';

import '../../core/di/injection.dart';
import '../../domain/entities/user.dart';
import '../../domain/usecases/auth_usecases.dart';

part 'auth_provider.g.dart';

// Auth state class
class AuthState {
  final bool isAuthenticated;
  final bool isLoading;
  final User? user;
  final String? error;

  const AuthState({
    this.isAuthenticated = false,
    this.isLoading = false,
    this.user,
    this.error,
  });

  AuthState copyWith({
    bool? isAuthenticated,
    bool? isLoading,
    User? user,
    String? error,
  }) {
    return AuthState(
      isAuthenticated: isAuthenticated ?? this.isAuthenticated,
      isLoading: isLoading ?? this.isLoading,
      user: user ?? this.user,
      error: error ?? this.error,
    );
  }
}

// Auth provider
@riverpod
class Auth extends _$Auth {
  late final CheckAuthStatusUseCase _checkAuthStatusUseCase;
  late final GetCurrentUserUseCase _getCurrentUserUseCase;
  late final LoginUseCase _loginUseCase;
  late final RegisterUseCase _registerUseCase;
  late final LogoutUseCase _logoutUseCase;
  late final ForgotPasswordUseCase _forgotPasswordUseCase;

  @override
  AuthState build() {
    _checkAuthStatusUseCase = getIt<CheckAuthStatusUseCase>();
    _getCurrentUserUseCase = getIt<GetCurrentUserUseCase>();
    _loginUseCase = getIt<LoginUseCase>();
    _registerUseCase = getIt<RegisterUseCase>();
    _logoutUseCase = getIt<LogoutUseCase>();
    _forgotPasswordUseCase = getIt<ForgotPasswordUseCase>();

    _checkAuthStatus();
    return const AuthState(isLoading: true);
  }

  Future<void> _checkAuthStatus() async {
    try {
      final isAuthenticated = await _checkAuthStatusUseCase();

      if (isAuthenticated) {
        final result = await _getCurrentUserUseCase();
        result.fold(
          (failure) => state = state.copyWith(
            isAuthenticated: false,
            isLoading: false,
            error: failure.message,
          ),
          (user) => state = state.copyWith(
            isAuthenticated: true,
            isLoading: false,
            user: user,
          ),
        );
      } else {
        state = state.copyWith(
          isAuthenticated: false,
          isLoading: false,
        );
      }
    } catch (e) {
      state = state.copyWith(
        isAuthenticated: false,
        isLoading: false,
        error: e.toString(),
      );
    }
  }

  Future<void> login(String email, String password, {bool rememberMe = false}) async {
    state = state.copyWith(isLoading: true, error: null);

    final result = await _loginUseCase(email, password, rememberMe: rememberMe);

    result.fold(
      (failure) => state = state.copyWith(
        isLoading: false,
        error: failure.message,
      ),
      (user) => state = state.copyWith(
        isAuthenticated: true,
        isLoading: false,
        user: user,
      ),
    );
  }

  Future<void> register(String name, String email, String password) async {
    state = state.copyWith(isLoading: true, error: null);

    final result = await _registerUseCase(name, email, password);

    result.fold(
      (failure) => state = state.copyWith(
        isLoading: false,
        error: failure.message,
      ),
      (user) => state = state.copyWith(
        isAuthenticated: true,
        isLoading: false,
        user: user,
      ),
    );
  }

  Future<void> logout() async {
    state = state.copyWith(isLoading: true);

    final result = await _logoutUseCase();

    result.fold(
      (failure) => state = const AuthState(
        isAuthenticated: false,
        isLoading: false,
      ), // Even if logout fails, clear local state
      (_) => state = const AuthState(
        isAuthenticated: false,
        isLoading: false,
      ),
    );
  }

  Future<void> forgotPassword(String email) async {
    state = state.copyWith(isLoading: true, error: null);

    final result = await _forgotPasswordUseCase(email);

    result.fold(
      (failure) => state = state.copyWith(
        isLoading: false,
        error: failure.message,
      ),
      (_) => state = state.copyWith(isLoading: false),
    );
  }

  void clearError() {
    state = state.copyWith(error: null);
  }
}
