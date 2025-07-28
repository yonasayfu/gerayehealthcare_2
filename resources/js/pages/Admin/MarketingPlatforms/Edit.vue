<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import PrimaryButton from '@/components/ui/button/Button.vue'

interface MarketingPlatform {
  id: number;
  name: string;
  api_endpoint: string;
  api_credentials: string;
  is_active: boolean;
}

const props = defineProps<{
  marketingPlatform: MarketingPlatform;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Platforms', href: route('admin.marketing-platforms.index') },
  { title: props.marketingPlatform.name, href: route('admin.marketing-platforms.show', props.marketingPlatform.id) },
  { title: 'Edit', href: route('admin.marketing-platforms.edit', props.marketingPlatform.id) },
]

const form = useForm({
  name: props.marketingPlatform.name,
  api_endpoint: props.marketingPlatform.api_endpoint,
  api_credentials: props.marketingPlatform.api_credentials,
  is_active: props.marketingPlatform.is_active,
});

const submit = () => {
  form.put(route('admin.marketing-platforms.update', props.marketingPlatform.id));
};
</script>

<template>
  <Head :title="`Edit ${marketingPlatform.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Marketing Platform</h1>
        <p class="text-sm text-muted-foreground">Update the details of the marketing platform.</p>
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
            Update Platform
          </PrimaryButton>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
