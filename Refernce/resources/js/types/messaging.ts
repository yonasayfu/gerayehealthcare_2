export interface User {
  id: number;
  name: string;
  email: string;
  profile_photo_path?: string;
  profile_photo_url?: string;
  staff?: Staff;
}

export interface Staff {
  id: number;
  user_id: number;
  position?: string;
  department?: string;
}

export interface Message {
  id: number;
  sender_id: number;
  receiver_id: number;
  message: string | null;
  attachment_path?: string | null;
  attachment_filename?: string | null;
  attachment_mime_type?: string | null;
  attachment_url?: string | null;
  reply_to_id?: number | null;
  parent_id?: number | null;
  read_at?: string | null;
  priority: 'low' | 'normal' | 'high' | 'urgent';
  message_type: 'text' | 'file' | 'image' | 'system';
  created_at: string;
  updated_at: string;
  deleted_at?: string | null;
  isOptimistic?: boolean;
  sender?: User;
  receiver?: User;
  replyTo?: Message;
  parent?: Message;
  reactions?: Reaction[];
  replies?: Message[];
}

export interface Reaction {
  id: number;
  reactable_type: string;
  reactable_id: number;
  user_id: number;
  emoji: string;
  created_at: string;
  updated_at: string;
  user?: User;
}

export interface Conversation {
  id: number;
  name: string;
  email: string;
  profile_photo_url?: string;
  profile_photo_path?: string;
  staff?: Staff;
  unread_count: number;
  last_message?: {
    id: number;
    message: string;
    created_at: string;
    sender_name: string;
    has_attachment: boolean;
  };
}

export interface Group {
  id: number;
  name: string;
  description?: string;
  created_by: number;
  is_private: boolean;
  max_members: number;
  member_count: number;
  created_at: string;
  updated_at: string;
  deleted_at?: string | null;
  creator?: User;
  members?: GroupMember[];
  latest_message?: GroupMessage;
}

export interface GroupMember {
  id: number;
  group_id: number;
  user_id: number;
  role: 'owner' | 'admin' | 'member';
  joined_at: string;
  created_at: string;
  updated_at: string;
  user?: User;
  group?: Group;
}

export interface GroupMessage {
  id: number;
  group_id: number;
  sender_id: number;
  message: string | null;
  reply_to_id?: number | null;
  attachment_path?: string | null;
  attachment_filename?: string | null;
  attachment_mime_type?: string | null;
  attachment_url?: string | null;
  message_type: 'text' | 'file' | 'image' | 'system';
  is_pinned: boolean;
  created_at: string;
  updated_at: string;
  deleted_at?: string | null;
  group?: Group;
  sender?: User;
  replyTo?: GroupMessage;
  reactions?: Reaction[];
}

export interface TypingStatus {
  typing: boolean;
}

export interface MessageFormData {
  message?: string;
  attachment?: File;
  reply_to_id?: number;
  priority?: 'low' | 'normal' | 'high' | 'urgent';
}

export interface GroupFormData {
  name: string;
  description?: string;
  is_private?: boolean;
  max_members?: number;
  member_ids?: number[];
}

export interface MessageFilters {
  search?: string;
  priority?: 'low' | 'normal' | 'high' | 'urgent';
  read_status?: 'read' | 'unread';
  message_type?: 'text' | 'file' | 'image' | 'system';
  date_from?: string;
  date_to?: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
}

export interface ApiResponse<T = any> {
  success: boolean;
  message: string;
  data?: T;
  errors?: Record<string, string[]>;
}

export interface BroadcastEvent {
  message?: Message;
  group_message?: GroupMessage;
  reaction?: Reaction;
  typing?: TypingStatus;
  user_id?: number;
  group_id?: number;
}

export interface MessageExportOptions {
  format: 'csv' | 'json' | 'pdf';
  date_from?: string;
  date_to?: string;
  include_attachments?: boolean;
}

export interface NotificationData {
  id: string;
  type: string;
  data: {
    sender_id: number;
    sender_name: string;
    message_preview: string;
    message_id: number;
    conversation_id: number;
    group_id?: number;
  };
  read_at?: string;
  created_at: string;
}

export interface MessageStats {
  total_messages: number;
  unread_messages: number;
  total_conversations: number;
  total_groups: number;
  messages_today: number;
  messages_this_week: number;
  messages_this_month: number;
}

// Event types for real-time messaging
export type MessageEvent = 
  | 'message.new'
  | 'message.updated'
  | 'message.deleted'
  | 'message.reacted'
  | 'message.read'
  | 'user.typing'
  | 'user.stopped-typing';

export type GroupMessageEvent = 
  | 'group-message.new'
  | 'group-message.updated'
  | 'group-message.deleted'
  | 'group-message.reacted'
  | 'group-message.pinned'
  | 'group-message.unpinned';

export type GroupEvent = 
  | 'group.created'
  | 'group.updated'
  | 'group.deleted'
  | 'group.member-added'
  | 'group.member-removed'
  | 'group.member-role-changed';

// Utility types
export type MessagePriority = Message['priority'];
export type MessageType = Message['message_type'];
export type GroupMemberRole = GroupMember['role'];
export type ReactionEmoji = string;

// Form validation types
export interface MessageValidationErrors {
  message?: string[];
  attachment?: string[];
  receiver_id?: string[];
  reply_to_id?: string[];
  priority?: string[];
}

export interface GroupValidationErrors {
  name?: string[];
  description?: string[];
  max_members?: string[];
  member_ids?: string[];
}
