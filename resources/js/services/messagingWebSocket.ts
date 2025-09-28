import { useMessagingStore } from '@/stores/messaging';
import type { GerayeMessage, MessageReadEvent, MessageSentEvent, UserTypingEvent } from '@/types/messaging';

declare global {
    interface Window {
        Echo: any;
    }
}

export class MessagingWebSocketService {
    private messagingStore: ReturnType<typeof useMessagingStore>;
    private connectedChannels: Set<string> = new Set();
    private typingTimeouts: Map<number, NodeJS.Timeout> = new Map();

    constructor() {
        this.messagingStore = useMessagingStore();
    }

    /**
     * Initialize WebSocket connection for messaging
     */
    init(userId: number): void {
        if (!window.Echo) {
            console.warn('Echo is not available, skipping WebSocket initialization');
            return;
        }

        console.log('Initializing messaging WebSocket for user:', userId);

        // Listen to private user channel for typing indicators and direct messages
        this.subscribeToUserChannel(userId);

        // Listen to presence channel for online status
        this.subscribeToPresenceChannel();

        // Fetch initial online users
        this.messagingStore.fetchOnlineUsers();
    }

    /**
     * Subscribe to user's private channel
     */
    private subscribeToUserChannel(userId: number): void {
        const channelName = `user.${userId}`;

        if (this.connectedChannels.has(channelName)) {
            return;
        }

        try {
            const channel = window.Echo.private(channelName)
                // Typing indicators
                .listen('.user.typing', (data: UserTypingEvent) => {
                    console.log('User typing event received:', data);
                    this.handleTypingEvent(data);
                })
                // New messages
                .listen('.message.sent', (data: MessageSentEvent) => {
                    console.log('New message event received:', data);
                    this.handleNewMessage(data.message);
                })
                // Message read receipts
                .listen('.message.read', (data: MessageReadEvent) => {
                    console.log('Message read event received:', data);
                    this.handleMessageRead(data);
                });

            this.connectedChannels.add(channelName);
            console.log(`Subscribed to channel: ${channelName}`);
        } catch (error) {
            console.error(`Failed to subscribe to channel: ${channelName}`, error);
        }
    }

    /**
     * Subscribe to presence channel for online status
     */
    private subscribeToPresenceChannel(): void {
        const channelName = 'online-users';

        if (this.connectedChannels.has(channelName)) {
            return;
        }

        try {
            const channel = window.Echo.join(channelName)
                .here((users: any[]) => {
                    console.log('Users currently online:', users);
                    this.messagingStore.setOnlineUsers(users.map((user) => user.id));
                })
                .joining((user: any) => {
                    console.log('User joined:', user);
                    const currentOnline = [...this.messagingStore.onlineUsers];
                    if (!currentOnline.includes(user.id)) {
                        this.messagingStore.setOnlineUsers([...currentOnline, user.id]);
                    }
                })
                .leaving((user: any) => {
                    console.log('User left:', user);
                    const currentOnline = this.messagingStore.onlineUsers.filter((id) => id !== user.id);
                    this.messagingStore.setOnlineUsers(currentOnline);
                })
                .error((error: any) => {
                    console.error('Presence channel error:', error);
                });

            this.connectedChannels.add(channelName);
            console.log(`Subscribed to presence channel: ${channelName}`);
        } catch (error) {
            console.error(`Failed to subscribe to presence channel: ${channelName}`, error);
        }
    }

    /**
     * Subscribe to a conversation channel for group messaging
     */
    subscribeToConversation(conversationId: number): void {
        const channelName = `conversation.${conversationId}`;

        if (this.connectedChannels.has(channelName)) {
            return;
        }

        try {
            const channel = window.Echo.private(channelName)
                .listen('.message.sent', (data: MessageSentEvent) => {
                    this.handleNewMessage(data.message);
                })
                .listen('.message.read', (data: MessageReadEvent) => {
                    this.handleMessageRead(data);
                })
                .listen('.user.typing', (data: UserTypingEvent) => {
                    this.handleTypingEvent(data);
                });

            this.connectedChannels.add(channelName);
            console.log(`Subscribed to conversation channel: ${channelName}`);
        } catch (error) {
            console.error(`Failed to subscribe to conversation channel: ${channelName}`, error);
        }
    }

    /**
     * Unsubscribe from a conversation channel
     */
    unsubscribeFromConversation(conversationId: number): void {
        const channelName = `conversation.${conversationId}`;

        if (!this.connectedChannels.has(channelName)) {
            return;
        }

        try {
            window.Echo.leave(channelName);
            this.connectedChannels.delete(channelName);
            console.log(`Unsubscribed from conversation channel: ${channelName}`);
        } catch (error) {
            console.error(`Failed to unsubscribe from conversation channel: ${channelName}`, error);
        }
    }

