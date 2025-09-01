import 'package:equatable/equatable.dart';

class User extends Equatable {
  final int id;
  final String name;
  final String email;
  final String? profilePhotoPath;
  final String? profilePhotoUrl;
  final DateTime? emailVerifiedAt;
  final DateTime createdAt;
  final DateTime updatedAt;
  final Staff? staff;

  const User({
    required this.id,
    required this.name,
    required this.email,
    this.profilePhotoPath,
    this.profilePhotoUrl,
    this.emailVerifiedAt,
    required this.createdAt,
    required this.updatedAt,
    this.staff,
  });

  @override
  List<Object?> get props => [
        id,
        name,
        email,
        profilePhotoPath,
        profilePhotoUrl,
        emailVerifiedAt,
        createdAt,
        updatedAt,
        staff,
      ];

  bool get isEmailVerified => emailVerifiedAt != null;
  bool get hasProfilePhoto => profilePhotoUrl != null || profilePhotoPath != null;
  bool get isStaff => staff != null;

  String get displayName => name;
  String get initials {
    final words = name.trim().split(' ');
    if (words.isEmpty) return '';
    
    if (words.length == 1) {
      return words[0].substring(0, 1).toUpperCase();
    } else {
      return (words[0].substring(0, 1) + words[1].substring(0, 1)).toUpperCase();
    }
  }

  User copyWith({
    int? id,
    String? name,
    String? email,
    String? profilePhotoPath,
    String? profilePhotoUrl,
    DateTime? emailVerifiedAt,
    DateTime? createdAt,
    DateTime? updatedAt,
    Staff? staff,
  }) {
    return User(
      id: id ?? this.id,
      name: name ?? this.name,
      email: email ?? this.email,
      profilePhotoPath: profilePhotoPath ?? this.profilePhotoPath,
      profilePhotoUrl: profilePhotoUrl ?? this.profilePhotoUrl,
      emailVerifiedAt: emailVerifiedAt ?? this.emailVerifiedAt,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      staff: staff ?? this.staff,
    );
  }
}

class Staff extends Equatable {
  final int id;
  final int userId;
  final String? position;
  final String? department;
  final DateTime? hiredAt;
  final bool isActive;
  final DateTime createdAt;
  final DateTime updatedAt;

  const Staff({
    required this.id,
    required this.userId,
    this.position,
    this.department,
    this.hiredAt,
    required this.isActive,
    required this.createdAt,
    required this.updatedAt,
  });

  @override
  List<Object?> get props => [
        id,
        userId,
        position,
        department,
        hiredAt,
        isActive,
        createdAt,
        updatedAt,
      ];

  String get displayPosition => position ?? 'Staff Member';
  String get displayDepartment => department ?? 'General';

  Staff copyWith({
    int? id,
    int? userId,
    String? position,
    String? department,
    DateTime? hiredAt,
    bool? isActive,
    DateTime? createdAt,
    DateTime? updatedAt,
  }) {
    return Staff(
      id: id ?? this.id,
      userId: userId ?? this.userId,
      position: position ?? this.position,
      department: department ?? this.department,
      hiredAt: hiredAt ?? this.hiredAt,
      isActive: isActive ?? this.isActive,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
    );
  }
}
