<script setup lang="ts">
import { ref, watch, nextTick, computed } from 'vue';
import { useForm, usePage, Link } from '@inertiajs/vue3';
import { format, isToday, isYesterday } from 'date-fns';
import {
  LayoutGrid, UserPlus, UserCog, CalendarClock, Stethoscope, MessageCircle,
  Receipt, ShieldCheck, PackageCheck, ClipboardList, Hospital, ArrowBigRight,
  Megaphone, Globe2, CalendarDays, Users, BookOpen, Folder, ChevronDown,
  ChevronRight, CalendarCheck, UserCheck, Settings, Paperclip, Send, Trash2, Download, Check, CheckCheck
} from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { ScrollArea } from '@/components/ui/scroll-area';
import {
  DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import { useToast } from '@/components/ui/toast/use-toast';

const page = usePage();
const { toast } = useToast();

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
}

const page = usePage<PageProps>();
const { toast } = useToast();

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
  if (!form.message && !form.attachment) {
    toast({
      title: 'Cannot send empty message',
      description: 'Please type a message or attach a file.',
      variant: 'destructive',
    });
    return;
  }

  form.post(route('admin.messages.store'), {
    preserveScroll: true,
    onSuccess: () => {
      messageInput.value = '';
      removeAttachment();
      // After sending, re-fetch data to update messages and conversations
      // This will trigger the watch on props.messages and scroll to bottom
      window.location.href = route('admin.messages.index', selectedConversation.value?.id);
    },
    onError: (errors) => {
      console.error('Message send error:', errors);
      toast({
        title: 'Failed to send message',
        description: errors.message || 'An unknown error occurred.',
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

const isMyMessage = (message: any) => {
  return message.sender_id === page.props.auth.user.id;
};

const deleteMessage = (messageId: number) => {
  if (confirm('Are you sure you want to delete this message?')) {
    form.delete(route('admin.messages.destroy', messageId), {
      preserveScroll: true,
      onSuccess: () => {
        toast({
          title: 'Message deleted',
          description: 'The message has been successfully deleted.',
        });
        // Re-fetch messages for the current conversation
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
  }
};

const downloadAttachment = (messageId: number) => {
  window.open(route('admin.messages.download', messageId), '_blank');
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

const getAttachmentIcon = (mimeType: string) => {
  if (mimeType.startsWith('image/')) return '🖼️';
  if (mimeType.includes('pdf')) return '📄';
  if (mimeType.includes('wordprocessingml') || mimeType.includes('msword')) return '📝';
  if (mimeType.includes('spreadsheetml') || mimeType.includes('excel')) return '📊';
  if (mimeType.includes('text/plain')) return '📄';
  return '📎';
};

const getAttachmentDisplay = (message: any) => {
  if (message.attachment_mime_type.startsWith('image/')) {
    return `<img src="${message.attachment_url}" alt="${message.attachment_filename}" class="max-w-xs max-h-48 rounded-lg object-cover" />`;
  }
  return `<div class="flex items-center gap-2 p-2 border rounded-md bg-gray-50 dark:bg-gray-700">
            <span class="text-xl">${getAttachmentIcon(message.attachment_mime_type)}</span>
            <span>${message.attachment_filename}</span>
          </div>`;
};

</script>

<template>
  <div class="flex h-[calc(100vh-4rem)] bg-gray-50 dark:bg-gray-900">
    <!-- Conversation List Sidebar -->
    <div class="w-1/4 border-r bg-white dark:bg-gray-800 dark:border-gray-700 flex flex-col">
      <div class="p-4 border-b dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Conversations</h2>
        <div class="mt-4 flex items-center space-x-2">
          <Input
            v-model="searchInput"
            placeholder="Search users..."
            class="flex-grow"
            @keyup.enter="performSearch"
          />
          <Button @click="performSearch">Search</Button>
        </div>
      </div>
      <ScrollArea class="flex-grow overflow-y-auto">
        <ul class="divide-y dark:divide-gray-700">
          <li
            v-for="conversation in conversations"
            :key="conversation.id"
            :class="['p-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700', { 'bg-blue-50 dark:bg-blue-900': selectedConversation && selectedConversation.id === conversation.id }]"
            @click="selectConversation(conversation.id)"
          >
            <div class="flex items-center space-x-3">
              <Avatar>
                <AvatarImage :src="conversation.profile_photo_url" :alt="conversation.name" />
                <AvatarFallback>{{ conversation.name.charAt(0) }}</AvatarFallback>
              </Avatar>
              <div>
                <p class="font-medium text-gray-900 dark:text-white">{{ conversation.name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ conversation.email }}</p>
              </div>
            </div>
          </li>
        </ul>
      </ScrollArea>
    </div>

    <!-- Message Area -->
    <div class="flex-grow flex flex-col">
      <div class="p-4 border-b bg-white dark:bg-gray-800 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
          {{ selectedConversation ? selectedConversation.name : 'Select a Conversation' }}
        </h2>
      </div>

      <ScrollArea class="flex-grow p-4 overflow-y-auto custom-scrollbar">
        <div class="space-y-4">
          <div
            v-for="message in messages"
            :key="message.id"
            :class="['flex', isMyMessage(message) ? 'justify-end' : 'justify-start']"
          >
            <div
              :class="[
                'max-w-md p-3 rounded-lg shadow-sm',
                isMyMessage(message)
                  ? 'bg-blue-600 text-white rounded-br-none'
                  : 'bg-gray-200 text-gray-800 rounded-bl-none dark:bg-gray-700 dark:text-gray-200',
              ]"
            >
              <div v-if="message.message" class="text-sm">{{ message.message }}</div>
              <div v-if="message.attachment_path" class="mt-2">
                <div v-html="getAttachmentDisplay(message)"></div>
                <Button variant="link" class="text-xs p-0 h-auto mt-1" @click="downloadAttachment(message.id)">
                  <Download class="h-3 w-3 mr-1" /> Download {{ message.attachment_filename }}
                </Button>
              </div>
              <div :class="['text-xs mt-1', isMyMessage(message) ? 'text-blue-200' : 'text-gray-500 dark:text-gray-400']">
                {{ formatMessageDate(message.created_at) }}
                <span v-if="isMyMessage(message) && message.read_at" class="ml-1">
                  <CheckCheck class="h-3 w-3 inline-block" />
                </span>
                <span v-else-if="isMyMessage(message)" class="ml-1">
                  <Check class="h-3 w-3 inline-block" />
                </span>
              </div>
              <DropdownMenu v-if="isMyMessage(message) || page.props.auth.user.roles.includes('Admin') || page.props.auth.user.roles.includes('Super Admin')">
                <DropdownMenuTrigger as-child>
                  <Button variant="ghost" size="sm" class="h-6 w-6 p-0 absolute top-1 right-1">...</Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent>
                  <DropdownMenuItem @click="deleteMessage(message.id)">
                    <Trash2 class="h-4 w-4 mr-2" /> Delete
                  </DropdownMenuItem>
                  <DropdownMenuItem v-if="!message.read_at && message.receiver_id === page.props.auth.user.id" @click="markMessageAsRead(message.id)">
                    <Check class="h-4 w-4 mr-2" /> Mark as Read
                  </DropdownMenuItem>
                  <DropdownMenuItem v-if="message.read_at && message.receiver_id === page.props.auth.user.id" @click="markMessageAsUnread(message.id)">
                    <CheckCheck class="h-4 w-4 mr-2" /> Mark as Unread
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </div>
          </div>
          <div ref="messagesEndRef"></div>
        </div>
      </ScrollArea>

      <!-- Message Input -->
      <div v-if="selectedConversation" class="p-4 border-t bg-white dark:bg-gray-800 dark:border-gray-700">
        <div v-if="attachmentPreview" class="mb-2 p-2 border rounded-md bg-gray-50 dark:bg-gray-700 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <img v-if="attachmentInput?.type.startsWith('image/')" :src="attachmentPreview" class="h-16 w-16 object-cover rounded-md" />
            <span v-else class="text-xl">{{ getAttachmentIcon(attachmentInput?.type || '') }}</span>
            <span>{{ attachmentInput?.name }}</span>
          </div>
          <Button variant="ghost" size="sm" @click="removeAttachment">X</Button>
        </div>
        <div class="flex items-center space-x-2">
          <label for="attachment-input" class="cursor-pointer">
            <Paperclip class="h-6 w-6 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" />
            <Input id="attachment-input" type="file" class="hidden" @change="handleAttachmentChange" />
          </label>
          <Input
            v-model="messageInput"
            placeholder="Type your message..."
            class="flex-grow"
            @keyup.enter="sendMessage"
          />
          <Button @click="sendMessage" :disabled="form.processing">
            <Send class="h-5 w-5" />
          </Button>
        </div>
      </div>
      <div v-else class="p-4 border-t bg-white dark:bg-gray-800 dark:border-gray-700 text-center text-gray-500 dark:text-gray-400">
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
