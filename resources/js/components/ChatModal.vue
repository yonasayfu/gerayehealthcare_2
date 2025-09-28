<script setup lang="ts">
import { ref, watch, nextTick, computed, onMounted, onUnmounted } from 'vue';
import debounce from 'lodash/debounce';
import { useForm, usePage } from '@inertiajs/vue3';
import { Send, X, MessageSquareText, Search, Minus, GripVertical, Paperclip, Smile, File, Image, Check, CheckCheck, Clock } from 'lucide-vue-next';
import { format } from 'date-fns';
import axios from 'axios';
import { confirmDialog } from '@/lib/confirm';
import type { User, Message, Conversation, AppPageProps } from '@/types';
import CreateGroupModal from '@/components/CreateGroupModal.vue';
import EmojiPicker from 'vue3-emoji-picker';
import 'vue3-emoji-picker/css';

const props = defineProps<{
  isOpen: boolean;
  initialConversationId?: number | null;
}>();

const emit = defineEmits(['close', 'minimize']);

const page = usePage<AppPageProps>();
const authUser = computed<User>(() => page.props.auth.user);

// Permission checking functions
const hasPermission = (permission: string): boolean => {
  if (!authUser.value) return false;

  // Check if user is super admin
  if (authUser.value.roles?.some((role: string) => role === 'super-admin' || role === 'Super Admin')) {
    return true;
  }

  // Check if user has the specific permission
  return authUser.value.permissions?.includes(permission) || false;
};

const hasRole = (role: string): boolean => {
  if (!authUser.value) return false;
  return authUser.value.roles?.includes(role) || false;
};

const canEditMessage = (message: any): boolean => {
  if (!authUser.value || !message) return false;

  // User can edit their own messages
  if (message.sender_id === authUser.value.id) return true;

  // Admins can edit all messages
  if (hasRole('super-admin') || hasRole('admin') || hasPermission('edit all messages')) {
    return true;
  }

  return false;
};

const canDeleteMessage = (message: any): boolean => {
  if (!authUser.value || !message) return false;

  // User can delete their own messages
  if (message.sender_id === authUser.value.id) return true;

  // Admins can delete all messages
  if (hasRole('super-admin') || hasRole('admin') || hasPermission('delete all messages')) {
    return true;
  }

  return false;
};

const chatContainer = ref<HTMLElement | null>(null);
const search = ref<string>('');
const conversations = ref<Conversation[]>([]);
const groups = ref<Group[]>([]);
const selectedConversation = ref<Conversation | null>(null);
const selectedGroup = ref<Group | null>(null);
const messages = ref<Message[]>([]);
const loading = ref<boolean>(false);
const showConversationList = ref(true);
const isCounterpartTyping = ref(false)
const isGroupsTab = ref(false)
const showCreateGroupModal = ref(false)
// typing poll removed
let currentGroupChannel: any = null;

// Minimize handling (local, in addition to parent emit)
const isMinimized = ref(false)
const toggleMinimize = () => {
  isMinimized.value = true
  emit('minimize')
}
const restoreFromMinimize = () => {
  isMinimized.value = false
}

const form = useForm({
  receiver_id: null as number | null,
  message: '',
  attachment: null as File | null,
});

const attachmentInput = ref<HTMLInputElement | null>(null);
const messageInput = ref<HTMLTextAreaElement | null>(null);
const attachmentPreviewUrl = ref<string | null>(null)
const isSending = ref(false)

// Reply / Edit / Draft state
// Reply feature removed
const editingMessageId = ref<number | null>(null)
const editingText = ref<string>('')

// Draft persistence per conversation
const draftKey = computed(() => selectedConversation.value ? `chat:draft:${selectedConversation.value.id}` : null)
const saveDraft = () => {
  if (!draftKey.value) return
  localStorage.setItem(draftKey.value, form.message)
}
const loadDraft = () => {
  if (!draftKey.value) { form.message = ''; return }
  const d = localStorage.getItem(draftKey.value)
  form.message = d !== null ? d : ''
}

// --- Window Width for Responsiveness ---
const currentWindowWidth = ref(0);

const updateWindowWidth = () => {
  currentWindowWidth.value = window.innerWidth;
};

onMounted(() => {
  updateWindowWidth();
  window.addEventListener('resize', updateWindowWidth);
  modalWidth.value = currentWindowWidth.value > 1024 ? 800 : Math.min(currentWindowWidth.value - 40, 600);
  modalHeight.value = currentWindowWidth.value > 768 ? 600 : Math.min(currentWindowWidth.value - 80, 500);

  // Subscribe to user channel for real-time updates
  try {
    window.Echo.private(`users.${authUser.value.id}`)
      .listen('NewMessage', (e: { message: Message }) => {
        if (selectedConversation.value?.id === e.message.sender_id || selectedConversation.value?.id === e.message.receiver_id) {
          messages.value.push(e.message);
          scrollToBottom();
        }
      })
      // Reactions removed
      .listen('MessageUpdated', (e: { message: Message }) => {
        const index = messages.value.findIndex(m => m.id === e.message.id);
        if (index !== -1) {
          messages.value[index] = e.message;
        }
      })
      .listen('MessageDeleted', (e: { messageId: number }) => {
        messages.value = messages.value.filter(m => m.id !== e.messageId);
      });
  } catch (error) {
    console.warn('Broadcasting not available:', error);
  }
});

onUnmounted(() => {
  window.removeEventListener('resize', updateWindowWidth);

  // Unsubscribe from user channel
  try {
    window.Echo.private(`users.${authUser.value.id}`)
      .stopListening('NewMessage')
      
      .stopListening('MessageUpdated')
      .stopListening('MessageDeleted');

    // Unsubscribe from current group channel if any
    if (currentGroupChannel) {
      window.Echo.private(`groups.${selectedGroup.value.id}`)
        .stopListening('NewMessage')
        
        .stopListening('MessageUpdated')
        .stopListening('MessageDeleted');
    }
  } catch (error) {
    console.warn('Error unsubscribing from channels:', error);
  }
});

