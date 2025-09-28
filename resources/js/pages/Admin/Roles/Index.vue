<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { confirmDialog } from '@/lib/confirm'
import AppLayout from '@/layouts/AppLayout.vue';
import { Edit3, Trash2 } from 'lucide-vue-next';

defineProps<{
  roles: {
    data: Array<{
      id: number;
      name: string;
      permissions: Array<{ id: number; name: string }>;
    }>;
  };
}>();

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Role Management', href: route('admin.roles.index') },
];

async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete Role',
    message: 'Are you sure you want to delete this role? This action cannot be undone.',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.roles.destroy', id), {
    preserveScroll: true,
  });
}
</script>

<template>
  <Head title="Role Management" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Role Management</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Create and manage user roles and their permissions.</p>
          </div>
          <Link :href="route('admin.roles.create')" class="btn-glass btn-glass-sm">
            + Add New Role
          </Link>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3">Role Name</th>
              <th class="px-6 py-3">Permissions</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="role in roles.data" :key="role.id" class="border-b dark:border-gray-700">
              <td class="px-6 py-4 font-medium">{{ role.name }}</td>
              <td class="px-6 py-4">
                <!-- THIS IS THE FIX -->
                <div v-if="role.name === 'Super Admin'" class="flex flex-wrap gap-1">
                   <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full dark:bg-green-900 dark:text-green-200">
                    All Permissions Granted
                  </span>
                </div>
                <div v-else class="flex flex-wrap gap-1">
                  <span v-for="permission in role.permissions" :key="permission.id" class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full dark:bg-blue-900 dark:text-blue-200">
                    {{ permission.name.replace(/-/g, ' ') }}
                  </span>
                   <span v-if="!role.permissions || role.permissions.length === 0" class="text-gray-400 italic">No permissions assigned</span>
                </div>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link :href="route('admin.roles.edit', role.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-gray-700" title="Edit">
                    <Edit3 class="w-4 h-4 text-blue-600" />
                  </Link>
                  <button @click="destroy(role.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-gray-700" title="Delete">
                    <Trash2 class="w-4 h-4 text-red-600" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
