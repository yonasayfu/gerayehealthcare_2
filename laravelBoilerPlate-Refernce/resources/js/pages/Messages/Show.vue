<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{ message: any }>();
</script>

<template>
  <AppLayout>
    <Head title="Message" />
    <div class="p-4">
      <div class="rounded border p-4">
        <div class="mb-2 text-xs text-muted-foreground">{{ message?.created_at }}</div>
        <div class="text-sm">{{ message?.message }}</div>
      </div>
    </div>
  </AppLayout>
</template>

