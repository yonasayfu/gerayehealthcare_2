<script setup lang="ts">
import { computed, nextTick, ref, watch, onMounted, onUnmounted } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { confirmDialog } from '@/lib/confirm';
import { format, isToday, isYesterday } from 'date-fns';
import {
  UserPlus,
  Paperclip,
  Send,
  Download,
  Check,
  CheckCheck,
  Search,
  Pin,
  MessageSquare,
  MoreVertical,
} from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { ScrollArea } from '@/components/ui/scroll-area';

import { useToast } from '@/components/ui/toast/use-toast';
import axios from 'axios';
import EmojiPicker from 'vue3-emoji-picker';
import 'vue3-emoji-picker/css';
import CreateGroupModal from '@/components/CreateGroupModal.vue';

type ConversationType = 'direct' | 'channel';

interface StaffInfo {
  id: number;
  department?: string | null;
  title?: string | null;
  position?: string | null;
}

interface Conversation {
  id: number;
  type: ConversationType;
  name: string;
  email?: string | null;
  profile_photo_url?: string | null;
  unread?: number;
  staff?: StaffInfo | null;
  description?: string | null;
  member_count?: number | null;
  is_organization?: boolean;
  section_key?: string;
  section_label?: string;
}

interface ConversationSection {
  key: string;
  label: string;
  type: ConversationType;
  conversations: Conversation[];
}

interface MessageItem {
  id: number;
  context: ConversationType;
  group_id?: number | null;
  sender_id: number;
  receiver_id: number;
  message: string | null;
  read_at: string | null;
  attachment_path: string | null;
  attachment_filename: string | null;
  attachment_mime_type: string | null;
  attachment_url: string | null;
  created_at: string;
  sender: Conversation;
  receiver: Conversation;
  is_pinned?: boolean;
}

interface PageProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
      roles: string[];
      permissions: string[];
      profile_photo_url?: string | null;
    };
  };
  initialSections?: ConversationSection[];
  initialSelectedConversation?: Conversation | null;
  initialMessages?: MessageItem[];
  initialSearch?: string;
  initialContext?: ConversationType;
  messageRoutes?: Record<string, string>;
  canModerate?: boolean;
  [key: string]: unknown;
}

const props = defineProps<PageProps>();
const page = usePage<PageProps>();
const { toast } = useToast();

const flattenSections = (sections?: ConversationSection[]): Conversation[] => {
  if (!sections || !sections.length) return [];
  return sections.flatMap((section) =>
    (section.conversations ?? []).map((conversation) => ({
      ...conversation,
      section_key: section.key,
      section_label: section.label,
      type: conversation.type ?? section.type,
    }))
  );
};

const conversations = ref<Conversation[]>(flattenSections(props.initialSections));
const selectedConversation = ref<Conversation | null>(props.initialSelectedConversation ?? null);
const messages = ref<MessageItem[]>(props.initialMessages ?? []);
const messageInput = ref('');
const attachmentInput = ref<File | null>(null);
const attachmentPreview = ref<string | null>(null);
const messagesEndRef = ref<HTMLElement | null>(null);
const searchInput = ref(props.initialSearch ?? '');
const activeContext = ref<ConversationType>(props.initialContext ?? selectedConversation.value?.type ?? 'direct');

// Respect query params for context + conversation selection (e.g., after creating a group)
const qp = typeof window !== 'undefined' ? new URLSearchParams(window.location.search) : null;
const queryContext = (qp?.get('context') as ConversationType | null) ?? null;
const queryConversationId = qp?.get('conversation_id') ? Number(qp.get('conversation_id')) : null;
const appliedQuerySelection = ref(false);
const messageRoutes = computed(() => props.messageRoutes ?? {});
const canModerate = computed(() => props.canModerate ?? false);

const form = useForm({
  receiver_id: selectedConversation.value?.id ?? null,
  message: '',
  attachment: null as File | null,
});

const sending = ref(false);
const isProcessing = computed(() => form.processing || sending.value);
const showEmojiPicker = ref(false);
const editingMessageId = ref<number | null>(null);
const editMessageInput = ref('');
// Voice calls removed
const showCreateGroupModal = ref(false);
const handleGroupCreated = (newGroup: any) => {
  const conv = {
    id: newGroup.id,
    type: 'channel' as const,
    name: newGroup.name,
    description: newGroup.description ?? null,
    member_count: newGroup.members_count ?? null,
    is_organization: false,
    section_key: 'channels',
    section_label: 'Channels',
  };
  conversations.value = [...conversations.value, conv as any];
  activeContext.value = 'channel';
  window.location.href = resolveRoute('index', { context: 'channel', conversation_id: newGroup.id });
};