// --- Resizing Logic ---
const modalRef = ref<HTMLElement | null>(null);
const modalWidth = ref(800);
const modalHeight = ref(600);
const isResizing = ref(false);
let startX = 0, startY = 0, startWidth = 0, startHeight = 0;

const startResize = (e: MouseEvent) => {
  e.preventDefault();
  isResizing.value = true;
  startX = e.clientX;
  startY = e.clientY;
  startWidth = modalRef.value?.offsetWidth || modalWidth.value;
  startHeight = modalRef.value?.offsetHeight || modalHeight.value;

  document.addEventListener('mousemove', onMouseMove);
  document.addEventListener('mouseup', onMouseUp);
};

const onMouseMove = (e: MouseEvent) => {
  if (!isResizing.value) return;
  const dx = e.clientX - startX;
  const dy = e.clientY - startY;

  const minModalWidth = currentWindowWidth.value < 768 ? currentWindowWidth.value * 0.9 : 500;
  const maxModalWidth = currentWindowWidth.value * 0.95;
  const minModalHeight = currentWindowWidth.value < 768 ? currentWindowWidth.value * 0.8 : 400;
  const maxModalHeight = currentWindowWidth.value * 0.95;

  modalWidth.value = Math.min(maxModalWidth, Math.max(minModalWidth, startWidth + dx));
  modalHeight.value = Math.min(maxModalHeight, Math.max(minModalHeight, startHeight + dy));
};

const onMouseUp = () => {
  isResizing.value = false;
  document.removeEventListener('mousemove', onMouseMove);
  document.removeEventListener('mouseup', onMouseUp);
};

// --- Attachment Handling ---
const triggerAttachmentInput = () => {
  attachmentInput.value?.click();
};

const handleAttachmentChange = (e: Event) => {
  const target = e.target as HTMLInputElement;
  form.attachment = target.files?.[0] || null;
  if (attachmentPreviewUrl.value) URL.revokeObjectURL(attachmentPreviewUrl.value)
  if (form.attachment && form.attachment.type.startsWith('image/')) {
    attachmentPreviewUrl.value = URL.createObjectURL(form.attachment)
  } else {
    attachmentPreviewUrl.value = null
  }
};

const removeAttachment = () => {
  form.attachment = null;
  if (attachmentInput.value) attachmentInput.value.value = '';
  if (attachmentPreviewUrl.value) URL.revokeObjectURL(attachmentPreviewUrl.value)
  attachmentPreviewUrl.value = null
};

// --- Emoji Handling (Fixed) ---
const showEmojiPicker = ref(false);
const handleEmojiClick = () => {
  showEmojiPicker.value = !showEmojiPicker.value;
};

const onEmojiSelect = (emoji: { i: string }) => {
  if (messageInput.value) {
    const start = messageInput.value.selectionStart || 0;
    const end = messageInput.value.selectionEnd || 0;
    const text = form.message;
    const emojiChar = emoji.i || '';
    form.message = text.substring(0, start) + emojiChar + text.substring(end);

    nextTick(() => {
      if (messageInput.value) {
        messageInput.value.selectionEnd = start + emojiChar.length;
        messageInput.value.focus();
      }
    });
  }
  showEmojiPicker.value = false;
};

// --- Fetching Logic (Fixed) ---
const fetchMessages = async (recipientId: number | null = null, searchQuery: string = '') => {
  loading.value = true;
  try {
    const response = await axios.get(route('messages.data', { recipient: recipientId, search: searchQuery }));
    const sections = response.data.sections || [];
    const fetchedConversations: Conversation[] = sections.flatMap((section: any) =>
      (section.conversations || []).map((c: any) => ({
        ...c,
        section_key: section.key,
        section_label: section.label,
        type: c.type || section.type,
      }))
    );

    conversations.value = fetchedConversations;

    // Handle selected conversation
    let newSelectedConversation = null as any;
    if (recipientId) {
      newSelectedConversation = fetchedConversations.find(c => c.id === recipientId) || null;
    } else {
      newSelectedConversation = response.data.selectedConversation || null;
      if (!newSelectedConversation && props.initialConversationId) {
        newSelectedConversation = fetchedConversations.find(c => c.id === props.initialConversationId) || null;
      }
      if (!newSelectedConversation && fetchedConversations.length > 0) {
        const userId = authUser.value?.id
        newSelectedConversation = fetchedConversations.find(c => c.id !== userId) || fetchedConversations[0];
      }
    }

    if (selectedConversation.value?.id !== newSelectedConversation?.id) {
      selectedConversation.value = newSelectedConversation;
    }

    messages.value = (response.data.messages || []).filter((msg: any) => 
      msg && typeof msg === 'object' && msg.id && msg.created_at
    );

    form.receiver_id = selectedConversation.value?.id || null;
    scrollToBottom();
  } catch (error) {
    console.error('Error fetching messages:', error);
    messages.value = [];
  } finally {
    loading.value = false;
  }
};

const fetchGroups = async (searchQuery: string = '') => {
  loading.value = true;
  try {
    const response = await axios.get(route('groups.list', { search: searchQuery }));
    groups.value = response.data.data;
  } catch (error) {
    console.error('Error fetching groups:', error);
    groups.value = [];
  } finally {
    loading.value = false;
  }
};

const fetchGroupMessages = async (groupId: number) => {
  loading.value = true;
  try {
    const response = await axios.get(route('groups.messages.index', { group: groupId }));
    messages.value = response.data.data;
    scrollToBottom();
  } catch (error) {
    console.error('Error fetching group messages:', error);
    messages.value = [];
  } finally {
    loading.value = false;
  }
};

const selectConversation = (convoId: number) => {
  selectedConversation.value = conversations.value.find(c => c.id === convoId) || null;
  if (selectedConversation.value) {
    showConversationList.value = false
    fetchMessages(convoId)
  }
};

