import 'package:equatable/equatable.dart';

import 'user.dart';

enum MessageType {
  text,
  image,
  file,
  audio,
  video,
  location,
  system,
}

enum MessageStatus {
  sending,
  sent,
  delivered,
  read,
  failed,
}

class Message extends Equatable {
  final int id;
  final int conversationId;
  final int senderId;
  final User? sender;
  final String content;
  final MessageType type;
  final MessageStatus status;
  final DateTime createdAt;
  final DateTime updatedAt;
  final DateTime? readAt;
  final List<MessageAttachment> attachments;
  final Message? replyTo;
  final bool isEdited;
  final DateTime? editedAt;

  const Message({
    required this.id,
    required this.conversationId,
    required this.senderId,
    this.sender,
    required this.content,
    required this.type,
    required this.status,
    required this.createdAt,
    required this.updatedAt,
    this.readAt,
    this.attachments = const [],
    this.replyTo,
    this.isEdited = false,
    this.editedAt,
  });

  @override
  List<Object?> get props => [
        id,
        conversationId,
        senderId,
        sender,
        content,
        type,
        status,
        createdAt,
        updatedAt,
        readAt,
        attachments,
        replyTo,
        isEdited,
        editedAt,
      ];

  bool get isRead => readAt != null;
  bool get isSent => status == MessageStatus.sent || status == MessageStatus.delivered || status == MessageStatus.read;
  bool get hasAttachments => attachments.isNotEmpty;
  bool get isSystemMessage => type == MessageType.system;

  Message copyWith({
    int? id,
    int? conversationId,
    int? senderId,
    User? sender,
    String? content,
    MessageType? type,
    MessageStatus? status,
    DateTime? createdAt,
    DateTime? updatedAt,
    DateTime? readAt,
    List<MessageAttachment>? attachments,
    Message? replyTo,
    bool? isEdited,
    DateTime? editedAt,
  }) {
    return Message(
      id: id ?? this.id,
      conversationId: conversationId ?? this.conversationId,
      senderId: senderId ?? this.senderId,
      sender: sender ?? this.sender,
      content: content ?? this.content,
      type: type ?? this.type,
      status: status ?? this.status,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      readAt: readAt ?? this.readAt,
      attachments: attachments ?? this.attachments,
      replyTo: replyTo ?? this.replyTo,
      isEdited: isEdited ?? this.isEdited,
      editedAt: editedAt ?? this.editedAt,
    );
  }
}

class MessageAttachment extends Equatable {
  final int id;
  final int messageId;
  final String fileName;
  final String filePath;
  final String? fileUrl;
  final String mimeType;
  final int fileSize;
  final String? thumbnailUrl;
  final DateTime createdAt;

  const MessageAttachment({
    required this.id,
    required this.messageId,
    required this.fileName,
    required this.filePath,
    this.fileUrl,
    required this.mimeType,
    required this.fileSize,
    this.thumbnailUrl,
    required this.createdAt,
  });

  @override
  List<Object?> get props => [
        id,
        messageId,
        fileName,
        filePath,
        fileUrl,
        mimeType,
        fileSize,
        thumbnailUrl,
        createdAt,
      ];

  bool get isImage => mimeType.startsWith('image/');
  bool get isVideo => mimeType.startsWith('video/');
  bool get isAudio => mimeType.startsWith('audio/');
  bool get isDocument => !isImage && !isVideo && !isAudio;

  String get formattedFileSize {
    if (fileSize < 1024) {
      return '$fileSize B';
    } else if (fileSize < 1024 * 1024) {
      return '${(fileSize / 1024).toStringAsFixed(1)} KB';
    } else if (fileSize < 1024 * 1024 * 1024) {
      return '${(fileSize / (1024 * 1024)).toStringAsFixed(1)} MB';
    } else {
      return '${(fileSize / (1024 * 1024 * 1024)).toStringAsFixed(1)} GB';
    }
  }
}

class Conversation extends Equatable {
  final int id;
  final String? title;
  final bool isGroup;
  final List<User> participants;
  final Message? lastMessage;
  final int unreadCount;
  final DateTime createdAt;
  final DateTime updatedAt;
  final DateTime? lastActivityAt;
  final bool isMuted;
  final bool isPinned;

  const Conversation({
    required this.id,
    this.title,
    required this.isGroup,
    required this.participants,
    this.lastMessage,
    required this.unreadCount,
    required this.createdAt,
    required this.updatedAt,
    this.lastActivityAt,
    this.isMuted = false,
    this.isPinned = false,
  });

  @override
  List<Object?> get props => [
        id,
        title,
        isGroup,
        participants,
        lastMessage,
        unreadCount,
        createdAt,
        updatedAt,
        lastActivityAt,
        isMuted,
        isPinned,
      ];

  String get displayTitle {
    if (title != null && title!.isNotEmpty) {
      return title!;
    }
    
    if (isGroup) {
      return participants.map((p) => p.name).join(', ');
    }
    
    return participants.isNotEmpty ? participants.first.name : 'Unknown';
  }

  bool get hasUnreadMessages => unreadCount > 0;

  User? getOtherParticipant(int currentUserId) {
    if (isGroup) return null;
    return participants.firstWhere(
      (p) => p.id != currentUserId,
      orElse: () => participants.first,
    );
  }

  Conversation copyWith({
    int? id,
    String? title,
    bool? isGroup,
    List<User>? participants,
    Message? lastMessage,
    int? unreadCount,
    DateTime? createdAt,
    DateTime? updatedAt,
    DateTime? lastActivityAt,
    bool? isMuted,
    bool? isPinned,
  }) {
    return Conversation(
      id: id ?? this.id,
      title: title ?? this.title,
      isGroup: isGroup ?? this.isGroup,
      participants: participants ?? this.participants,
      lastMessage: lastMessage ?? this.lastMessage,
      unreadCount: unreadCount ?? this.unreadCount,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      lastActivityAt: lastActivityAt ?? this.lastActivityAt,
      isMuted: isMuted ?? this.isMuted,
      isPinned: isPinned ?? this.isPinned,
    );
  }
}
