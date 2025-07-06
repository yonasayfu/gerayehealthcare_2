<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Edit3, Trash2, Shield } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

defineProps<{
  users: {
    data: Array<{
      id: number;
      name: string;
      email: string;
      roles: Array<{ id: number; name: string }>;
    }>;
  };
  filters: {
    search: string;
  };
}>();

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'User Management', href: route('admin.users.index') },
];

const search = ref('');

watch(search, debounce((value) => {
    router.get(route('admin.users.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this user? This will also delete their associated staff profile.')) {
    router.delete(route('admin.users.destroy', id), {
      preserveScroll: true,
    });
  }
}
</script>

<template>
  <Head title="User Management" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">User Management</h1>
          <p class="text-sm text-muted-foreground">Assign roles and manage all system users.</p>
        </div>
        <Link :href="route('admin.users.create')" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
          + Add New Staff User
        </Link>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3">User Name</th>
              <th class="px-6 py-3">Email</th>
              <th class="px-6 py-3">Assigned Roles</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users.data" :key="user.id" class="border-b dark:border-gray-700">
              <td class="px-6 py-4 font-medium">{{ user.name }}</td>
              <td class="px-6 py-4 text-gray-500">{{ user.email }}</td>
              <td class="px-6 py-4">
                <div class="flex flex-wrap gap-2">
                  <span v-for="role in user.roles" :key="role.id" class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full dark:bg-indigo-900 dark:text-indigo-200">
                    <Shield class="h-3 w-3" />
                    {{ role.name }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link :href="route('admin.users.edit', user.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-gray-700" title="Edit Role">
                    <Edit3 class="w-4 h-4 text-blue-600" />
                  </Link>
                  <button @click="destroy(user.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-gray-700" title="Delete User">
                    <Trash2 class="w-4 h-4 text-red-600" />
                  </button>
                </div>
              </td>
            </tr>
             <tr v-if="users.data.length === 0">
              <td colspan="4" class="text-center px-6 py-4 text-gray-400">No users found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
