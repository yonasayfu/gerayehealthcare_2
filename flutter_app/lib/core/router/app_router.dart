import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../presentation/pages/splash/splash_page.dart';
import '../../presentation/pages/auth/login_page.dart';
import '../../presentation/pages/auth/register_page.dart';
import '../../presentation/pages/auth/forgot_password_page.dart';
import '../../presentation/pages/home/home_page.dart';
import '../../presentation/pages/profile/profile_page.dart';
import '../../presentation/pages/settings/settings_page.dart';

// Healthcare-specific imports
import '../../presentation/pages/dashboard/dashboard_page.dart';
import '../../presentation/pages/patients/patients_page.dart';
import '../../presentation/pages/patients/patient_detail_page.dart';
import '../../presentation/pages/patients/patient_form_page.dart';
import '../../presentation/pages/visits/visits_page.dart';
import '../../presentation/pages/visits/visit_detail_page.dart';
import '../../presentation/pages/visits/visit_form_page.dart';
import '../../presentation/pages/visits/visit_check_in_page.dart';
import '../../presentation/pages/visits/visit_check_out_page.dart';
import '../../presentation/pages/staff/staff_page.dart';
import '../../presentation/pages/staff/staff_detail_page.dart';
import '../../presentation/pages/staff/staff_schedule_page.dart';
import '../../presentation/pages/messages/conversations_page.dart';
import '../../presentation/pages/messages/chat_page.dart';
import '../../presentation/pages/notifications/notifications_page.dart';
import '../../presentation/pages/analytics/analytics_page.dart';
import '../../presentation/pages/inventory/inventory_page.dart';
import '../../presentation/pages/insurance/insurance_page.dart';
import '../../presentation/pages/marketing/marketing_page.dart';
import '../../presentation/pages/emergency/emergency_page.dart';
import '../../presentation/providers/auth_provider.dart';

// Route names for Healthcare App
class AppRoutes {
  // Authentication & Core
  static const String splash = '/';
  static const String login = '/login';
  static const String register = '/register';
  static const String forgotPassword = '/forgot-password';
  static const String home = '/home';
  static const String profile = '/profile';
  static const String settings = '/settings';

  // Healthcare Core Features
  static const String dashboard = '/dashboard';
  static const String patients = '/patients';
  static const String patientDetail = '/patients/:id';
  static const String patientCreate = '/patients/create';
  static const String patientEdit = '/patients/:id/edit';

  // Visit Management
  static const String visits = '/visits';
  static const String visitDetail = '/visits/:id';
  static const String visitCreate = '/visits/create';
  static const String visitEdit = '/visits/:id/edit';
  static const String visitCheckIn = '/visits/:id/check-in';
  static const String visitCheckOut = '/visits/:id/check-out';

  // Staff Management
  static const String staff = '/staff';
  static const String staffDetail = '/staff/:id';
  static const String staffSchedule = '/staff/:id/schedule';
  static const String mySchedule = '/my-schedule';

  // Messaging & Communication
  static const String messages = '/messages';
  static const String chat = '/messages/chat/:userId';
  static const String notifications = '/notifications';

  // Healthcare Analytics
  static const String analytics = '/analytics';
  static const String reports = '/reports';

  // Inventory Management
  static const String inventory = '/inventory';
  static const String inventoryDetail = '/inventory/:id';
  static const String inventoryRequests = '/inventory/requests';

  // Insurance & Billing
  static const String insurance = '/insurance';
  static const String insurancePolicies = '/insurance/policies';
  static const String insuranceClaims = '/insurance/claims';
  static const String billing = '/billing';

  // Marketing & Leads
  static const String marketing = '/marketing';
  static const String leads = '/leads';
  static const String leadDetail = '/leads/:id';
  static const String campaigns = '/campaigns';

  // Emergency & Quick Actions
  static const String emergency = '/emergency';
  static const String quickActions = '/quick-actions';

  // Documents & Files
  static const String documents = '/documents';
  static const String documentViewer = '/documents/:id/view';
}

