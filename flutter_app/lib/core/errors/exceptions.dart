/// Base class for all exceptions in the application
abstract class AppException implements Exception {
  final String message;
  final int? code;
  final dynamic data;

  const AppException({
    required this.message,
    this.code,
    this.data,
  });

  @override
  String toString() => 'AppException: $message (Code: $code)';
}

/// Server-related exceptions
class ServerException extends AppException {
  const ServerException({
    required super.message,
    super.code,
    super.data,
  });

  @override
  String toString() => 'ServerException: $message (Code: $code)';
}

/// Network-related exceptions
class NetworkException extends AppException {
  const NetworkException({
    required super.message,
    super.code,
    super.data,
  });

  @override
  String toString() => 'NetworkException: $message (Code: $code)';
}

/// Cache-related exceptions
class CacheException extends AppException {
  const CacheException({
    required super.message,
    super.code,
    super.data,
  });

  @override
  String toString() => 'CacheException: $message (Code: $code)';
}

/// Authentication-related exceptions
class AuthException extends AppException {
  const AuthException({
    required super.message,
    super.code,
    super.data,
  });

  @override
  String toString() => 'AuthException: $message (Code: $code)';
}

/// Validation-related exceptions
class ValidationException extends AppException {
  final Map<String, List<String>>? errors;

  const ValidationException({
    required super.message,
    super.code,
    super.data,
    this.errors,
  });

  @override
  String toString() => 'ValidationException: $message (Code: $code, Errors: $errors)';
}

/// Permission-related exceptions
class PermissionException extends AppException {
  const PermissionException({
    required super.message,
    super.code,
    super.data,
  });

  @override
  String toString() => 'PermissionException: $message (Code: $code)';
}

/// File-related exceptions
class FileException extends AppException {
  const FileException({
    required super.message,
    super.code,
    super.data,
  });

  @override
  String toString() => 'FileException: $message (Code: $code)';
}

/// Timeout-related exceptions
class TimeoutException extends AppException {
  const TimeoutException({
    required super.message,
    super.code,
    super.data,
  });

  @override
  String toString() => 'TimeoutException: $message (Code: $code)';
}

/// Unknown/Generic exceptions
class UnknownException extends AppException {
  const UnknownException({
    required super.message,
    super.code,
    super.data,
  });

  @override
  String toString() => 'UnknownException: $message (Code: $code)';
}
