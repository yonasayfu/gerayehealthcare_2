<script setup lang="ts">
import { ref, watch, nextTick, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Send } from 'lucide-vue-next';
import { format } from 'date-fns';
import type { BreadcrumbItemType } from '@/types';

const props = defineProps<{
  conversations: Array<any>;
  selectedConversation: any;
  messages: Array<any>;
}>();

const page = usePage();
const authUser = computed(() => page.props.auth.user);

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Messages', href: route('messages.index') },
];

const chatContainer = ref<HTMLElement | null>(null);

const form = useForm({
  receiver_id: props.selectedConversation?.id || null,
  message: '',
});

// Automatically scroll to the bottom of the chat window when new messages are loaded
const scrollToBottom = () => {
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
  });
};

watch(() => props.messages, scrollToBottom, { deep: true, immediate: true });
watch(() => props.selectedConversation, (newVal) => {
    form.receiver_id = newVal?.id || null;
});

const submit = () => {
  if (!form.receiver_id) return;
  form.post(route('messages.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('message');
      scrollToBottom();
    },
  });
};
</script>

<template>
  <Head title="Messages" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="h-[calc(100vh-100px)] flex">
      <!-- Conversation List (Left Pane) -->
      <div class="w-1/3 border-r border-gray-200 dark:border-gray-700 flex flex-col">
        <div class="p-4 border-b dark:border-gray-700">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Conversations</h2>
        </div>
        <div class="flex-grow overflow-y-auto">
          <Link
            v-for="convo in conversations"
            :key="convo.id"
            :href="route('messages.index', { recipient: convo.id })"
            class="block p-4 border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
            :class="{ 'bg-indigo-100 dark:bg-indigo-900/50': selectedConversation?.id === convo.id }"
          >
            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ convo.name }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ convo.staff?.position || 'User' }}</p>
          </Link>
        </div>
      </div>

      <!-- Chat Window (Right Pane) -->
      <div class="w-2/3 flex flex-col">
        <template v-if="selectedConversation">
          <!-- Chat Header -->
          <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ selectedConversation.name }}</h2>
          </div>

          <!-- Messages -->
          <div ref="chatContainer" class="flex-grow p-6 overflow-y-auto space-y-4">
            <div
              v-for="message in messages"
              :key="message.id"
              class="flex"
              :class="[message.sender_id === authUser.id ? 'justify-end' : 'justify-start']"
            >
              <div
                class="max-w-md rounded-lg px-4 py-2"
                :class="{
                  'bg-indigo-600 text-white': message.sender_id === authUser.id,
                  'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200': message.sender_id !== authUser.id,
                }"
              >
                <p>{{ message.message }}</p>
                <p class="text-xs mt-1 opacity-75">{{ format(new Date(message.created_at), 'p') }}</p>
              </div>
            </div>
             <div v-if="messages.length === 0" class="text-center text-gray-500">
                No messages yet. Start the conversation!
            </div>
          </div>

          <!-- Message Input Form -->
          <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <form @submit.prevent="submit" class="flex items-center gap-4">
              <input
                v-model="form.message"
                type="text"
                placeholder="Type a message..."
                class="flex-grow form-input w-full rounded-md border border-gray-300 px-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100"
                autocomplete="off"
              />
              <button
                type="submit"
                :disabled="form.processing || !form.message"
                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white disabled:opacity-50 transition"
              >
                <Send class="w-5 h-5" />
              </button>
            </form>
          </div>
        </template>
        <template v-else>
          <div class="flex-grow flex items-center justify-center">
            <p class="text-gray-500">Select a conversation to start messaging.</p>
          </div>
        </template>
      </div>
    </div>
  </AppLayout>
</template>