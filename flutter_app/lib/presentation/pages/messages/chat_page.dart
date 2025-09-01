import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../domain/entities/message.dart';
import '../../providers/auth_provider.dart';
import '../../widgets/common/app_loading.dart';
import '../../widgets/common/sync_status_widget.dart';
import '../../widgets/common/user_avatar.dart';

class ChatPage extends ConsumerStatefulWidget {
  final String userId;
  final String? userName;

  const ChatPage({
    super.key,
    required this.userId,
    this.userName,
  });

  @override
  ConsumerState<ChatPage> createState() => _ChatPageState();
}

class _ChatPageState extends ConsumerState<ChatPage> {
  final _messageController = TextEditingController();
  final _scrollController = ScrollController();
  final _focusNode = FocusNode();
  bool _isTyping = false;

  @override
  void initState() {
    super.initState();
    _scrollController.addListener(_onScroll);
    _messageController.addListener(_onMessageChanged);
  }

  @override
  void dispose() {
    _messageController.dispose();
    _scrollController.dispose();
    _focusNode.dispose();
    super.dispose();
  }

  void _onScroll() {
    if (_scrollController.position.pixels >=
        _scrollController.position.maxScrollExtent - 200) {
      // Load more messages
    }
  }

  void _onMessageChanged() {
    final isTyping = _messageController.text.isNotEmpty;
    if (isTyping != _isTyping) {
      setState(() {
        _isTyping = isTyping;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);
    final currentUser = authState.user;

    return Scaffold(
      appBar: AppBar(
        title: Row(
          children: [
            UserAvatar.small(name: widget.userName ?? 'User ${widget.userId}'),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    widget.userName ?? 'User ${widget.userId}',
                    style: const TextStyle(fontSize: 16),
                  ),
                  Text(
                    'Online', // Replace with actual status
                    style: TextStyle(
                      fontSize: 12,
                      color: Colors.green[600],
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
        actions: [
          const SyncIndicator(),
          IconButton(
            icon: const Icon(Icons.videocam),
            onPressed: _startVideoCall,
          ),
          IconButton(
            icon: const Icon(Icons.call),
            onPressed: _startVoiceCall,
          ),
          PopupMenuButton(
            itemBuilder: (context) => [
              const PopupMenuItem(
                value: 'view_profile',
                child: ListTile(
                  leading: Icon(Icons.person),
                  title: Text('View Profile'),
                  contentPadding: EdgeInsets.zero,
                ),
              ),
              const PopupMenuItem(
                value: 'mute',
                child: ListTile(
                  leading: Icon(Icons.volume_off),
                  title: Text('Mute'),
                  contentPadding: EdgeInsets.zero,
                ),
              ),
              const PopupMenuItem(
                value: 'clear_chat',
                child: ListTile(
                  leading: Icon(Icons.clear_all),
                  title: Text('Clear Chat'),
                  contentPadding: EdgeInsets.zero,
                ),
              ),
            ],
            onSelected: _handleMenuAction,
          ),
        ],
      ),
      body: Column(
        children: [
          // Messages List
          Expanded(
            child: _buildMessagesList(currentUser),
          ),

          // Message Input
          _buildMessageInput(),
        ],
      ),
    );
  }

  Widget _buildMessagesList(user) {
    final messages = _getMockMessages();
    if (messages.isEmpty) {
      return const Center(child: Text('No messages yet. Start the conversation!'));
    }
    return ListView.builder(
      controller: _scrollController,
      reverse: true,
      padding: const EdgeInsets.all(16),
      itemCount: messages.length,
      itemBuilder: (context, index) {
        final message = messages[index];
        final isMe = message.senderId == 1; // Assume current user ID is 1
        return _buildMessageBubble(message, isMe);
      },
    );
  }

  Widget _buildMessageBubble(Message message, bool isMe) {
    final theme = Theme.of(context);
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: isMe ? MainAxisAlignment.end : MainAxisAlignment.start,
        children: [
          Flexible(
            child: Container(
              constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.75),
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              decoration: BoxDecoration(
                color: isMe ? theme.colorScheme.primary : theme.colorScheme.surfaceVariant,
                borderRadius: BorderRadius.circular(20).copyWith(
                  bottomLeft: isMe ? const Radius.circular(20) : const Radius.circular(4),
                  bottomRight: isMe ? const Radius.circular(4) : const Radius.circular(20),
                ),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(message.content, style: TextStyle(
                    color: isMe ? theme.colorScheme.onPrimary : theme.colorScheme.onSurfaceVariant,
                  )),
                  const SizedBox(height: 4),
                  Text(_formatMessageTime(message.createdAt), style: TextStyle(
                    fontSize: 11,
                    color: isMe
                        ? theme.colorScheme.onPrimary.withOpacity(0.7)
                        : theme.colorScheme.onSurfaceVariant.withOpacity(0.7),
                  )),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMessageInput() {
    final theme = Theme.of(context);
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: theme.colorScheme.surface,
        border: Border(top: BorderSide(color: theme.dividerColor)),
      ),
      child: Row(
        children: [
          IconButton(icon: const Icon(Icons.attach_file), onPressed: _showAttachmentOptions),
          Expanded(
            child: TextField(
              controller: _messageController,
              focusNode: _focusNode,
              decoration: InputDecoration(
                hintText: 'Type a message...',
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(25), borderSide: BorderSide.none),
                filled: true,
                fillColor: theme.colorScheme.surfaceVariant,
                contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
              ),
              maxLines: null,
              textCapitalization: TextCapitalization.sentences,
              onSubmitted: (_) => _sendMessage(),
            ),
          ),
          const SizedBox(width: 8),
          FloatingActionButton.small(
            onPressed: _isTyping ? _sendMessage : _recordVoiceMessage,
            child: Icon(_isTyping ? Icons.send : Icons.mic),
          ),
        ],
      ),
    );
  }

  String _formatMessageTime(DateTime dateTime) {
    return '${dateTime.hour.toString().padLeft(2, '0')}:${dateTime.minute.toString().padLeft(2, '0')}';
  }

  void _sendMessage() {
    final text = _messageController.text.trim();
    if (text.isEmpty) return;
    print('Sending message: $text');
    _messageController.clear();
    _focusNode.requestFocus();
  }

  void _recordVoiceMessage() {
    ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Voice recording coming soon')));
  }

  void _showAttachmentOptions() {
    showModalBottomSheet(
      context: context,
      builder: (context) => Container(
        padding: const EdgeInsets.all(20),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            ListTile(leading: const Icon(Icons.photo_camera), title: const Text('Camera'), onTap: () => Navigator.pop(context)),
            ListTile(leading: const Icon(Icons.photo_library), title: const Text('Gallery'), onTap: () => Navigator.pop(context)),
          ],
        ),
      ),
    );
  }

  void _startVideoCall() => ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Video call coming soon')));
  void _startVoiceCall() => ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Voice call coming soon')));
  void _handleMenuAction(String action) => ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$action coming soon')));

  List<Message> _getMockMessages() {
    return [
      Message(id: 1, conversationId: 1, senderId: 2, content: 'Hey there! How are you doing?', type: MessageType.text, status: MessageStatus.read, createdAt: DateTime.now().subtract(const Duration(hours: 2)), updatedAt: DateTime.now().subtract(const Duration(hours: 2))),
      Message(id: 2, conversationId: 1, senderId: 1, content: 'I\'m doing great! Thanks for asking.', type: MessageType.text, status: MessageStatus.delivered, createdAt: DateTime.now().subtract(const Duration(hours: 1)), updatedAt: DateTime.now().subtract(const Duration(hours: 1))),
    ];
  }
}
