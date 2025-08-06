<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Shield, Mail, ArrowLeft } from 'lucide-vue-next';

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
        <Link :href="route('admin.users.index')" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">
          <ArrowLeft class="h-4 w-4" />
          Back to User Management
        </Link>
      </div>
      <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
        <div class="flex items-center space-x-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ user.name }}</h2>
            <div class="flex items-center gap-2 text-sm text-muted-foreground mt-1">
              <Mail class="h-4 w-4" />
              <a :href="`mailto:${user.email}`" class="hover:underline">{{ user.email }}</a>
            </div>
          </div>
        </div>

        <div class="mt-8 border-t pt-6">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Assigned Roles</h3>
          <div class="mt-4 flex flex-wrap gap-2">
            <span v-for="role in user.roles" :key="role.id" class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-semibold rounded-full dark:bg-indigo-900 dark:text-indigo-200">
              <Shield class="h-4 w-4" />
              {{ role.name }}
            </span>
