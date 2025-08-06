<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Shield } from 'lucide-vue-next';

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
      <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">{{ user.name }}</h2>
        <p class="text-sm text-muted-foreground">{{ user.email }}</p>

        <div class="mt-6">
          <h3 class="text-lg font-medium text-gray-800 dark:text-white">Assigned Roles</h3>
          <div class="mt-2 flex flex-wrap gap-2">
