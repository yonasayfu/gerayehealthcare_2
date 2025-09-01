import 'package:json_annotation/json_annotation.dart';

part 'api_response_model.g.dart';

@JsonSerializable(genericArgumentFactories: true)
class ApiResponse<T> {
  final bool success;
  final String? message;
  final T? data;
  final Map<String, dynamic>? errors;
  final Map<String, dynamic>? meta;

  const ApiResponse({
    required this.success,
    this.message,
    this.data,
    this.errors,
    this.meta,
  });

  factory ApiResponse.fromJson(
    Map<String, dynamic> json,
    T Function(Object? json) fromJsonT,
  ) =>
      _$ApiResponseFromJson(json, fromJsonT);

  Map<String, dynamic> toJson(Object Function(T value) toJsonT) =>
      _$ApiResponseToJson(this, toJsonT);

  // Helper constructors
  factory ApiResponse.success({
    required T data,
    String? message,
    Map<String, dynamic>? meta,
  }) {
    return ApiResponse<T>(
      success: true,
      data: data,
      message: message,
      meta: meta,
    );
  }

  factory ApiResponse.error({
    required String message,
    Map<String, dynamic>? errors,
    Map<String, dynamic>? meta,
  }) {
    return ApiResponse<T>(
      success: false,
      message: message,
      errors: errors,
      meta: meta,
    );
  }

  // Check if response has data
  bool get hasData => data != null;

  // Check if response has errors
  bool get hasErrors => errors != null && errors!.isNotEmpty;

  // Get error message
  String get errorMessage => message ?? 'An error occurred';

  // Get validation errors
  Map<String, List<String>> get validationErrors {
    if (!hasErrors) return {};
    
    final Map<String, List<String>> result = {};
    errors!.forEach((key, value) {
      if (value is List) {
        result[key] = value.cast<String>();
      } else if (value is String) {
        result[key] = [value];
      } else {
        result[key] = [value.toString()];
      }
    });
    
    return result;
  }

  @override
  String toString() {
    return 'ApiResponse{success: $success, message: $message, data: $data, errors: $errors}';
  }

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is ApiResponse &&
          runtimeType == other.runtimeType &&
          success == other.success &&
          message == other.message &&
          data == other.data &&
          errors == other.errors;

  @override
  int get hashCode =>
      success.hashCode ^
      message.hashCode ^
      data.hashCode ^
      errors.hashCode;
}
