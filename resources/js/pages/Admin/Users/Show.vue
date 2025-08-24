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
      <div class="flex justify-start mb-4 print:hidden">
        <Link :href="route('admin.users.index')" class="btn-glass btn-glass-sm">Back to List</Link>
      </div>

<<<<<<< HEAD
      <!-- compact liquid glass header (now full-width and same sizing as main card) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">User Details</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">User: {{ user.name || user.id }}</p>
=======
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow overflow-hidden">
        <!-- liquidGlass header -->
        <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
          <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
          <div class="liquidGlass-content flex items-center justify-between p-6">
            <div class="print:hidden">
              <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">User Details</h3>
              <p class="text-sm text-gray-600 dark:text-gray-300">User: {{ user.name || user.email || user.id }}</p>
            </div>
>>>>>>> yonas/feature/modern-refacotr
          </div>
        </div>

        <!-- content -->
        <div class="p-6 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-muted-foreground">Name</p>
              <p class="font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Email</p>
              <p class="font-medium text-gray-900 dark:text-gray-100">{{ user.email }}</p>
            </div>
            <div class="md:col-span-2">
              <p class="text-sm text-muted-foreground mb-1">Roles</p>
              <div class="flex flex-wrap gap-2">
                <span v-for="role in user.roles" :key="role.id" class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full dark:bg-indigo-900 dark:text-indigo-200">
                  <Shield class="h-3 w-3" /> {{ role.name }}
                </span>
                <span v-if="!user.roles || user.roles.length === 0" class="text-gray-400 text-sm italic">No roles assigned</span>
              </div>
            </div>
          </div>
        </div>
      </div>
  </AppLayout>
</template>
