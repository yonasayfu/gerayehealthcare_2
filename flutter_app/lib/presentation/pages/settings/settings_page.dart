import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/constants/app_constants.dart';
import '../../../domain/entities/app_settings.dart';
import '../../providers/auth_provider.dart';
import '../../providers/settings_provider.dart';
import '../../widgets/common/app_loading.dart';

class SettingsPage extends ConsumerWidget {
  const SettingsPage({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final settingsState = ref.watch(settingsProvider);
    final authState = ref.watch(authProvider);

    if (settingsState.isLoading) {
      return const Scaffold(
        body: Center(child: AppLoading.circular()),
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text('Settings'),
        actions: [
          PopupMenuButton(
            itemBuilder: (context) => [
              const PopupMenuItem(
                value: 'export',
                child: ListTile(
                  leading: Icon(Icons.download),
                  title: Text('Export Settings'),
                  contentPadding: EdgeInsets.zero,
                ),
              ),
              const PopupMenuItem(
                value: 'import',
                child: ListTile(
                  leading: Icon(Icons.upload),
                  title: Text('Import Settings'),
                  contentPadding: EdgeInsets.zero,
                ),
              ),
              const PopupMenuItem(
                value: 'reset',
                child: ListTile(
                  leading: Icon(Icons.restore, color: Colors.red),
                  title: Text('Reset Settings', style: TextStyle(color: Colors.red)),
                  contentPadding: EdgeInsets.zero,
                ),
              ),
            ],
            onSelected: (value) => _handleMenuAction(context, ref, value),
          ),
        ],
      ),
      body: ListView(
        children: [
          // User Profile Section
          if (authState.user != null) ...[
            _buildUserProfileSection(context, authState.user!),
            const Divider(),
          ],

          // Appearance Section
          _buildSectionHeader(context, 'Appearance'),
          _buildThemeModeTile(context, ref, settingsState.settings),
          _buildLanguageTile(context, ref, settingsState.settings),
          _buildFontSizeTile(context, ref, settingsState.settings),
          const Divider(),

          // Notifications Section
          _buildSectionHeader(context, 'Notifications'),
          _buildNotificationsTile(context, ref, settingsState.settings),
          _buildPushNotificationsTile(context, ref, settingsState.settings),
          _buildEmailNotificationsTile(context, ref, settingsState.settings),
          _buildNotificationFrequencyTile(context, ref, settingsState.settings),
          const Divider(),

          // Sound & Vibration Section
          _buildSectionHeader(context, 'Sound & Vibration'),
          _buildSoundTile(context, ref, settingsState.settings),
          _buildVibrationTile(context, ref, settingsState.settings),
          const Divider(),

          // Security Section
          _buildSectionHeader(context, 'Security'),
          _buildBiometricTile(context, ref, settingsState.settings),
          _buildAutoLockTile(context, ref, settingsState.settings),
          if (settingsState.settings.isAutoLockEnabled)
            _buildAutoLockDurationTile(context, ref, settingsState.settings),
          const Divider(),

          // Privacy Section
          _buildSectionHeader(context, 'Privacy'),
          _buildLocationTile(context, ref, settingsState.settings),
          _buildAnalyticsTile(context, ref, settingsState.settings),
          _buildCrashReportingTile(context, ref, settingsState.settings),
          const Divider(),

          // Data & Storage Section
          _buildSectionHeader(context, 'Data & Storage'),
          _buildDataSaverTile(context, ref, settingsState.settings),
          _buildAutoDownloadTile(context, ref, settingsState.settings),
          const Divider(),

          // About Section
          _buildSectionHeader(context, 'About'),
          _buildAboutTile(context),
          _buildVersionTile(context),
          const Divider(),

          // Account Section
          _buildSectionHeader(context, 'Account'),
          _buildLogoutTile(context, ref),

          const SizedBox(height: 32),
        ],
      ),
    );
  }

  Widget _buildUserProfileSection(BuildContext context, user) {
    return Container(
      padding: const EdgeInsets.all(16),
      child: Row(
        children: [
          CircleAvatar(
            radius: 30,
            backgroundColor: Theme.of(context).primaryColor,
            child: Text(
              user.initials,
              style: const TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  user.name,
                  style: Theme.of(context).textTheme.titleLarge?.copyWith(
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  user.email,
                  style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    color: Colors.grey[600],
                  ),
                ),
              ],
            ),
          ),
          IconButton(
            icon: const Icon(Icons.edit),
            onPressed: () {
              // TODO: Navigate to profile edit page
            },
          ),
        ],
      ),
    );
  }

  Widget _buildSectionHeader(BuildContext context, String title) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
      child: Text(
        title,
        style: Theme.of(context).textTheme.titleMedium?.copyWith(
          color: Theme.of(context).primaryColor,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }

  Widget _buildThemeModeTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return ListTile(
      leading: const Icon(Icons.palette_outlined),
      title: const Text('Theme'),
      subtitle: Text(_getThemeModeDisplayName(settings.themeMode)),
      onTap: () => _showThemeModeDialog(context, ref, settings.themeMode),
    );
  }

  Widget _buildLanguageTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return ListTile(
      leading: const Icon(Icons.language_outlined),
      title: const Text('Language'),
      subtitle: Text(settings.language.displayName),
      onTap: () => _showLanguageDialog(context, ref, settings.language),
    );
  }

  Widget _buildFontSizeTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return ListTile(
      leading: const Icon(Icons.text_fields_outlined),
      title: const Text('Font Size'),
      subtitle: Text(_getFontSizeDisplayName(settings.fontSize)),
      onTap: () => _showFontSizeDialog(context, ref, settings.fontSize),
    );
  }

  Widget _buildNotificationsTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return SwitchListTile(
      secondary: const Icon(Icons.notifications_outlined),
      title: const Text('Notifications'),
      subtitle: const Text('Enable all notifications'),
      value: settings.isNotificationsEnabled,
      onChanged: (value) => ref.read(settingsProvider.notifier).updateNotificationSettings(
        isNotificationsEnabled: value,
      ),
    );
  }

  Widget _buildPushNotificationsTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return SwitchListTile(
      secondary: const Icon(Icons.push_pin_outlined),
      title: const Text('Push Notifications'),
      subtitle: const Text('Receive push notifications'),
      value: settings.isPushNotificationsEnabled,
      onChanged: settings.isNotificationsEnabled ? (value) =>
        ref.read(settingsProvider.notifier).updateNotificationSettings(
          isPushNotificationsEnabled: value,
        ) : null,
    );
  }

  Widget _buildEmailNotificationsTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return SwitchListTile(
      secondary: const Icon(Icons.email_outlined),
      title: const Text('Email Notifications'),
      subtitle: const Text('Receive email notifications'),
      value: settings.isEmailNotificationsEnabled,
      onChanged: settings.isNotificationsEnabled ? (value) =>
        ref.read(settingsProvider.notifier).updateNotificationSettings(
          isEmailNotificationsEnabled: value,
        ) : null,
    );
  }

  Widget _buildNotificationFrequencyTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return ListTile(
      leading: const Icon(Icons.schedule_outlined),
      title: const Text('Notification Frequency'),
      subtitle: Text(_getNotificationFrequencyDisplayName(settings.notificationFrequency)),
      enabled: settings.isNotificationsEnabled,
      onTap: settings.isNotificationsEnabled ? () =>
        _showNotificationFrequencyDialog(context, ref, settings.notificationFrequency) : null,
    );
  }

  Widget _buildSoundTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return SwitchListTile(
      secondary: const Icon(Icons.volume_up_outlined),
      title: const Text('Sound'),
      subtitle: const Text('Play notification sounds'),
      value: settings.isSoundEnabled,
      onChanged: (value) => ref.read(settingsProvider.notifier).updateNotificationSettings(),
    );
  }

  Widget _buildVibrationTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return SwitchListTile(
      secondary: const Icon(Icons.vibration_outlined),
      title: const Text('Vibration'),
      subtitle: const Text('Vibrate for notifications'),
      value: settings.isVibrationEnabled,
      onChanged: (value) => ref.read(settingsProvider.notifier).updateNotificationSettings(),
    );
  }

  Widget _buildBiometricTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return SwitchListTile(
      secondary: const Icon(Icons.fingerprint_outlined),
      title: const Text('Biometric Authentication'),
      subtitle: const Text('Use fingerprint or face unlock'),
      value: settings.isBiometricEnabled,
      onChanged: (value) => ref.read(settingsProvider.notifier).updateSecuritySettings(
        isBiometricEnabled: value,
      ),
    );
  }

  Widget _buildAutoLockTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return SwitchListTile(
      secondary: const Icon(Icons.lock_clock_outlined),
      title: const Text('Auto Lock'),
      subtitle: const Text('Automatically lock the app'),
      value: settings.isAutoLockEnabled,
      onChanged: (value) => ref.read(settingsProvider.notifier).updateSecuritySettings(
        isAutoLockEnabled: value,
      ),
    );
  }

  Widget _buildAutoLockDurationTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return ListTile(
      leading: const Icon(Icons.timer_outlined),
      title: const Text('Auto Lock Duration'),
      subtitle: Text('${settings.autoLockDuration} minutes'),
      onTap: () => _showAutoLockDurationDialog(context, ref, settings.autoLockDuration),
    );
  }

  Widget _buildLocationTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return SwitchListTile(
      secondary: const Icon(Icons.location_on_outlined),
      title: const Text('Location Services'),
      subtitle: const Text('Allow location access'),
      value: settings.isLocationEnabled,
      onChanged: (value) => ref.read(settingsProvider.notifier).updatePrivacySettings(
        isLocationEnabled: value,
      ),
    );
  }

  Widget _buildAnalyticsTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return SwitchListTile(
      secondary: const Icon(Icons.analytics_outlined),
      title: const Text('Analytics'),
      subtitle: const Text('Help improve the app'),
      value: settings.isAnalyticsEnabled,
      onChanged: (value) => ref.read(settingsProvider.notifier).updatePrivacySettings(
        isAnalyticsEnabled: value,
      ),
    );
  }

  Widget _buildCrashReportingTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return SwitchListTile(
      secondary: const Icon(Icons.bug_report_outlined),
      title: const Text('Crash Reporting'),
      subtitle: const Text('Send crash reports'),
      value: settings.isCrashReportingEnabled,
      onChanged: (value) => ref.read(settingsProvider.notifier).updatePrivacySettings(
        isCrashReportingEnabled: value,
      ),
    );
  }

  Widget _buildDataSaverTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return SwitchListTile(
      secondary: const Icon(Icons.data_saver_on_outlined),
      title: const Text('Data Saver'),
      subtitle: const Text('Reduce data usage'),
      value: settings.isDataSaverEnabled,
      onChanged: (value) => ref.read(settingsProvider.notifier).updateDataSettings(
        isDataSaverEnabled: value,
      ),
    );
  }

  Widget _buildAutoDownloadTile(BuildContext context, WidgetRef ref, AppSettings settings) {
    return SwitchListTile(
      secondary: const Icon(Icons.download_outlined),
      title: const Text('Auto Download'),
      subtitle: const Text('Automatically download content'),
      value: settings.isAutoDownloadEnabled,
      onChanged: (value) => ref.read(settingsProvider.notifier).updateDataSettings(
        isAutoDownloadEnabled: value,
      ),
    );
  }

  Widget _buildAboutTile(BuildContext context) {
    return ListTile(
      leading: const Icon(Icons.info_outlined),
      title: const Text('About'),
      subtitle: const Text('App information and licenses'),
      onTap: () {
        showAboutDialog(
          context: context,
          applicationName: AppConstants.appName,
          applicationVersion: AppConstants.appVersion,
          applicationLegalese: '© 2024 ${AppConstants.appName}',
        );
      },
    );
  }

  Widget _buildVersionTile(BuildContext context) {
    return ListTile(
      leading: const Icon(Icons.info_outlined),
      title: const Text('Version'),
      subtitle: Text('${AppConstants.appVersion} (${AppConstants.buildNumber})'),
    );
  }

  Widget _buildLogoutTile(BuildContext context, WidgetRef ref) {
    return ListTile(
      leading: const Icon(Icons.logout, color: Colors.red),
      title: const Text('Logout', style: TextStyle(color: Colors.red)),
      onTap: () => _showLogoutDialog(context, ref),
    );
  }

  // Helper methods for display names
  String _getThemeModeDisplayName(AppThemeMode mode) {
    switch (mode) {
      case AppThemeMode.system:
        return 'System';
      case AppThemeMode.light:
        return 'Light';
      case AppThemeMode.dark:
        return 'Dark';
    }
  }

  String _getFontSizeDisplayName(FontSize size) {
    switch (size) {
      case FontSize.small:
        return 'Small';
      case FontSize.medium:
        return 'Medium';
      case FontSize.large:
        return 'Large';
      case FontSize.extraLarge:
        return 'Extra Large';
    }
  }

  String _getNotificationFrequencyDisplayName(NotificationFrequency frequency) {
    switch (frequency) {
      case NotificationFrequency.immediately:
        return 'Immediately';
      case NotificationFrequency.every15Minutes:
        return 'Every 15 minutes';
      case NotificationFrequency.hourly:
        return 'Hourly';
      case NotificationFrequency.daily:
        return 'Daily';
      case NotificationFrequency.never:
        return 'Never';
    }
  }

  // Dialog methods
  void _showThemeModeDialog(BuildContext context, WidgetRef ref, AppThemeMode currentMode) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Choose Theme'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: AppThemeMode.values.map((mode) {
            return RadioListTile<AppThemeMode>(
              title: Text(_getThemeModeDisplayName(mode)),
              value: mode,
              groupValue: currentMode,
              onChanged: (value) {
                if (value != null) {
                  ref.read(settingsProvider.notifier).updateThemeMode(value);
                  Navigator.of(context).pop();
                }
              },
            );
          }).toList(),
        ),
      ),
    );
  }

  void _showLanguageDialog(BuildContext context, WidgetRef ref, AppLanguage currentLanguage) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Choose Language'),
        content: SizedBox(
          width: double.maxFinite,
          child: ListView(
            shrinkWrap: true,
            children: AppLanguage.values.map((language) {
              return RadioListTile<AppLanguage>(
                title: Text(language.displayName),
                value: language,
                groupValue: currentLanguage,
                onChanged: (value) {
                  if (value != null) {
                    ref.read(settingsProvider.notifier).updateLanguage(value);
                    Navigator.of(context).pop();
                  }
                },
              );
            }).toList(),
          ),
        ),
      ),
    );
  }

  void _showFontSizeDialog(BuildContext context, WidgetRef ref, FontSize currentSize) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Choose Font Size'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: FontSize.values.map((size) {
            return RadioListTile<FontSize>(
              title: Text(_getFontSizeDisplayName(size)),
              value: size,
              groupValue: currentSize,
              onChanged: (value) {
                if (value != null) {
                  ref.read(settingsProvider.notifier).updateFontSize(value);
                  Navigator.of(context).pop();
                }
              },
            );
          }).toList(),
        ),
      ),
    );
  }

  void _showNotificationFrequencyDialog(BuildContext context, WidgetRef ref, NotificationFrequency currentFrequency) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Notification Frequency'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: NotificationFrequency.values.map((frequency) {
            return RadioListTile<NotificationFrequency>(
              title: Text(_getNotificationFrequencyDisplayName(frequency)),
              value: frequency,
              groupValue: currentFrequency,
              onChanged: (value) {
                if (value != null) {
                  ref.read(settingsProvider.notifier).updateNotificationSettings(frequency: value);
                  Navigator.of(context).pop();
                }
              },
            );
          }).toList(),
        ),
      ),
    );
  }

  void _showAutoLockDurationDialog(BuildContext context, WidgetRef ref, int currentDuration) {
    final durations = [1, 2, 5, 10, 15, 30, 60];

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Auto Lock Duration'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: durations.map((duration) {
            return RadioListTile<int>(
              title: Text('$duration minute${duration == 1 ? '' : 's'}'),
              value: duration,
              groupValue: currentDuration,
              onChanged: (value) {
                if (value != null) {
                  ref.read(settingsProvider.notifier).updateSecuritySettings(autoLockDuration: value);
                  Navigator.of(context).pop();
                }
              },
            );
          }).toList(),
        ),
      ),
    );
  }

  void _showLogoutDialog(BuildContext context, WidgetRef ref) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Logout'),
        content: const Text('Are you sure you want to logout?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Cancel'),
          ),
          TextButton(
            onPressed: () {
              Navigator.of(context).pop();
              ref.read(authProvider.notifier).logout();
            },
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('Logout'),
          ),
        ],
      ),
    );
  }

  void _handleMenuAction(BuildContext context, WidgetRef ref, String action) {
    switch (action) {
      case 'export':
        ref.read(settingsProvider.notifier).exportSettings();
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Settings exported successfully')),
        );
        break;
      case 'import':
        // TODO: Implement file picker for importing settings
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Import feature coming soon')),
        );
        break;
      case 'reset':
        _showResetSettingsDialog(context, ref);
        break;
    }
  }

  void _showResetSettingsDialog(BuildContext context, WidgetRef ref) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Reset Settings'),
        content: const Text('Are you sure you want to reset all settings to default? This action cannot be undone.'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Cancel'),
          ),
          TextButton(
            onPressed: () {
              Navigator.of(context).pop();
              ref.read(settingsProvider.notifier).resetSettings();
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Settings reset to default')),
              );
            },
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('Reset'),
          ),
        ],
      ),
    );
  }
}