    /**
     * Handle typing indicator events
     */
    private handleTypingEvent(data: UserTypingEvent): void {
        const conversationId = this.getConversationIdFromTyping(data);

        if (!conversationId) return;

        // Clear existing timeout for this user
        const existingTimeout = this.typingTimeouts.get(data.user_id);
        if (existingTimeout) {
            clearTimeout(existingTimeout);
        }

        // Get current typing users for this conversation
        const currentTypingUsers = this.messagingStore.typingUsers[conversationId] || [];

        if (data.is_typing) {
            // Add user to typing list if not already there
            const existingUser = currentTypingUsers.find((user) => user.id === data.user_id);
            if (!existingUser) {
                const typingUsers = [...currentTypingUsers, { id: data.user_id, name: data.user_name }];
                this.messagingStore.setTyping(conversationId, typingUsers);
            }

            // Set timeout to remove typing indicator after 3 seconds
            const timeout = setTimeout(() => {
                const updatedUsers = (this.messagingStore.typingUsers[conversationId] || []).filter((user) => user.id !== data.user_id);
                this.messagingStore.setTyping(conversationId, updatedUsers);
                this.typingTimeouts.delete(data.user_id);
            }, 3000);

            this.typingTimeouts.set(data.user_id, timeout);
        } else {
            // Remove user from typing list
            const updatedUsers = currentTypingUsers.filter((user) => user.id !== data.user_id);
            this.messagingStore.setTyping(conversationId, updatedUsers);
            this.typingTimeouts.delete(data.user_id);
        }
    }

    /**
     * Handle new message events
     */
    private handleNewMessage(message: GerayeMessage): void {
        console.log('Received new message:', message);
        // Add message to store
        this.messagingStore.addMessage(message);

        // Update conversation if it exists
        const conversation = this.messagingStore.conversations.find(
            (conv) => conv.id === (message.group_id || this.getDirectConversationId(message)),
        );

        if (conversation) {
            const updatedConversation = {
                ...conversation,
                last_message: message,
                last_message_at: message.created_at,
                unread_count: conversation.unread_count + 1,
            };
            this.messagingStore.updateConversation(updatedConversation);
        }

        // Show notification if not in current conversation
        const shouldShow = this.shouldShowNotification(message);
        console.log('Should show notification:', shouldShow);
        if (shouldShow) {
            this.showMessageNotification(message);
        }
    }

    /**
     * Handle message read events
     */
    private handleMessageRead(data: MessageReadEvent): void {
        // Update message read status in store
        const message = this.messagingStore.messages.find((msg) => msg.id === data.message_id);
        if (message) {
            message.read_at = data.read_at;
        }
    }

    /**
     * Send typing indicator
     */
    sendTypingIndicator(userId: number, isTyping: boolean): void {
        this.messagingStore.sendTypingIndicator(userId, isTyping);
    }

    /**
     * Show message notification
     */
    private showMessageNotification(message: GerayeMessage): void {
        if ('Notification' in window && Notification.permission === 'granted') {
            const notification = new Notification(message.sender.name, {
                body: message.message || 'Sent an attachment',
                icon: message.sender.profile_photo_url || '/images/default-avatar.png',
                tag: `message-${message.id}`,
                renotify: true,
            });

            notification.onclick = () => {
                window.focus();
                notification.close();
                // Navigate to conversation if needed
            };

            // Auto close after 5 seconds
            setTimeout(() => notification.close(), 5000);
        }
    }

    /**
     * Check if notification should be shown
     */
    private shouldShowNotification(message: GerayeMessage): boolean {
        const currentUser = this.messagingStore.user;
        const currentConversation = this.messagingStore.selectedConversation;

        // Don't show notification for own messages
        if (message.sender_id === currentUser?.id) {
            return false;
        }

        // Don't show if the conversation is currently active
        if (currentConversation?.id === (message.group_id || this.getDirectConversationId(message))) {
            return false;
        }

        // Check notification settings
        return this.messagingStore.settings.notifications;
    }

    /**
     * Get conversation ID from typing event
     */
    private getConversationIdFromTyping(data: UserTypingEvent): number | null {
        // This would depend on how you structure typing events
        // For now, assume it's a direct conversation with the typing user
        return data.user_id;
    }

    /**
     * Get direct conversation ID from message
     */
    private getDirectConversationId(message: GerayeMessage): number | null {
        if (message.group_id) return message.group_id;

        const currentUser = this.messagingStore.user;
        if (!currentUser) return null;

        // For direct messages, use the other user's ID as conversation ID
        return message.sender_id === currentUser.id ? message.receiver_id : message.sender_id;
    }

    /**
     * Request notification permission
     */
    async requestNotificationPermission(): Promise<NotificationPermission> {
        if (!('Notification' in window)) {
            console.warn('This browser does not support notifications');
            return 'denied';
        }

        if (Notification.permission === 'granted') {
            return 'granted';
        }

        if (Notification.permission === 'denied') {
            return 'denied';
        }

        const permission = await Notification.requestPermission();
        return permission;
    }

    /**
     * Disconnect all WebSocket connections
     */
    disconnect(): void {
        if (!window.Echo) return;

        // Clear typing timeouts
        this.typingTimeouts.forEach((timeout) => clearTimeout(timeout));
        this.typingTimeouts.clear();

        // Leave all channels
        this.connectedChannels.forEach((channelName) => {
            try {
                window.Echo.leave(channelName);
                console.log(`Left channel: ${channelName}`);
            } catch (error) {
                console.error(`Failed to leave channel: ${channelName}`, error);
            }
        });

        this.connectedChannels.clear();
        console.log('Disconnected from all WebSocket channels');
    }

    /**
     * Get connected channels (for debugging)
     */
    getConnectedChannels(): string[] {
        return Array.from(this.connectedChannels);
    }
}

// Singleton instance
let messagingWebSocketService: MessagingWebSocketService | null = null;

export const getMessagingWebSocketService = (): MessagingWebSocketService => {
    if (!messagingWebSocketService) {
        messagingWebSocketService = new MessagingWebSocketService();
    }
    return messagingWebSocketService;
};