// Router provider
final routerProvider = Provider<GoRouter>((ref) {
  final authState = ref.watch(authProvider);
  
  return GoRouter(
    initialLocation: AppRoutes.splash,
    debugLogDiagnostics: true,
    redirect: (context, state) {
      final isAuthenticated = authState.isAuthenticated;
      final isLoading = authState.isLoading;
      
      // Show splash while loading
      if (isLoading) {
        return AppRoutes.splash;
      }
      
      // Define public routes (accessible without authentication)
      final publicRoutes = [
        AppRoutes.splash,
        AppRoutes.login,
        AppRoutes.register,
        AppRoutes.forgotPassword,
      ];
      
      final isPublicRoute = publicRoutes.contains(state.matchedLocation);
      
      // Redirect to login if not authenticated and trying to access private route
      if (!isAuthenticated && !isPublicRoute) {
        return AppRoutes.login;
      }
      
      // Redirect to home if authenticated and trying to access auth routes
      if (isAuthenticated && (state.matchedLocation == AppRoutes.login || 
                             state.matchedLocation == AppRoutes.register)) {
        return AppRoutes.home;
      }
      
      // No redirect needed
      return null;
    },
    routes: [
      // Splash route
      GoRoute(
        path: AppRoutes.splash,
        name: 'splash',
        builder: (context, state) => const SplashPage(),
      ),
      
      // Authentication routes
      GoRoute(
        path: AppRoutes.login,
        name: 'login',
        builder: (context, state) => const LoginPage(),
      ),
      GoRoute(
        path: AppRoutes.register,
        name: 'register',
        builder: (context, state) => const RegisterPage(),
      ),
      GoRoute(
        path: AppRoutes.forgotPassword,
        name: 'forgot-password',
        builder: (context, state) => const ForgotPasswordPage(),
      ),
      
      // Main app routes
      GoRoute(
        path: AppRoutes.home,
        name: 'home',
        builder: (context, state) => const HomePage(),
      ),

      // Dashboard route
      GoRoute(
        path: AppRoutes.dashboard,
        name: 'dashboard',
        builder: (context, state) => const DashboardPage(),
      ),

      // Profile routes
      GoRoute(
        path: AppRoutes.profile,
        name: 'profile',
        builder: (context, state) => const ProfilePage(),
      ),

      // Patient routes
      GoRoute(
        path: AppRoutes.patients,
        name: 'patients',
        builder: (context, state) => const PatientsPage(),
        routes: [
          GoRoute(
            path: 'create',
            name: 'patient-create',
            builder: (context, state) => const PatientFormPage(),
          ),
          GoRoute(
            path: ':id',
            name: 'patient-detail',
            builder: (context, state) {
              final patientId = state.pathParameters['id']!;
              return PatientDetailPage(patientId: patientId);
            },
            routes: [
              GoRoute(
                path: 'edit',
                name: 'patient-edit',
                builder: (context, state) {
                  final patientId = state.pathParameters['id']!;
                  return PatientFormPage(patientId: patientId);
                },
              ),
            ],
          ),
        ],
      ),

      // Visit routes
      GoRoute(
        path: AppRoutes.visits,
        name: 'visits',
        builder: (context, state) => const VisitsPage(),
        routes: [
          GoRoute(
            path: 'create',
            name: 'visit-create',
            builder: (context, state) => const VisitFormPage(),
          ),
          GoRoute(
            path: ':id',
            name: 'visit-detail',
            builder: (context, state) {
              final visitId = state.pathParameters['id']!;
              return VisitDetailPage(visitId: visitId);
            },
            routes: [
              GoRoute(
                path: 'edit',
                name: 'visit-edit',
                builder: (context, state) {
                  final visitId = state.pathParameters['id']!;
                  return VisitFormPage(visitId: visitId);
                },
              ),
              GoRoute(
                path: 'check-in',
                name: 'visit-check-in',
                builder: (context, state) {
                  final visitId = state.pathParameters['id']!;
                  return VisitCheckInPage(visitId: visitId);
                },
              ),
              GoRoute(
                path: 'check-out',
                name: 'visit-check-out',
                builder: (context, state) {
                  final visitId = state.pathParameters['id']!;
                  return VisitCheckOutPage(visitId: visitId);
                },
              ),
            ],
          ),
        ],
      ),

      // Staff routes
      GoRoute(
        path: AppRoutes.staff,
        name: 'staff',
        builder: (context, state) => const StaffPage(),
        routes: [
          GoRoute(
            path: ':id',
            name: 'staff-detail',
            builder: (context, state) {
              final staffId = state.pathParameters['id']!;
              return StaffDetailPage(staffId: staffId);
            },
            routes: [
              GoRoute(
                path: 'schedule',
                name: 'staff-schedule',
                builder: (context, state) {
                  final staffId = state.pathParameters['id']!;
                  return StaffSchedulePage(staffId: staffId);
                },
              ),
            ],
          ),
        ],
      ),

      // My Schedule route
      GoRoute(
        path: AppRoutes.mySchedule,
        name: 'my-schedule',
        builder: (context, state) => const StaffSchedulePage(),
      ),
      
      // Messages routes
      GoRoute(
        path: AppRoutes.messages,
        name: 'messages',
        builder: (context, state) => const ConversationsPage(),
        routes: [
          GoRoute(
            path: 'chat/:userId',
            name: 'chat',
            builder: (context, state) {
              final userId = state.pathParameters['userId']!;
              final userName = state.uri.queryParameters['userName'];
              return ChatPage(
                userId: userId,
                userName: userName,
              );
            },
          ),
        ],
      ),

      // Notifications route
      GoRoute(
        path: AppRoutes.notifications,
        name: 'notifications',
        builder: (context, state) => const NotificationsPage(),
      ),

      // Analytics route
      GoRoute(
        path: AppRoutes.analytics,
        name: 'analytics',
        builder: (context, state) => const AnalyticsPage(),
      ),

      // Inventory route
      GoRoute(
        path: AppRoutes.inventory,
        name: 'inventory',
        builder: (context, state) => const InventoryPage(),
      ),

      // Insurance route
      GoRoute(
        path: AppRoutes.insurance,
        name: 'insurance',
        builder: (context, state) => const InsurancePage(),
      ),

      // Marketing route
      GoRoute(
        path: AppRoutes.marketing,
        name: 'marketing',
        builder: (context, state) => const MarketingPage(),
      ),

      // Emergency route
      GoRoute(
        path: AppRoutes.emergency,
        name: 'emergency',
        builder: (context, state) => const EmergencyPage(),
      ),

      // Settings route
      GoRoute(
        path: AppRoutes.settings,
        name: 'settings',
        builder: (context, state) => const SettingsPage(),
      ),
    ],
    
    // Error page
    errorBuilder: (context, state) => Scaffold(
      appBar: AppBar(title: const Text('Error')),
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(
              Icons.error_outline,
              size: 64,
              color: Colors.red,
            ),
            const SizedBox(height: 16),
            Text(
              'Page not found',
              style: Theme.of(context).textTheme.headlineSmall,
            ),
            const SizedBox(height: 8),
            Text(
              'The page "${state.matchedLocation}" could not be found.',
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 16),
            ElevatedButton(
              onPressed: () => context.go(AppRoutes.home),
              child: const Text('Go Home'),
            ),
          ],
        ),
      ),
    ),
  );
});