const selectGroup = (groupId: number) => {
  selectedGroup.value = groups.value.find(g => g.id === groupId) || null;
  if (selectedGroup.value) {
    showConversationList.value = false;
    fetchGroupMessages(groupId);
  }
};

// --- Watchers ---
watch(() => props.isOpen, (newVal: boolean) => {
  if (newVal) {
    if (currentWindowWidth.value < 768) showConversationList.value = true;
    if (isGroupsTab.value) {
      fetchGroups();
    } else {
      fetchMessages(props.initialConversationId || null);
    }
    startTypingPoll();
  } else {
    // Reset modal size
    modalWidth.value = 800;
    modalHeight.value = 600;
  }
});

watch(isGroupsTab, (isGroup) => {
  if (isGroup) {
    fetchGroups(search.value)
  } else {
    fetchMessages(null, search.value)
  }
})

const debouncedSearch = debounce((val:string) => {
  if (isGroupsTab.value) {
    fetchGroups(val)
  } else if (selectedConversation.value) {
    fetchMessages(selectedConversation.value.id, val)
  } else {
    fetchMessages(null, val)
  }
}, 300)

watch(search, (value: string) => {
  debouncedSearch(value)
});

const scrollToBottom = () => {
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
  });
};

watch(() => messages.value, scrollToBottom, { deep: true });

const previousSelectedConversationId = ref<number | null>(null);
watch(() => selectedConversation.value, (newVal: Conversation | null) => {
  if (!newVal) return;
  const newId = newVal.id;
  
  if (newId !== previousSelectedConversationId.value) {
    form.receiver_id = newId;
    fetchMessages(newId);
    if (currentWindowWidth.value < 768) showConversationList.value = false;
    startTypingPoll();
    // load saved draft for this conversation
    nextTick(loadDraft)
  }
  previousSelectedConversationId.value = newId;
}, { immediate: true });