// Lightweight action menu state for reliable 3-dot actions on all sizes
const openMenuId = ref<number | null>(null);
const openActionMenu = (id: number) => {
  openMenuId.value = openMenuId.value === id ? null : id;
  // Close on outside click
  const close = (e: MouseEvent) => {
    const target = e.target as HTMLElement;
    if (!target.closest('[data-action-menu]')) {
      openMenuId.value = null;
      document.removeEventListener('click', close);
    }
  };
  document.addEventListener('click', close);
};

watch(
  () => props.initialSections,
  (newSections?: ConversationSection[]) => {
    const flattened = flattenSections(newSections);
    if (flattened.length) {
      conversations.value = flattened;
    }
  }
);

watch(
  () => props.initialSelectedConversation,
  (nextConversation?: Conversation | null) => {
    if (!nextConversation) return;

    if (!conversations.value.some((conversation) => conversation.id === nextConversation.id)) {
      conversations.value = [...conversations.value, nextConversation];
    }

    const fromList = conversations.value.find((conversation) => conversation.id === nextConversation.id) ?? nextConversation;
    selectedConversation.value = fromList;
    form.receiver_id = selectedConversation.value?.id ?? null;
    if (selectedConversation.value?.type) {
      activeContext.value = selectedConversation.value.type;
    }
  }
);

watch(
  () => props.initialMessages,
  (nextMessages?: MessageItem[]) => {
    messages.value = nextMessages ?? [];
    nextTick(scrollToBottom);
  }
);

watch(
  () => props.initialSearch,
  (value) => {
    if (typeof value === 'string') {
      searchInput.value = value;
    }
  }
);

watch(
  () => props.initialContext,
  (value) => {
    if (value && value !== activeContext.value) {
      activeContext.value = value;
    }
  }
);

watch(messageInput, (value: string) => {
  form.message = value;
});

const filteredConversations = computed(() => {
  const term = searchInput.value.trim().toLowerCase();
  const scoped = conversations.value.filter((conversation) => conversation.type === activeContext.value);
  if (!term) {
    return scoped;
  }
  return scoped.filter((conversation) => [
    conversation.name,
    conversation.email,
    conversation.description,
    conversation.staff?.department,
  ]
    .filter(Boolean)
    .some((field) => field!.toLowerCase().includes(term)));
});

const availableTabs = computed(() => {
  const seen = new Set<ConversationType>();
  const tabs: { type: ConversationType; label: string }[] = [];
  conversations.value.forEach((conversation) => {
    if (seen.has(conversation.type)) return;
    seen.add(conversation.type);
    tabs.push({
      type: conversation.type,
      label: conversation.type === 'channel' ? 'Channels' : 'Team',
    });
  });
  return tabs;
});

const pinnedMessage = computed(() => messages.value.find((message) => message.is_pinned));

const groupedConversations = computed(() => {
  const groups = new Map<string, { label: string; conversations: Conversation[] }>();
  filteredConversations.value.forEach((conversation) => {
    const key = conversation.section_key ?? conversation.type;
    if (!groups.has(key)) {
      groups.set(key, {
        label: conversation.section_label ?? (conversation.type === 'channel' ? 'Channels' : 'Team'),
        conversations: [],
      });
    }
    groups.get(key)!.conversations.push(conversation);
  });
  return Array.from(groups.entries()).map(([key, value]) => ({ key, ...value }));
});

const scrollToBottom = () => {
  if (messagesEndRef.value) {
    messagesEndRef.value.scrollIntoView({ behavior: 'smooth' });
  }
};

const conversationMeta = (conversation: Conversation) => {
  if (conversation.staff?.department) return conversation.staff.department;
  if (conversation.member_count) return `${conversation.member_count} members`;
  return conversation.email ?? conversation.description ?? '';
};

const conversationInitials = (conversation: Conversation) => {
  if (!conversation.name) return '?';
  return conversation.name.substring(0, 2).toUpperCase();
};

watch(
  () => conversations.value.length,
  () => {
    if (!conversations.value.length) {
      selectedConversation.value = null;
      return;
    }

    // Apply explicit query selection exactly once, if provided
    if (!appliedQuerySelection.value && queryContext && queryConversationId) {
      const match = conversations.value.find(c => c.type === queryContext && c.id === queryConversationId);
      if (match) {
        activeContext.value = queryContext;
        selectedConversation.value = match;
        form.receiver_id = selectedConversation.value.type === 'direct' ? selectedConversation.value.id : null;
        appliedQuerySelection.value = true;
        return;
      }
    }

    // Respect server-selected conversation; do not override when already set
    if (selectedConversation.value && conversations.value.some((c) => c.id === selectedConversation.value!.id)) {
      return;
    }

    if (!conversations.value.some((conversation) => conversation.type === activeContext.value)) {
      activeContext.value = conversations.value[0].type;
    }

    selectedConversation.value = conversations.value[0];
    form.receiver_id = selectedConversation.value.id;
  }
);

watch(
  () => activeContext.value,
  (nextContext) => {
    const candidates = conversations.value.filter((conversation) => conversation.type === nextContext);
    if (!candidates.length) return;
    if (!selectedConversation.value || selectedConversation.value.type !== nextContext) {
      selectedConversation.value = candidates[0];
      form.receiver_id = selectedConversation.value.id;
    }
  }
);

