<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import { confirmDialog } from '@/lib/confirm'

import AppLayout from '@/layouts/AppLayout.vue'
import { Download, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search, FileText } from 'lucide-vue-next'
import Pagination from '@/components/Pagination.vue' // Use the component
import { format } from 'date-fns' // Import format for date
import { useTableFilters } from '@/composables/useTableFilters'

const props = defineProps<{ staff: any; filters: any }>()

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Staff', href: '/dashboard/staff' },
]

const { search, perPage, toggleSort } = useTableFilters({
  routeName: 'admin.staff.index',
  initial: {
    search: props.filters?.search,
    sort: props.filters?.sort,
    direction: props.filters?.direction,
    per_page: props.filters?.per_page ?? props.staff?.per_page ?? 5,
  }
})

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

// URL updates handled by composable

async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete Staff',
    message: 'Are you sure you want to delete this staff?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.staff.destroy', id), {
    preserveScroll: true,
  })
}

function exportData(type: 'csv' | 'pdf') {
  // Open export in a new tab for Staff
  window.open(route('admin.staff.export', { type }), '_blank');
}

function exportCsv() {
  exportData('csv');
}

function exportPdf() {
  exportData('pdf');
}

function printCurrentView() {
  setTimeout(() => {
    try {
      window.print();
    } catch (error) {
      console.error('Print failed:', error);
      alert('Failed to open print dialog for current view. Please check your browser settings or try again.');
    }
  }, 100); // Small delay for reliability
}

const printAllStaff = () => {
  window.open(route('admin.staff.printAll', { preview: true }), '_blank');
};

function onToggleSort(field: string) { toggleSort(field) }
</script>

<template>
  <Head title="Staff" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Staff</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage staff</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.staff.create')" class="btn-glass">
              <span>Add Staff Member</span>
            </Link>
            <button @click="exportData('csv')" class="btn-glass btn-glass-sm">
              <Download class="icon" />
              <span class="hidden sm:inline">Export CSV</span>
            </button>
            <button @click="printCurrentView" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Search / per page -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="search"
            type="text"
            placeholder="Search by name, email, position..."
            class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-100 relative z-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400 z-20" />
        </div>

        <div>
            <label for="perPage" class="mr-2 text-sm text-white dark:text-gray-300">Per Page:</label>
            <PerPageSelect v-model="perPage" id="perPage" />
          </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Staff List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3">#</th>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('first_name')">
                First Name <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('last_name')">
                Last Name <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="onToggleSort('email')">
                Email <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3">Phone</th>
              <th class="px-6 py-3">Position</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(member, i) in staff.data" :key="member.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ (staff.from ?? 1) + i }}</td>
              <td class="px-6 py-4">{{ member.first_name }}</td>
              <td class="px-6 py-4">{{ member.last_name }}</td>
              <td class="px-6 py-4">{{ member.email ?? '-' }}</td>
              <td class="px-6 py-4">{{ member.phone ?? '-' }}</td>
              <td class="px-6 py-4">{{ member.position ?? '-' }}</td>
              <td class="px-6 py-4">{{ member.status ?? '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.staff.show', member.id)"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.staff.edit', member.id)"
                    class="inline-flex items-center p-2 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-gray-700"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="destroy(member.id)"
                    class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="staff.data.length === 0">
              <td colspan="8" class="text-center px-6 py-4 text-gray-400">No staff found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      
      <!-- THE FIX IS HERE: Replaced the manual links with the reusable Pagination component -->
      <Pagination v-if="staff.data.length > 0" :links="staff.links" class="mt-6 flex justify-center print:hidden" />
      <p v-if="staff.total" class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300 print:hidden">
        Showing {{ staff.from || 0 }}–{{ staff.to || 0 }} of {{ staff.total }}
      </p>

      <!-- spacer to prevent content under footer when printing -->
      <div class="hidden print:block h-24"></div>

      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p> 
      </div>

    </div>
  </AppLayout>
</template>

<style>
@media print {
  .print-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    padding-bottom: 8px;
  }
}
</style>