// Navigation helper extension
extension GoRouterExtension on GoRouter {
  void pushAndClearStack(String location) {
    while (canPop()) {
      pop();
    }
    pushReplacement(location);
  }
}

// Healthcare Navigation helper methods
class AppNavigation {
  // Authentication & Core
  static void toLogin(BuildContext context) {
    context.go(AppRoutes.login);
  }

  static void toHome(BuildContext context) {
    context.go(AppRoutes.home);
  }

  static void toDashboard(BuildContext context) {
    context.go(AppRoutes.dashboard);
  }

  static void toProfile(BuildContext context) {
    context.push(AppRoutes.profile);
  }

  // Patient Navigation
  static void toPatients(BuildContext context) {
    context.push(AppRoutes.patients);
  }

  static void toPatientDetail(BuildContext context, String patientId) {
    context.push('/patients/$patientId');
  }

  static void toPatientCreate(BuildContext context) {
    context.push('/patients/create');
  }

  static void toPatientEdit(BuildContext context, String patientId) {
    context.push('/patients/$patientId/edit');
  }

  // Visit Navigation
  static void toVisits(BuildContext context) {
    context.push(AppRoutes.visits);
  }

  static void toVisitDetail(BuildContext context, String visitId) {
    context.push('/visits/$visitId');
  }

  static void toVisitCreate(BuildContext context, {String? patientId}) {
    final uri = Uri(
      path: '/visits/create',
      queryParameters: patientId != null ? {'patient_id': patientId} : null,
    );
    context.push(uri.toString());
  }

  static void toVisitCheckIn(BuildContext context, String visitId) {
    context.push('/visits/$visitId/check-in');
  }

  static void toVisitCheckOut(BuildContext context, String visitId) {
    context.push('/visits/$visitId/check-out');
  }

  // Staff Navigation
  static void toStaff(BuildContext context) {
    context.push(AppRoutes.staff);
  }

  static void toStaffDetail(BuildContext context, String staffId) {
    context.push('/staff/$staffId');
  }

  static void toStaffSchedule(BuildContext context, String staffId) {
    context.push('/staff/$staffId/schedule');
  }

  static void toMySchedule(BuildContext context) {
    context.push(AppRoutes.mySchedule);
  }

  // Communication
  static void toMessages(BuildContext context) {
    context.push(AppRoutes.messages);
  }

  static void toChat(BuildContext context, String userId, {String? userName}) {
    final uri = Uri(
      path: '/messages/chat/$userId',
      queryParameters: userName != null ? {'userName': userName} : null,
    );
    context.push(uri.toString());
  }

  static void toNotifications(BuildContext context) {
    context.push(AppRoutes.notifications);
  }

  // Healthcare Features
  static void toAnalytics(BuildContext context) {
    context.push(AppRoutes.analytics);
  }

  static void toInventory(BuildContext context) {
    context.push(AppRoutes.inventory);
  }

  static void toInsurance(BuildContext context) {
    context.push(AppRoutes.insurance);
  }

  static void toMarketing(BuildContext context) {
    context.push(AppRoutes.marketing);
  }

  static void toEmergency(BuildContext context) {
    context.push(AppRoutes.emergency);
  }

  static void toSettings(BuildContext context) {
    context.push(AppRoutes.settings);
  }

  // Utility
  static void back(BuildContext context) {
    if (context.canPop()) {
      context.pop();
    } else {
      context.go(AppRoutes.home);
    }
  }

  static void backToDashboard(BuildContext context) {
    context.go(AppRoutes.dashboard);
  }

  static void clearStackAndGo(BuildContext context, String route) {
    while (context.canPop()) {
      context.pop();
    }
    context.pushReplacement(route);
  }
}
