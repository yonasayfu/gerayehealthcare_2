import 'package:json_annotation/json_annotation.dart';
import 'package:equatable/equatable.dart';

part 'staff_model.g.dart';

@JsonSerializable()
class StaffModel extends Equatable {
  final int id;
  
  @JsonKey(name: 'user_id')
  final int userId;
  
  final String? phone;
  final String specialization;
  final String? qualification;
  
  @JsonKey(name: 'experience_years')
  final int? experienceYears;
  
  @JsonKey(name: 'hourly_rate')
  final double? hourlyRate;
  
  final String status;
  
  @JsonKey(name: 'availability_status')
  final String availabilityStatus;
  
  @JsonKey(name: 'work_schedule')
  final Map<String, dynamic>? workSchedule;
  
  final String? location;
  final String? department;
  final String? notes;
  
  @JsonKey(name: 'created_at')
  final String createdAt;
  
  @JsonKey(name: 'updated_at')
  final String updatedAt;
  
  // Relationships
  final UserModel? user;
  
  @JsonKey(name: 'visit_services')
  final List<VisitServiceModel>? visitServices;
  
  @JsonKey(name: 'assigned_patients')
  final List<PatientModel>? assignedPatients;

  const StaffModel({
    required this.id,
    required this.userId,
    this.phone,
    required this.specialization,
    this.qualification,
    this.experienceYears,
    this.hourlyRate,
    required this.status,
    required this.availabilityStatus,
    this.workSchedule,
    this.location,
    this.department,
    this.notes,
    required this.createdAt,
    required this.updatedAt,
    this.user,
    this.visitServices,
    this.assignedPatients,
  });

  factory StaffModel.fromJson(Map<String, dynamic> json) =>
      _$StaffModelFromJson(json);

  Map<String, dynamic> toJson() => _$StaffModelToJson(this);

  // Helper methods
  String get displayName => user?.name ?? 'Unknown Staff';
  
  String get initials {
    final name = displayName;
    final names = name.split(' ');
    if (names.length >= 2) {
      return '${names.first[0]}${names.last[0]}'.toUpperCase();
    }
    return name.isNotEmpty ? name[0].toUpperCase() : '?';
  }
  
  String get email => user?.email ?? '';
  
  String get primaryContact => phone ?? email;
  
  bool get isActive => status.toLowerCase() == 'active';
  bool get isAvailable => availabilityStatus.toLowerCase() == 'available';
  bool get isBusy => availabilityStatus.toLowerCase() == 'busy';
  bool get isOffline => availabilityStatus.toLowerCase() == 'offline';
  bool get isOnEmergency => availabilityStatus.toLowerCase() == 'emergency';
  
  String get experienceDisplay {
    if (experienceYears == null) return 'Experience not specified';
    if (experienceYears == 1) return '1 year experience';
    return '$experienceYears years experience';
  }
  
  String get hourlyRateDisplay {
    if (hourlyRate == null) return 'Rate not specified';
    return '\$${hourlyRate!.toStringAsFixed(2)}/hour';
  }
  
  String get availabilityStatusDisplay {
    switch (availabilityStatus.toLowerCase()) {
      case 'available':
        return 'Available';
      case 'busy':
        return 'Busy';
      case 'offline':
        return 'Offline';
      case 'emergency':
        return 'On Emergency';
      default:
        return availabilityStatus;
    }
  }
  
  // Get work schedule for a specific day
  Map<String, dynamic>? getScheduleForDay(String day) {
    if (workSchedule == null) return null;
    return workSchedule![day.toLowerCase()] as Map<String, dynamic>?;
  }
  
  // Check if staff is working on a specific day
  bool isWorkingOn(String day) {
    final daySchedule = getScheduleForDay(day);
    return daySchedule != null && daySchedule['is_working'] == true;
  }
  
  // Get working hours for a specific day
  String getWorkingHours(String day) {
    final daySchedule = getScheduleForDay(day);
    if (daySchedule == null || daySchedule['is_working'] != true) {
      return 'Not working';
    }
    
    final startTime = daySchedule['start_time'] as String?;
    final endTime = daySchedule['end_time'] as String?;
    
    if (startTime != null && endTime != null) {
      return '$startTime - $endTime';
    }
    
    return 'Schedule not specified';
  }
  
  // Get total patients assigned
  int get totalPatientsAssigned => assignedPatients?.length ?? 0;
  
  // Get total visits
  int get totalVisits => visitServices?.length ?? 0;
  
  // Get completed visits
  int get completedVisits => visitServices
      ?.where((visit) => visit.status.toLowerCase() == 'completed')
      .length ?? 0;
  
  // Calculate completion rate
  double get completionRate {
    if (totalVisits == 0) return 0.0;
    return (completedVisits / totalVisits) * 100;
  }

  @override
  List<Object?> get props => [
        id,
        userId,
        phone,
        specialization,
        qualification,
        experienceYears,
        hourlyRate,
        status,
        availabilityStatus,
        workSchedule,
        location,
        department,
        notes,
        createdAt,
        updatedAt,
      ];

  StaffModel copyWith({
    int? id,
    int? userId,
    String? phone,
    String? specialization,
    String? qualification,
    int? experienceYears,
    double? hourlyRate,
    String? status,
    String? availabilityStatus,
    Map<String, dynamic>? workSchedule,
    String? location,
    String? department,
    String? notes,
    String? createdAt,
    String? updatedAt,
    UserModel? user,
    List<VisitServiceModel>? visitServices,
    List<PatientModel>? assignedPatients,
  }) {
    return StaffModel(
      id: id ?? this.id,
      userId: userId ?? this.userId,
      phone: phone ?? this.phone,
      specialization: specialization ?? this.specialization,
      qualification: qualification ?? this.qualification,
      experienceYears: experienceYears ?? this.experienceYears,
      hourlyRate: hourlyRate ?? this.hourlyRate,
      status: status ?? this.status,
      availabilityStatus: availabilityStatus ?? this.availabilityStatus,
      workSchedule: workSchedule ?? this.workSchedule,
      location: location ?? this.location,
      department: department ?? this.department,
      notes: notes ?? this.notes,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      user: user ?? this.user,
      visitServices: visitServices ?? this.visitServices,
      assignedPatients: assignedPatients ?? this.assignedPatients,
    );
  }
}

@JsonSerializable()
class UserModel extends Equatable {
  final int id;
  final String name;
  final String email;
  
  @JsonKey(name: 'email_verified_at')
  final String? emailVerifiedAt;
  
  @JsonKey(name: 'created_at')
  final String createdAt;
  
  @JsonKey(name: 'updated_at')
  final String updatedAt;

  const UserModel({
    required this.id,
    required this.name,
    required this.email,
    this.emailVerifiedAt,
    required this.createdAt,
    required this.updatedAt,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) =>
      _$UserModelFromJson(json);

  Map<String, dynamic> toJson() => _$UserModelToJson(this);

  bool get isEmailVerified => emailVerifiedAt != null;

  @override
  List<Object?> get props => [
        id,
        name,
        email,
        emailVerifiedAt,
        createdAt,
        updatedAt,
      ];
}

// Forward declarations
class VisitServiceModel {}
class PatientModel {}
