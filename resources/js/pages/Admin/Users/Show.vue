<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Shield } from 'lucide-vue-next';
import ShowHeader from '@/components/ShowHeader.vue'

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
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow overflow-hidden">
        <ShowHeader title="User Details" :subtitle="`User: ${user.name || user.email || user.id}`">
          <template #actions>
            <Link :href="route('admin.users.index')" class="btn-glass btn-glass-sm">Back</Link>
          </template>
        </ShowHeader>

        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
          <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
          <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
          <p class="text-gray-600 dark:text-gray-400 print-document-title">User Details</p>
          <hr class="my-3 border-gray-300 print:my-2">
        </div>

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
    </div>
  </AppLayout>
</template>
