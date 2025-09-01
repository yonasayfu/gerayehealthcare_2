import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/router/app_router.dart';
import '../../../domain/entities/message.dart';
import '../../providers/auth_provider.dart';
import '../../widgets/common/app_empty_state.dart';
import '../../widgets/common/app_error_widget.dart';
import '../../widgets/common/app_loading.dart';
import '../../widgets/common/sync_status_widget.dart';
import '../../widgets/common/user_avatar.dart';

class ConversationsPage extends ConsumerStatefulWidget {
  const ConversationsPage({super.key});

  @override
  ConsumerState<ConversationsPage> createState() => _ConversationsPageState();
}

class _ConversationsPageState extends ConsumerState<ConversationsPage> {
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
      // Load more conversations
    }
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);
    final currentUser = authState.user;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Messages'),
        actions: [
          const SyncIndicator(),
          IconButton(
            icon: const Icon(Icons.search),
            onPressed: _showSearchDialog,
          ),
        ],
      ),
      body: Column(
        children: [
          // Search Bar
          if (_searchController.text.isNotEmpty)
            Container(
              padding: const EdgeInsets.all(16.0),
              child: TextField(
                controller: _searchController,
                decoration: InputDecoration(
                  hintText: 'Search conversations...',
                  prefixIcon: const Icon(Icons.search),
                  suffixIcon: IconButton(
                    icon: const Icon(Icons.clear),
                    onPressed: () {
                      _searchController.clear();
                      setState(() {});
                    },
                  ),
                  border: const OutlineInputBorder(),
                ),
                onChanged: (value) => setState(() {}),
              ),
            ),

          // Conversations List
          Expanded(
            child: _buildConversationsList(currentUser),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: _showNewConversationDialog,
        child: const Icon(Icons.chat),
      ),
    );
  }

  Widget _buildConversationsList(user) {
    // Mock conversations for now - replace with actual provider
    final mockConversations = _getMockConversations();

    if (mockConversations.isEmpty) {
      return const EmptyConversations();
    }

    return RefreshIndicator(
      onRefresh: () async {
        // Refresh conversations
      },
      child: ListView.builder(
        controller: _scrollController,
        itemCount: mockConversations.length,
        itemBuilder: (context, index) {
          final conversation = mockConversations[index];
          return _buildConversationTile(conversation, user);
        },
      ),
    );
  }

  Widget _buildConversationTile(Conversation conversation, user) {
    final otherParticipant = conversation.getOtherParticipant(user?.id ?? 0);
    final theme = Theme.of(context);

    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      child: ListTile(
        leading: Stack(
          children: [
            UserAvatar(
              user: otherParticipant,
              size: AvatarSize.medium,
            ),
            if (conversation.hasUnreadMessages)
              Positioned(
                right: 0,
                top: 0,
                child: Container(
                  width: 12,
                  height: 12,
                  decoration: BoxDecoration(
                    color: theme.colorScheme.primary,
                    shape: BoxShape.circle,
                    border: Border.all(color: Colors.white, width: 2),
                  ),
                ),
              ),
          ],
        ),
        title: Row(
          children: [
            Expanded(
              child: Text(
                conversation.displayTitle,
                style: TextStyle(
                  fontWeight: conversation.hasUnreadMessages 
                      ? FontWeight.bold 
                      : FontWeight.normal,
                ),
              ),
            ),
            if (conversation.isPinned)
              Icon(
                Icons.push_pin,
                size: 16,
                color: theme.colorScheme.primary,
              ),
            if (conversation.isMuted)
              Icon(
                Icons.volume_off,
                size: 16,
                color: Colors.grey[600],
              ),
          ],
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (conversation.lastMessage != null) ...[
              Text(
                _getLastMessagePreview(conversation.lastMessage!),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(
                  fontWeight: conversation.hasUnreadMessages 
                      ? FontWeight.w600 
                      : FontWeight.normal,
                ),
              ),
              const SizedBox(height: 4),
            ],
            Row(
              children: [
                Text(
                  _formatTime(conversation.lastActivityAt ?? conversation.updatedAt),
                  style: TextStyle(
                    fontSize: 12,
                    color: Colors.grey[600],
                  ),
                ),
                if (conversation.hasUnreadMessages) ...[
                  const Spacer(),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                    decoration: BoxDecoration(
                      color: theme.colorScheme.primary,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      '${conversation.unreadCount}',
                      style: TextStyle(
                        color: theme.colorScheme.onPrimary,
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ],
              ],
            ),
          ],
        ),
        trailing: PopupMenuButton(
          itemBuilder: (context) => [
            PopupMenuItem(
              value: 'pin',
              child: ListTile(
                leading: Icon(conversation.isPinned ? Icons.push_pin_outlined : Icons.push_pin),
                title: Text(conversation.isPinned ? 'Unpin' : 'Pin'),
                contentPadding: EdgeInsets.zero,
              ),
            ),
            PopupMenuItem(
              value: 'mute',
              child: ListTile(
                leading: Icon(conversation.isMuted ? Icons.volume_up : Icons.volume_off),
                title: Text(conversation.isMuted ? 'Unmute' : 'Mute'),
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
          onSelected: (value) => _handleConversationAction(value, conversation),
        ),
        onTap: () => AppNavigation.toChat(
          context, 
          conversation.id.toString(),
          userName: conversation.displayTitle,
        ),
      ),
    );
  }

  String _getLastMessagePreview(Message message) {
    switch (message.type) {
      case MessageType.text:
        return message.content;
      case MessageType.image:
        return '📷 Photo';
      case MessageType.file:
        return '📎 File';
      case MessageType.audio:
        return '🎵 Audio';
      case MessageType.video:
        return '🎥 Video';
      case MessageType.location:
        return '📍 Location';
      case MessageType.system:
        return message.content;
    }
  }

  String _formatTime(DateTime dateTime) {
    final now = DateTime.now();
    final difference = now.difference(dateTime);

    if (difference.inDays > 0) {
      return '${difference.inDays}d ago';
    } else if (difference.inHours > 0) {
      return '${difference.inHours}h ago';
    } else if (difference.inMinutes > 0) {
      return '${difference.inMinutes}m ago';
    } else {
      return 'Just now';
    }
  }

  void _handleConversationAction(String action, Conversation conversation) {
    switch (action) {
      case 'pin':
        // Toggle pin status
        break;
      case 'mute':
        // Toggle mute status
        break;
      case 'delete':
        _showDeleteConfirmation(conversation);
        break;
    }
  }

  void _showDeleteConfirmation(Conversation conversation) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Delete Conversation'),
        content: Text('Are you sure you want to delete this conversation with ${conversation.displayTitle}?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Cancel'),
          ),
          TextButton(
            onPressed: () {
              Navigator.of(context).pop();
              // Delete conversation
            },
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('Delete'),
          ),
        ],
      ),
    );
  }

  void _showSearchDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Search Conversations'),
        content: TextField(
          controller: _searchController,
          decoration: const InputDecoration(
            hintText: 'Enter search term...',
            border: OutlineInputBorder(),
          ),
          onChanged: (value) => setState(() {}),
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

  void _showNewConversationDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('New Conversation'),
        content: const Text('Select users to start a new conversation.'),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(),
            child: const Text('Cancel'),
          ),
          TextButton(
            onPressed: () {
              Navigator.of(context).pop();
              // Navigate to user selection
            },
            child: const Text('Select Users'),
          ),
        ],
      ),
    );
  }

  List<Conversation> _getMockConversations() {
    // Mock data - replace with actual data from provider
    return [
      Conversation(
        id: 1,
        title: null,
        isGroup: false,
        participants: [], // Add mock participants
        unreadCount: 3,
        createdAt: DateTime.now().subtract(const Duration(days: 1)),
        updatedAt: DateTime.now().subtract(const Duration(minutes: 30)),
        lastActivityAt: DateTime.now().subtract(const Duration(minutes: 30)),
        isPinned: true,
        lastMessage: Message(
          id: 1,
          conversationId: 1,
          senderId: 2,
          content: 'Hey, how are you doing?',
          type: MessageType.text,
          status: MessageStatus.delivered,
          createdAt: DateTime.now().subtract(const Duration(minutes: 30)),
          updatedAt: DateTime.now().subtract(const Duration(minutes: 30)),
        ),
      ),
      Conversation(
        id: 2,
        title: 'Project Team',
        isGroup: true,
        participants: [], // Add mock participants
        unreadCount: 0,
        createdAt: DateTime.now().subtract(const Duration(days: 2)),
        updatedAt: DateTime.now().subtract(const Duration(hours: 2)),
        lastActivityAt: DateTime.now().subtract(const Duration(hours: 2)),
        isMuted: true,
        lastMessage: Message(
          id: 2,
          conversationId: 2,
          senderId: 3,
          content: 'Meeting at 3 PM',
          type: MessageType.text,
          status: MessageStatus.read,
          createdAt: DateTime.now().subtract(const Duration(hours: 2)),
          updatedAt: DateTime.now().subtract(const Duration(hours: 2)),
        ),
      ),
    ];
  }
}
