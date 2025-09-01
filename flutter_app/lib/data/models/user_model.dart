import 'package:json_annotation/json_annotation.dart';
import '../../domain/entities/user.dart';

part 'user_model.g.dart';

@JsonSerializable()
class UserModel extends User {
  @JsonKey(fromJson: _staffFromJson, toJson: _staffToJson)
  final StaffModel? staffModel;

  const UserModel({
    required super.id,
    required super.name,
    required super.email,
    super.profilePhotoPath,
    super.profilePhotoUrl,
    super.emailVerifiedAt,
    required super.createdAt,
    required super.updatedAt,
    this.staffModel,
  }) : super(staff: staffModel);

  static StaffModel? _staffFromJson(Map<String, dynamic>? json) {
    return json != null ? StaffModel.fromJson(json) : null;
  }

  static Map<String, dynamic>? _staffToJson(StaffModel? staff) {
    return staff?.toJson();
  }

  factory UserModel.fromJson(Map<String, dynamic> json) => _$UserModelFromJson(json);
  Map<String, dynamic> toJson() => _$UserModelToJson(this);

  factory UserModel.fromEntity(User user) {
    return UserModel(
      id: user.id,
      name: user.name,
      email: user.email,
      profilePhotoPath: user.profilePhotoPath,
      profilePhotoUrl: user.profilePhotoUrl,
      emailVerifiedAt: user.emailVerifiedAt,
      createdAt: user.createdAt,
      updatedAt: user.updatedAt,
      staffModel: user.staff != null ? StaffModel.fromEntity(user.staff!) : null,
    );
  }
}

@JsonSerializable()
class StaffModel extends Staff {
  const StaffModel({
    required super.id,
    required super.userId,
    super.position,
    super.department,
    super.hiredAt,
    required super.isActive,
    required super.createdAt,
    required super.updatedAt,
  });

  factory StaffModel.fromJson(Map<String, dynamic> json) => _$StaffModelFromJson(json);
  Map<String, dynamic> toJson() => _$StaffModelToJson(this);

  factory StaffModel.fromEntity(Staff staff) {
    return StaffModel(
      id: staff.id,
      userId: staff.userId,
      position: staff.position,
      department: staff.department,
      hiredAt: staff.hiredAt,
      isActive: staff.isActive,
      createdAt: staff.createdAt,
      updatedAt: staff.updatedAt,
    );
  }
}

@JsonSerializable()
class AuthResponse {
  final String accessToken;
  final String? refreshToken;
  final UserModel user;
  final String tokenType;
  final int? expiresIn;

  const AuthResponse({
    required this.accessToken,
    this.refreshToken,
    required this.user,
    this.tokenType = 'Bearer',
    this.expiresIn,
  });

  factory AuthResponse.fromJson(Map<String, dynamic> json) => _$AuthResponseFromJson(json);
  Map<String, dynamic> toJson() => _$AuthResponseToJson(this);
}

@JsonSerializable()
class LoginRequest {
  final String email;
  final String password;
  final bool rememberMe;

  const LoginRequest({
    required this.email,
    required this.password,
    this.rememberMe = false,
  });

  factory LoginRequest.fromJson(Map<String, dynamic> json) => _$LoginRequestFromJson(json);
  Map<String, dynamic> toJson() => _$LoginRequestToJson(this);
}

@JsonSerializable()
class RegisterRequest {
  final String name;
  final String email;
  final String password;
  final String passwordConfirmation;

  const RegisterRequest({
    required this.name,
    required this.email,
    required this.password,
    required this.passwordConfirmation,
  });

  factory RegisterRequest.fromJson(Map<String, dynamic> json) => _$RegisterRequestFromJson(json);
  Map<String, dynamic> toJson() => _$RegisterRequestToJson(this);
}

@JsonSerializable()
class ForgotPasswordRequest {
  final String email;

  const ForgotPasswordRequest({required this.email});

  factory ForgotPasswordRequest.fromJson(Map<String, dynamic> json) => _$ForgotPasswordRequestFromJson(json);
  Map<String, dynamic> toJson() => _$ForgotPasswordRequestToJson(this);
}

@JsonSerializable()
class ResetPasswordRequest {
  final String email;
  final String token;
  final String password;
  final String passwordConfirmation;

  const ResetPasswordRequest({
    required this.email,
    required this.token,
    required this.password,
    required this.passwordConfirmation,
  });

  factory ResetPasswordRequest.fromJson(Map<String, dynamic> json) => _$ResetPasswordRequestFromJson(json);
  Map<String, dynamic> toJson() => _$ResetPasswordRequestToJson(this);
}
