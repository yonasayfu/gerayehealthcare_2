# Geraye Healthcare - Messaging System Documentation

## Overview

This document provides a comprehensive overview of the messaging system implementation for the Geraye Healthcare application. The system features modern messaging capabilities including real-time communication, file attachments, voice messages, message reactions, and advanced features like typing indicators and online status tracking.

## Table of Contents

1. [System Architecture](#system-architecture)
2. [Backend Implementation](#backend-implementation)
3. [Frontend Implementation](#frontend-implementation)
4. [API Integration](#api-integration)
5. [Real-time Features](#real-time-features)
6. [Database Schema](#database-schema)
7. [Development Workflow](#development-workflow)
8. [Integration Guide](#integration-guide)
9. [Future Enhancements](#future-enhancements)

---

## System Architecture

### High-Level Overview

The messaging system is built with a Laravel 11 backend and Vue.js 3 frontend, utilizing:

- **Backend**: Laravel 11 with PostgreSQL database
- **Frontend**: Vue.js 3 with TypeScript, Pinia for state management, TailwindCSS for styling
- **Real-time**: Laravel Echo with Pusher/Reverb for WebSocket broadcasting
- **Authentication**: Laravel Sanctum for API token management
- **File Storage**: Laravel filesystem with public disk storage

### Architecture Patterns

1. **API-First Design**: All communication happens through versioned REST API endpoints
2. **Service Layer Pattern**: Business logic separated into service classes
3. **Event-Driven Architecture**: Real-time features implemented using Laravel Events and Broadcasting
4. **Repository Pattern**: Models handle data access with relationships
5. **Composition API**: Frontend uses Vue 3 Composition API for better reactivity and code organization

---

## Backend Implementation

### Core Models

#### 1. Message Model (`app/Models/Message.php`)
Primary model for direct messaging between users.

```php
// Key properties
- id: Primary key
- sender_id: Foreign key to users table
- receiver_id: Foreign key to users table  
- message: Text content (nullable)
- reply_to_id: Foreign key for message threading
- attachment_path: File path for attachments
- attachment_filename: Original filename
- attachment_mime_type: File MIME type
- attachment_type: Categorized type (image, video, audio, voice, file)
- duration: Voice message duration in seconds
- read_at: Timestamp when message was read
- edited_at: Timestamp when message was last edited
- is_pinned: Boolean flag for pinned messages

// Key relationships
- sender(): BelongsTo User
- receiver(): BelongsTo User
- replyTo(): BelongsTo Message
- messageReactions(): HasMany MessageReaction
```

#### 2. MessageReaction Model (`app/Models/MessageReaction.php`)
Handles emoji reactions for direct messages.

```php
// Key properties
- id: Primary key
- message_id: Foreign key to messages table
- user_id: Foreign key to users table
- emoji: Emoji string (max 10 characters)

// Key relationships
- message(): BelongsTo Message
- user(): BelongsTo User
```

#### 3. User Model Extensions
Extended with messaging-related functionality:

```php
- last_seen_at: Timestamp for online status tracking
```

### Service Classes

#### 1. MessageService (`app/Services/Messaging/MessageService.php`)
Core service for direct messaging functionality.

**Key Methods:**
- `sendDirectMessage(User $recipient, array $data): Message`
- `addReaction(Message $message, User $user, string $emoji): MessageReaction`
- `removeReaction(Message $message, User $user, string $emoji): void`
- `forwardMessage(Message $message, array $userIds): array`
- `editMessage(Message $message, string $newContent): Message`
- `deleteMessage(Message $message): void`
- `pinMessage(Message $message): Message`
- `unpinMessage(Message $message): Message`

#### 2. TelegramInboxService (`app/Services/Messaging/TelegramInboxService.php`)
Handles inbox functionality and message transformation.

**Key Methods:**
- `getInboxData(User $user, array $params): array`
- `transformDirectMessage(Message $message, int $currentUserId): array`
- `transformChannelMessage(GroupMessage $message, int $currentUserId): array`

### API Controllers

#### 1. MessageController (`app/Http/Controllers/MessageController.php`)
Legacy controller for basic messaging functionality.

#### 2. Api\V1\MessageController (`app/Http/Controllers/Api/V1/MessageController.php`)
Enhanced API controller with modern messaging features.

**New Enhanced Endpoints:**
- `POST /api/v1/messages/typing` - Send typing indicators
- `GET /api/v1/messages/online-users` - Get online users
- `POST /api/v1/messages/{message}/react` - Add emoji reactions
- `DELETE /api/v1/messages/{message}/react` - Remove emoji reactions
- `POST /api/v1/messages/{message}/forward` - Forward messages
- `PATCH /api/v1/messages/{message}` - Edit messages
- `POST /api/v1/messages/{message}/reply` - Reply to messages
- `POST /api/v1/messages/voice` - Send voice messages
- `GET /api/v1/messages/threads/{user}/media` - Get media files
- `GET /api/v1/messages/threads/{user}/files` - Get document files
- `POST /api/v1/messages/threads/{user}/clear` - Clear conversation
- `POST /api/v1/messages/{message}/read-receipt` - Mark as read

### Broadcasting Events

#### 1. UserTyping Event (`app/Events/UserTyping.php`)
Broadcasts typing indicators to private user channels.

```php
// Channel: user.{target_user_id}
// Event: user.typing
// Data: { user_id, user_name, is_typing }
```

#### 2. MessageRead Event (`app/Events/MessageRead.php`)
Broadcasts read receipts to message channels.

```php
// Channel: message.{message_id}
// Event: message.read  
// Data: { message_id, user_id, read_at }
```

### Middleware

#### UpdateLastSeen (`app/Http/Middleware/UpdateLastSeen.php`)
Updates user's `last_seen_at` timestamp on authenticated requests for online status tracking.

---

## Frontend Implementation

### Vue Components Structure

#### 1. Main Messaging Component
`resources/js/components/messaging/MessagingExample.vue`

A complete messaging interface featuring:
- **Conversation Sidebar**: List of conversations with unread counts and online indicators
- **Chat Area**: Message display with bubbles, reactions, attachments
- **Message Input**: Text input with file upload and voice recording
- **Real-time Features**: Typing indicators, online status, read receipts

#### 2. Core Composable
`resources/js/composables/useMessaging.ts`

Provides reactive messaging functionality:

```typescript
// Key features provided
- Message sending/receiving
- Voice message recording
- Real-time typing indicators  
- File attachment handling
- Message reactions
- Online status tracking
- Utility functions for formatting
```

### State Management

#### Pinia Store (`resources/js/stores/messaging.ts`)
Centralized state management for messaging data.

**Key State:**
```typescript
{
  user: GerayeUser | null
  conversations: GerayeConversation[]
  selectedConversationId: number | null  
  messages: GerayeMessage[]
  isLoading: boolean
  isProcessing: boolean
  typingUsers: Record<number, GerayeUser[]>
  onlineUsers: number[]
}
```

**Enhanced API Methods:**
- `sendTypingIndicator()` - Send typing status
- `fetchOnlineUsers()` - Get online user list
- `sendVoiceMessage()` - Send voice recordings
- `forwardMessage()` - Forward messages to other users
- `replyToMessage()` - Reply to specific messages
- `getMediaFiles()` & `getFiles()` - Filter attachments by type
- `clearThread()` - Clear conversation history
- `markMessageAsRead()` - Mark messages as read
- `searchMessages()` - Search within conversations

### WebSocket Service

#### MessagingWebSocketService (`resources/js/services/messagingWebSocket.ts`)
Handles real-time WebSocket connections and events.

**Key Features:**
- User private channel subscriptions
- Presence channel for online status
- Conversation channel subscriptions
- Event handling for typing, new messages, read receipts
- Notification management
- Automatic connection cleanup

### TypeScript Types

#### Main Project Types (`resources/js/types/messaging.ts`)
Comprehensive type definitions matching Laravel models:

```typescript
interface GerayeUser {
  id: number
  name: string
  email: string
  profile_photo_url?: string
  last_seen?: string
  is_online?: boolean
}

interface GerayeMessage {
  id: number
  sender_id: number
  receiver_id?: number
  message: string | null
  attachment_path?: string
  attachment_type?: string
  duration?: number
  read_at?: string
  edited_at?: string
  is_pinned: boolean
  created_at: string
  updated_at: string
  sender: GerayeUser
  reactions?: GerayeReaction[]
}
```

---

## API Integration

### Authentication
All API endpoints require Laravel Sanctum authentication:
```typescript
headers: {
  'Authorization': `Bearer ${token}`,
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}
```

### Request/Response Patterns

#### Standard Success Response
```json
{
  "data": { /* resource data */ }
}
```

#### Error Response
```json
{
  "message": "Error description",
  "errors": { /* validation errors if applicable */ }
}
```

### Rate Limiting
All endpoints have rate limiting configured:
- Read operations: 60 requests/minute
- Write operations: 30 requests/minute  
- Typing indicators: 120 requests/minute
- File downloads: 120 requests/minute

---

## Real-time Features

### WebSocket Channels

#### Private User Channels
- **Channel**: `user.{user_id}`
- **Events**: `user.typing`, `message.sent`, `message.read`
- **Authentication**: Required via Laravel Sanctum

#### Presence Channels  
- **Channel**: `online-users`
- **Events**: User join/leave events
- **Data**: Online user list maintenance

#### Message Channels
- **Channel**: `message.{message_id}` 
- **Events**: `message.read`
- **Purpose**: Read receipt broadcasting

### Event Broadcasting Flow

1. **User Action** → Frontend triggers API call
2. **API Processing** → Backend processes request
3. **Event Broadcasting** → Laravel broadcasts event via Pusher/Reverb
4. **WebSocket Delivery** → Event delivered to subscribed clients
5. **Frontend Update** → UI updates reactively via Pinia store

---

## Database Schema

### Core Tables

#### messages
```sql
CREATE TABLE messages (
    id BIGSERIAL PRIMARY KEY,
    sender_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    receiver_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    message TEXT,
    reply_to_id BIGINT REFERENCES messages(id) ON DELETE SET NULL,
    attachment_path VARCHAR(255),
    attachment_filename VARCHAR(255),
    attachment_mime_type VARCHAR(255),
    attachment_type VARCHAR(20),
    duration INTEGER, -- for voice messages in seconds
    read_at TIMESTAMP,
    edited_at TIMESTAMP,
    is_pinned BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_messages_sender_receiver ON messages(sender_id, receiver_id);
CREATE INDEX idx_messages_created_at ON messages(created_at);
CREATE INDEX idx_messages_read_at ON messages(read_at);
```

#### message_reactions
```sql
CREATE TABLE message_reactions (
    id BIGSERIAL PRIMARY KEY,
    message_id BIGINT NOT NULL REFERENCES messages(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    emoji VARCHAR(10) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    
    UNIQUE(message_id, user_id, emoji)
);

-- Indexes  
CREATE INDEX idx_message_reactions_message_id ON message_reactions(message_id);
```

#### users (additions)
```sql
ALTER TABLE users 
ADD COLUMN last_seen_at TIMESTAMP;

-- Index for online status queries
CREATE INDEX idx_users_last_seen_at ON users(last_seen_at);
```

---

## Development Workflow

### Backend Development

#### Adding New API Endpoints
1. Create controller method in `app/Http/Controllers/Api/V1/MessageController.php`
2. Add route in `routes/api.php` with appropriate middleware
3. Create Form Request class for validation if needed
4. Update Service class with business logic
5. Add broadcasting events if real-time functionality required

#### Database Changes
1. Create migration: `php artisan make:migration description`
2. Update model fillable fields and relationships
3. Update factory and seeder if applicable
4. Run migration: `php artisan migrate`

### Frontend Development

#### Adding New Features
1. Update TypeScript interfaces in `resources/js/types/messaging.ts`
2. Add API methods to Pinia store `resources/js/stores/messaging.ts`
3. Update WebSocket service if real-time features needed
4. Create/update Vue components
5. Update composable if shared functionality needed

#### Component Development
1. Components in `resources/js/components/messaging/`
2. Use TypeScript interfaces for type safety
3. Leverage Pinia stores for state management
4. Follow TailwindCSS utility-first approach

---

## Integration Guide

### Quick Setup

#### 1. Backend Setup
```bash
# Run migrations
php artisan migrate

# Start Laravel development server
php artisan serve
```

#### 2. Frontend Setup
```bash
# Install dependencies
npm install

# Start Vite development server
npm run dev
```

#### 3. Broadcasting Setup (Optional)
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_key
PUSHER_APP_SECRET=your_secret
PUSHER_APP_CLUSTER=us2
```

### Using the Messaging System

#### Basic Integration
```vue
<template>
  <MessagingExample :user="currentUser" />
</template>

<script setup>
import MessagingExample from '@/components/messaging/MessagingExample.vue'
import { useMessaging } from '@/composables/useMessaging'

const messaging = useMessaging()
const currentUser = { id: 1, name: 'John Doe', email: 'john@example.com' }

onMounted(async () => {
  await messaging.initialize(currentUser)
})
</script>
```

#### Custom Implementation
```typescript
import { useMessagingStore } from '@/stores/messaging'
import { getMessagingWebSocketService } from '@/services/messagingWebSocket'

// Initialize store
const messagingStore = useMessagingStore()
messagingStore.setUser(currentUser)

// Initialize WebSocket
const webSocketService = getMessagingWebSocketService()
webSocketService.init(currentUser.id)

// Send message
await messagingStore.sendMessage({
  message: 'Hello!',
  receiver_id: 2
})
```

---

## Future Enhancements

### Planned Features

#### Short-term (Week 2)
- **Voice/Video Calls**: WebRTC integration for calling
- **Advanced Search**: Full-text search with message indexing
- **Settings Panel**: User preferences and chat settings
- **Notification Enhancements**: Desktop notifications with sound alerts

#### Medium-term (Week 3-4)  
- **Message Analytics**: Read receipts, delivery status, message statistics
- **File Organization**: Better file management with folders and tagging
- **Multiple Themes**: Dark mode, custom themes, and UI customization
- **Performance Optimization**: Message virtualization, caching strategies

#### Long-term
- **Message Scheduling**: Send messages at specified times
- **Message Templates**: Quick message templates for common responses
- **Multi-language Support**: i18n integration
- **Message Encryption**: End-to-end encryption for sensitive conversations

### Scalability Considerations

#### Database Optimization
- Message partitioning by date for large datasets
- Redis caching for frequently accessed conversations
- Full-text search indexing for message search

#### Real-time Performance
- WebSocket connection pooling
- Message queue optimization for broadcasting
- CDN integration for file attachments

#### Frontend Performance
- Virtual scrolling for long message lists
- Lazy loading for attachments and media
- Service worker for offline messaging

---

## Notes for Future Development

### Architecture Decisions Made
1. **Dual Reaction Systems**: Maintained backward compatibility with existing polymorphic reactions for group messages while implementing specific MessageReaction for direct messages
2. **Service Layer**: Separated business logic from controllers for better testability and maintainability
3. **Event-Driven Real-time**: Used Laravel's built-in broadcasting system for real-time features
4. **TypeScript Integration**: Full type safety across frontend components and API interactions

### Code Quality Standards
- All API endpoints include proper authentication and rate limiting
- TypeScript interfaces maintain consistency between frontend and backend
- Vue components use Composition API for better reactivity
- Service classes handle complex business logic
- Broadcasting events follow consistent naming conventions

### Testing Considerations
- API endpoints should have feature tests
- Frontend components should have unit tests
- Real-time features should be tested with mock WebSocket connections
- File upload functionality requires integration testing

---

## Troubleshooting

### Common Issues

#### WebSocket Connection Issues
- Check Pusher/Reverb configuration in `.env`
- Verify authentication tokens are being sent correctly
- Ensure firewall allows WebSocket connections

#### File Upload Problems
- Check storage disk permissions
- Verify file size limits in `php.ini`
- Ensure storage symlink exists: `php artisan storage:link`

#### Real-time Features Not Working
- Confirm broadcasting driver is set correctly
- Check Laravel Echo configuration in frontend
- Verify event broadcasting is enabled

### Debug Commands
```bash
# Check Laravel configuration
php artisan config:show

# Test database connection  
php artisan tinker --execute="DB::connection()->getPdo();"

# Check storage link
php artisan storage:link

# Clear caches
php artisan cache:clear && php artisan config:clear && php artisan route:clear
```

---

This documentation provides a comprehensive overview of the messaging system implementation. For specific implementation details, refer to the individual files mentioned throughout this document.