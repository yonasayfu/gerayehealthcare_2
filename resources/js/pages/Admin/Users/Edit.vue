<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
  user: {
    id: number;
    name: string;
    email: string;
    roles: Array<{ name: string }>;
  };
  roles: string[];
}>();

const form = useForm({
  roles: (props.user.roles && props.user.roles.map(r => r.name)) || [],
});

const submit = () => {
  form.put(route('admin.users.update', props.user.id));
};

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'User Management', href: route('admin.users.index') },
  { title: 'Edit User', href: route('admin.users.edit', props.user.id) },
];
</script>

<template>
  <Head title="Edit User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit User</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update user information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Name</label>
              <input type="text" :value="props.user.name" disabled class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg block w-full p-2.5 bg-gray-50 dark:bg-gray-900/50" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
              <input type="email" :value="props.user.email" disabled class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg block w-full p-2.5 bg-gray-50 dark:bg-gray-900/50" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Roles</label>
              <select v-model="form.roles" multiple class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800 min-h-32">
                <option v-for="r in props.roles" :key="r" :value="r">{{ r }}</option>
              </select>
              <div v-if="form.errors.roles" class="text-red-500 text-sm mt-1">{{ form.errors.roles }}</div>
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.users.index')" class="btn-glass btn-glass-sm">Cancel</Link>
            <button type="submit" :disabled="form.processing" class="btn-glass btn-glass-sm">
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
