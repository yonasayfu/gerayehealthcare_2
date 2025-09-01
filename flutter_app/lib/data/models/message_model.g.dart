// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'message_model.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

MessageModel _$MessageModelFromJson(Map<String, dynamic> json) => MessageModel(
      id: (json['id'] as num).toInt(),
      conversationId: (json['conversationId'] as num).toInt(),
      senderId: (json['senderId'] as num).toInt(),
      senderModel: MessageModel._userFromJson(
          json['senderModel'] as Map<String, dynamic>?),
      content: json['content'] as String,
      type: $enumDecode(_$MessageTypeEnumMap, json['type']),
      status: $enumDecode(_$MessageStatusEnumMap, json['status']),
      createdAt: DateTime.parse(json['createdAt'] as String),
      updatedAt: DateTime.parse(json['updatedAt'] as String),
      readAt: json['readAt'] == null
          ? null
          : DateTime.parse(json['readAt'] as String),
      attachmentModels: json['attachmentModels'] == null
          ? const []
          : MessageModel._attachmentsFromJson(
              json['attachmentModels'] as List?),
      replyToModel: MessageModel._replyToFromJson(
          json['replyToModel'] as Map<String, dynamic>?),
      isEdited: json['isEdited'] as bool? ?? false,
      editedAt: json['editedAt'] == null
          ? null
          : DateTime.parse(json['editedAt'] as String),
    );

Map<String, dynamic> _$MessageModelToJson(MessageModel instance) =>
    <String, dynamic>{
      'id': instance.id,
      'conversationId': instance.conversationId,
      'senderId': instance.senderId,
      'content': instance.content,
      'type': _$MessageTypeEnumMap[instance.type]!,
      'status': _$MessageStatusEnumMap[instance.status]!,
      'createdAt': instance.createdAt.toIso8601String(),
      'updatedAt': instance.updatedAt.toIso8601String(),
      'readAt': instance.readAt?.toIso8601String(),
      'isEdited': instance.isEdited,
      'editedAt': instance.editedAt?.toIso8601String(),
      'senderModel': MessageModel._userToJson(instance.senderModel),
      'attachmentModels':
          MessageModel._attachmentsToJson(instance.attachmentModels),
      'replyToModel': MessageModel._replyToToJson(instance.replyToModel),
    };

const _$MessageTypeEnumMap = {
  MessageType.text: 'text',
  MessageType.image: 'image',
  MessageType.file: 'file',
  MessageType.audio: 'audio',
  MessageType.video: 'video',
  MessageType.location: 'location',
  MessageType.system: 'system',
};

const _$MessageStatusEnumMap = {
  MessageStatus.sending: 'sending',
  MessageStatus.sent: 'sent',
  MessageStatus.delivered: 'delivered',
  MessageStatus.read: 'read',
  MessageStatus.failed: 'failed',
};

MessageAttachmentModel _$MessageAttachmentModelFromJson(
        Map<String, dynamic> json) =>
    MessageAttachmentModel(
      id: (json['id'] as num).toInt(),
      messageId: (json['messageId'] as num).toInt(),
      fileName: json['fileName'] as String,
      filePath: json['filePath'] as String,
      fileUrl: json['fileUrl'] as String?,
      mimeType: json['mimeType'] as String,
      fileSize: (json['fileSize'] as num).toInt(),
      thumbnailUrl: json['thumbnailUrl'] as String?,
      createdAt: DateTime.parse(json['createdAt'] as String),
    );

Map<String, dynamic> _$MessageAttachmentModelToJson(
        MessageAttachmentModel instance) =>
    <String, dynamic>{
      'id': instance.id,
      'messageId': instance.messageId,
      'fileName': instance.fileName,
      'filePath': instance.filePath,
      'fileUrl': instance.fileUrl,
      'mimeType': instance.mimeType,
      'fileSize': instance.fileSize,
      'thumbnailUrl': instance.thumbnailUrl,
      'createdAt': instance.createdAt.toIso8601String(),
    };

ConversationModel _$ConversationModelFromJson(Map<String, dynamic> json) =>
    ConversationModel(
      id: (json['id'] as num).toInt(),
      title: json['title'] as String?,
      isGroup: json['isGroup'] as bool,
      participantModels: ConversationModel._participantsFromJson(
          json['participantModels'] as List),
      lastMessageModel: ConversationModel._lastMessageFromJson(
          json['lastMessageModel'] as Map<String, dynamic>?),
      unreadCount: (json['unreadCount'] as num).toInt(),
      createdAt: DateTime.parse(json['createdAt'] as String),
      updatedAt: DateTime.parse(json['updatedAt'] as String),
      lastActivityAt: json['lastActivityAt'] == null
          ? null
          : DateTime.parse(json['lastActivityAt'] as String),
      isMuted: json['isMuted'] as bool? ?? false,
      isPinned: json['isPinned'] as bool? ?? false,
    );

Map<String, dynamic> _$ConversationModelToJson(ConversationModel instance) =>
    <String, dynamic>{
      'id': instance.id,
      'title': instance.title,
      'isGroup': instance.isGroup,
      'unreadCount': instance.unreadCount,
      'createdAt': instance.createdAt.toIso8601String(),
      'updatedAt': instance.updatedAt.toIso8601String(),
      'lastActivityAt': instance.lastActivityAt?.toIso8601String(),
      'isMuted': instance.isMuted,
      'isPinned': instance.isPinned,
      'participantModels':
          ConversationModel._participantsToJson(instance.participantModels),
      'lastMessageModel':
          ConversationModel._lastMessageToJson(instance.lastMessageModel),
    };

SendMessageRequest _$SendMessageRequestFromJson(Map<String, dynamic> json) =>
    SendMessageRequest(
      conversationId: (json['conversationId'] as num).toInt(),
      content: json['content'] as String,
      type: $enumDecodeNullable(_$MessageTypeEnumMap, json['type']) ??
          MessageType.text,
      replyToId: (json['replyToId'] as num?)?.toInt(),
      attachmentPaths: (json['attachmentPaths'] as List<dynamic>?)
          ?.map((e) => e as String)
          .toList(),
    );

Map<String, dynamic> _$SendMessageRequestToJson(SendMessageRequest instance) =>
    <String, dynamic>{
      'conversationId': instance.conversationId,
      'content': instance.content,
      'type': _$MessageTypeEnumMap[instance.type]!,
      'replyToId': instance.replyToId,
      'attachmentPaths': instance.attachmentPaths,
    };

CreateConversationRequest _$CreateConversationRequestFromJson(
        Map<String, dynamic> json) =>
    CreateConversationRequest(
      participantIds: (json['participantIds'] as List<dynamic>)
          .map((e) => (e as num).toInt())
          .toList(),
      title: json['title'] as String?,
      isGroup: json['isGroup'] as bool? ?? false,
    );

Map<String, dynamic> _$CreateConversationRequestToJson(
        CreateConversationRequest instance) =>
    <String, dynamic>{
      'participantIds': instance.participantIds,
      'title': instance.title,
      'isGroup': instance.isGroup,
    };