const resolveRoute = (key: string, params: Record<string, unknown> = {}) => {
  const routesMap = messageRoutes.value ?? {};
  const name = routesMap[key] ?? key;
  return route(name, params);
};

const selectConversation = (conversationId: number) => {
  if (selectedConversation.value?.id === conversationId) return;

  const conversation = conversations.value.find((item) => item.id === conversationId);
  if (!conversation) return;
  
  activeContext.value = conversation.type;
  
  const params: Record<string, unknown> = {
    context: conversation.type,
    conversation_id: conversationId,
  };

  if (params.context === 'direct') {
    params.recipient = conversationId;
  }
  
  router.get(route('messages.inbox'), params, {
    preserveState: true,
    preserveScroll: true,
    only: ['initialSections', 'initialMessages', 'initialSelectedConversation', 'initialContext'],
    onSuccess: () => {
      nextTick(() => {
        scrollToBottom();
      });
    }
  });
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
      attachmentPreview.value = null;
    }
  } else {
    removeAttachment();
  }
};

const removeAttachment = () => {
  attachmentInput.value = null;
  form.attachment = null;
  attachmentPreview.value = null;
  const fileInput = document.getElementById('attachment-input') as HTMLInputElement | null;
  if (fileInput) {
    fileInput.value = '';
  }
};

  

