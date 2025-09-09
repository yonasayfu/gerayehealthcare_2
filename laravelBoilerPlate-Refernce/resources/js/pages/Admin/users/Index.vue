<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Edit3, Trash2, Shield, Eye } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { confirmDialog } from '@/lib/confirm';
import debounce from 'lodash/debounce';
import Pagination from '@/components/Pagination.vue';

const props = defineProps<{
  users: {
    data: Array<{
      id: number;
      name: string;
      email: string;
      roles: Array<{ id: number; name: string }>;
    }>;
    links: Array<any>;
  };
  filters: {
    search: string;
    per_page: string;
  };
}>();

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'User Management', href: route('admin.users.index') },
];

const search = ref(props.filters.search || '');
const perPage = ref(props.filters.per_page || '5');

watch([search, perPage], debounce((newValues) => {
    router.get(route('admin.users.index'), {
        search: newValues[0],
        per_page: newValues[1],
    }, {
        preserveState: true,
        replace: true,
    });
}, 300));

async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete User',
    message: 'Are you sure you want to delete this user? This will also delete their associated staff profile.',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.users.destroy', id), {
    preserveScroll: true,
  });
}
</script>

<template>
  <Head title="User Management" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">User Management</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Assign roles and manage all system users.</p>
          </div>
          <Link :href="route('admin.users.create')" class="btn-glass btn-glass-sm">
            + Add New Staff User
          </Link>
        </div>
      </div>

       <!-- Filters -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="relative w-full md:w-1/3">
          <input
            type="text"
            v-model="search"
            placeholder="Search by name or email..."
            class="form-input w-full rounded-md border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm"
          />
           <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-1.85z" />
          </svg>
        </div>
        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Items per page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 bg-white text-gray-900 sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
        </div>
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
                  <Link
                    :href="route('admin.users.show', user.id)"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.users.edit', user.id)"
                    class="inline-flex items-center p-2 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-gray-700"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="destroy(user.id)"
                    class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
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
      <Pagination :links="users.links" />
    </div>
  </AppLayout>
</template>