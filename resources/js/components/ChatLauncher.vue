<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { MessageSquare, X } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';

const showPanel = ref(false);
const openPanel = () => (showPanel.value = true);
const closePanel = () => (showPanel.value = false);

const inboxUrl = computed(() => route('messages.inbox'));

// Ensure Ziggy routes exist before iframe render
const page = usePage();
</script>

<template>
  <div class="fixed bottom-4 right-4 z-40">
    <button
      type="button"
      class="relative flex h-12 w-12 items-center justify-center rounded-full bg-primary text-primary-foreground shadow-lg transition hover:scale-105 focus:outline-none focus:ring-2 focus:ring-primary/60 focus:ring-offset-2"
      @click="openPanel"
      title="Open Messages"
    >
      <MessageSquare class="h-6 w-6" />
    </button>

    <Transition name="chat-panel">
      <div
        v-if="showPanel"
        class="fixed inset-0 z-50 flex items-end justify-end bg-black/40 backdrop-blur-sm"
      >
        <div class="h-[85vh] w-full max-w-4xl rounded-t-3xl bg-background shadow-2xl">
          <div class="flex items-center justify-between border-b px-4 py-3">
            <div class="text-sm font-semibold text-muted-foreground">Messages</div>
            <button
              type="button"
              class="rounded-full p-1 text-muted-foreground transition hover:bg-muted hover:text-foreground"
              @click="closePanel"
            >
              <X class="h-5 w-5" />
            </button>
          </div>
          <iframe
            class="h-[calc(85vh-3.25rem)] w-full border-0"
            :src="inboxUrl"
            title="Messages Inbox"
          ></iframe>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.chat-panel-enter-active,
.chat-panel-leave-active {
  transition: opacity 0.2s ease;
}
.chat-panel-enter-from,
.chat-panel-leave-to {
  opacity: 0;
}
</style>
