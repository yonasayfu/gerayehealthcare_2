<script setup lang="ts">
import { ref, watch, nextTick, computed } from 'vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';
import { confirmDialog } from '@/lib/confirm'
import { format, isToday, isYesterday } from 'date-fns';
import {
  LayoutGrid, UserPlus, UserCog, CalendarClock, Stethoscope, MessageCircle,
  Receipt, ShieldCheck, PackageCheck, ClipboardList, Hospital, ArrowBigRight,
  Megaphone, Globe2, CalendarDays, Users, BookOpen, Folder, ChevronDown,
  ChevronRight, CalendarCheck, UserCheck, Settings, Paperclip, Send, Trash2, Download, Check, CheckCheck, X
} from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { ScrollArea } from '@/components/ui/scroll-area';
import {
  DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import { useToast } from '@/components/ui/toast/use-toast';

interface UserConversation {
  id: number;
  name: string;
  email: string;
  profile_photo_url: string;
  staff?: any; // Assuming staff might be loaded for some users
}

interface MessageItem {
  id: number;
  sender_id: number;
  receiver_id: number;
  message: string | null;
  read_at: string | null;
  attachment_path: string | null;
  attachment_filename: string | null;
  attachment_mime_type: string | null;
  attachment_url: string | null;
  created_at: string;
  sender: UserConversation;
  receiver: UserConversation;
}

interface PageProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
      roles: string[];
      permissions: string[];
    };
  };
  conversations: UserConversation[];
  selectedConversation: UserConversation | null;
  messages: MessageItem[];
  filters: {
    search?: string;
  };
  [key: string]: any; // Add index signature to satisfy Inertia's PageProps constraint
}

const props = defineProps<PageProps>();

const page = usePage<PageProps>();
const { toast } = useToast();

const conversations = ref<UserConversation[]>(props.conversations);
const selectedConversation = ref<UserConversation | null>(props.selectedConversation);
const messages = ref<MessageItem[]>(props.messages);
const messageInput = ref('');
const attachmentInput = ref<File | null>(null);
const attachmentPreview = ref<string | null>(null);
const messagesEndRef = ref<HTMLElement | null>(null);
const searchInput = ref(props.filters.search || '');

const form = useForm({
  receiver_id: selectedConversation.value?.id || null,
  message: '',
  attachment: null as File | null,
});

watch(() => props.conversations, (newConversations: UserConversation[]) => {
  conversations.value = newConversations;
});

watch(() => props.selectedConversation, (newSelectedConversation: UserConversation | null) => {
  selectedConversation.value = newSelectedConversation;
  form.receiver_id = newSelectedConversation?.id || null;
});

watch(() => props.messages, (newMessages: MessageItem[]) => {
  messages.value = newMessages;
  nextTick(scrollToBottom);
});

watch(messageInput, (newValue: string) => {
  form.message = newValue;
});

const scrollToBottom = () => {
  if (messagesEndRef.value) {
    messagesEndRef.value.scrollIntoView({ behavior: 'smooth' });
  }
};

const selectConversation = (conversationId: number) => {
  // Navigate to the new conversation, which will trigger a new Inertia visit
  // and update props.selectedConversation and props.messages
  window.location.href = route('admin.messages.index', conversationId);
};

const handleAttachmentChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    const file = target.files[0];
    attachmentInput.value = file;
    form.attachment = file;

    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (e) => {
        attachmentPreview.value = e.target?.result as string;
      };
      reader.readAsDataURL(file);
    } else {
      attachmentPreview.value = null; // Clear image preview for non-image files
    }
  } else {
    attachmentInput.value = null;
    form.attachment = null;
    attachmentPreview.value = null;
  }
};

const removeAttachment = () => {
  attachmentInput.value = null;
  form.attachment = null;
  attachmentPreview.value = null;
  const fileInput = document.getElementById('attachment-input') as HTMLInputElement;
  if (fileInput) {
    fileInput.value = ''; // Clear the file input
  }
};

