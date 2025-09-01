import 'package:flutter/material.dart';

class UserDetailPage extends StatelessWidget {
  final String userId;
  
  const UserDetailPage({super.key, required this.userId});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('User $userId')),
      body: Center(child: Text('User Detail Page for ID: $userId - Coming Soon')),
    );
  }
}
