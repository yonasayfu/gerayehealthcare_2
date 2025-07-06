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
  // Get the first role name, or default to ''
  role: props.user.roles[0]?.name || '',
});

const submit = () => {
  form.put(route('admin.users.update', props.user.id));
};
</script>

<template>
  <Head :title="`Edit User: ${user.name}`" />
  <AppLayout>
    <div class="p-6 space-y-6">
      <div>
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit User: {{ user.name }}</h1>
        <p class="text-sm text-muted-foreground">{{ user.email }}</p>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 max-w-xl">
        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <label for="role" class="block text-sm font-medium text-gray-900 dark:text-white">Assign Role</label>
            <select
              id="role"
              v-model="form.role"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700"
            >
              <option v-for="roleName in roles" :key="roleName" :value="roleName">
                {{ roleName }}
              </option>
            </select>
             <div v-if="form.errors.role" class="text-red-500 text-sm mt-1">{{ form.errors.role }}</div>
          </div>

          <div class="flex justify-end space-x-4 border-t dark:border-gray-700 pt-6">
            <Link :href="route('admin.users.index')" class="px-4 py-2 border rounded-md text-sm font-medium">Cancel</Link>
            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium disabled:opacity-50">
              {{ form.processing ? 'Saving...' : 'Update Role' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
