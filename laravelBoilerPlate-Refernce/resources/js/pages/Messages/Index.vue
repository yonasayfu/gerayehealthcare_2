<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
  conversations: Array<any>;
  selectedConversation?: any;
  messages: Array<any>;
  unreadCount: number;
}>();
</script>

<template>
  <AppLayout>
    <Head title="Messages" />
    <div class="grid gap-4 p-4 md:grid-cols-3">
      <aside class="space-y-2 rounded border p-3">
        <h2 class="text-sm font-semibold">Conversations</h2>
        <ul class="space-y-1 text-sm">
          <li v-for="c in conversations" :key="c.id" class="flex items-center justify-between">
            <span>{{ c.name }}</span>
            <span v-if="c.unread_count" class="rounded bg-red-600 px-2 py-0.5 text-white">{{ c.unread_count }}</span>
          </li>
        </ul>
      </aside>
      <section class="md:col-span-2 rounded border p-3">
        <div v-if="!selectedConversation" class="text-sm text-muted-foreground">Select a conversation</div>
        <div v-else class="space-y-2">
          <h3 class="text-sm font-semibold">Chat with {{ selectedConversation.name }}</h3>
          <div class="space-y-2">
            <div v-for="m in messages" :key="m.id" class="rounded border p-2">
              <div class="text-xs text-muted-foreground">{{ m.created_at }}</div>
              <div class="text-sm">{{ m.message }}</div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </AppLayout>
  </template>

