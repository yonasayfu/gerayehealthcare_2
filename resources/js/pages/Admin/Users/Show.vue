<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
  user: {
    id: number;
    name: string;
    email: string;
    roles: Array<{ id: number; name: string }>;
  };
}>();

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'User Management', href: route('admin.users.index') },
  { title: props.user.name, href: route('admin.users.show', props.user.id) },
];
</script>

<template>
  <Head :title="`User: ${user.name}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex justify-start mb-4">
        <Link :href="route('admin.users.index')" class="btn-glass btn-glass-sm">
          Back to List
        </Link>
      </div>
          <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

      <!-- compact liquid glass header (now full-width and same sizing as main card) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">User Details</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">User: {{ user.name || user.id }}</p>
          </div>
          <!-- top actions intentionally removed to avoid duplication; see footer -->
        </div>
      </div>
        </div>
      </div>
  </AppLayout>
</template>