const sendMessage = async () => {
  if (!messageInput.value.trim() && !attachmentInput.value) {
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

  // Prepare payload
  const isChannel = selectedConversation.value.type === 'channel';
  const messageText = messageInput.value.trim();

  if (messageText.length > 5000) {
    toast({
      title: 'Message too long',
      description: 'Message cannot exceed 5000 characters.',
      variant: 'destructive',
    });
    return;
  }

  const formData = new FormData();
  if (!isChannel) {
    formData.append('receiver_id', String(selectedConversation.value.id));
  }
  if (messageText) formData.append('message', messageText);
  if (attachmentInput.value) formData.append('attachment', attachmentInput.value);

  sending.value = true;

  try {
    const url = isChannel
      ? resolveRoute('groupStore', { group: selectedConversation.value.id })
      : resolveRoute('store');
    
    const response = await axios.post(url, formData);
    
    // Add the new message to the messages array
    if (response.data.message) {
      messages.value = [...messages.value, response.data.message];
      nextTick(() => {
        scrollToBottom();
      });
    }
    
    // Clear input and reset state
    messageInput.value = '';
    removeAttachment();
    
    toast({
      title: 'Message sent',
      description: 'Your message has been sent successfully.',
    });
    } catch (error: any) {
      let errorMessage = 'An unknown error occurred.';
      const errors = error.response?.data?.errors ?? {};
      
      if (errors.message) {
        errorMessage = Array.isArray(errors.message) ? errors.message[0] : errors.message;
      } else if (errors.attachment) {
        errorMessage = Array.isArray(errors.attachment) ? errors.attachment[0] : errors.attachment;
      } else if (errors.receiver_id) {
        errorMessage = Array.isArray(errors.receiver_id) ? errors.receiver_id[0] : errors.receiver_id;
      }

      toast({
        title: 'Failed to send message',
        description: errorMessage,
        variant: 'destructive',
      });
    } finally {
      sending.value = false;
    }
};

const formatMessageDate = (value: string | null) => {
  if (!value) return '';
  const date = new Date(value);
  if (isToday(date)) return format(date, 'p');
  if (isYesterday(date)) return `Yesterday ${format(date, 'p')}`;
  return format(date, 'MMM d, yyyy p');
};

const isMyMessage = (message: MessageItem) => message.sender_id === page.props.auth.user.id;

const deleteMessage = async (message: MessageItem) => {
  const ok = await confirmDialog({
    title: 'Delete message',
    message: 'Are you sure you want to delete this message?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  });
  if (!ok) return;

  const isChannel = message.context === 'channel';
  const url = isChannel
    ? resolveRoute('groupDestroy', { group: selectedConversation.value?.id, message: message.id })
    : resolveRoute('destroy', { message: message.id });

  axios
    .delete(url)
    .then(() => {
      toast({ title: 'Message deleted' });
      // Remove locally for instant UX; server already deleted
      messages.value = messages.value.filter((m) => m.id !== message.id);
    })
    .catch((error) => {
      toast({
        title: 'Failed to delete message',
        description: error.response?.data?.error || 'An unknown error occurred.',
        variant: 'destructive',
      });
    });
};

const deleteForMe = async (message: MessageItem) => {
  try {
    await axios.post(resolveRoute('hide', { message: message.id }));
    messages.value = messages.value.filter((m) => m.id !== message.id);
    toast({ title: 'Message removed for you' });
  } catch (error) {
    toast({ title: 'Failed to remove', description: 'Please try again.', variant: 'destructive' });
  }
};

const downloadAttachment = (message: MessageItem) => {
  if (message.context === 'channel') {
    window.open(resolveRoute('groupDownload', { group: selectedConversation.value?.id, message: message.id }), '_blank');
  } else {
    window.open(resolveRoute('download', { message: message.id }), '_blank');
  }
};

const autoResize = (event: Event) => {
  const textarea = event.target as HTMLTextAreaElement;
  textarea.style.height = 'auto';
  textarea.style.height = Math.min(textarea.scrollHeight, 140) + 'px';
};

const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 Bytes';
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(1024));
  return `${parseFloat((bytes / Math.pow(1024, i)).toFixed(2))} ${sizes[i]}`;
};

const handleEmojiSelect = (emoji: any) => {
  const value = emoji?.i || emoji?.native || emoji?.unicode || '';
  if (value) {
    messageInput.value += value;
  }
  showEmojiPicker.value = false;
};

const pinMessage = (message: MessageItem) => {
  const isChannel = message.context === 'channel';
  const url = isChannel
    ? resolveRoute('groupPin', { group: selectedConversation.value?.id, message: message.id })
    : resolveRoute('pin', { message: message.id });

  axios
    .post(url)
    .then(() => {
      toast({ title: 'Message pinned' });
      window.location.href = resolveRoute('index', {
        context: selectedConversation.value?.type ?? activeContext.value,
        conversation_id: selectedConversation.value?.id,
        recipient: selectedConversation.value?.type === 'direct' ? selectedConversation.value.id : undefined,
      });
    })
    .catch(() => {
      toast({ title: 'Failed to pin message', variant: 'destructive' });
    });
};

const unpinMessage = (message: MessageItem) => {
  const isChannel = message.context === 'channel';
  const url = isChannel
    ? resolveRoute('groupUnpin', { group: selectedConversation.value?.id, message: message.id })
    : resolveRoute('unpin', { message: message.id });

  axios
    .post(url)
    .then(() => {
      toast({ title: 'Message unpinned' });
      window.location.href = resolveRoute('index', {
        context: selectedConversation.value?.type ?? activeContext.value,
        conversation_id: selectedConversation.value?.id,
        recipient: selectedConversation.value?.type === 'direct' ? selectedConversation.value.id : undefined,
      });
    })
    .catch(() => {
      toast({ title: 'Failed to unpin message', variant: 'destructive' });
    });
};

// Mark read/unread removed from UI

const getAttachmentIcon = (mimeType: string | null | undefined) => {
  if (!mimeType) return '📎';
  if (mimeType.startsWith('image/')) return '🖼️';
  if (mimeType.includes('pdf')) return '📄';
  if (mimeType.includes('word') || mimeType.includes('msword')) return '📝';
  if (mimeType.includes('excel') || mimeType.includes('sheet')) return '📊';
  return '📎';
};

const getAttachmentDisplay = (message: MessageItem) => {
  if (message.attachment_mime_type?.startsWith('image/') && message.attachment_url) {
    return `<img src="${message.attachment_url}" alt="${message.attachment_filename ?? 'Attachment'}" class="max-w-xs max-h-56 rounded-xl object-cover" />`;
  }

  return `<div class="flex items-center gap-2 rounded-lg border border-slate-600 bg-slate-800/70 px-3 py-2 text-sm">
    <span class="text-lg">${getAttachmentIcon(message.attachment_mime_type)}</span>
    <span>${message.attachment_filename ?? 'Attachment'}</span>
  </div>`;
};

const startEditing = (message: MessageItem) => {
  editingMessageId.value = message.id;
  editMessageInput.value = message.message ?? '';
};

const cancelEditing = () => {
  editingMessageId.value = null;
  editMessageInput.value = '';
};

const saveEdit = () => {
  if (!editingMessageId.value) return;
  if (!editMessageInput.value.trim()) {
    toast({ title: 'Message required', description: 'Message cannot be empty.', variant: 'destructive' });
    return;
  }

  const target = messages.value.find((m) => m.id === editingMessageId.value);
  const isChannel = target?.context === 'channel';
  const url = isChannel
    ? resolveRoute('groupUpdate', { group: selectedConversation.value?.id, message: editingMessageId.value })
    : resolveRoute('update', { message: editingMessageId.value });

  axios
    .patch(url, { message: editMessageInput.value.trim() })
    .then(() => {
      cancelEditing();
      window.location.href = resolveRoute('index', {
        context: selectedConversation.value?.type ?? activeContext.value,
        conversation_id: selectedConversation.value?.id,
        recipient: selectedConversation.value?.type === 'direct' ? selectedConversation.value.id : undefined,
      });
    })
    .catch((error) => {
      toast({
        title: 'Failed to update message',
        description: error.response?.data?.error || 'Please try again.',
        variant: 'destructive',
      });
    });
};

watch(
  () => messages.value,
  () => {
    if (editingMessageId.value && !messages.value.some((message) => message.id === editingMessageId.value)) {
      cancelEditing();
    }
  }
);

  // Voice call handlers removed

  // Realtime removal when MessageDeleted arrives
  let userChannel: any = null;
  let groupChannel: any = null;

  function handleMessageDeleted(event: any) {
    const id = event?.messageId;
    if (!id) return;
    messages.value = messages.value.filter((m) => m.id !== id);
  }

  onMounted(() => {
    try {
      userChannel = (window as any).Echo?.private?.(`users.${page.props.auth.user.id}`);
      userChannel?.listen?.('MessageDeleted', handleMessageDeleted);
      userChannel?.listen?.('NewMessage', (event: any) => {
        if (event.message && !messages.value.some(m => m.id === event.message.id)) {
            const conversation = conversations.value.find(c => c.id === event.message.sender_id);
            if (conversation) {
                conversation.unread = (conversation.unread || 0) + 1;
            }
            if (selectedConversation.value?.id === event.message.sender_id) {
                messages.value.push(event.message);
                nextTick(() => {
                    scrollToBottom();
                });
            }
        }
      });
    } catch {}
  });

  watch(selectedConversation, (conv) => {
    try {
      // Unsubscribe previous group channel
      groupChannel?.stopListening?.('MessageDeleted');
      groupChannel = null;
      if (conv && conv.type === 'channel') {
        groupChannel = (window as any).Echo?.private?.(`groups.${conv.id}`);
        groupChannel?.listen?.('MessageDeleted', handleMessageDeleted);
      }
    } catch {}
  });

  onUnmounted(() => {
    try {
      userChannel?.stopListening?.('MessageDeleted');
      groupChannel?.stopListening?.('MessageDeleted');
    } catch {}
  });

  // --- In-thread Search ---
  const showSearch = ref(false);
  const searchText = ref('');
  const searchBusy = ref(false);
  const searchResults = ref<MessageItem[]>([]);
  let searchTimer: number | null = null;

  const openSearch = () => {
    showSearch.value = !showSearch.value;
    searchText.value = '';
    searchResults.value = [];
  };

  const runSearch = async () => {
    if (!selectedConversation.value || !searchText.value.trim()) {
      searchResults.value = [];
      return;
    }
    searchBusy.value = true;
    try {
      if (selectedConversation.value.type === 'direct') {
        const res = await axios.get(resolveRoute('search', { user: selectedConversation.value.id, q: searchText.value.trim() }));
        searchResults.value = (res.data?.results ?? []) as any;
      } else {
        const res = await axios.get(resolveRoute('groupSearch', { group: selectedConversation.value.id, q: searchText.value.trim() }));
        searchResults.value = (res.data?.results ?? []) as any;
      }
    } catch {
      searchResults.value = [];
    } finally {
      searchBusy.value = false;
    }
  };

  watch(searchText, () => {
    if (searchTimer) window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(runSearch, 350) as unknown as number;
  });

  // --- Jump to message + highlight ---
  const highlightedMessageId = ref<number | null>(null);
  const jumpToMessage = async (id: number) => {
    showSearch.value = false;
    await nextTick();
    const el = document.getElementById(`msg-${id}`);
    if (el) {
      el.scrollIntoView({ behavior: 'smooth', block: 'center' });
      highlightedMessageId.value = id;
      window.setTimeout(() => { highlightedMessageId.value = null; }, 2000);
    }
  };
</script>

<template>
  <div class="flex h-[calc(100vh-4rem)] bg-background text-foreground overflow-hidden">
    <aside class="flex w-[320px] min-w-[280px] flex-col border-r border-border bg-background/90 backdrop-blur">
        <div class="border-b border-border px-5 py-6 space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold">Inbox</h2>
          <span v-if="isProcessing" class="text-xs text-muted-foreground">Updating…</span>
        </div>
        <p class="mt-1 text-sm text-muted-foreground">Stay connected with your teammates.</p>
        <div class="relative">
          <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500" />
          <Input
            v-model="searchInput"
            type="search"
            placeholder="Search people or teams"
            class="h-11 rounded-xl border-input bg-background/80 pl-9 pr-3 text-sm text-foreground placeholder:text-muted-foreground"
          />
        </div>
        <div v-if="availableTabs.length > 1" class="flex gap-2">
          <Button
            v-for="tab in availableTabs"
            :key="tab.type"
            size="sm"
            :variant="tab.type === activeContext ? 'default' : 'outline'"
            class="flex-1 rounded-xl"
            @click="activeContext = tab.type"
          >
            {{ tab.label }}
          </Button>
        </div>
        <div v-if="activeContext === 'channel'" class="mt-3">
          <Button class="w-full rounded-xl" variant="outline" @click="showCreateGroupModal = true">Create Group</Button>
        </div>
      </div>

      <ScrollArea class="flex-1">
        <div v-if="!groupedConversations.length" class="px-5 py-8 text-sm text-muted-foreground">
          No conversations found.
        </div>
        <div v-else class="space-y-6 px-3 py-4">
          <div v-for="group in groupedConversations" :key="group.key" class="space-y-2">
            <p class="px-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">{{ group.label }}</p>
            <div class="space-y-2">
              <button
                v-for="conversation in group.conversations"
                :key="conversation.id"
                type="button"
                @click="selectConversation(conversation.id)"
                :class="[
                  'w-full rounded-xl border px-4 py-3 text-left transition shadow-sm',
                  conversation.id === selectedConversation?.id
                    ? 'border-indigo-400/70 bg-indigo-500/20 text-foreground'
                    : 'border-transparent bg-muted/70 hover:border-indigo-500/40 hover:bg-muted'
                ]"
              >
                <div class="flex items-center gap-3">
                  <Avatar class="h-10 w-10 border border-slate-700">
                    <AvatarImage v-if="conversation.profile_photo_url" :src="conversation.profile_photo_url" :alt="conversation.name" />
                    <AvatarFallback>{{ conversationInitials(conversation) }}</AvatarFallback>
                  </Avatar>
                  <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                      <p class="truncate text-sm font-semibold">{{ conversation.name }}</p>
                      <span
                        v-if="conversation.unread"
                        class="inline-flex h-5 min-w-[22px] items-center justify-center rounded-full bg-indigo-500 px-2 text-[11px] font-semibold text-white"
                      >
                        {{ conversation.unread }}
                      </span>
                    </div>
                    <p class="truncate text-xs text-muted-foreground">{{ conversationMeta(conversation) }}</p>
                  </div>
                </div>
              </button>
            </div>
          </div>
        </div>
      </ScrollArea>
    </aside>

    <section class="flex flex-1 flex-col overflow-hidden bg-background">
      <template v-if="selectedConversation">
        <header class="border-b border-border px-6 py-5">
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
              <Avatar class="h-12 w-12 border border-border">
                <AvatarImage v-if="selectedConversation.profile_photo_url" :src="selectedConversation.profile_photo_url" :alt="selectedConversation.name" />
                <AvatarFallback>{{ conversationInitials(selectedConversation) }}</AvatarFallback>
              </Avatar>
              <div>
                <h1 class="text-lg font-semibold">{{ selectedConversation.name }}</h1>
                <p class="text-sm text-muted-foreground">{{ conversationMeta(selectedConversation) }}</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <Button variant="ghost" size="icon" class="text-muted-foreground hover:text-foreground" @click="openSearch">
                <Search class="h-5 w-5" />
              </Button>
              <Button variant="ghost" size="icon" class="text-muted-foreground hover:text-foreground" @click="showCreateGroupModal = true" :title="'Create Group'">
                <UserPlus class="h-5 w-5" />
              </Button>
            </div>
          </div>
          <div class="mt-3 flex flex-wrap items-center gap-4 text-xs text-muted-foreground">
            <span v-if="selectedConversation.staff?.department">{{ selectedConversation.staff.department }}</span>
            <span>{{ selectedConversation.email }}</span>
          </div>
        </header>

        <div v-if="pinnedMessage" class="mx-6 mt-4 rounded-xl border border-indigo-400/70 bg-indigo-500/10 px-4 py-3 text-sm text-indigo-900 dark:text-indigo-100 flex items-center gap-3">
          <Pin class="h-4 w-4" />
          <div class="flex-1 truncate">
            {{ pinnedMessage.message || pinnedMessage.attachment_filename || 'Pinned message' }}
          </div>
        </div>

        <!-- Thread search popover -->
        <div v-if="showSearch" class="px-6 pt-3">
          <div class="rounded-xl border border-border bg-muted/40 p-3">
            <div class="flex items-center gap-2">
              <Input v-model="searchText" placeholder="Search this conversation..." class="flex-1" />
              <Button variant="outline" size="sm" @click="searchText = ''; searchResults = []">Clear</Button>
            </div>
            <div v-if="searchBusy" class="mt-2 text-xs text-muted-foreground">Searching…</div>
            <div v-else class="mt-2 max-h-56 overflow-y-auto">
              <div v-if="!searchResults.length && searchText" class="text-xs text-muted-foreground">No matches.</div>
              <button
                v-for="r in searchResults"
                :key="r.id"
                class="w-full text-left rounded-md p-2 hover:bg-muted"
                @click="jumpToMessage(r.id)"
              >
                <div class="text-xs text-muted-foreground">{{ r.created_at }}</div>
                <div class="text-sm truncate" v-if="r.message">{{ r.message }}</div>
                <div class="text-sm text-muted-foreground" v-else>Attachment: {{ r.attachment_filename }}</div>
              </button>
            </div>
          </div>
        </div>

        <ScrollArea class="flex-1 px-6 py-6">
          <div class="space-y-6">
            <div
              v-for="message in messages"
              :key="message.id"
              class="flex items-end gap-3"
              :id="`msg-${message.id}`"
              :class="isMyMessage(message) ? 'justify-end' : 'justify-start'"
            >
              <Avatar v-if="!isMyMessage(message)" class="h-9 w-9 border border-border">
                <AvatarImage v-if="message.sender.profile_photo_url" :src="message.sender.profile_photo_url" :alt="message.sender.name" />
                <AvatarFallback>{{ message.sender.name.charAt(0) }}</AvatarFallback>
              </Avatar>

              <div class="max-w-xl space-y-2">
                <div class="relative">
                  <Button
                    variant="ghost"
                    size="icon"
                    class="absolute h-7 w-7 text-muted-foreground hover:text-foreground opacity-100 z-20"
                    :class="isMyMessage(message) ? 'top-1 left-1 right-auto' : 'top-1 right-1'"
                    @click.stop="openActionMenu(message.id)"
                  >
                    <MoreVertical class="h-4 w-4" />
                  </Button>

                  <!-- Inline lightweight action menu -->
                  <div
                    v-if="openMenuId === message.id"
                    data-action-menu
                    class="absolute z-50 mt-2 w-44 rounded-md border border-border bg-background p-1 shadow-md"
                    :class="isMyMessage(message) ? 'left-1 top-8' : 'right-1 top-8'"
                  >
                    <button
                      v-if="(message.context === 'direct' && isMyMessage(message)) || (message.context === 'channel' && isMyMessage(message))"
                      class="w-full px-3 py-2 text-left hover:bg-muted rounded"
                      @click.stop="openMenuId = null; startEditing(message)"
                    >Edit message</button>
                    <button
                      v-if="message.context === 'direct' && (isMyMessage(message) || canModerate)"
                      class="w-full px-3 py-2 text-left hover:bg-muted rounded"
                      @click.stop="openMenuId = null; (message.read_at ? markMessageAsUnread(message) : markMessageAsRead(message))"
                    >
                      <template v-if="message.read_at">Mark as unread</template>
                      <template v-else>Mark as read</template>
                    </button>
                    <button
                      class="w-full px-3 py-2 text-left hover:bg-muted rounded"
                      @click.stop="openMenuId = null; (message.is_pinned ? unpinMessage(message) : pinMessage(message))"
                    >
                      <template v-if="message.is_pinned">Unpin message</template>
                      <template v-else>Pin message</template>
                    </button>
                    <button
                      v-if="message.context === 'direct' && (isMyMessage(message) || message.receiver_id === page.props.auth.user.id)"
                      class="w-full px-3 py-2 text-left hover:bg-muted rounded"
                      @click.stop="openMenuId = null; deleteForMe(message)"
                    >Delete for me</button>
                    <button
                      v-if="(message.context === 'direct' && (isMyMessage(message) || canModerate)) || (message.context === 'channel' && isMyMessage(message))"
                      class="w-full px-3 py-2 text-left text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded"
                      @click.stop="openMenuId = null; deleteMessage(message)"
                    >Delete for everyone</button>
                    <button v-if="message.context !== 'direct' && !isMyMessage(message)" disabled class="w-full px-3 py-2 text-left text-muted-foreground">Actions limited</button>
                  </div>

                  <div
                    :class="[
                      'rounded-3xl border px-4 py-3 shadow-sm backdrop-blur',
                      isMyMessage(message)
                        ? 'ml-auto border-transparent bg-gradient-to-r from-indigo-500 via-purple-500 to-fuchsia-500 text-white'
                        : ['border-border bg-muted text-foreground', highlightedMessageId === message.id ? 'ring-2 ring-indigo-400' : '']
                    ]"
                  >
                    <div v-if="editingMessageId === message.id" class="space-y-2">
                      <textarea
                        v-model="editMessageInput"
                        rows="3"
                        class="w-full rounded-xl border border-input bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-indigo-400"
                      />
                      <div class="flex items-center justify-end gap-2">
                        <Button variant="ghost" size="sm" class="text-slate-300 hover:text-white" @click="cancelEditing">Cancel</Button>
                        <Button size="sm" class="bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-3" @click="saveEdit">Save</Button>
                      </div>
                    </div>
                    <template v-else>
                      <p v-if="message.message" class="whitespace-pre-line text-sm leading-relaxed">{{ message.message }}</p>
                      <div v-if="message.attachment_path" class="mt-3">
                        <div v-html="getAttachmentDisplay(message)"></div>
                        <Button
                          variant="link"
                          class="mt-2 flex items-center gap-2 p-0 text-xs text-foreground/80 hover:text-foreground"
                          @click="downloadAttachment(message)"
                        >
                          <Download class="h-3 w-3" /> Download {{ message.attachment_filename || 'file' }}
                        </Button>
                      </div>
                    </template>
                  </div>
                </div>

                <div
                  class="flex items-center gap-2 text-xs text-slate-400"
                  :class="isMyMessage(message) ? 'justify-end' : 'justify-start'"
                >
                  <span>{{ formatMessageDate(message.created_at) }}</span>
                  <span v-if="message.is_pinned" class="flex items-center gap-1 text-[10px] uppercase tracking-wide text-indigo-300">
                    <Pin class="h-3 w-3" /> Pinned
                  </span>
                  <span v-if="isMyMessage(message)" class="flex items-center gap-1 text-indigo-200">
                    <component :is="message.read_at ? CheckCheck : Check" class="h-3 w-3" />
                  </span>
                </div>
              </div>

              <Avatar v-if="isMyMessage(message)" class="h-9 w-9 border border-border">
                <AvatarImage v-if="page.props.auth.user.profile_photo_url" :src="page.props.auth.user.profile_photo_url" :alt="page.props.auth.user.name" />
                <AvatarFallback>{{ page.props.auth.user.name.charAt(0) }}</AvatarFallback>
              </Avatar>
            </div>
            <div ref="messagesEndRef"></div>
          </div>
        </ScrollArea>

        <footer class="border-t border-border bg-background/80 px-6 py-4">
          <div v-if="attachmentPreview" class="mb-3 flex items-center justify-between rounded-lg border border-border bg-muted px-4 py-2 text-sm">
            <div class="flex items-center gap-3">
              <span class="text-lg">{{ getAttachmentIcon(attachmentInput?.type || '') }}</span>
              <div>
                <p class="font-medium">{{ attachmentInput?.name }}</p>
                <p class="text-xs text-muted-foreground">{{ formatFileSize(attachmentInput?.size || 0) }}</p>
              </div>
            </div>
            <Button variant="ghost" size="icon" class="text-muted-foreground hover:text-foreground" @click="removeAttachment">
              ✕
            </Button>
          </div>

         <div class="flex items-center gap-2">
            <div class="flex-1">
              <textarea
                v-model="messageInput"
                placeholder="Write a message…"
                class="h-auto min-h-[44px] max-h-[140px] w-full resize-none rounded-xl border border-input bg-background px-4 py-2.5 text-sm text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400"
                rows="1"
                @input="autoResize"
                @keydown.enter.exact.prevent="sendMessage"
                @keydown.enter.shift.exact="messageInput += '\n'"
              />
              <div class="mt-1 flex items-center justify-between text-xs text-muted-foreground">
                <span v-if="isProcessing" class="flex items-center gap-2">
                  <span class="h-3 w-3 animate-spin rounded-full border border-current border-t-transparent"></span>
                  Sending…
                </span>
                <span v-else-if="messageInput.length">{{ messageInput.length }}/5000 characters</span>
                <span v-else>Press Enter to send · Shift+Enter for newline</span>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <div class="relative">
                <Button
                  variant="ghost"
                  size="icon"
                  class="h-11 w-11 rounded-xl border border-input bg-background text-muted-foreground transition hover:bg-muted"
                  @click="showEmojiPicker = !showEmojiPicker"
                >
                  😊
                </Button>
                <div
                  v-if="showEmojiPicker"
                  class="absolute bottom-12 right-0 z-50 rounded-2xl border border-border bg-background p-2 shadow-xl"
                >
                  <EmojiPicker theme="dark" @select="handleEmojiSelect" />
                </div>
              </div>
              <label
                for="attachment-input"
                class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-xl border border-input bg-background text-muted-foreground transition hover:bg-muted"
              >
                <Paperclip class="h-5 w-5" />
                <Input
                  id="attachment-input"
                  type="file"
                  class="hidden"
                  accept="image/*,application/pdf,.doc,.docx,.txt,.csv,.xlsx,.ppt,.pptx"
                  @change="handleAttachmentChange"
                />
              </label>
              <Button
                class="h-11 rounded-xl bg-gradient-to-r from-indigo-500 to-fuchsia-500 px-5 text-sm font-semibold shadow-lg shadow-indigo-500/30 transition hover:from-indigo-400 hover:to-fuchsia-400"
                :disabled="isProcessing || (!messageInput.trim() && !attachmentInput)"
                @click="sendMessage"
              >
                <Send class="mr-2 h-4 w-4" /> Send
              </Button>
            </div>
          </div>
        </footer>
      </template>

      <template v-else>
        <div class="flex flex-1 flex-col items-center justify-center gap-4 px-6 text-center">
          <div class="rounded-full bg-indigo-500/10 p-4 text-indigo-300">
            <MessageSquare class="h-8 w-8" />
          </div>
          <div>
            <h2 class="text-lg font-semibold">Choose a conversation</h2>
            <p class="mt-1 text-sm text-slate-400">Pick someone from the sidebar to start chatting.</p>
          </div>
        </div>
      </template>
    </section>
  </div>

  <!-- Voice call overlay removed -->

  <!-- Create group modal (reuses existing component) -->
  <CreateGroupModal :show="showCreateGroupModal" @close="showCreateGroupModal = false" @groupCreated="handleGroupCreated" />
</template>
