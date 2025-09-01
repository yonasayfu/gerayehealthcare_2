import 'package:json_annotation/json_annotation.dart';
import 'package:equatable/equatable.dart';

part 'visit_service_model.g.dart';

@JsonSerializable()
class VisitServiceModel extends Equatable {
  final int id;
  
  @JsonKey(name: 'patient_id')
  final int patientId;
  
  @JsonKey(name: 'assigned_staff_id')
  final int? assignedStaffId;
  
  @JsonKey(name: 'service_id')
  final int? serviceId;
  
  @JsonKey(name: 'visit_date')
  final String visitDate;
  
  @JsonKey(name: 'scheduled_start_time')
  final String? scheduledStartTime;
  
  @JsonKey(name: 'scheduled_end_time')
  final String? scheduledEndTime;
  
  @JsonKey(name: 'actual_start_time')
  final String? actualStartTime;
  
  @JsonKey(name: 'actual_end_time')
  final String? actualEndTime;
  
  @JsonKey(name: 'actual_duration')
  final int? actualDuration; // in minutes
  
  @JsonKey(name: 'service_type')
  final String serviceType;
  
  final String status;
  final String priority;
  
  @JsonKey(name: 'visit_location')
  final String? visitLocation;
  
  @JsonKey(name: 'patient_location')
  final String? patientLocation;
  
  @JsonKey(name: 'gps_coordinates')
  final String? gpsCoordinates;
  
  @JsonKey(name: 'check_in_time')
  final String? checkInTime;
  
  @JsonKey(name: 'check_out_time')
  final String? checkOutTime;
  
  @JsonKey(name: 'check_in_location')
  final String? checkInLocation;
  
  @JsonKey(name: 'check_out_location')
  final String? checkOutLocation;
  
  @JsonKey(name: 'service_notes')
  final String? serviceNotes;
  
  @JsonKey(name: 'patient_condition')
  final String? patientCondition;
  
  @JsonKey(name: 'treatment_provided')
  final String? treatmentProvided;
  
  @JsonKey(name: 'medications_administered')
  final String? medicationsAdministered;
  
  @JsonKey(name: 'follow_up_required')
  final bool followUpRequired;
  
  @JsonKey(name: 'follow_up_date')
  final String? followUpDate;
  
  @JsonKey(name: 'follow_up_notes')
  final String? followUpNotes;
  
  @JsonKey(name: 'total_cost')
  final double? totalCost;
  
  @JsonKey(name: 'paid_amount')
  final double? paidAmount;
  
  @JsonKey(name: 'payment_status')
  final String? paymentStatus;
  
  @JsonKey(name: 'payment_method')
  final String? paymentMethod;
  
  @JsonKey(name: 'insurance_claim_id')
  final int? insuranceClaimId;
  
  final double? rating;
  final String? feedback;
  
  @JsonKey(name: 'cancellation_reason')
  final String? cancellationReason;
  
  @JsonKey(name: 'cancelled_at')
  final String? cancelledAt;
  
  @JsonKey(name: 'cancelled_by')
  final int? cancelledBy;
  
  @JsonKey(name: 'created_at')
  final String createdAt;
  
  @JsonKey(name: 'updated_at')
  final String updatedAt;
  
  // Relationships
  final PatientModel? patient;
  
  @JsonKey(name: 'assigned_staff')
  final StaffModel? assignedStaff;
  
  final ServiceModel? service;
  
  @JsonKey(name: 'insurance_claim')
  final InsuranceClaimModel? insuranceClaim;

  const VisitServiceModel({
    required this.id,
    required this.patientId,
    this.assignedStaffId,
    this.serviceId,
    required this.visitDate,
    this.scheduledStartTime,
    this.scheduledEndTime,
    this.actualStartTime,
    this.actualEndTime,
    this.actualDuration,
    required this.serviceType,
    required this.status,
    required this.priority,
    this.visitLocation,
    this.patientLocation,
    this.gpsCoordinates,
    this.checkInTime,
    this.checkOutTime,
    this.checkInLocation,
    this.checkOutLocation,
    this.serviceNotes,
    this.patientCondition,
    this.treatmentProvided,
    this.medicationsAdministered,
    required this.followUpRequired,
    this.followUpDate,
    this.followUpNotes,
    this.totalCost,
    this.paidAmount,
    this.paymentStatus,
    this.paymentMethod,
    this.insuranceClaimId,
    this.rating,
    this.feedback,
    this.cancellationReason,
    this.cancelledAt,
    this.cancelledBy,
    required this.createdAt,
    required this.updatedAt,
    this.patient,
    this.assignedStaff,
    this.service,
    this.insuranceClaim,
  });

  factory VisitServiceModel.fromJson(Map<String, dynamic> json) =>
      _$VisitServiceModelFromJson(json);

  Map<String, dynamic> toJson() => _$VisitServiceModelToJson(this);

  // Helper methods
  String get patientName => patient?.fullName ?? 'Unknown Patient';
  String get staffName => assignedStaff?.displayName ?? 'Unassigned';
  String get serviceName => service?.name ?? serviceType;
  
  bool get isScheduled => status.toLowerCase() == 'scheduled';
  bool get isInProgress => status.toLowerCase() == 'in_progress';
  bool get isCompleted => status.toLowerCase() == 'completed';
  bool get isCancelled => status.toLowerCase() == 'cancelled';
  bool get isNoShow => status.toLowerCase() == 'no_show';
  
  bool get isHighPriority => priority.toLowerCase() == 'high';
  bool get isUrgent => priority.toLowerCase() == 'urgent';
  bool get isEmergency => priority.toLowerCase() == 'emergency';
  