watch(selectedGroup, (newGroup, oldGroup) => {
  try {
    // Unsubscribe from old group channel
    if (oldGroup && currentGroupChannel) {
      window.Echo.private(`groups.${oldGroup.id}`)
        .stopListening('NewMessage')
        .stopListening('MessageReacted')
        .stopListening('MessageUpdated')
        .stopListening('MessageDeleted');
    }

    // Subscribe to new group channel
    if (newGroup) {
      currentGroupChannel = window.Echo.private(`groups.${newGroup.id}`)
      .listen('NewMessage', (e: { message: Message }) => {
        messages.value.push(e.message);
        scrollToBottom();
      })
      // reactions removed
/*
        const message = messages.value.find(m => m.id === e.reaction.reactable_id);
        if (message) {
          if (!message.reactions) message.reactions = [];
          const existingReactionIndex = message.reactions.findIndex(r => r.id === e.reaction.id);
          if (existingReactionIndex !== -1) {
            message.reactions[existingReactionIndex] = e.reaction;
          } else {
            message.reactions.push(e.reaction);
          }
        }
      })
      .listen('MessageUpdated', (e: { message: Message }) => {
        const index = messages.value.findIndex(m => m.id === e.message.id);
        if (index !== -1) {
          messages.value[index] = e.message;
        }
      })
      .listen('MessageDeleted', (e: { messageId: number }) => {
        messages.value = messages.value.filter(m => m.id !== e.messageId);
      });
    }
  } catch (error) {
    console.warn('Error managing group channels:', error);
  }
});

// --- Message Submission (Fixed) ---
const submit = async () => {
  if (isGroupsTab.value) {
    if (!selectedGroup.value || (!form.message.trim() && !form.attachment) || isSending.value) return;

    try {
      isSending.value = true;
      const formData = new FormData();
      formData.append('message', form.message);
      if (form.attachment) formData.append('attachment', form.attachment);
      // reply_to removed

      await axios.post(route('groups.messages.store', { group: selectedGroup.value.id }), formData);

      form.message = '';
      form.attachment = null;
      if (attachmentInput.value) attachmentInput.value.value = '';
      if (attachmentPreviewUrl.value) URL.revokeObjectURL(attachmentPreviewUrl.value);
      attachmentPreviewUrl.value = null;
      // reply target removed

      fetchGroupMessages(selectedGroup.value.id);
    } catch (error) {
      console.error('Error sending group message:', error);
      alert('Failed to send message. Please try again.');
    } finally {
      isSending.value = false;
      nextTick(() => messageInput.value?.focus());
      if (messageInput.value && messageInput.value.style) {
        messageInput.value.style.height = 'auto';
      }
    }
  } else {
    if (!form.receiver_id || (!form.message.trim() && !form.attachment) || isSending.value) return;

    try {
      isSending.value = true;
      const fd = new FormData();
      fd.append('receiver_id', String(form.receiver_id));
      fd.append('message', form.message);
      if (form.attachment) fd.append('attachment', form.attachment);
      // reply_to removed

      await axios.post(route('messages.store'), fd);

      // Reset local form state
      form.message = '';
      form.attachment = null;
      if (attachmentInput.value) attachmentInput.value.value = '';
      if (attachmentPreviewUrl.value) URL.revokeObjectURL(attachmentPreviewUrl.value);
      attachmentPreviewUrl.value = null;
      // reply target removed

      // Refresh current conversation
      fetchMessages(selectedConversation.value?.id || null);
    } catch (e) {
      console.error('Error sending direct message:', e);
      alert('Failed to send message. Please try again.');
    } finally {
      isSending.value = false;
      nextTick(() => messageInput.value?.focus());
      if (messageInput.value && messageInput.value.style) {
        messageInput.value.style.height = 'auto';
      }
    }
  }
};

// --- Typing indicator ---
// typing removed
const sendTyping = () => { return }
  if (!form.receiver_id || isGroupsTab.value) return;
  // Fire-and-forget typing ping
  axios.post(route('messages.typing'), { receiver_id: form.receiver_id }).catch(() => {})
  // Save draft as user types
  saveDraft()
}

// typing removed
const startTypingPoll = () => { return }
  // Temporarily disabled to prevent 403 errors
  return;

  if (!selectedConversation.value || isGroupsTab.value) return;
  if (typingPollHandle) window.clearInterval(typingPollHandle);
  const poll = async () => {
    if (!selectedConversation.value) return;
    try {
      const res = await axios.get(route('messages.typingStatus', selectedConversation.value.id))
      isCounterpartTyping.value = !!(res.data?.typing)
    } catch (_) { /* ignore */ }
  }
  poll();
  typingPollHandle = window.setInterval(poll, 2000)
}

// Actions: edit, delete (reply removed)
const startEdit = (m: Message) => {
  editingMessageId.value = m.id
  editingText.value = m.message || ''
}
const cancelEdit = () => {
  editingMessageId.value = null
  editingText.value = ''
}
const saveEdit = async (m: Message) => {
  if (!editingMessageId.value) return
  try {
    if (isGroupsTab.value) {
      await axios.patch(route('groups.messages.update', { group: selectedGroup.value.id, message: m.id }), { message: editingText.value })
    } else {
      await axios.patch(route('messages.update', m.id), { message: editingText.value })
    }
    const idx = messages.value.findIndex(x => x.id === m.id)
    if (idx !== -1) messages.value[idx].message = editingText.value
    cancelEdit()
  } catch (e) {
    alert('Failed to edit message')
  }
}
const deleteMessage = async (m: Message) => {
  try {
    if (isGroupsTab.value) {
      await axios.delete(route('groups.messages.destroy', { group: selectedGroup.value.id, message: m.id }))
    } else {
      await axios.delete(route('messages.destroy', m.id))
    }
    messages.value = messages.value.filter(x => x.id !== m.id)
  } catch (e) {
    alert('Failed to delete message')
  }
}

const deleteForMe = async (m: Message) => {
  if (isGroupsTab.value) {
    // Not supported for groups with current schema
    alert('Delete for me is available for direct messages only.');
    return;
  }
  try {
    await axios.post(route('messages.hide', m.id))
    messages.value = messages.value.filter(x => x.id !== m.id)
  } catch (e) {
    alert('Failed to remove message for you')
  }
}

// Context menu state and handlers
const contextMenu = ref<{
  visible: boolean,
  x: number,
  y: number,
  msg: any|null,
  owned: boolean,
  canEdit: boolean,
  canDelete: boolean
}>({
  visible: false,
  x: 0,
  y: 0,
  msg: null,
  owned: false,
  canEdit: false,
  canDelete: false
})
const longPressTimer = ref<number | null>(null);

const onTouchStart = (event: TouchEvent, message: Message) => {
  longPressTimer.value = window.setTimeout(() => {
    openContextMenu(event, message);
  }, 500); // 500ms for long press
};

const onTouchEnd = () => {
  if (longPressTimer.value) {
    clearTimeout(longPressTimer.value);
    longPressTimer.value = null;
  }
};

const openContextMenu = (e: MouseEvent | TouchEvent, m: any) => {
  e.preventDefault();
  e.stopPropagation();

  // Close any existing context menu first
  closeContextMenu();

  const owned = m.sender_id === authUser.value?.id;
  const canEdit = canEditMessage(m);
  const canDelete = canDeleteMessage(m);

  let x: number, y: number;

  if (e instanceof TouchEvent) {
    const touch = e.touches[0] || e.changedTouches[0];
    x = touch.clientX;
    y = touch.clientY;
  } else {
    x = e.clientX;
    y = e.clientY;
  }

  // Adjust position if menu would go off-screen
  const menuWidth = 180;
  const menuHeight = 150;

  if (x + menuWidth > window.innerWidth) {
    x = window.innerWidth - menuWidth - 10;
  }

  if (y + menuHeight > window.innerHeight) {
    y = window.innerHeight - menuHeight - 10;
  }

  // Ensure minimum distance from edges
  x = Math.max(10, x);
  y = Math.max(10, y);

  contextMenu.value = {
    visible: true,
    x,
    y,
    msg: m,
    owned,
    canEdit,
    canDelete
  };

  // Close menu when clicking outside - use nextTick to avoid immediate closure
  nextTick(() => {
    setTimeout(() => {
        const handleClickOutside = (event: Event) => {
          const target = event.target as Element;
          if (!target.closest('.context-menu')) {
            closeContextMenu();
          }
        };

        document.addEventListener('click', handleClickOutside, { once: true });
        document.addEventListener('contextmenu', handleClickOutside, { once: true });
    }, 0);
  });
}

const closeContextMenu = () => {
  contextMenu.value.visible = false;
  contextMenu.value.msg = null;
  contextMenu.value.owned = false;
  contextMenu.value.canEdit = false;
  contextMenu.value.canDelete = false;
}
const onCtxReply = () => {
  if (contextMenu.value.msg) {
    startReply(contextMenu.value.msg);
    // Focus on message input
    nextTick(() => {
      const messageInput = document.querySelector('#message-input') as HTMLTextAreaElement;
      if (messageInput) {
        messageInput.focus();
      }
    });
  }
  closeContextMenu();
}

const onCtxEdit = () => {
  if (contextMenu.value.msg && contextMenu.value.canEdit) {
    startEdit(contextMenu.value.msg);
    // Focus on message input
    nextTick(() => {
      const messageInput = document.querySelector('#message-input') as HTMLTextAreaElement;
      if (messageInput) {
        messageInput.focus();
        messageInput.select();
      }
    });
  }
  closeContextMenu();
}

const onCtxDelete = async () => {
  if (contextMenu.value.msg && contextMenu.value.canDelete) {
    // Show confirmation dialog
    (await confirmDialog({
      title: 'Delete message',
      message: 'Are you sure you want to delete this message for everyone? This cannot be undone.',
      confirmText: 'Delete',
      cancelText: 'Cancel',
    })) {
      try {
        await deleteMessage(contextMenu.value.msg);
      } catch (error) {
        console.error('Failed to delete message:', error);
        alert('Failed to delete message. Please try again.');
      }
    }
  }
  closeContextMenu();
}
const onCtxReact = (emoji: string) => {
  if (!contextMenu.value.msg) return;
  if (isGroupsTab.value) {
    axios.post(route('groups.messages.react', { group: selectedGroup.value.id, message: contextMenu.value.msg.id }), { emoji }).catch(()=>{})
  } else {
    axios.post(route('messages.react', contextMenu.value.msg.id), { emoji }).catch(()=>{})
  }
  closeContextMenu()
}

const groupedReactions = (reactions: any[]) => {
  if (!reactions) return {}
  return reactions.reduce((acc, reaction) => {
    if (!acc[reaction.emoji]) {
      acc[reaction.emoji] = []
    }
    acc[reaction.emoji].push(reaction)
    return acc
  }, {} as Record<string, any[]>)
}

const handleGroupCreated = (newGroup: Group) => {
  groups.value.push(newGroup);
  selectedGroup.value = newGroup;
  showCreateGroupModal.value = false;
  showConversationList.value = false; // Hide conversation list to show chat area
};

// --- In-thread Search ---
const showThreadSearch = ref(false)
const threadSearchText = ref('')
const threadSearchBusy = ref(false)
const threadSearchResults = ref<Message[]>([])
let threadSearchTimer: number | null = null

const openThreadSearch = () => {
  showThreadSearch.value = !showThreadSearch.value
  threadSearchText.value = ''
  threadSearchResults.value = []
}

const runThreadSearch = async () => {
  if (isGroupsTab.value && !selectedGroup.value) return
  if (!isGroupsTab.value && !selectedConversation.value) return
  if (!threadSearchText.value.trim()) { threadSearchResults.value = []; return }
  threadSearchBusy.value = true
  try {
    if (isGroupsTab.value) {
      const res = await axios.get(route('groups.messages.search', { group: selectedGroup.value!.id, q: threadSearchText.value.trim() }))
      threadSearchResults.value = res.data?.results || []
    } else {
      const res = await axios.get(route('messages.thread.search', { user: selectedConversation.value!.id, q: threadSearchText.value.trim() }))
      threadSearchResults.value = res.data?.results || []
    }
  } catch (_) {
    threadSearchResults.value = []
  } finally {
    threadSearchBusy.value = false
  }
}

watch(threadSearchText, (val) => {
  if (threadSearchTimer) window.clearTimeout(threadSearchTimer)
  threadSearchTimer = window.setTimeout(runThreadSearch, 350) as unknown as number
})

// Jump to message within modal and highlight
const highlightedId = ref<number|null>(null)
const jumpToThreadMessage = async (id: number) => {
  showThreadSearch.value = false
  await nextTick()
  const el = document.getElementById(`modal-msg-${id}`)
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'center' })
    highlightedId.value = id
    window.setTimeout(() => { highlightedId.value = null }, 2000)
  }
}
</script>

