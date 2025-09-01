import 'package:json_annotation/json_annotation.dart';

import '../../domain/entities/message.dart';
import 'user_model.dart';

part 'message_model.g.dart';

@JsonSerializable()
class MessageModel extends Message {
  @JsonKey(fromJson: _userFromJson, toJson: _userToJson)
  final UserModel? senderModel;

  @JsonKey(fromJson: _attachmentsFromJson, toJson: _attachmentsToJson)
  final List<MessageAttachmentModel> attachmentModels;

  @JsonKey(fromJson: _replyToFromJson, toJson: _replyToToJson)
  final MessageModel? replyToModel;

  const MessageModel({
    required super.id,
    required super.conversationId,
    required super.senderId,
    this.senderModel,
    required super.content,
    required super.type,
    required super.status,
    required super.createdAt,
    required super.updatedAt,
    super.readAt,
    this.attachmentModels = const [],
    this.replyToModel,
    super.isEdited = false,
    super.editedAt,
  }) : super(
          sender: senderModel,
          attachments: attachmentModels,
          replyTo: replyToModel,
        );

  factory MessageModel.fromJson(Map<String, dynamic> json) => _$MessageModelFromJson(json);
  Map<String, dynamic> toJson() => _$MessageModelToJson(this);

  static UserModel? _userFromJson(Map<String, dynamic>? json) {
    return json != null ? UserModel.fromJson(json) : null;
  }

  static Map<String, dynamic>? _userToJson(UserModel? user) {
    return user?.toJson();
  }

  static List<MessageAttachmentModel> _attachmentsFromJson(List<dynamic>? json) {
    return json?.map((e) => MessageAttachmentModel.fromJson(e)).toList() ?? [];
  }

  static List<Map<String, dynamic>> _attachmentsToJson(List<MessageAttachmentModel> attachments) {
    return attachments.map((e) => e.toJson()).toList();
  }

  static MessageModel? _replyToFromJson(Map<String, dynamic>? json) {
    return json != null ? MessageModel.fromJson(json) : null;
  }

  static Map<String, dynamic>? _replyToToJson(MessageModel? message) {
    return message?.toJson();
  }

  factory MessageModel.fromEntity(Message message) {
    return MessageModel(
      id: message.id,
      conversationId: message.conversationId,
      senderId: message.senderId,
      senderModel: message.sender != null ? UserModel.fromEntity(message.sender!) : null,
      content: message.content,
      type: message.type,
      status: message.status,
      createdAt: message.createdAt,
      updatedAt: message.updatedAt,
      readAt: message.readAt,
      attachmentModels: message.attachments
          .map((a) => MessageAttachmentModel.fromEntity(a))
          .toList(),
      replyToModel: message.replyTo != null ? MessageModel.fromEntity(message.replyTo!) : null,
      isEdited: message.isEdited,
      editedAt: message.editedAt,
    );
  }
}

@JsonSerializable()
class MessageAttachmentModel extends MessageAttachment {
  const MessageAttachmentModel({
    required super.id,
    required super.messageId,
    required super.fileName,
    required super.filePath,
    super.fileUrl,
    required super.mimeType,
    required super.fileSize,
    super.thumbnailUrl,
    required super.createdAt,
  });

  factory MessageAttachmentModel.fromJson(Map<String, dynamic> json) => 
      _$MessageAttachmentModelFromJson(json);
  Map<String, dynamic> toJson() => _$MessageAttachmentModelToJson(this);

  factory MessageAttachmentModel.fromEntity(MessageAttachment attachment) {
    return MessageAttachmentModel(
      id: attachment.id,
      messageId: attachment.messageId,
      fileName: attachment.fileName,
      filePath: attachment.filePath,
      fileUrl: attachment.fileUrl,
      mimeType: attachment.mimeType,
      fileSize: attachment.fileSize,
      thumbnailUrl: attachment.thumbnailUrl,
      createdAt: attachment.createdAt,
    );
  }
}

@JsonSerializable()
class ConversationModel extends Conversation {
  @JsonKey(fromJson: _participantsFromJson, toJson: _participantsToJson)
  final List<UserModel> participantModels;

  @JsonKey(fromJson: _lastMessageFromJson, toJson: _lastMessageToJson)
  final MessageModel? lastMessageModel;

  const ConversationModel({
    required super.id,
    super.title,
    required super.isGroup,
    required this.participantModels,
    this.lastMessageModel,
    required super.unreadCount,
    required super.createdAt,
    required super.updatedAt,
    super.lastActivityAt,
    super.isMuted = false,
    super.isPinned = false,
  }) : super(
          participants: participantModels,
          lastMessage: lastMessageModel,
        );

  factory ConversationModel.fromJson(Map<String, dynamic> json) => 
      _$ConversationModelFromJson(json);
  Map<String, dynamic> toJson() => _$ConversationModelToJson(this);

  static List<UserModel> _participantsFromJson(List<dynamic> json) {
    return json.map((e) => UserModel.fromJson(e)).toList();
  }

  static List<Map<String, dynamic>> _participantsToJson(List<UserModel> participants) {
    return participants.map((e) => e.toJson()).toList();
  }

  static MessageModel? _lastMessageFromJson(Map<String, dynamic>? json) {
    return json != null ? MessageModel.fromJson(json) : null;
  }

  static Map<String, dynamic>? _lastMessageToJson(MessageModel? message) {
    return message?.toJson();
  }

  factory ConversationModel.fromEntity(Conversation conversation) {
    return ConversationModel(
      id: conversation.id,
      title: conversation.title,
      isGroup: conversation.isGroup,
      participantModels: conversation.participants
          .map((p) => UserModel.fromEntity(p))
          .toList(),
      lastMessageModel: conversation.lastMessage != null 
          ? MessageModel.fromEntity(conversation.lastMessage!) 
          : null,
      unreadCount: conversation.unreadCount,
      createdAt: conversation.createdAt,
      updatedAt: conversation.updatedAt,
      lastActivityAt: conversation.lastActivityAt,
      isMuted: conversation.isMuted,
      isPinned: conversation.isPinned,
    );
  }
}

// Request/Response models
@JsonSerializable()
class SendMessageRequest {
  final int conversationId;
  final String content;
  final MessageType type;
  final int? replyToId;
  final List<String>? attachmentPaths;

  const SendMessageRequest({
    required this.conversationId,
    required this.content,
    this.type = MessageType.text,
    this.replyToId,
    this.attachmentPaths,
  });

  factory SendMessageRequest.fromJson(Map<String, dynamic> json) => 
      _$SendMessageRequestFromJson(json);
  Map<String, dynamic> toJson() => _$SendMessageRequestToJson(this);
}

@JsonSerializable()
class CreateConversationRequest {
  final List<int> participantIds;
  final String? title;
  final bool isGroup;

  const CreateConversationRequest({
    required this.participantIds,
    this.title,
    this.isGroup = false,
  });

  factory CreateConversationRequest.fromJson(Map<String, dynamic> json) => 
      _$CreateConversationRequestFromJson(json);
  Map<String, dynamic> toJson() => _$CreateConversationRequestToJson(this);
}
