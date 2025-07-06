<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
</script>

<template>
  <Head title="Coming Soon" />
  <AppLayout>
    <div class="p-6">
      <div class="rounded-lg bg-muted/40 p-8 text-center shadow-sm">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Feature Coming Soon</h1>
        <p class="mt-2 text-muted-foreground">This section is under construction. Please check back later!</p>
      </div>
    </div>
  </AppLayout>
</template>