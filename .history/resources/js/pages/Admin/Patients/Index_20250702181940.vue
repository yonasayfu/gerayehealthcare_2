<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer } from 'lucide-vue-next'

defineProps<{ patients: any }>()

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Patients', href: '/dashboard/patients' },
]

// Local search query
const search = ref('')

// Debounced search (optional)
watch(search, (value) => {
  router.get(route('patients.index'), { search: value }, { preserveState: true })
})

// Delete patient
function destroy(id: number) {
  if (confirm('Are you sure you want to delete this patient?')) {
    router.delete(route('patients.destroy', id))
  }
}

// Export handlers (assume backend route handles CSV/PDF export)
function exportData(type: 'csv' | 'pdf') {
  window.open(route(`patients.export`, { type }), '_blank')
}

// Print
function printTable() {
  window.print()
}
</script>

<template>
  <Head title="Patients" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <!-- Page Header -->
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Patients</h1>
          <p class="text-sm text-muted-foreground">Manage all patient records here.</p>
        </div>

        <div class="flex flex-wrap gap-2">
          <Link
            href="/dashboard/patients/create"
            class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-md transition"
          >
            + Add Patient
          </Link>
          <button @click="exportData('csv')" class="btn-icon">
            <Download class="h-4 w-4" />
            CSV
          </button>
          <button @click="exportData('pdf')" class="btn-icon">
            <FileText class="h-4 w-4" />
            PDF
          </button>
          <button @click="printTable" class="btn-icon">
            <Printer class="h-4 w-4" />
            Print
          </button>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="flex justify-end">
        <input
          type="text"
          v-model="search"
          placeholder="Search by name/email..."
          class="form-input w-full md:w-1/3 rounded-md border border-gray-300 px-3 py-2 text-sm"
        />
      </div>

      <!-- Data Table -->
      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3">Name</th>
              <th class="px-6 py-3">Email</th>
              <th class="px-6 py-3">Phone</th>
              <th class="px-6 py-3">Gender</th>
              <th class="px-6 py-3">Emergency Contact</th>
              <th class="px-6 py-3 text-right">Actions</th>
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
              <td class="px-6 py-4 text-right space-x-2">
                <Link
                  :href="route('patients.edit', patient.id)"
                  class="text-blue-600 hover:text-blue-800"
                  title="Edit"
                >
                  <Edit3 class="w-4 h-4" />
                </Link>
                <button
                  @click="destroy(patient.id)"
                  class="text-red-600 hover:text-red-800"
                  title="Delete"
                >
                  <Trash2 class="w-4 h-4" />
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
        <div class="flex items-center space-x-1">
          <Link
            v-for="(link, i) in patients.links"
            :key="i"
            :href="link.url || ''"
            v-html="link.label"
            :class="[
              'px-3 py-1 rounded-md text-sm',
              link.active
                ? 'bg-primary-600 text-white'
                : 'hover:bg-gray-200 dark:hover:bg-gray-700',
              !link.url && 'cursor-not-allowed text-gray-400'
            ]"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style>
.btn-icon {
  @apply inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200;
}
</style>