<template>
  <div v-if="props.isOpen" class="fixed bottom-6 right-6 z-50 chat-modal-widget"
       :class="{ 'fixed inset-0 !bottom-0 !right-0 !top-0 !left-0': currentWindowWidth < 768 }" >
    <div
      ref="modalRef"
      class="relative liquidGlass-wrapper text-foreground rounded-2xl shadow-2xl flex flex-col overflow-hidden transition-all duration-200 ease-in-out"
      :style="{
        width: currentWindowWidth < 768 ? '95vw' : (modalWidth + 'px'),
        height: currentWindowWidth < 768 ? '90vh' : (modalHeight + 'px'),
        maxWidth: currentWindowWidth < 768 ? '95vw' : '100%',
        maxHeight: currentWindowWidth < 768 ? '90vh' : '100%',
      }"
      v-show="!isMinimized"
    >
      <div class="p-3 border-b border-white/30 dark:border-white/10 flex justify-between items-center bg-transparent liquidGlass-content">
        <h2 class="text-lg font-semibold flex items-center gap-2">
          <MessageSquareText class="w-5 h-5 text-primary" /> GeraChat 
        </h2>
        <div class="flex items-center gap-2">
          <button @click="toggleMinimize" class="text-foreground/80 hover:text-foreground p-1 rounded-full hover:bg-white/10 dark:hover:bg-white/10 transition">
            <Minus class="w-5 h-5" />
          </button>
          <button @click="emit('close')" class="text-foreground/80 hover:text-foreground p-1 rounded-full hover:bg-white/10 dark:hover:bg-white/10 transition">
            <X class="w-5 h-5" />
          </button>
        </div>
      </div>

      <div class="flex-grow flex min-h-0"
           :class="{
             'flex-col': currentWindowWidth < 768,
             'md:flex-row': currentWindowWidth >= 768
           }">

        <!-- Conversation List -->
        <div v-show="showConversationList || currentWindowWidth >= 768"
             class="w-full md:w-1/3 flex flex-col bg-transparent border-r md:border-white/10 overflow-hidden liquidGlass-content"
             :class="{ 'border-b': currentWindowWidth < 768 }">
          <div class="p-4 border-b border-white/30 dark:border-white/10 relative">
            <div class="flex gap-2 mb-4">
              <button @click="isGroupsTab = false" class="btn-glass btn-glass-sm w-1/2" :class="!isGroupsTab ? 'ring-1 ring-primary/40' : ''">People</button>
              <button @click="isGroupsTab = true" class="btn-glass btn-glass-sm w-1/2" :class="isGroupsTab ? 'ring-1 ring-primary/40' : ''">Groups</button>
            </div>
            <button
              v-if="isGroupsTab && (hasPermission('create groups') || hasRole('admin') || hasRole('super-admin'))"
              @click="showCreateGroupModal = true"
              class="w-full mb-4 btn-glass"
            >
              Create New Group
            </button>
            <div class="relative search-glass">
              <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-foreground/70 w-5 h-5" />
              <input
                type="text"
                v-model="search"
                placeholder="Search conversations..."
                class="pl-10 pr-10 form-input w-full rounded-full border border-white/30 dark:border-white/10 px-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary focus:outline-none bg-white/50 dark:bg-white/5 text-foreground placeholder:text-foreground/60"
              />
              <button v-if="search" @click="search = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-foreground/70 hover:text-foreground p-1 rounded-full">
                <X class="w-4 h-4" />
              </button>
            </div>
          </div>
          <div class="flex-grow overflow-y-auto custom-scrollbar">
            <template v-if="!isGroupsTab">
              <div v-if="loading && conversations.length === 0" class="p-4 text-center text-foreground/70">Loading conversations...</div>
              <div v-else-if="conversations.length === 0" class="p-4 text-center text-foreground/70">No conversations found.</div>
                <div
                  v-else
                  v-for="convo in conversations"
                  :key="convo.id"
                  @click="selectConversation(convo.id)"
                  class="flex items-center gap-3 p-4 border-b border-white/20 dark:border-white/10 hover:bg-white/10 dark:hover:bg-white/10 transition cursor-pointer"
                  :class="{ 'bg-white/5 dark:bg-white/5': selectedConversation?.id === convo.id }"
                >
                  <img 
                    v-if="convo.profile_photo_url" 
                    :src="convo.profile_photo_url" 
                    :alt="convo.name" 
                    class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                  >
                  <div v-else class="w-10 h-10 rounded-full bg-white/20 dark:bg-white/10 flex items-center justify-center text-white text-sm font-medium flex-shrink-0">
                    {{ convo.name.charAt(0).toUpperCase() }}
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="truncate text-foreground" :class="convo.unread > 0 ? 'font-semibold' : 'font-medium'">{{ convo.name }}</p>
                    <p class="text-xs text-foreground/70 truncate">{{ convo.staff?.position || 'User' }}</p>
                  </div>
                  <div v-if="convo.unread > 0" class="ml-2 inline-flex items-center justify-center rounded-full bg-indigo-600 text-white text-xs px-2 py-0.5">
                    {{ convo.unread }}
                  </div>
                </div>
            </template>
            <template v-else>
              <div v-if="loading && groups.length === 0" class="p-4 text-center text-foreground/70">Loading groups...</div>
              <div v-else-if="groups.length === 0" class="p-4 text-center text-foreground/70">No groups found.</div>
              <div
                v-else
                v-for="group in groups"
                :key="group.id"
                @click="selectGroup(group.id)"
                class="flex items-center gap-3 p-4 border-b border-white/20 dark:border-white/10 hover:bg-white/10 dark:hover:bg-white/10 transition cursor-pointer"
                :class="{ 'bg-white/5 dark:bg-white/5': selectedGroup?.id === group.id }"
              >
                <div class="w-10 h-10 rounded-full bg-white/20 dark:bg-white/10 flex items-center justify-center text-white text-sm font-medium flex-shrink-0">
                  {{ group.name.charAt(0).toUpperCase() }}
                </div>
                <div class="min-w-0 flex-1">
                  <p class="truncate font-medium text-foreground">{{ group.name }}</p>
                  <p class="text-xs text-foreground/70 truncate">{{ group.members_count }} members</p>
                </div>
              </div>
            </template>
            </div>
        </div>

        <!-- Chat Area -->
        <div v-show="!showConversationList || currentWindowWidth >= 768"
             class="w-full md:w-2/3 flex flex-col bg-transparent min-h-0">
          <template v-if="loading && !selectedConversation && !selectedGroup">
            <div class="flex-grow flex items-center justify-center">
              <div class="text-center text-muted-foreground">
                <svg class="animate-spin h-8 w-8 text-primary mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2">Loading messages...</p>
              </div>
            </div>
