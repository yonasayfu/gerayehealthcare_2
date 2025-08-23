<template>
  <Head title="Create Insurance Policy" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <div class="liquidGlass-wrapper relative overflow-hidden rounded-xl p-5">
        <div class="absolute inset-0 pointer-events-none bg-gradient-to-br from-white/10 to-white/5"></div>
        <div class="relative flex items-center justify-between">
          <div>
            <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create Insurance Policy</h1>
            <p class="text-sm text-muted-foreground">Fill in the details and save.</p>
          </div>
          <Link :href="route('admin.insurance-policies.index')" class="btn-glass btn-glass-sm">Back to List</Link>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-900 overflow-hidden shadow rounded-lg">
        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="insurance_company_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Insurance Company</label>
            <select id="insurance_company_id" v-model="form.insurance_company_id" class="mt-1 block w-full shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5 bg-white dark:bg-gray-800" required>
              <option value="">Select an Insurance Company</option>
              <option v-for="company in insuranceCompanies" :key="company.id" :value="company.id">{{ company.name }}</option>
            </select>
            <div v-if="form.errors.insurance_company_id" class="text-red-500 text-sm mt-1">{{ form.errors.insurance_company_id }}</div>
          </div>

          <div>
            <label for="corporate_client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Corporate Client</label>
            <select id="corporate_client_id" v-model="form.corporate_client_id" class="mt-1 block w-full shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5 bg-white dark:bg-gray-800" required>
              <option value="">Select a Corporate Client</option>
              <option v-for="client in corporateClients" :key="client.id" :value="client.id">{{ client.organization_name }}</option>
            </select>
            <div v-if="form.errors.corporate_client_id" class="text-red-500 text-sm mt-1">{{ form.errors.corporate_client_id }}</div>
          </div>

          <div>
            <label for="service_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Service Type</label>
            <input type="text" id="service_type" v-model="form.service_type" class="mt-1 block w-full shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5 dark:bg-gray-800 dark:text-white" required>
            <div v-if="form.errors.service_type" class="text-red-500 text-sm mt-1">{{ form.errors.service_type }}</div>
          </div>

          <div>
            <label for="coverage_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Coverage Percentage</label>
            <input type="number" id="coverage_percentage" v-model="form.coverage_percentage" class="mt-1 block w-full shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5 dark:bg-gray-800 dark:text-white" step="0.01" required>
            <div v-if="form.errors.coverage_percentage" class="text-red-500 text-sm mt-1">{{ form.errors.coverage_percentage }}</div>
          </div>

          <div>
            <label for="coverage_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Coverage Type</label>
            <select id="coverage_type" v-model="form.coverage_type" class="mt-1 block w-full shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5 bg-white dark:bg-gray-800" required>
              <option value="">Select Coverage Type</option>
              <option value="Full">Full</option>
              <option value="Partial">Partial</option>
            </select>
            <div v-if="form.errors.coverage_type" class="text-red-500 text-sm mt-1">{{ form.errors.coverage_type }}</div>
          </div>

          <div class="md:col-span-2">
            <label for="is_active" class="flex items-center">
              <input type="checkbox" id="is_active" v-model="form.is_active" class="rounded border border-gray-300 text-cyan-600 shadow-sm focus:ring-cyan-600">
              <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Is Active</span>
            </label>
            <div v-if="form.errors.is_active" class="text-red-500 text-sm mt-1">{{ form.errors.is_active }}</div>
          </div>

          <div class="md:col-span-2">
            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
            <textarea id="notes" v-model="form.notes" rows="3" class="mt-1 block w-full shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5 dark:bg-gray-800 dark:text-white"></textarea>
            <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">{{ form.errors.notes }}</div>
          </div>
        </form>
      </div>

      <div class="p-6 border-t border-gray-200 rounded-b">
        <div class="flex flex-wrap gap-2">
          <Link :href="route('admin.insurance-policies.index')" class="btn-glass btn-glass-sm">Cancel</Link>
          <button @click="submit" :disabled="form.processing" class="btn-glass btn-glass-sm" type="submit">
            {{ form.processing ? 'Creating...' : 'Create Policy' }}
          </button>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  insuranceCompanies: Array,
  corporateClients: Array,
})

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Insurance', href: route('admin.insurance-policies.index') },
  { title: 'Policies', href: route('admin.insurance-policies.index') },
  { title: 'Create', href: route('admin.insurance-policies.create') },
]

const form = useForm({
  insurance_company_id: '',
  corporate_client_id: '',
  service_type: '',
  coverage_percentage: '',
  coverage_type: '',
  is_active: true,
  notes: '',
})

const submit = () => {
  form.post(route('admin.insurance-policies.store'))
}
</script>
