<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const form = useForm({
  // User fields
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  // Staff fields
  first_name: '',
  last_name: '',
  position: '',
  department: '',
  phone: '',
  hire_date: '',
});

const submit = () => {
  form.post(route('admin.users.store'));
};
</script>

<template>
  <Head title="Add New Staff User" />
  <AppLayout>
    <div class="p-6 space-y-6">
       <div>
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Add New Staff User</h1>
        <p class="text-sm text-muted-foreground">This will create both a user account and a linked staff profile.</p>
      </div>
      <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-8">
        <!-- User Account Details -->
        <div class="space-y-4">
            <h3 class="font-medium text-lg">User Account Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name">User Name</label>
                    <input id="name" type="text" v-model="form.name" class="mt-1 block w-full rounded-md"/>
                    <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                </div>
                <div>
                    <label for="email">Login Email</label>
                    <input id="email" type="email" v-model="form.email" class="mt-1 block w-full rounded-md"/>
                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                </div>
                <div>
                    <label for="password">Password</label>
                    <input id="password" type="password" v-model="form.password" class="mt-1 block w-full rounded-md"/>
                    <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
                </div>
                <div>
                    <label for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" type="password" v-model="form.password_confirmation" class="mt-1 block w-full rounded-md"/>
                </div>
            </div>
        </div>

        <!-- Staff Profile Details -->
        <div class="space-y-4 border-t dark:border-gray-700 pt-8">
            <h3 class="font-medium text-lg">Staff Profile Details</h3>
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name">First Name</label>
                    <input id="first_name" type="text" v-model="form.first_name" class="mt-1 block w-full rounded-md"/>
                    <div v-if="form.errors.first_name" class="text-red-500 text-sm mt-1">{{ form.errors.first_name }}</div>
                </div>
                 <div>
                    <label for="last_name">Last Name</label>
                    <input id="last_name" type="text" v-model="form.last_name" class="mt-1 block w-full rounded-md"/>
                    <div v-if="form.errors.last_name" class="text-red-500 text-sm mt-1">{{ form.errors.last_name }}</div>
                </div>
                 <div>
                    <label for="phone">Phone Number</label>
                    <input id="phone" type="text" v-model="form.phone" class="mt-1 block w-full rounded-md"/>
                    <div v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</div>
                </div>
                 <div>
                    <label for="position">Position / Job Title</label>
                    <input id="position" type="text" v-model="form.position" class="mt-1 block w-full rounded-md"/>
                    <div v-if="form.errors.position" class="text-red-500 text-sm mt-1">{{ form.errors.position }}</div>
                </div>
                 <div>
                    <label for="department">Department</label>
                    <input id="department" type="text" v-model="form.department" class="mt-1 block w-full rounded-md"/>
                    <div v-if="form.errors.department" class="text-red-500 text-sm mt-1">{{ form.errors.department }}</div>
                </div>
                 <div>
                    <label for="hire_date">Hire Date</label>
                    <input id="hire_date" type="date" v-model="form.hire_date" class="mt-1 block w-full rounded-md"/>
                    <div v-if="form.errors.hire_date" class="text-red-500 text-sm mt-1">{{ form.errors.hire_date }}</div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4 border-t dark:border-gray-700 pt-6">
          <Link :href="route('admin.users.index')" class="px-4 py-2 border rounded-md text-sm font-medium">Cancel</Link>
          <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium disabled:opacity-50">
            {{ form.processing ? 'Creating...' : 'Create Staff User' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