</template>
          
          <template v-else-if="!selectedConversation && !selectedGroup">
            <div class="flex-grow flex items-center justify-center p-6 text-center">
              <div class="text-muted-foreground">
                <MessageSquareText class="w-12 h-12 mx-auto mb-4 text-primary" />
                <p class="text-lg font-semibold">Select a conversation or group</p>
                <p class="text-sm">Choose a person or group from the left pane to start chatting.</p>
              </div>
            </div>
</template>
          
          <template v-else-if="selectedConversation || selectedGroup">
            <!-- Chat Header -->
            <div class="p-4 border-b border-gray-200/70 dark:border-gray-700/60 bg-transparent flex justify-between items-center">
              <h2 v-if="selectedConversation" class="text-lg font-semibold">{{ selectedConversation.name }}</h2>
              <h2 v-if="selectedGroup" class="text-lg font-semibold">{{ selectedGroup.name }}</h2>
              <div class="hidden md:flex items-center gap-3 text-sm text-muted-foreground">
                <button @click="openThreadSearch" class="p-2 rounded-md hover:bg-accent/50" title="Search">
                  <Search class="w-4 h-4" />
                </button>
                <a v-if="selectedConversation" :href="route('messages.export', selectedConversation.id)" class="px-2 py-1 rounded-md border border-gray-200 dark:border-gray-700 hover:bg-accent">Download Chat</a>
              </div>
              <button 
                v-if="currentWindowWidth < 768" 
                @click="showConversationList = true" 
                class="text-muted-foreground hover:text-foreground transition-colors px-3 py-1 rounded-md border border-border"
              >
                Back
              </button>
            </div>

            <!-- In-thread search panel -->
            <div v-if="showThreadSearch" class="px-4 pt-2">
              <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-transparent p-3">
                <div class="flex items-center gap-2">
                  <input type="text" v-model="threadSearchText" placeholder="Search this conversation..." class="flex-1 px-3 py-2 text-sm rounded-md border border-gray-200 dark:border-gray-700 bg-transparent" />
                  <button class="px-3 py-2 text-sm rounded-md border border-gray-200 dark:border-gray-700" @click="threadSearchText=''; threadSearchResults=[]">Clear</button>
                </div>
                <div v-if="threadSearchBusy" class="mt-2 text-xs text-muted-foreground">Searching…</div>
                <div v-else class="mt-2 max-h-40 overflow-y-auto">
                  <div v-if="!threadSearchResults.length && threadSearchText" class="text-xs text-muted-foreground">No matches.</div>
                  <button v-for="r in threadSearchResults" :key="r.id" class="w-full text-left rounded-md p-2 hover:bg-muted/50" @click="jumpToThreadMessage(r.id)">
                    <div class="text-xs text-muted-foreground">{{ r.created_at }}</div>
                    <div class="text-sm truncate" v-if="r.message">{{ r.message }}</div>
                    <div class="text-sm text-muted-foreground" v-else>Attachment: {{ r.attachment_filename }}</div>
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Messages Area -->
            <div ref="chatContainer" class="flex-grow p-6 overflow-y-auto space-y-4 custom-scrollbar min-h-0">
                            <div v-if="messages.length === 0 && loading" class="text-center text-muted-foreground py-10">Loading messages...</div>
              <div v-else-if="messages.length === 0 && !loading" class="text-center text-muted-foreground py-10">
                <p v-if="selectedConversation">No messages yet with {{ selectedConversation.name }}.</p>
                <p v-if="selectedGroup">No messages yet in {{ selectedGroup.name }}.</p>
                <p>Start the conversation!</p>
              </div>
              
              <!-- Messages -->
              <div 
                v-for="message in messages"
                :key="message.id"
                class="flex"
                :class="[message.sender_id === authUser.id ? 'justify-end' : 'justify-start']"
              >
                <div
                  @contextmenu.prevent="openContextMenu($event, message)"
                  @touchstart="onTouchStart($event, message)"
                  @touchend="onTouchEnd"
                  @touchcancel="onTouchEnd"
                  class="group max-w-[75%] rounded-xl px-3 py-2 text-sm shadow relative"
                  :class="{
                    'bg-blue-500 text-white self-end': message.sender_id === authUser.id,
                    'bg-gray-200 text-gray-800 self-start': message.sender_id !== authUser.id,
                    'opacity-80': message.isOptimistic
                  }"
                >
                  <!-- 3-dot menu button -->
                  <button
                    @click.stop.prevent="openContextMenu($event, message)"
                    class="absolute top-1 right-1 opacity-100 transition-opacity p-1 rounded-full hover:bg-black/10 dark:hover:bg-white/10"
                    :class="{
                      'text-white hover:bg-white/20': message.sender_id === authUser.id,
                      'text-gray-600 hover:bg-gray-300': message.sender_id !== authUser.id
                    }"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                    </svg>
                  </button>
                  <!-- Reply preview (quoted) -->
                  

                  <!-- Edit mode -->
                  <template v-if="editingMessageId === message.id">
                    <textarea v-model="editingText" class="w-full rounded-md p-2 text-sm text-gray-900" rows="2"></textarea>
                    <div class="flex justify-end gap-2 mt-2">
                      <button class="text-xs px-2 py-1 rounded bg-gray-200" @click="cancelEdit">Cancel</button>
                      <button class="text-xs px-2 py-1 rounded bg-indigo-600 text-white" @click="saveEdit(message)">Save</button>
                    </div>
