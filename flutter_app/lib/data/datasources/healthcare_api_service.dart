import 'package:dio/dio.dart';
import 'package:retrofit/retrofit.dart';

import '../models/patient_model.dart';
import '../models/visit_service_model.dart';
import '../models/staff_model.dart';
import '../models/api_response_model.dart';
import '../models/pagination_model.dart';

part 'healthcare_api_service.g.dart';

@RestApi()
abstract class HealthcareApiService {
  factory HealthcareApiService(Dio dio, {String baseUrl}) = _HealthcareApiService;

  // Patient APIs
  @GET('/patients')
  Future<ApiResponse<PaginationModel<PatientModel>>> getPatients({
    @Query('page') int? page,
    @Query('per_page') int? perPage,
    @Query('search') String? search,
    @Query('status') String? status,
    @Query('sort_by') String? sortBy,
    @Query('sort_order') String? sortOrder,
  });

  @GET('/patients/{id}')
  Future<ApiResponse<PatientModel>> getPatient(@Path('id') int id);

  @POST('/patients')
  Future<ApiResponse<PatientModel>> createPatient(@Body() Map<String, dynamic> patient);

  @PUT('/patients/{id}')
  Future<ApiResponse<PatientModel>> updatePatient(
    @Path('id') int id,
    @Body() Map<String, dynamic> patient,
  );

  @DELETE('/patients/{id}')
  Future<ApiResponse<void>> deletePatient(@Path('id') int id);

  // Visit Service APIs
  @GET('/visit-services')
  Future<ApiResponse<PaginationModel<VisitServiceModel>>> getVisitServices({
    @Query('page') int? page,
    @Query('per_page') int? perPage,
    @Query('patient_id') int? patientId,
    @Query('staff_id') int? staffId,
    @Query('status') String? status,
    @Query('service_type') String? serviceType,
    @Query('start_date') String? startDate,
    @Query('end_date') String? endDate,
  });

  @GET('/visit-services/{id}')
  Future<ApiResponse<VisitServiceModel>> getVisitService(@Path('id') int id);

  @POST('/visit-services')
  Future<ApiResponse<VisitServiceModel>> createVisitService(@Body() Map<String, dynamic> visitService);

  @PUT('/visit-services/{id}')
  Future<ApiResponse<VisitServiceModel>> updateVisitService(
    @Path('id') int id,
    @Body() Map<String, dynamic> visitService,
  );

  @POST('/visit-services/{id}/check-in')
  Future<ApiResponse<VisitServiceModel>> checkInVisit(
    @Path('id') int id,
    @Body() Map<String, dynamic> checkInData,
  );

  @POST('/visit-services/{id}/check-out')
  Future<ApiResponse<VisitServiceModel>> checkOutVisit(
    @Path('id') int id,
    @Body() Map<String, dynamic> checkOutData,
  );

  @POST('/visit-services/{id}/complete')
  Future<ApiResponse<VisitServiceModel>> completeVisit(
    @Path('id') int id,
    @Body() Map<String, dynamic> completionData,
  );

  // Staff APIs
  @GET('/staff')
  Future<ApiResponse<PaginationModel<StaffModel>>> getStaff({
    @Query('page') int? page,
    @Query('per_page') int? perPage,
    @Query('search') String? search,
    @Query('specialization') String? specialization,
    @Query('status') String? status,
    @Query('availability_status') String? availabilityStatus,
  });

  @GET('/staff/{id}')
  Future<ApiResponse<StaffModel>> getStaffMember(@Path('id') int id);

  @PUT('/staff/{id}/availability')
  Future<ApiResponse<StaffModel>> updateStaffAvailability(
    @Path('id') int id,
    @Body() Map<String, dynamic> availabilityData,
  );

  @GET('/staff/{id}/schedule')
  Future<ApiResponse<List<VisitServiceModel>>> getStaffSchedule(
    @Path('id') int id,
    @Query('start_date') String startDate,
    @Query('end_date') String endDate,
  );

  // Marketing APIs
  @GET('/marketing/campaigns')
  Future<ApiResponse<PaginationModel<dynamic>>> getMarketingCampaigns({
    @Query('page') int? page,
    @Query('per_page') int? perPage,
    @Query('status') String? status,
  });

  @GET('/marketing/leads')
  Future<ApiResponse<PaginationModel<dynamic>>> getMarketingLeads({
    @Query('page') int? page,
    @Query('per_page') int? perPage,
    @Query('status') String? status,
    @Query('source') String? source,
  });

  @POST('/marketing/leads')
  Future<ApiResponse<dynamic>> createLead(@Body() Map<String, dynamic> lead);

  @POST('/marketing/leads/{id}/convert')
  Future<ApiResponse<PatientModel>> convertLead(
    @Path('id') int id,
    @Body() Map<String, dynamic> conversionData,
  );

