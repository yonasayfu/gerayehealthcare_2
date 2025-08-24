<template>
  <Head title="Edit Corporate Client" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="liquidGlass-wrapper relative overflow-hidden rounded-xl p-5">
        <div class="absolute inset-0 pointer-events-none bg-gradient-to-br from-white/10 to-white/5"></div>
        <div class="relative flex items-center justify-between">
          <div>
            <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Corporate Client</h1>
            <p class="text-sm text-muted-foreground">Update the details of this corporate client.</p>
          </div>
          <Link :href="route('admin.corporate-clients.index')" class="btn-glass btn-glass-sm">Back to List</Link>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-900 overflow-hidden shadow rounded-lg">
        <form @submit.prevent="submit" class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="organization_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Organization Name</label>
              <input type="text" id="organization_name" v-model="form.organization_name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-cyan-600 focus:ring-cyan-600" required>
              <div v-if="form.errors.organization_name" class="text-red-500 text-sm mt-1">{{ form.errors.organization_name }}</div>
            </div>

            <div>
              <label for="contact_person" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Contact Person</label>
              <input type="text" id="contact_person" v-model="form.contact_person" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-cyan-600 focus:ring-cyan-600">
              <div v-if="form.errors.contact_person" class="text-red-500 text-sm mt-1">{{ form.errors.contact_person }}</div>
            </div>

            <div>
              <label for="contact_email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Contact Email</label>
              <input type="email" id="contact_email" v-model="form.contact_email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-cyan-600 focus:ring-cyan-600">
              <div v-if="form.errors.contact_email" class="text-red-500 text-sm mt-1">{{ form.errors.contact_email }}</div>
            </div>

            <div>
              <label for="contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Contact Phone</label>
              <input type="text" id="contact_phone" v-model="form.contact_phone" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-cyan-600 focus:ring-cyan-600">
              <div v-if="form.errors.contact_phone" class="text-red-500 text-sm mt-1">{{ form.errors.contact_phone }}</div>
            </div>

            <div>
              <label for="tin_number" class="block text-sm font-medium text-gray-700 dark:text-gray-200">TIN Number</label>
              <input type="text" id="tin_number" v-model="form.tin_number" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-cyan-600 focus:ring-cyan-600">
              <div v-if="form.errors.tin_number" class="text-red-500 text-sm mt-1">{{ form.errors.tin_number }}</div>
            </div>

            <div>
              <label for="trade_license_number" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Trade License Number</label>
              <input type="text" id="trade_license_number" v-model="form.trade_license_number" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-cyan-600 focus:ring-cyan-600">
              <div v-if="form.errors.trade_license_number" class="text-red-500 text-sm mt-1">{{ form.errors.trade_license_number }}</div>
            </div>

            <div class="md:col-span-2">
              <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Address</label>
              <textarea id="address" v-model="form.address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-cyan-600 focus:ring-cyan-600"></textarea>
              <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address }}</div>
            </div>
          </div>

          <div class="mt-6 flex items-center justify-end gap-2">
            <button type="submit" :disabled="form.processing" class="btn-glass btn-glass-sm">
              Update Client
            </button>
            <Link :href="route('admin.corporate-clients.index')" class="btn-glass btn-glass-sm">Cancel</Link>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  corporateClient: Object,
})

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Insurance', href: route('admin.corporate-clients.index') },
  { title: 'Corporate Clients', href: route('admin.corporate-clients.index') },
  { title: 'Edit', href: '' },
]

const form = useForm({
  organization_name: props.corporateClient.organization_name,
  contact_person: props.corporateClient.contact_person,
  contact_email: props.corporateClient.contact_email,
  contact_phone: props.corporateClient.contact_phone,
  tin_number: props.corporateClient.tin_number,
  trade_license_number: props.corporateClient.trade_license_number,
  address: props.corporateClient.address,
})

const submit = () => {
  form.put(route('admin.corporate-clients.update', props.corporateClient.id))
}
</script>
