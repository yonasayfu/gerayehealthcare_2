<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import ResourcePageLayout from '@/components/ResourcePageLayout.vue'
import ResourceActions from '@/components/ResourceActions.vue'
import PermissionGuard from '@/components/PermissionGuard.vue'
import { useResourcePage } from '@/composables/useResourcePage'
import { format } from 'date-fns'
import type { PatientPagination } from '@/types/index.d.ts'

const props = defineProps<{
  patients: PatientPagination;
  filters?: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
  };
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Patients', href: route('admin.patients.index') },
];

const columns = [
  { key: 'first_name', label: 'First Name', sortable: true },
  { key: 'last_name', label: 'Last Name', sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'phone', label: 'Phone', sortable: false },
  { key: 'gender', label: 'Gender', sortable: true },
  { key: 'created_at', label: 'Created', sortable: true },
];

const { deleteRecord, exportData, printCurrentView } = useResourcePage({
  routeName: 'admin.patients',
  deleteConfirmTitle: 'Delete Patient',
  deleteConfirmMessage: 'Are you sure you want to delete this patient? This action cannot be undone.',
});

// Calculate current index for row numbering
const currentIndex = (props.patients.current_page - 1) * props.patients.per_page;
</script>

<template>
  <Head title="Patients" />

  <PermissionGuard permission="view patients" fallback>
    <ResourcePageLayout
      title="Patients"
      description="Manage patient records"
      :breadcrumbs="breadcrumbs"
      :create-route="route('admin.patients.create')"
      create-text="Add Patient"
      search-placeholder="Search patients by name, email, phone..."
      print-title="Patients List"
      route-name="admin.patients"
      :columns="columns"
      :data="patients"
      :filters="filters || {}"
      @delete="deleteRecord"
      @export="exportData"
      @print="printCurrentView"
    >
      <template #table-rows="{ data: patientsData, handleDelete }">
        <tr 
          v-for="(patient, index) in patientsData" 
          :key="patient.id"
          class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
        >
          <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
            {{ currentIndex + index + 1 }}
          </td>
          <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
            {{ patient.first_name }}
          </td>
          <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
            {{ patient.last_name }}
          </td>
          <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
            {{ patient.email }}
          </td>
          <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
            {{ patient.phone }}
          </td>
          <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="{
                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': patient.gender === 'Male',
                    'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300': patient.gender === 'Female',
                    'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300': patient.gender === 'Other'
                  }">
              {{ patient.gender }}
            </span>
          </td>
          <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
            {{ format(new Date(patient.created_at), 'MMM dd, yyyy') }}
          </td>
          <td class="px-6 py-4">
            <PermissionGuard permissions="['view patients', 'edit patients', 'delete patients']">
              <ResourceActions
                :item="patient"
                :show-route="route('admin.patients.show', patient.id)"
                :edit-route="route('admin.patients.edit', patient.id)"
                :show-view="true"
                :show-edit="true"
                :show-delete="true"
                @delete="handleDelete"
              />
            </PermissionGuard>
          </td>
        </tr>
      </template>

      <template #empty-state>
        <div class="text-center py-12">
          <div class="text-gray-500 dark:text-gray-400">
            <p class="text-lg font-medium">No patients found</p>
            <p class="text-sm">Try adjusting your search criteria or add a new patient</p>
          </div>
        </div>
      </template>
    </ResourcePageLayout>

    <template #fallback>
      <div class="text-center py-8">
        <div class="text-gray-500 dark:text-gray-400">
          <p class="text-lg font-medium">Access Denied</p>
          <p class="text-sm">You don't have permission to view patients.</p>
        </div>
      </div>
    </template>
  </PermissionGuard>
</template>
