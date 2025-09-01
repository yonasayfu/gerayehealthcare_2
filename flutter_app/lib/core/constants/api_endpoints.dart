/// API endpoint constants
class ApiEndpoints {
  // Base
  static const String baseUrl = 'http://localhost:8000/api/v1';
  
  // Authentication
  static const String login = '/auth/login';
  static const String register = '/auth/register';
  static const String logout = '/auth/logout';
  static const String refresh = '/auth/refresh';
  static const String forgotPassword = '/auth/forgot-password';
  static const String resetPassword = '/auth/reset-password';
  static const String user = '/auth/user';
  static const String tokens = '/auth/tokens';
  static const String revokeToken = '/auth/tokens/{token}';
  
  // User Management
  static const String users = '/users';
  static const String userById = '/users/{id}';
  static const String userPermissions = '/users/{id}/permissions';
  static const String assignRole = '/users/{id}/assign-role';
  static const String removeRole = '/users/{id}/remove-role';
  
  // Staff Management
  static const String staff = '/staff';
  static const String staffById = '/staff/{id}';
  static const String staffDropdown = '/staff/dropdown';
  static const String staffSearch = '/staff/search';
  static const String staffAvailable = '/staff/available';
  static const String staffStats = '/staff/stats';
  static const String staffDepartments = '/staff/departments';
  static const String staffPositions = '/staff/positions';
  
  // Messaging
  static const String messageThreads = '/messages/threads';
  static const String messageThread = '/messages/threads/{user}';
  static const String sendMessage = '/messages/threads/{user}/messages';
  static const String markMessageRead = '/messages/{message}/read';
  static const String markMessageUnread = '/messages/{message}/unread';
  static const String deleteMessage = '/messages/{message}';
  static const String unreadCount = '/messages/unread/count';
  static const String reactToMessage = '/messages/{message}/react';
  static const String removeReaction = '/messages/{message}/react';
  static const String typing = '/messages/typing';
  static const String typingStatus = '/messages/typing/{user}';
  static const String exportMessages = '/messages/export/{user}/csv';
  
  // Groups
  static const String groups = '/groups';
  static const String groupById = '/groups/{id}';
  static const String addGroupMember = '/groups/{id}/members';
  static const String removeGroupMember = '/groups/{id}/members/{user}';
  static const String leaveGroup = '/groups/{id}/leave';
  
  // Notifications
  static const String notifications = '/notifications';
  static const String notificationById = '/notifications/{id}';
  static const String markNotificationRead = '/notifications/{id}/read';
  static const String markAllNotificationsRead = '/notifications/mark-all-read';
  static const String deleteNotification = '/notifications/{id}';
  static const String deleteAllReadNotifications = '/notifications/delete-all-read';
  static const String notificationPreferences = '/notifications/preferences';
  static const String updateNotificationPreferences = '/notifications/preferences';
  static const String unreadNotificationCount = '/notifications/unread/count';
  static const String notificationStats = '/notifications/stats';
  
  // Global Search
  static const String globalSearch = '/search';
  static const String searchUsers = '/search/users';
  static const String searchStaff = '/search/staff';
  static const String searchMessages = '/search/messages';
  
  // File Upload
  static const String uploadFile = '/files/upload';
  static const String downloadFile = '/files/download/{id}';
  static const String deleteFile = '/files/{id}';
  
  // Roles & Permissions
  static const String roles = '/roles';
  static const String roleById = '/roles/{id}';
  static const String permissions = '/permissions';
  static const String permissionById = '/permissions/{id}';
  
  // Dashboard & Analytics
  static const String dashboardStats = '/dashboard/stats';
  static const String userStats = '/dashboard/user-stats';
  static const String messageStats = '/dashboard/message-stats';
  static const String notificationStats = '/dashboard/notification-stats';
  
  // System
  static const String systemInfo = '/system/info';
  static const String healthCheck = '/system/health';
  static const String version = '/system/version';
  
  // Helper methods to build URLs with parameters
  static String buildUrl(String endpoint, Map<String, dynamic>? params) {
    String url = baseUrl + endpoint;
    
    if (params != null) {
      params.forEach((key, value) {
        url = url.replaceAll('{$key}', value.toString());
      });
    }
    
    return url;
  }
  
  static String buildUrlWithQuery(String endpoint, Map<String, dynamic>? params, Map<String, dynamic>? queryParams) {
    String url = buildUrl(endpoint, params);
    
    if (queryParams != null && queryParams.isNotEmpty) {
      final query = queryParams.entries
          .where((entry) => entry.value != null)
          .map((entry) => '${entry.key}=${Uri.encodeComponent(entry.value.toString())}')
          .join('&');
      
      if (query.isNotEmpty) {
        url += '?$query';
      }
    }
    
    return url;
  }
}