const sendMessage = () => {
  // Enhanced validation
  if (!form.message?.trim() && !form.attachment) {
    toast({
      title: 'Cannot send empty message',
      description: 'Please type a message or attach a file.',
      variant: 'destructive',
    });
    return;
  }

  if (!selectedConversation.value) {
    toast({
      title: 'No conversation selected',
      description: 'Please select a conversation first.',
      variant: 'destructive',
    });
    return;
  }

  // Check message length
  if (form.message && form.message.length > 5000) {
    toast({
      title: 'Message too long',
      description: 'Message cannot exceed 5000 characters.',
      variant: 'destructive',
    });
    return;
  }

  // Set receiver_id for the form
  form.receiver_id = selectedConversation.value.id;

  form.post(route('admin.messages.store'), {
    preserveScroll: true,
    onSuccess: () => {
      messageInput.value = '';
      form.reset('message');
      removeAttachment();
      toast({
        title: 'Message sent',
        description: 'Your message has been sent successfully.',
      });
      // After sending, re-fetch data to update messages and conversations
      // This will trigger the watch on props.messages and scroll to bottom
      window.location.href = route('admin.messages.index', selectedConversation.value?.id);
    },
    onError: (errors) => {
      console.error('Message send error:', errors);

      // Handle specific validation errors
      let errorMessage = 'An unknown error occurred.';
      if (errors.message) {
        errorMessage = Array.isArray(errors.message) ? errors.message[0] : errors.message;
      } else if (errors.attachment) {
        errorMessage = Array.isArray(errors.attachment) ? errors.attachment[0] : errors.attachment;
      } else if (errors.receiver_id) {
        errorMessage = 'Invalid recipient selected.';
      }

      toast({
        title: 'Failed to send message',
        description: errorMessage,
        variant: 'destructive',
      });
    },
  });
};

const formatMessageDate = (dateString: string) => {
  const date = new Date(dateString);
  if (isToday(date)) {
    return format(date, 'p'); // e.g., 10:30 AM
  } else if (isYesterday(date)) {
    return 'Yesterday ' + format(date, 'p');
  } else {
    return format(date, 'MMM d, yyyy p'); // e.g., Jul 16, 2025 10:30 AM
  }
};

const isMyMessage = (message: MessageItem) => {
  return message.sender_id === page.props.auth.user.id;
};

const deleteMessage = async (messageId: number) => {
  const ok = await confirmDialog({
    title: 'Delete Message',
    message: 'Are you sure you want to delete this message?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  form.delete(route('admin.messages.destroy', messageId), {
    preserveScroll: true,
    onSuccess: () => {
      toast({
        title: 'Message deleted',
        description: 'The message has been successfully deleted.',
      });
      window.location.href = route('admin.messages.index', selectedConversation.value?.id);
    },
    onError: (errors) => {
      console.error('Message delete error:', errors);
      toast({
        title: 'Failed to delete message',
        description: errors.error || 'An unknown error occurred.',
        variant: 'destructive',
      });
    },
  });
};

const downloadAttachment = (messageId: number) => {
  window.open(route('admin.messages.download', messageId), '_blank');
};

// Auto-resize textarea
const autoResize = (event: Event) => {
  const textarea = event.target as HTMLTextAreaElement;
  textarea.style.height = 'auto';
  textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
};

// Format file size
const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const markMessageAsRead = (messageId: number) => {
  form.post(route('admin.messages.markRead', messageId), {
    preserveScroll: true,
    onSuccess: () => {
      toast({
        title: 'Message marked as read',
      });
      window.location.href = route('admin.messages.index', selectedConversation.value?.id);
    },
    onError: (errors) => {
      console.error('Mark as read error:', errors);
      toast({
        title: 'Failed to mark message as read',
        description: errors.error || 'An unknown error occurred.',
        variant: 'destructive',
      });
    },
  });
};

const markMessageAsUnread = (messageId: number) => {
  form.post(route('admin.messages.markUnread', messageId), {
    preserveScroll: true,
    onSuccess: () => {
      toast({
        title: 'Message marked as unread',
      });
      window.location.href = route('admin.messages.index', selectedConversation.value?.id);
    },
    onError: (errors) => {
      console.error('Mark as unread error:', errors);
      toast({
        title: 'Failed to mark message as unread',
        description: errors.error || 'An unknown error occurred.',
        variant: 'destructive',
      });
    },
  });
};

const performSearch = () => {
  window.location.href = route('admin.messages.index', { search: searchInput.value });
};

