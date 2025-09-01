import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/router/app_router.dart';
import '../../providers/auth_provider.dart';

class HomePage extends ConsumerWidget {
  const HomePage({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Home'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () => ref.read(authProvider.notifier).logout(),
          ),
        ],
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            const Text(
              'Welcome to Laravel Boilerplate Mobile!',
              style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 32),
            _buildNavigationCard(
              context,
              'Users',
              'Manage users and staff',
              Icons.people,
              () => AppNavigation.toUsers(context),
            ),
            const SizedBox(height: 16),
            _buildNavigationCard(
              context,
              'Messages',
              'Chat and messaging',
              Icons.message,
              () => AppNavigation.toMessages(context),
            ),
            const SizedBox(height: 16),
            _buildNavigationCard(
              context,
              'Notifications',
              'View notifications',
              Icons.notifications,
              () => AppNavigation.toNotifications(context),
            ),
            const SizedBox(height: 16),
            _buildNavigationCard(
              context,
              'Settings',
              'App settings',
              Icons.settings,
              () => AppNavigation.toSettings(context),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildNavigationCard(
    BuildContext context,
    String title,
    String subtitle,
    IconData icon,
    VoidCallback onTap,
  ) {
    return Card(
      child: ListTile(
        leading: Icon(icon, size: 32),
        title: Text(title),
        subtitle: Text(subtitle),
        trailing: const Icon(Icons.arrow_forward_ios),
        onTap: onTap,
      ),
    );
  }
}
