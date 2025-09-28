<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { X, Send, Paperclip } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const props = defineProps<{
  senderName?: string;
  senderId?: number;
}>();

const emit = defineEmits<{
  close: [];
}>();

const isOpen = ref(true);
const message = ref('');
const attachment = ref<File | null>(null);
const isSending = ref(false);

const form = useForm({
  receiver_id: props.senderId,
  message: '',
  attachment: null as File | null,
});

const handleAttachmentChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    attachment.value = target.files[0];
    form.attachment = target.files[0];
  }
};

const sendMessage = () => {
  if (!message.value.trim() && !attachment.value) return;

  isSending.value = true;
  form.message = message.value;

  form.post(route('messages.store'), {
    preserveScroll: true,
    onSuccess: () => {
      message.value = '';
      attachment.value = null;
      isSending.value = false;
      close();
    },
    onError: () => {
      isSending.value = false;
    }
  });
};

const close = () => {
  isOpen.value = false;
  emit('close');
};

const handleKeyDown = (event: KeyboardEvent) => {
  if (event.key === 'Escape') {
    close();
  }
};

onMounted(() => {
  document.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="close"></div>

    <!-- Modal -->
    <div class="relative bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-md mx-4 border border-white/20 dark:border-gray-700/50 backdrop-blur-lg">
      <div class="p-4 border-b border-white/20 dark:border-gray-700/50 flex items-center space-x-4">
        <img :src="props.sender?.profile_photo_url || '/images/default-avatar.png'" alt="Avatar" class="w-10 h-10 rounded-full" />
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
          Message {{ senderName || 'User' }}
        </h3>
        <Button variant="ghost" size="icon" @click="close" class="ml-auto">
          <X class="h-5 w-5" />
        </Button>
      </div>

      <div class="p-6 space-y-6">
        <div>
          <textarea
            v-model="message"
            placeholder="Type your message..."
            class="w-full min-h-[120px] rounded-lg border border-input bg-background px-4 py-3 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-shadow duration-200 ease-in-out shadow-sm focus:shadow-md"
            @keydown.enter.exact.prevent="sendMessage"
            @keydown.enter.shift.exact="message += '\n'"
          />
          <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-right">
            {{ message.length }} / 2000
          </div>
        </div>

        <div v-if="attachment" class="flex items-center justify-between bg-gray-100 dark:bg-gray-800 p-3 rounded-lg">
          <div class="flex items-center space-x-3">
            <Paperclip class="h-5 w-5 text-gray-500 dark:text-gray-400" />
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ attachment.name }}</span>
          </div>
          <Button variant="ghost" size="icon" @click="attachment = null; form.attachment = null">
            <X class="h-4 w-4" />
          </Button>
        </div>

        <div class="flex items-center justify-between">
          <label class="cursor-pointer p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors">
            <Paperclip class="h-6 w-6 text-gray-500 dark:text-gray-400" />
            <Input
              type="file"
              class="hidden"
              accept="image/*,application/pdf,.doc,.docx,.txt"
              @change="handleAttachmentChange"
            />
          </label>

          <Button
            @click="sendMessage"
            :disabled="isSending || (!message.trim() && !attachment)"
            class="bg-indigo-600 hover:bg-indigo-700"
          >
            <Send class="h-4 w-4 mr-2" />
            Send
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>