  // Inventory APIs
  @GET('/inventory/items')
  Future<ApiResponse<PaginationModel<dynamic>>> getInventoryItems({
    @Query('page') int? page,
    @Query('per_page') int? perPage,
    @Query('search') String? search,
    @Query('category') String? category,
    @Query('status') String? status,
    @Query('low_stock') bool? lowStock,
  });

  @POST('/inventory/items/{id}/adjust-stock')
  Future<ApiResponse<dynamic>> adjustInventoryStock(
    @Path('id') int id,
    @Body() Map<String, dynamic> adjustmentData,
  );

  @GET('/inventory/requests')
  Future<ApiResponse<PaginationModel<dynamic>>> getInventoryRequests({
    @Query('page') int? page,
    @Query('per_page') int? perPage,
    @Query('status') String? status,
  });

  @POST('/inventory/requests')
  Future<ApiResponse<dynamic>> createInventoryRequest(@Body() Map<String, dynamic> request);

  // Insurance APIs
  @GET('/insurance/policies')
  Future<ApiResponse<PaginationModel<dynamic>>> getInsurancePolicies({
    @Query('page') int? page,
    @Query('per_page') int? perPage,
    @Query('patient_id') int? patientId,
    @Query('company_id') int? companyId,
    @Query('status') String? status,
  });

  @GET('/insurance/claims')
  Future<ApiResponse<PaginationModel<dynamic>>> getInsuranceClaims({
    @Query('page') int? page,
    @Query('per_page') int? perPage,
    @Query('patient_id') int? patientId,
    @Query('policy_id') int? policyId,
    @Query('status') String? status,
  });

  @POST('/insurance/claims')
  Future<ApiResponse<dynamic>> createInsuranceClaim(@Body() Map<String, dynamic> claim);

  // Analytics APIs
  @GET('/analytics/dashboard')
  Future<ApiResponse<Map<String, dynamic>>> getDashboardAnalytics({
    @Query('period') String? period,
  });

  @GET('/analytics/patients')
  Future<ApiResponse<Map<String, dynamic>>> getPatientAnalytics({
    @Query('start_date') String? startDate,
    @Query('end_date') String? endDate,
  });

  @GET('/analytics/visits')
  Future<ApiResponse<Map<String, dynamic>>> getVisitAnalytics({
    @Query('start_date') String? startDate,
    @Query('end_date') String? endDate,
  });

  @GET('/analytics/revenue')
  Future<ApiResponse<Map<String, dynamic>>> getRevenueAnalytics({
    @Query('start_date') String? startDate,
    @Query('end_date') String? endDate,
  });

  @GET('/analytics/staff')
  Future<ApiResponse<Map<String, dynamic>>> getStaffAnalytics({
    @Query('start_date') String? startDate,
    @Query('end_date') String? endDate,
  });

  // Bulk Operations APIs
  @POST('/bulk/patients')
  Future<ApiResponse<Map<String, dynamic>>> bulkCreatePatients(@Body() Map<String, dynamic> data);

  @PUT('/bulk/patients')
  Future<ApiResponse<Map<String, dynamic>>> bulkUpdatePatients(@Body() Map<String, dynamic> data);

  @POST('/bulk/export')
  Future<ApiResponse<Map<String, dynamic>>> bulkExport(@Body() Map<String, dynamic> exportData);

  // Messaging APIs
  @GET('/messages/threads')
  Future<ApiResponse<PaginationModel<dynamic>>> getMessageThreads({
    @Query('page') int? page,
    @Query('per_page') int? perPage,
  });

  @GET('/messages/threads/{userId}')
  Future<ApiResponse<PaginationModel<dynamic>>> getMessageThread(
    @Path('userId') int userId,
    @Query('page') int? page,
    @Query('per_page') int? perPage,
  );

  @POST('/messages/threads/{userId}/messages')
  Future<ApiResponse<dynamic>> sendMessage(
    @Path('userId') int userId,
    @Body() Map<String, dynamic> message,
  );

  // Notification APIs
  @GET('/notifications')
  Future<ApiResponse<PaginationModel<dynamic>>> getNotifications({
    @Query('page') int? page,
    @Query('per_page') int? perPage,
    @Query('unread_only') bool? unreadOnly,
  });

  @POST('/notifications/{id}/read')
  Future<ApiResponse<void>> markNotificationAsRead(@Path('id') int id);

  @POST('/push-tokens')
  Future<ApiResponse<void>> registerPushToken(@Body() Map<String, dynamic> tokenData);

  // File Upload APIs
  @POST('/documents')
  @MultiPart()
  Future<ApiResponse<dynamic>> uploadDocument(
    @Part() File file,
    @Part() String type,
    @Part() String? description,
    @Part() int? patientId,
    @Part() int? visitServiceId,
  );

  @GET('/documents/{id}/download')
  Future<Response> downloadDocument(@Path('id') int id);
}
