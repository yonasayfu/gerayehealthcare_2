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
  role: (props.user.roles && props.user.roles[0]?.name) || '',
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
                  class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
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
            <button type="submit" @click="submit" :disabled="form.processing" class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
              {{ form.processing ? 'Saving...' : 'Update Role' }}
            </button>
        </div>

    </div>
  </AppLayout>
</template>