</template>
                  <template v-else>
                  <p class="break-words" v-if="message.message">{{ message.message }}</p>
              <div v-if="message.attachment || message.attachment_url" class="mt-2 p-2 bg-white/20 rounded-md" :id="`modal-msg-${message.id}`">
                      <a 
                        :href="message.attachment_url || (message.attachment && message.attachment.url)" 
                        target="_blank" 
                        download 
                        class="flex items-center gap-2 hover:underline"
                        :class="message.sender_id === authUser.id ? 'text-white' : 'text-gray-700'"
                      >
                          <template v-if="(message.attachment_mime_type && message.attachment_mime_type.startsWith('image/')) || 
                                         (message.attachment && message.attachment.mime_type?.startsWith('image/'))">
                            <Image class="w-5 h-5" />
</template>
                          <template v-else>
                            <File class="w-5 h-5" />
</template>
                          <span>{{ (message.attachment_filename || (message.attachment && message.attachment.filename)) || 'Download File' }}</span>
                      </a>
                  </div>
</template>
                  <div class="flex items-center justify-end gap-1 mt-1">
                    <span 
                      class="text-[11px]"
                      :class="message.sender_id === authUser.id ? 'text-blue-200' : 'text-gray-500'"
                    >
                      {{ format(new Date(message.created_at), 'p') }}
                    </span>
                    <template v-if="message.sender_id === authUser.id">
                      <Clock v-if="message.isOptimistic" class="w-3.5 h-3.5 text-blue-200" />
                      <CheckCheck v-else-if="message.read_at" class="w-3.5 h-3.5 text-blue-200" title="Read" />
                      <Check v-else class="w-3.5 h-3.5 text-blue-200" title="Sent" />
