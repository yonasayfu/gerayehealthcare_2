import 'package:json_annotation/json_annotation.dart';
import 'package:equatable/equatable.dart';

part 'patient_model.g.dart';

@JsonSerializable()
class PatientModel extends Equatable {
  final int id;
  
  @JsonKey(name: 'full_name')
  final String fullName;
  
  final String? email;
  
  @JsonKey(name: 'phone_number')
  final String? phoneNumber;
  
  @JsonKey(name: 'date_of_birth')
  final String? dateOfBirth;
  
  final String? gender;
  final String? address;
  final String? city;
  final String? state;
  final String? country;
  
  @JsonKey(name: 'postal_code')
  final String? postalCode;
  
  @JsonKey(name: 'emergency_contact_name')
  final String? emergencyContactName;
  
  @JsonKey(name: 'emergency_contact_phone')
  final String? emergencyContactPhone;
  
  @JsonKey(name: 'emergency_contact_relationship')
  final String? emergencyContactRelationship;
  
  @JsonKey(name: 'medical_history')
  final String? medicalHistory;
  
  final String? allergies;
  
  @JsonKey(name: 'current_medications')
  final String? currentMedications;
  
  @JsonKey(name: 'insurance_provider')
  final String? insuranceProvider;
  
  @JsonKey(name: 'insurance_policy_number')
  final String? insurancePolicyNumber;
  
  @JsonKey(name: 'preferred_language')
  final String? preferredLanguage;
  
  @JsonKey(name: 'blood_type')
  final String? bloodType;
  
  final double? height;
  final double? weight;
  
  @JsonKey(name: 'lead_source')
  final String? leadSource;
  
  @JsonKey(name: 'marketing_campaign_id')
  final int? marketingCampaignId;
  
  @JsonKey(name: 'assigned_staff_id')
  final int? assignedStaffId;
  
  final String status;
  final String? notes;
  
  @JsonKey(name: 'created_at')
  final String createdAt;
  
  @JsonKey(name: 'updated_at')
  final String updatedAt;
  
  // Relationships
  @JsonKey(name: 'assigned_staff')
  final StaffModel? assignedStaff;
  
  @JsonKey(name: 'visit_services')
  final List<VisitServiceModel>? visitServices;
  
  @JsonKey(name: 'insurance_policies')
  final List<InsurancePolicyModel>? insurancePolicies;

  const PatientModel({
    required this.id,
    required this.fullName,
    this.email,
    this.phoneNumber,
    this.dateOfBirth,
    this.gender,
    this.address,
    this.city,
    this.state,
    this.country,
    this.postalCode,
    this.emergencyContactName,
    this.emergencyContactPhone,
    this.emergencyContactRelationship,
    this.medicalHistory,
    this.allergies,
    this.currentMedications,
    this.insuranceProvider,
    this.insurancePolicyNumber,
    this.preferredLanguage,
    this.bloodType,
    this.height,
    this.weight,
    this.leadSource,
    this.marketingCampaignId,
    this.assignedStaffId,
    required this.status,
    this.notes,
    required this.createdAt,
    required this.updatedAt,
    this.assignedStaff,
    this.visitServices,
    this.insurancePolicies,
  });

  factory PatientModel.fromJson(Map<String, dynamic> json) =>
      _$PatientModelFromJson(json);

  Map<String, dynamic> toJson() => _$PatientModelToJson(this);

  // Helper methods
  String get displayName => fullName;
  
  String get initials {
    final names = fullName.split(' ');
    if (names.length >= 2) {
      return '${names.first[0]}${names.last[0]}'.toUpperCase();
    }
    return fullName.isNotEmpty ? fullName[0].toUpperCase() : '?';
  }
  
  String get primaryContact => phoneNumber ?? email ?? 'No contact';
  
  bool get hasEmergencyContact => 
      emergencyContactName != null && emergencyContactPhone != null;
  
  bool get hasInsurance => 
      insuranceProvider != null && insurancePolicyNumber != null;
  
  bool get isActive => status.toLowerCase() == 'active';
  
  int? get age {
    if (dateOfBirth == null) return null;
    try {
      final birthDate = DateTime.parse(dateOfBirth!);
      final now = DateTime.now();
      int age = now.year - birthDate.year;
      if (now.month < birthDate.month || 
          (now.month == birthDate.month && now.day < birthDate.day)) {
        age--;
      }
      return age;
    } catch (e) {
      return null;
    }
  }
  