  bool get isCheckedIn => checkInTime != null;
  bool get isCheckedOut => checkOutTime != null;
  
  bool get isPaid => paymentStatus?.toLowerCase() == 'paid';
  bool get isPartiallyPaid => paymentStatus?.toLowerCase() == 'partial';
  bool get isUnpaid => paymentStatus?.toLowerCase() == 'unpaid';
  
  bool get hasRating => rating != null && rating! > 0;
  bool get hasFeedback => feedback != null && feedback!.isNotEmpty;
  
  // Get visit duration in minutes
  int? get scheduledDuration {
    if (scheduledStartTime == null || scheduledEndTime == null) return null;
    try {
      final start = DateTime.parse('${visitDate}T$scheduledStartTime');
      final end = DateTime.parse('${visitDate}T$scheduledEndTime');
      return end.difference(start).inMinutes;
    } catch (e) {
      return null;
    }
  }
  
  // Get actual duration or calculate from check-in/out times
  int? get totalDuration {
    if (actualDuration != null) return actualDuration;
    if (checkInTime != null && checkOutTime != null) {
      try {
        final checkIn = DateTime.parse('${visitDate}T$checkInTime');
        final checkOut = DateTime.parse('${visitDate}T$checkOutTime');
        return checkOut.difference(checkIn).inMinutes;
      } catch (e) {
        return null;
      }
    }
    return null;
  }
  
  // Get formatted duration
  String get durationDisplay {
    final duration = totalDuration ?? scheduledDuration;
    if (duration == null) return 'Duration not specified';
    
    final hours = duration ~/ 60;
    final minutes = duration % 60;
    
    if (hours > 0) {
      return '${hours}h ${minutes}m';
    }
    return '${minutes}m';
  }
  
  // Get visit time display
  String get timeDisplay {
    if (scheduledStartTime != null && scheduledEndTime != null) {
      return '$scheduledStartTime - $scheduledEndTime';
    }
    if (scheduledStartTime != null) {
      return 'From $scheduledStartTime';
    }
    return 'Time not specified';
  }
  
  // Get status display with color coding
  String get statusDisplay {
    switch (status.toLowerCase()) {
      case 'scheduled':
        return 'Scheduled';
      case 'in_progress':
        return 'In Progress';
      case 'completed':
        return 'Completed';
      case 'cancelled':
        return 'Cancelled';
      case 'no_show':
        return 'No Show';
      default:
        return status;
    }
  }
  
  // Get priority display
  String get priorityDisplay {
    switch (priority.toLowerCase()) {
      case 'low':
        return 'Low Priority';
      case 'normal':
        return 'Normal';
      case 'high':
        return 'High Priority';
      case 'urgent':
        return 'Urgent';
      case 'emergency':
        return 'Emergency';
      default:
        return priority;
    }
  }
  
  // Get payment status display
  String get paymentStatusDisplay {
    switch (paymentStatus?.toLowerCase()) {
      case 'paid':
        return 'Paid';
      case 'partial':
        return 'Partially Paid';
      case 'unpaid':
        return 'Unpaid';
      case 'pending':
        return 'Payment Pending';
      default:
        return 'Payment Status Unknown';
    }
  }
  
  // Get outstanding amount
  double get outstandingAmount {
    if (totalCost == null) return 0.0;
    return totalCost! - (paidAmount ?? 0.0);
  }
  
  // Check if visit is overdue
  bool get isOverdue {
    if (!isScheduled) return false;
    try {
      final visitDateTime = DateTime.parse('${visitDate}T${scheduledStartTime ?? '00:00:00'}');
      return DateTime.now().isAfter(visitDateTime);
    } catch (e) {
      return false;
    }
  }
  
  // Get visit date and time as DateTime
  DateTime? get visitDateTime {
    try {
      return DateTime.parse('${visitDate}T${scheduledStartTime ?? '00:00:00'}');
    } catch (e) {
      return null;
    }
  }

  @override
  List<Object?> get props => [
        id,
        patientId,
        assignedStaffId,
        serviceId,
        visitDate,
        scheduledStartTime,
        scheduledEndTime,
        actualStartTime,
        actualEndTime,
        actualDuration,
        serviceType,
        status,
        priority,
        visitLocation,
        patientLocation,
        gpsCoordinates,
        checkInTime,
        checkOutTime,
        checkInLocation,
        checkOutLocation,
        serviceNotes,
        patientCondition,
        treatmentProvided,
        medicationsAdministered,
        followUpRequired,
        followUpDate,
        followUpNotes,
        totalCost,
        paidAmount,
        paymentStatus,
        paymentMethod,
        insuranceClaimId,
        rating,
        feedback,
        cancellationReason,
        cancelledAt,
        cancelledBy,
        createdAt,
        updatedAt,
      ];
}

// Forward declarations and related models
class PatientModel {}
class StaffModel {}

@JsonSerializable()
class ServiceModel extends Equatable {
  final int id;
  final String name;
  final String? description;
  
  @JsonKey(name: 'base_price')
  final double basePrice;
  
  final String category;
  final int duration; // in minutes
  final String status;

  const ServiceModel({
    required this.id,
    required this.name,
    this.description,
    required this.basePrice,
    required this.category,
    required this.duration,
    required this.status,
  });

  factory ServiceModel.fromJson(Map<String, dynamic> json) =>
      _$ServiceModelFromJson(json);

  Map<String, dynamic> toJson() => _$ServiceModelToJson(this);

  @override
  List<Object?> get props => [id, name, description, basePrice, category, duration, status];
}

class InsuranceClaimModel {}
