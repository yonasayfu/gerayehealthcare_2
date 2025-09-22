// Geraye Healthcare Messaging Types
export interface GerayeUser {
  id: number;
  name: string;
  email: string;
  profile_photo_url?: string | null;
  staff?: {
    id: number;
    department?: string;
    position?: string;
  } | null;
  last_seen?: string;
  is_online?: boolean;
}

export interface GerayeMessage {
  id: number;
  sender_id: number;
  receiver_id?: number | null;
  group_id?: number | null;
  message: string | null;
  reply_to_id?: number | null;
  attachment_path?: string | null;
  attachment_filename?: string | null;
  attachment_mime_type?: string | null;
  attachment_url?: string | null;
  created_at: string;
  updated_at: string;
  read_at?: string | null;
  is_pinned: boolean;
  sender: GerayeUser;
  receiver?: GerayeUser;
  replyTo?: GerayeMessage;
  reactions?: GerayeReaction[];
  state: 'sent' | 'delivered' | 'read' | 'failed';
  type: 'text' | 'file' | 'image' | 'audio' | 'video';
}

export interface GerayeReaction {
  id: number;
  user_id: number;
  reactable_id: number;
  reactable_type: string;
  emoji: string;
  user: GerayeUser;
  created_at: string;
}

export interface GerayeConversation {
  id: number;
  type: 'direct' | 'group';
  name: string;
  avatar?: string | null;
  description?: string | null;
  unread_count: number;
  last_message?: GerayeMessage;
  last_message_at?: string;
  participants: GerayeUser[];
  is_online?: boolean;
  typing_users?: GerayeUser[];
  is_archived?: boolean;
  is_muted?: boolean;
  member_count?: number;
  admin_ids?: number[];
  created_at: string;
}

export interface GerayeGroup {
  id: number;
  name: string;
  description?: string;
  avatar?: string;
  admin_ids: number[];
  member_count: number;
  members: GerayeUser[];
  created_at: string;
  updated_at: string;
}

export interface GerayeNotification {
  id: string;
  type: string;
  notifiable_type: string;
  notifiable_id: number;
  data: {
    title: string;
    message: string;
    action_url?: string;
    user?: GerayeUser;
  };
  read_at?: string | null;
  created_at: string;
}

export interface GerayeTypingIndicator {
  user_id: number;
  conversation_id: number;
  conversation_type: 'direct' | 'group';
  is_typing: boolean;
  timestamp: string;
}

export interface MessageFormData {
  message?: string;
  attachment?: File;
  receiver_id?: number;
  group_id?: number;
  reply_to_id?: number;
  priority?: 'low' | 'normal' | 'high';
  message_type?: 'text' | 'file' | 'image' | 'audio' | 'video';
}

export interface ConversationSection {
  key: string;
  label: string;
  type: 'direct' | 'group';
  conversations: GerayeConversation[];
}

export interface MessagingSettings {
  read_receipts: boolean;
  typing_indicators: boolean;
  online_status: boolean;
  notifications: boolean;
  sound_notifications: boolean;
  dark_mode: boolean;
  message_preview: boolean;
  auto_download_media: boolean;
  compact_mode: boolean;
}

// API Response Types
export interface ConversationsResponse {
  sections: ConversationSection[];
  total: number;
}

export interface MessagesResponse {
  data: GerayeMessage[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export interface SendMessageResponse {
  message: GerayeMessage;
  conversation: GerayeConversation;
}

// Store State Interface
export interface MessagingState {
  user: GerayeUser | null;
  conversations: GerayeConversation[];
  selectedConversationId: number | null;
  messages: GerayeMessage[];
  isLoading: boolean;
  isProcessing: boolean;
  search: string;
  settings: MessagingSettings;
  notifications: GerayeNotification[];
  typingUsers: Record<number, GerayeUser[]>;
  onlineUsers: number[];
  activeSidebarComponent: 'messages' | 'contacts' | 'settings' | 'notifications' | 'calls';
}

// Event Types for Real-time
export interface NewMessageEvent {
  message: GerayeMessage;
}

export interface MessageUpdatedEvent {
  message: GerayeMessage;
}

export interface MessageDeletedEvent {
  messageId: number;
  conversationId: number;
}

export interface UserTypingEvent {
  user: GerayeUser;
  conversationId: number;
  conversationType: 'direct' | 'group';
  isTyping: boolean;
}

export interface UserOnlineEvent {
  userId: number;
  isOnline: boolean;
  lastSeen?: string;
}