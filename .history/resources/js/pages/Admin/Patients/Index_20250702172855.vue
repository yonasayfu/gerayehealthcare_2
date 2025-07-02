<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItemType } from '@/types'

defineProps<{ patients: any }>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Patients', href: '/dashboard/patients' },
]

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this patient?')) {
    router.delete(route('patients.destroy', id))
  }
}
</script>

<template>
  <Head title="Patients" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Patients</h1>
        <Link
          href="/dashboard/patients/create"
          class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md text-sm font-medium transition"
        >
          + New Patient
        </Link>
      </div>

      <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-700 dark:text-gray-200">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-500 dark:text-gray-400">
            <tr>
              <th class="px-6 py-3">Full Name</th>
              <th class="px-6 py-3">Email</th>
              <th class="px-6 py-3">Phone</th>
              <th class="px-6 py-3">Gender</th>
              <th class="px-6 py-3">Emergency Contact</th>
              <th class="px-6 py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="patient in patients.data"
              :key="patient.id"
              class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800"
            >
              <td class="px-6 py-4">{{ patient.full_name }}</td>
              <td class="px-6 py-4">{{ patient.email ?? '-' }}</td>
              <td class="px-6 py-4">{{ patient.phone_number ?? '-' }}</td>
              <td class="px-6 py-4">{{ patient.gender ?? '-' }}</td>
              <td class="px-6 py-4">{{ patient.emergency_contact ?? '-' }}</td>
              <td class="px-6 py-4 flex space-x-2">
                <Link
                  :href="route('patients.edit', patient.id)"
                  class="text-blue-600 hover:underline text-sm"
                >
                  Edit
                </Link>
                <button
                  @click="destroy(patient.id)"
                  class="text-red-600 hover:underline text-sm"
                >
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="patients.data.length === 0">
              <td colspan="6" class="text-center px-6 py-4 text-gray-400">No patients found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex justify-end">
        <div class="flex items-center space-x-2">
          <Link
            v-for="(link, index) in patients.links"
            :key="index"
            :href="link.url || ''"
            v-html="link.label"
            :class="[
              'px-3 py-1 rounded-md text-sm',
              link.active
                ? 'bg-primary-600 text-white'
                : 'hover:bg-gray-200 dark:hover:bg-gray-700',
              !link.url && 'cursor-not-allowed text-gray-400'
            ]"
            :disabled="!link.url"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
