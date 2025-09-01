import 'package:dartz/dartz.dart';

import '../../core/errors/failures.dart';
import '../entities/message.dart';

abstract class MessageRepository {
  Future<Either<Failure, List<Conversation>>> getConversations({int page = 1, int limit = 20});
  Future<Either<Failure, Conversation>> getConversation(int id);
  Future<Either<Failure, Conversation>> createConversation(List<int> participantIds, {String? title, bool isGroup = false});
  Future<Either<Failure, void>> deleteConversation(int id);
  
  Future<Either<Failure, List<Message>>> getMessages(int conversationId, {int page = 1, int limit = 50});
  Future<Either<Failure, Message>> sendMessage(int conversationId, String content, {MessageType type = MessageType.text, int? replyToId, List<String>? attachmentPaths});
  Future<Either<Failure, Message>> updateMessage(int id, String content);
  Future<Either<Failure, void>> deleteMessage(int id);
  Future<Either<Failure, void>> markMessageAsRead(int messageId);
  Future<Either<Failure, void>> markConversationAsRead(int conversationId);
  
  Future<Either<Failure, String>> uploadAttachment(String filePath);
}