const getAttachmentIcon = (mimeType: string | null | undefined) => {
  if (!mimeType) return '📎';
  if (mimeType.startsWith('image/')) return '🖼️';
  if (mimeType.includes('pdf')) return '📄';
  if (mimeType.includes('wordprocessingml') || mimeType.includes('msword')) return '📝';
  if (mimeType.includes('spreadsheetml') || mimeType.includes('excel')) return '📊';
  if (mimeType.includes('text/plain')) return '📄';
  return '📎';
};

const getAttachmentDisplay = (message: MessageItem) => {
  if (message.attachment_mime_type?.startsWith('image/')) {
    return `<img src="${message.attachment_url}" alt="${message.attachment_filename}" class="max-w-xs max-h-48 rounded-lg object-cover" />`;
  }
  return `<div class="flex items-center gap-2 p-2 border rounded-md bg-gray-50 dark:bg-gray-700">
            <span class="text-xl">${getAttachmentIcon(message.attachment_mime_type)}</span>
            <span>${message.attachment_filename}</span>
          </div>`;
};

</script>

<template>
  <div class="flex h-[calc(100vh-4rem)] bg-background">
    <!-- Conversation List Sidebar -->
    <div class="w-1/4 border-r bg-card dark:border-border flex flex-col">
      <div class="p-4 border-b dark:border-border">
        <h2 class="text-xl font-semibold text-foreground">Conversations</h2>
        <div class="mt-4 flex items-center space-x-2">
          <Input
            v-model="searchInput"
            placeholder="Search users..."
            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-5"
            @keyup.enter="performSearch"
          />
          <Button @click="performSearch">Search</Button>
        </div>
      </div>
      <ScrollArea class="flex-grow overflow-y-auto">
        <ul class="divide-y dark:divide-border">
          <li
            v-for="conversation in conversations"
            :key="conversation.id"
            :class="['p-4 cursor-pointer hover:bg-accent', { 'bg-primary/10': selectedConversation && selectedConversation.id === conversation.id }]"
            @click="selectConversation(conversation.id)"
          >
            <div class="flex items-center space-x-3">
              <Avatar>
                <AvatarImage :src="conversation.profile_photo_url" :alt="conversation.name" />
                <AvatarFallback>{{ conversation.name.charAt(0) }}</AvatarFallback>
              </Avatar>
              <div>
                <p class="font-medium text-foreground">{{ conversation.name }}</p>
                <p class="text-sm text-muted-foreground">{{ conversation.email }}</p>
              </div>
            </div>
          </li>
        </ul>
      </ScrollArea>
    </div>

    <!-- Message Area -->
    <div class="flex-grow flex flex-col bg-background">
      <div v-if="selectedConversation" class="p-4 border-b dark:border-border flex justify-between items-center">
        <div class="flex items-center space-x-3">
          <Avatar>
            <AvatarImage :src="selectedConversation.profile_photo_url" :alt="selectedConversation.name" />
            <AvatarFallback>{{ selectedConversation.name.charAt(0) }}</AvatarFallback>
          </Avatar>
          <div>
            <p class="font-medium text-foreground">{{ selectedConversation.name }}</p>
            <p class="text-sm text-muted-foreground">{{ selectedConversation.email }}</p>
          </div>
        </div>
        <Button variant="ghost" size="icon">
          <UserPlus class="h-5 w-5" />
        </Button>
      </div>
      <div v-else class="p-4 border-b dark:border-border">
        <h2 class="text-xl font-semibold text-foreground">
          Select a Conversation
        </h2>
      </div>

      <ScrollArea class="flex-grow p-6 overflow-y-auto custom-scrollbar">
        <div class="space-y-6">
          <div
            v-for="message in messages"
            :key="message.id"
            :class="['flex items-end gap-2', isMyMessage(message) ? 'justify-end' : 'justify-start']"
          >
            <Avatar v-if="!isMyMessage(message)" class="h-8 w-8">
              <AvatarImage :src="message.sender.profile_photo_url" :alt="message.sender.name" />
              <AvatarFallback>{{ message.sender.name.charAt(0) }}</AvatarFallback>
            </Avatar>
            <div
              :class="[
                'max-w-lg p-3 rounded-2xl',
                isMyMessage(message)
                  ? 'bg-primary text-primary-foreground rounded-br-none'
                  : 'bg-secondary text-secondary-foreground rounded-bl-none',
              ]"
            >
              <div v-if="message.message" class="text-sm">{{ message.message }}</div>
              <div v-if="message.attachment_path" class="mt-2">
                <div v-html="getAttachmentDisplay(message)"></div>
                <Button variant="link" class="text-xs p-0 h-auto mt-1" @click="downloadAttachment(message.id)">
                  <Download class="h-3 w-3 mr-1" /> Download {{ message.attachment_filename }}
                </Button>
              </div>
              <div :class="['text-xs mt-1', isMyMessage(message) ? 'text-primary-foreground/70' : 'text-muted-foreground']">
                {{ formatMessageDate(message.created_at) }}
                <span v-if="isMyMessage(message) && message.read_at" class="ml-1">
                  <CheckCheck class="h-3 w-3 inline-block" />
                </span>
                <span v-else-if="isMyMessage(message)" class="ml-1">
                  <Check class="h-3 w-3 inline-block" />
                </span>
              </div>
            </div>
             <Avatar v-if="isMyMessage(message)" class="h-8 w-8">
              <AvatarImage :src="page.props.auth.user.profile_photo_url" :alt="page.props.auth.user.name" />
              <AvatarFallback>{{ page.props.auth.user.name.charAt(0) }}</AvatarFallback>
            </Avatar>
          </div>
          <div ref="messagesEndRef"></div>
        </div>
      </ScrollArea>

      <!-- Message Input -->
      <div v-if="selectedConversation" class="p-4 border-t dark:border-border">
        <div v-if="attachmentPreview" class="mb-2 p-2 border rounded-md bg-accent flex items-center justify-between">
          <div class="flex items-center gap-2">
            <img v-if="attachmentInput?.type.startsWith('image/')" :src="attachmentPreview" class="h-16 w-16 object-cover rounded-md" />
            <span v-else class="text-xl">{{ getAttachmentIcon(attachmentInput?.type || '') }}</span>
            <div class="flex flex-col">
              <span class="font-medium">{{ attachmentInput?.name }}</span>
              <span class="text-xs text-muted-foreground">{{ formatFileSize(attachmentInput?.size || 0) }}</span>
            </div>
          </div>
          <Button variant="ghost" size="sm" @click="removeAttachment">
            <X class="h-4 w-4" />
          </Button>
        </div>

        <div class="space-y-2">
          <div class="relative">
            <textarea
              v-model="messageInput"
              placeholder="Type your message..."
              class="w-full min-h-[40px] max-h-[120px] resize-none rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 pr-20"
              @keydown.enter.exact.prevent="sendMessage"
              @keydown.enter.shift.exact="messageInput += '\n'"
              rows="1"
              @input="autoResize"
            />
            <div class="absolute inset-y-0 right-0 flex items-center">
               <label for="attachment-input" class="cursor-pointer p-2 hover:bg-accent rounded-md transition-colors">
                  <Paperclip class="h-5 w-5 text-muted-foreground hover:text-foreground" />
                  <Input
                    id="attachment-input"
                    type="file"
                    class="hidden"
                    accept="image/*,application/pdf,.doc,.docx,.txt,.csv,.xlsx,.ppt,.pptx"
                    @change="handleAttachmentChange"
                  />
              </label>
              <Button
                @click="sendMessage"
                :disabled="form.processing || (!messageInput.trim() && !attachmentInput)"
                size="icon"
                class="mr-2"
              >
                  <Send class="h-5 w-5" />
              </Button>
            </div>
          </div>

          <!-- Character counter and status -->
          <div class="flex justify-between items-center text-xs text-muted-foreground">
            <div class="flex items-center gap-2">
              <span v-if="form.processing" class="flex items-center gap-1">
                <div class="animate-spin h-3 w-3 border border-current border-t-transparent rounded-full"></div>
                Sending...
              </span>
              <span v-else-if="messageInput.length > 0">
                {{ messageInput.length }}/5000 characters
              </span>
            </div>
            <div class="text-xs text-muted-foreground">
              Press Enter to send, Shift+Enter for new line
            </div>
          </div>
        </div>
      </div>
      <div v-else class="p-4 border-t dark:border-border text-center text-muted-foreground">
        Select a conversation to start messaging.
      </div>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 18px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; border: 2px solid transparent; background-clip: content-box; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
.custom-scrollbar { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
</style>
