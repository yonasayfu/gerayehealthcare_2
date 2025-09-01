import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/router/app_router.dart';
import '../../providers/users_provider.dart';
import '../../widgets/common/app_empty_state.dart';
import '../../widgets/common/app_error_widget.dart';
import '../../widgets/common/app_loading.dart';
import '../../widgets/common/user_avatar.dart';

class UsersPage extends ConsumerStatefulWidget {
  const UsersPage({super.key});

  @override
  ConsumerState<UsersPage> createState() => _UsersPageState();
}

class _UsersPageState extends ConsumerState<UsersPage> {
  final _searchController = TextEditingController();
  final _scrollController = ScrollController();

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(_onScroll);
  }

  @override
  void dispose() {
    _searchController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  void _onScroll() {
    if (_scrollController.position.pixels >=
        _scrollController.position.maxScrollExtent - 200) {
      ref.read(usersProvider.notifier).loadMoreUsers();
    }
  }

  @override
  Widget build(BuildContext context) {
    final usersState = ref.watch(usersProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Users'),
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: _showFilterDialog,
          ),
        ],
      ),
      body: Column(
        children: [
          // Search Bar
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: TextField(
              controller: _searchController,
              decoration: InputDecoration(
                hintText: 'Search users...',
                prefixIcon: const Icon(Icons.search),
                suffixIcon: _searchController.text.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear),
                        onPressed: () {
                          _searchController.clear();
                          ref.read(usersProvider.notifier).searchUsers('');
                        },
                      )
                    : null,
                border: const OutlineInputBorder(),
              ),
              onChanged: (value) {
                ref.read(usersProvider.notifier).searchUsers(value);
              },
            ),
          ),

          // Active Filters
          if (usersState.selectedRole != null || usersState.selectedDepartment != null)
            Container(
              height: 50,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: ListView(
                scrollDirection: Axis.horizontal,
                children: [
                  if (usersState.selectedRole != null)
                    Padding(
                      padding: const EdgeInsets.only(right: 8),
                      child: Chip(
                        label: Text('Role: ${usersState.selectedRole}'),
                        onDeleted: () => ref.read(usersProvider.notifier).filterByRole(null),
                      ),
                    ),
                  if (usersState.selectedDepartment != null)
                    Padding(
                      padding: const EdgeInsets.only(right: 8),
                      child: Chip(
                        label: Text('Dept: ${usersState.selectedDepartment}'),
                        onDeleted: () => ref.read(usersProvider.notifier).filterByDepartment(null),
                      ),
                    ),
                  TextButton.icon(
                    onPressed: () => ref.read(usersProvider.notifier).clearFilters(),
                    icon: const Icon(Icons.clear_all),
                    label: const Text('Clear All'),
                  ),
                ],
              ),
            ),

          // Users List
          Expanded(
            child: _buildUsersList(usersState),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          // TODO: Navigate to add user page
        },
        child: const Icon(Icons.person_add),
      ),
    );
  }

  Widget _buildUsersList(UsersState state) {
    if (state.isLoading && state.users.isEmpty) {
      return const Center(child: AppLoading.circular());
    }

    if (state.error != null && state.users.isEmpty) {
      return AppErrorWidget(
        message: state.error!,
        onRetry: () => ref.read(usersProvider.notifier).loadUsers(refresh: true),
      );
    }

    if (state.users.isEmpty) {
      return const EmptyUsers();
    }

    return RefreshIndicator(
      onRefresh: () => ref.read(usersProvider.notifier).loadUsers(refresh: true),
      child: ListView.builder(
        controller: _scrollController,
        itemCount: state.users.length + (state.isLoadingMore ? 1 : 0),
        itemBuilder: (context, index) {
          if (index >= state.users.length) {
            return const Padding(
              padding: EdgeInsets.all(16.0),
              child: Center(child: AppLoading.circular()),
            );
          }

          final user = state.users[index];
          return _buildUserTile(user);
        },
      ),
    );
  }

  Widget _buildUserTile(user) {
    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      child: ListTile(
        leading: UserAvatar(user: user),
        title: Text(
          user.name,
          style: const TextStyle(fontWeight: FontWeight.w600),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(user.email),
            if (user.isStaff) ...[
              const SizedBox(height: 4),
              Row(
                children: [
                  Icon(
                    Icons.work_outline,
                    size: 16,
                    color: Colors.grey[600],
                  ),
                  const SizedBox(width: 4),
                  Text(
                    user.staff?.displayPosition ?? 'Staff',
                    style: TextStyle(
                      fontSize: 12,
                      color: Colors.grey[600],
                    ),
                  ),
                  if (user.staff?.department != null) ...[
                    const SizedBox(width: 8),
                    Text(
                      '• ${user.staff!.displayDepartment}',
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.grey[600],
                      ),
                    ),
                  ],
                ],
              ),
            ],
          ],
        ),
        trailing: PopupMenuButton(
          itemBuilder: (context) => [
            const PopupMenuItem(
              value: 'view',
              child: ListTile(
                leading: Icon(Icons.visibility),
                title: Text('View Profile'),
                contentPadding: EdgeInsets.zero,
              ),
            ),
            const PopupMenuItem(
              value: 'edit',
              child: ListTile(
                leading: Icon(Icons.edit),
                title: Text('Edit'),
                contentPadding: EdgeInsets.zero,
              ),
            ),
            const PopupMenuItem(
              value: 'message',
              child: ListTile(
                leading: Icon(Icons.message),
                title: Text('Send Message'),
                contentPadding: EdgeInsets.zero,
              ),
            ),
            const PopupMenuItem(
              value: 'delete',
              child: ListTile(
                leading: Icon(Icons.delete, color: Colors.red),
                title: Text('Delete', style: TextStyle(color: Colors.red)),
                contentPadding: EdgeInsets.zero,
              ),
            ),
          ],
          onSelected: (value) => _handleUserAction(value, user),
        ),
        onTap: () => AppNavigation.toUserDetail(context, user.id.toString()),
      ),
    );
  }

  void _handleUserAction(String action, user) {
    switch (action) {
      case 'view':
        AppNavigation.toUserDetail(context, user.id.toString());
        break;
      case 'edit':
        // TODO: Navigate to edit user page
        break;
      case 'message':
        AppNavigation.toChat(context, user.id.toString(), userName: user.name);
        break;
      case 'delete':
        _showDeleteConfirmation(user);
        break;
    }
  }

  void _showDeleteConfirmation(user) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Delete User'),
        content: Text('Are you sure you want to delete ${user.name}?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Cancel'),
          ),
          TextButton(
            onPressed: () {
              Navigator.of(context).pop();
              ref.read(usersProvider.notifier).deleteUser(user.id);
            },
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('Delete'),
          ),
        ],
      ),
    );
  }

  void _showFilterDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Filter Users'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Role filter
            DropdownButtonFormField<String>(
              decoration: const InputDecoration(labelText: 'Role'),
              value: ref.read(usersProvider).selectedRole,
              items: const [
                DropdownMenuItem(value: null, child: Text('All Roles')),
                DropdownMenuItem(value: 'admin', child: Text('Admin')),
                DropdownMenuItem(value: 'staff', child: Text('Staff')),
                DropdownMenuItem(value: 'user', child: Text('User')),
              ],
              onChanged: (value) => ref.read(usersProvider.notifier).filterByRole(value),
            ),
            const SizedBox(height: 16),
            // Department filter
            DropdownButtonFormField<String>(
              decoration: const InputDecoration(labelText: 'Department'),
              value: ref.read(usersProvider).selectedDepartment,
              items: const [
                DropdownMenuItem(value: null, child: Text('All Departments')),
                DropdownMenuItem(value: 'engineering', child: Text('Engineering')),
                DropdownMenuItem(value: 'marketing', child: Text('Marketing')),
                DropdownMenuItem(value: 'sales', child: Text('Sales')),
                DropdownMenuItem(value: 'hr', child: Text('Human Resources')),
              ],
              onChanged: (value) => ref.read(usersProvider.notifier).filterByDepartment(value),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Close'),
          ),
        ],
      ),
    );
  }
}
