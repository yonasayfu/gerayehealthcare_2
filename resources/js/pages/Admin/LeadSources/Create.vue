<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import PrimaryButton from '@/components/ui/button/Button.vue'

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Lead Sources', href: route('admin.lead-sources.index') },
  { title: 'Create', href: route('admin.lead-sources.create') },
]

const form = useForm({
  name: '',
  category: '',
  description: '',
  is_active: true,
});

const submit = () => {
  form.post(route('admin.lead-sources.store'));
};

const categories = [
  'Organic',
  'Paid',
  'Referral',
  'Direct',
  'Social Media',
  'Email Marketing',
  'Offline Ads',
];
</script>

<template>
  <Head title="Create Lead Source" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create Lead Source</h1>
        <p class="text-sm text-muted-foreground">Fill in the details to create a new lead source.</p>
      </div>

      <form @submit.prevent="submit" class="space-y-6 bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
        <div>
          <InputLabel for="name" value="Source Name" />
          <TextInput
            id="name"
            type="text"
            class="mt-1 block w-full"
            v-model="form.name"
            required
            autofocus
          />
          <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div>
          <InputLabel for="category" value="Category" />
          <select
            id="category"
            v-model="form.category"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
          >
            <option value="">Select a Category</option>
            <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.category" />
        </div>

        <div>
          <InputLabel for="description" value="Description" />
          <textarea
            id="description"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
            v-model="form.description"
          ></textarea>
          <InputError class="mt-2" :message="form.errors.description" />
        </div>

        <div class="flex items-center gap-2">
          <input type="checkbox" id="is_active" v-model="form.is_active" class="rounded border-gray-300 text-cyan-600 shadow-sm focus:ring-cyan-500" />
          <InputLabel for="is_active" value="Is Active" />
          <InputError class="mt-2" :message="form.errors.is_active" />
        </div>

        <div class="flex items-center justify-end">
          <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Create Lead Source
          </PrimaryButton>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