  String get ageDisplay => age != null ? '$age years' : 'Unknown';
  
  double? get bmi {
    if (height == null || weight == null || height == 0) return null;
    final heightInMeters = height! / 100; // Convert cm to meters
    return weight! / (heightInMeters * heightInMeters);
  }
  
  String get bmiCategory {
    final bmiValue = bmi;
    if (bmiValue == null) return 'Unknown';
    
    if (bmiValue < 18.5) return 'Underweight';
    if (bmiValue < 25) return 'Normal';
    if (bmiValue < 30) return 'Overweight';
    return 'Obese';
  }
  
  bool get hasCompleteProfile {
    return email != null &&
           phoneNumber != null &&
           dateOfBirth != null &&
           gender != null &&
           address != null;
  }
  
  // Get formatted address
  String get formattedAddress {
    final parts = <String>[];
    if (address != null) parts.add(address!);
    if (city != null) parts.add(city!);
    if (state != null) parts.add(state!);
    if (country != null) parts.add(country!);
    return parts.join(', ');
  }

  @override
  List<Object?> get props => [
        id,
        fullName,
        email,
        phoneNumber,
        dateOfBirth,
        gender,
        address,
        city,
        state,
        country,
        postalCode,
        emergencyContactName,
        emergencyContactPhone,
        emergencyContactRelationship,
        medicalHistory,
        allergies,
        currentMedications,
        insuranceProvider,
        insurancePolicyNumber,
        preferredLanguage,
        bloodType,
        height,
        weight,
        leadSource,
        marketingCampaignId,
        assignedStaffId,
        status,
        notes,
        createdAt,
        updatedAt,
      ];

  PatientModel copyWith({
    int? id,
    String? fullName,
    String? email,
    String? phoneNumber,
    String? dateOfBirth,
    String? gender,
    String? address,
    String? city,
    String? state,
    String? country,
    String? postalCode,
    String? emergencyContactName,
    String? emergencyContactPhone,
    String? emergencyContactRelationship,
    String? medicalHistory,
    String? allergies,
    String? currentMedications,
    String? insuranceProvider,
    String? insurancePolicyNumber,
    String? preferredLanguage,
    String? bloodType,
    double? height,
    double? weight,
    String? leadSource,
    int? marketingCampaignId,
    int? assignedStaffId,
    String? status,
    String? notes,
    String? createdAt,
    String? updatedAt,
    StaffModel? assignedStaff,
    List<VisitServiceModel>? visitServices,
    List<InsurancePolicyModel>? insurancePolicies,
  }) {
    return PatientModel(
      id: id ?? this.id,
      fullName: fullName ?? this.fullName,
      email: email ?? this.email,
      phoneNumber: phoneNumber ?? this.phoneNumber,
      dateOfBirth: dateOfBirth ?? this.dateOfBirth,
      gender: gender ?? this.gender,
      address: address ?? this.address,
      city: city ?? this.city,
      state: state ?? this.state,
      country: country ?? this.country,
      postalCode: postalCode ?? this.postalCode,
      emergencyContactName: emergencyContactName ?? this.emergencyContactName,
      emergencyContactPhone: emergencyContactPhone ?? this.emergencyContactPhone,
      emergencyContactRelationship: emergencyContactRelationship ?? this.emergencyContactRelationship,
      medicalHistory: medicalHistory ?? this.medicalHistory,
      allergies: allergies ?? this.allergies,
      currentMedications: currentMedications ?? this.currentMedications,
      insuranceProvider: insuranceProvider ?? this.insuranceProvider,
      insurancePolicyNumber: insurancePolicyNumber ?? this.insurancePolicyNumber,
      preferredLanguage: preferredLanguage ?? this.preferredLanguage,
      bloodType: bloodType ?? this.bloodType,
      height: height ?? this.height,
      weight: weight ?? this.weight,
      leadSource: leadSource ?? this.leadSource,
      marketingCampaignId: marketingCampaignId ?? this.marketingCampaignId,
      assignedStaffId: assignedStaffId ?? this.assignedStaffId,
      status: status ?? this.status,
      notes: notes ?? this.notes,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      assignedStaff: assignedStaff ?? this.assignedStaff,
      visitServices: visitServices ?? this.visitServices,
      insurancePolicies: insurancePolicies ?? this.insurancePolicies,
    );
  }
}

// Forward declarations for related models
class StaffModel {}
class VisitServiceModel {}
class InsurancePolicyModel {}
