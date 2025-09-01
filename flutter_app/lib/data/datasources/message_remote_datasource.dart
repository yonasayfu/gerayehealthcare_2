import 'package:injectable/injectable.dart';

import '../../core/constants/api_endpoints.dart';
import '../../core/errors/exceptions.dart';
import '../../core/network/dio_client.dart';
import '../models/message_model.dart';

abstract class MessageRemoteDataSource {
  Future<List<ConversationModel>> getConversations({int page = 1, int limit = 20});
  Future<ConversationModel> getConversation(int id);
  Future<ConversationModel> createConversation(CreateConversationRequest request);
  Future<void> deleteConversation(int id);
  
  Future<List<MessageModel>> getMessages(int conversationId, {int page = 1, int limit = 50});
  Future<MessageModel> sendMessage(SendMessageRequest request);
  Future<MessageModel> updateMessage(int id, String content);
  Future<void> deleteMessage(int id);
  Future<void> markMessageAsRead(int messageId);
  Future<void> markConversationAsRead(int conversationId);
  
  Future<String> uploadAttachment(String filePath);
}

@LazySingleton(as: MessageRemoteDataSource)
class MessageRemoteDataSourceImpl implements MessageRemoteDataSource {
  final DioClient _dioClient;

  MessageRemoteDataSourceImpl(this._dioClient);

  @override
  Future<List<ConversationModel>> getConversations({int page = 1, int limit = 20}) async {
    try {
      final response = await _dioClient.get(
        ApiEndpoints.conversations,
        queryParameters: {'page': page, 'limit': limit},
      );

      if (response.data['success'] == true) {
        final List<dynamic> conversationsJson = response.data['data']['conversations'];
        return conversationsJson.map((json) => ConversationModel.fromJson(json)).toList();
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to fetch conversations',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<ConversationModel> getConversation(int id) async {
    try {
      final response = await _dioClient.get('${ApiEndpoints.conversations}/$id');

      if (response.data['success'] == true) {
        return ConversationModel.fromJson(response.data['data']);
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Conversation not found',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<ConversationModel> createConversation(CreateConversationRequest request) async {
    try {
      final response = await _dioClient.post(
        ApiEndpoints.conversations,
        data: request.toJson(),
      );

      if (response.data['success'] == true) {
        return ConversationModel.fromJson(response.data['data']);
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to create conversation',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<void> deleteConversation(int id) async {
    try {
      final response = await _dioClient.delete('${ApiEndpoints.conversations}/$id');

      if (response.data['success'] != true) {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to delete conversation',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<List<MessageModel>> getMessages(int conversationId, {int page = 1, int limit = 50}) async {
    try {
      final response = await _dioClient.get(
        '${ApiEndpoints.conversations}/$conversationId/messages',
        queryParameters: {'page': page, 'limit': limit},
      );

      if (response.data['success'] == true) {
        final List<dynamic> messagesJson = response.data['data']['messages'];
        return messagesJson.map((json) => MessageModel.fromJson(json)).toList();
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to fetch messages',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<MessageModel> sendMessage(SendMessageRequest request) async {
    try {
      final response = await _dioClient.post(
        ApiEndpoints.messages,
        data: request.toJson(),
      );

      if (response.data['success'] == true) {
        return MessageModel.fromJson(response.data['data']);
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to send message',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<MessageModel> updateMessage(int id, String content) async {
    try {
      final response = await _dioClient.put(
        '${ApiEndpoints.messages}/$id',
        data: {'content': content},
      );

      if (response.data['success'] == true) {
        return MessageModel.fromJson(response.data['data']);
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to update message',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<void> deleteMessage(int id) async {
    try {
      final response = await _dioClient.delete('${ApiEndpoints.messages}/$id');

      if (response.data['success'] != true) {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to delete message',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<void> markMessageAsRead(int messageId) async {
    try {
      final response = await _dioClient.post('${ApiEndpoints.messages}/$messageId/read');

      if (response.data['success'] != true) {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to mark message as read',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<void> markConversationAsRead(int conversationId) async {
    try {
      final response = await _dioClient.post('${ApiEndpoints.conversations}/$conversationId/read');

      if (response.data['success'] != true) {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to mark conversation as read',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<String> uploadAttachment(String filePath) async {
    try {
      // TODO: Implement file upload using FormData
      // This is a placeholder implementation
      final response = await _dioClient.post(
        '${ApiEndpoints.messages}/upload',
        data: {'file_path': filePath},
      );

      if (response.data['success'] == true) {
        return response.data['data']['url'];
      } else {
        throw ServerException(
          message: response.data['message'] ?? 'Failed to upload attachment',
          code: response.statusCode,
        );
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw ServerException(message: e.toString());
    }
  }
}
