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
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit User: {{ user.name }}
            </h3>
            <Link :href="route('admin.users.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
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
            </form>
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <div class="flex justify-end space-x-4">
              <Link :href="route('admin.users.index')" class="px-4 py-2 border rounded-md text-sm font-medium">Cancel</Link>
              <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium disabled:opacity-50">
                {{ form.processing ? 'Saving...' : 'Update Role' }}
              </button>
            </div>
        </div>

    </div>
  </AppLayout>
</template>
