<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import PrimaryButton from '@/components/ui/button/Button.vue'

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Platforms', href: route('admin.marketing-platforms.index') },
  { title: 'Create', href: route('admin.marketing-platforms.create') },
]

const form = useForm({
  name: '',
  api_endpoint: '',
  api_credentials: '',
  is_active: true,
});

const submit = () => {
  form.post(route('admin.marketing-platforms.store'));
};
</script>

<template>
  <Head title="Create Marketing Platform" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create Marketing Platform</h1>
        <p class="text-sm text-muted-foreground">Fill in the details to create a new marketing platform.</p>
      </div>

      <form @submit.prevent="submit" class="space-y-6 bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
        <div>
          <InputLabel for="name" value="Platform Name" />
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
          <InputLabel for="api_endpoint" value="API Endpoint" />
          <TextInput
            id="api_endpoint"
            type="url"
            class="mt-1 block w-full"
            v-model="form.api_endpoint"
          />
          <InputError class="mt-2" :message="form.errors.api_endpoint" />
        </div>

        <div>
          <InputLabel for="api_credentials" value="API Credentials" />
          <TextInput
            id="api_credentials"
            type="text"
            class="mt-1 block w-full"
            v-model="form.api_credentials"
          />
          <InputError class="mt-2" :message="form.errors.api_credentials" />
        </div>

        <div class="flex items-center gap-2">
          <input type="checkbox" id="is_active" v-model="form.is_active" class="rounded border-gray-300 text-cyan-600 shadow-sm focus:ring-cyan-500" />
          <InputLabel for="is_active" value="Is Active" />
          <InputError class="mt-2" :message="form.errors.is_active" />
        </div>

        <div class="flex items-center justify-end">
          <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Create Platform
          </PrimaryButton>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