</template>
                  </div>

                  <!-- Reactions -->
                  <div v-if="message.reactions && message.reactions.length > 0" class="absolute -bottom-3 right-2 flex items-center gap-1">
                    <div v-for="(group, emoji) in groupedReactions(message.reactions)" :key="emoji" class="flex items-center bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full px-1.5 py-0.5 text-xs shadow-sm">
                      <span>{{ emoji }}</span>
                      <span class="ml-1 text-gray-600 dark:text-gray-300 text-[10px]">{{ group.length }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Message Input Area -->
            <div class="p-4 border-t border-gray-200/70 dark:border-gray-700/60 bg-transparent relative">
              <div v-if="showEmojiPicker" class="absolute bottom-full right-0 mb-2 z-10 shadow-lg rounded-lg overflow-hidden">
                <EmojiPicker :native="true" @select="onEmojiSelect" />
              </div>

              <!-- Reply target chip -->
              <div v-if="replyTarget" class="absolute -top-7 left-4 right-4 text-xs flex items-center justify-between bg-accent/40 px-2 py-1 rounded">
                <span>Replying to: {{ replyTarget.message?.slice(0,40) || 'Attachment' }}</span>
                <button class="text-muted-foreground hover:text-foreground" @click="clearReply"><X class="w-3 h-3" /></button>
              </div>

              <form @submit.prevent="submit" class="flex items-end gap-2">
                <img 
                  :src="authUser.profile_photo_url || `https://ui-avatars.com/api/?name=${authUser.name}&background=random`" 
                  :alt="authUser.name" 
                  class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                >

                <input
                  type="file"
                  ref="attachmentInput"
                  @change="handleAttachmentChange"
                  class="hidden"
                  accept="image/*, application/pdf, .doc, .docx, .xlsx, .txt"
                />
                <button
                  type="button"
                  @click="triggerAttachmentInput"
                  class="text-muted-foreground hover:text-foreground p-2 rounded-full hover:bg-muted transition flex-shrink-0"
                  title="Attach File"
                >
                  <Paperclip class="w-5 h-5" />
                </button>
                <button
                  type="button"
                  @click="handleEmojiClick"
                  class="text-muted-foreground hover:text-foreground p-2 rounded-full hover:bg-muted transition flex-shrink-0"
                  title="Emoji"
                >
                  <Smile class="w-5 h-5" />
                </button>

                <div v-if="form.attachment" class="flex items-center gap-3 flex-shrink-0 max-w-[220px]">
                  <div v-if="attachmentPreviewUrl" class="w-12 h-12 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                    <img :src="attachmentPreviewUrl" alt="preview" class="w-full h-full object-cover" />
                  </div>
                  <div v-else class="text-xs bg-muted rounded-full px-3 py-1 overflow-hidden whitespace-nowrap text-ellipsis">
                    {{ form.attachment.name }}
                  </div>
                  <button type="button" @click="removeAttachment" class="text-muted-foreground hover:text-foreground">
                    <X class="w-4 h-4" />
                  </button>
                </div>

                <textarea
                  ref="messageInput"
                  v-model="form.message"
                  placeholder="Type a message… (Enter to send, Shift+Enter for newline)"
                  class="w-full flex-grow resize-none rounded-xl border border-input px-4 py-2.5 text-sm leading-5 shadow-sm focus:ring-2 focus:ring-primary focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder:text-gray-500 dark:placeholder:text-gray-400 min-h-[44px] max-h-[140px] overflow-y-auto"
                  rows="1"
                  @input="(event) => { sendTyping(); const target = event.target as HTMLTextAreaElement; if (target && target.style) { target.style.height = 'auto'; target.style.height = Math.min(target.scrollHeight, 140) + 'px'; } }"
                  @keydown.enter.prevent="!$event.shiftKey ? submit() : null"
                />
                
                <button
                  type="submit"
                  :disabled="form.processing || (!form.message.trim() && !form.attachment) || (!selectedConversation?.id && !selectedGroup?.id)"
                  class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-primary hover:bg-primary/90 text-primary-foreground disabled:opacity-40 disabled:cursor-not-allowed transition duration-150 ease-in-out flex-shrink-0"
                >
                  <svg v-if="isSending" class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                  <Send v-else class="w-5 h-5" />
                </button>
              </form>
            </div>
</template>
        </div>
      </div>
      
      <!-- Resize Handle -->
      <div
        v-if="currentWindowWidth >= 768"
        @mousedown="startResize"
        class="resize-handle absolute -bottom-2 -right-2 w-8 h-8 cursor-se-resize flex items-center justify-center text-muted-foreground hover:text-foreground transition"
      >
        <GripVertical class="w-5 h-5 rotate-45" />
      </div>
      <!-- Minimized pill -->
    </div>
    <button
      v-if="isMinimized && currentWindowWidth >= 768"
      @click="restoreFromMinimize"
      class="mt-2 ml-auto flex items-center gap-2 rounded-full px-4 py-2 bg-white/80 dark:bg-gray-900/70 backdrop-blur border border-gray-200/60 dark:border-gray-700/60 shadow hover:shadow-md transition"
    >
      <MessageSquareText class="w-4 h-4 text-primary" />
      <span class="text-sm">Open Chat</span>
    </button>
  </div>

  
  
  <!-- Context Menu (teleport to body to avoid z-index/overflow issues) -->
  <teleport to="body">
    <div v-if="contextMenu.visible"
         :style="{
           top: contextMenu.y + 'px',
           left: contextMenu.x + 'px',
           zIndex: 2147483647
         }"
         class="context-menu fixed bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl text-sm min-w-[180px] overflow-hidden"
         @click.stop>
      <div class="py-1">
                <button
          v-if="contextMenu.canEdit"
          class="flex items-center w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          @click="onCtxEdit"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
          </svg>
          Edit
        </button>
        <button
          v-if="!isGroupsTab && (contextMenu.owned || (contextMenu.msg && (contextMenu.msg.sender_id === authUser.id || contextMenu.msg.receiver_id === authUser.id)))"
          class="flex items-center w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
          @click="() => { if (contextMenu.msg) deleteForMe(contextMenu.msg) }"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l6 6m-6-6l6-6"/>
          </svg>
          Delete for me
        </button>
        <button
          v-if="contextMenu.canDelete"
          class="flex items-center w-full text-left px-4 py-2 hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 transition-colors"
          @click="onCtxDelete"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
          Delete for everyone
        </button>
      </div>
      
    </div>
  </teleport>

  <CreateGroupModal
    :show="showCreateGroupModal"
    @close="showCreateGroupModal = false"
    @groupCreated="handleGroupCreated"
  />
</template>

<style scoped>
.chat-modal-widget {
  z-index: 9999;
}

.resize-handle {
  z-index: 10000;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: hsl(var(--background));
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: hsl(var(--muted));
  border-radius: 5px;
  border: 2px solid hsl(var(--background));
  background-clip: content-box;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: hsl(var(--muted-foreground));
}
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: hsl(var(--muted)) hsl(var(--background));
}

/* Message bubble styling */
.bg-blue-500 {
  background-color: #3b82f6;
}

.bg-gray-200 {
  background-color: #e5e7eb;
}
</style>
