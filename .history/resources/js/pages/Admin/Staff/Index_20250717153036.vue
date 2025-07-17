<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue' // Use the component
import { format } from 'date-fns' // Import format for date

const props = defineProps<{ staff: any; filters: any }>()

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Staff', href: '/dashboard/staff' },
]

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 10)

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

watch([search, sortField, sortDirection, perPage], debounce(() => {
  router.get('/dashboard/staff', {
    search: search.value,
    sort: sortField.value,
    direction: sortDirection.value,
    per_page: perPage.value,
  }, {
    preserveState: true,
    replace: true,
  })
}, 500))

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this staff?')) {
    router.delete(route('admin.staff.destroy', id))
  }
}

function exportData(type: 'csv' | 'pdf') {
  const form = document.createElement('form');
  form.method = 'GET';
  form.action = `/dashboard/staff/export`;
  form.target = '_blank';

  const input = document.createElement('input');
  input.type = 'hidden';
  input.name = 'type';
  input.value = type;
  form.appendChild(input);

  document.body.appendChild(form);
  form.submit();
  document.body.removeChild(form);
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
    window.open(route('admin.staff.export', { type: 'pdf' }), '_blank');
};

function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
}
</script>

<template>
  <Head title="Staff" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Staff</h1>
          <p class="text-sm text-muted-foreground">Manage all staff records here.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <Link :href="route('admin.staff.create')" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-md transition">
            + Add Staff
          </Link>
          <button @click="exportData('csv')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Download class="h-4 w-4" /> CSV
          </button>
          <button @click="exportData('pdf')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <FileText class="h-4 w-4" /> PDF
          </button>
          <button @click="printAllStaff" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Printer class="h-4 w-4" /> Print All
          </button>
          <button @click="printCurrentView" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
            <Printer class="h-4 w-4" /> Print Current View
          </button>
        </div>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="relative w-full md:w-1/3">
          <input
            type="text"
            v-model="search"
            placeholder="Search staff..."
            class="form-input w-full rounded-md border border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100"
          />
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" />
        </div>

        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Pagination per page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home-to-Home Care</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Staff List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('first_name')">
                First Name <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('last_name')">
                Last Name <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('email')">
                Email <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3">Phone</th>
              <th class="px-6 py-3">Position</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="member in staff.data" :key="member.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ member.first_name }}</td>
              <td class="px-6 py-4">{{ member.last_name }}</td>
              <td class="px-6 py-4">{{ member.email ?? '-' }}</td>
              <td class="px-6 py-4">{{ member.phone ?? '-' }}</td>
              <td class="px-6 py-4">{{ member.position ?? '-' }}</td>
              <td class="px-6 py-4">{{ member.status ?? '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                    <Link :href="route('admin.staff.show', member.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-indigo-600" title="View Details">
  <Eye class="w-4 h-4" />
</Link>

                  <Link :href="route('admin.staff.edit', member.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600" title="Edit">
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="destroy(member.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="staff.data.length === 0">
              <td colspan="7" class="text-center px-6 py-4 text-gray-400">No staff found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      
      <!-- THE FIX IS HERE: Replaced the manual links with the reusable Pagination component -->
      <Pagination v-if="staff.data.length > 0" :links="staff.links" class="mt-6 flex justify-center print:hidden" />

      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p> </div>

    </div>
  </AppLayout>
</template>

<style>
/* Print-specific styles for Index.vue (Print Current View) */
@media print {
  @page {
    size: A4 landscape; /* Landscape is often better for tables */
    margin: 0.5cm;
  }

  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: visible !important;
  }

  /* Hide elements */
  .print\:hidden {
    display: none !important;
  }

  /* Specific styles for the print header content (logo and clinic name) */
  .print-header-content {
      display: block !important; /* Show header */
      text-align: center;
      padding-top: 0.5cm;
      padding-bottom: 0.5cm;
      margin-bottom: 0.8cm;
  }
  .print-logo {
      max-width: 150px; /* Adjust as needed */
      max-height: 50px; /* Adjust as needed */
      margin-bottom: 0.5rem;
      display: block;
      margin-left: auto;
      margin-right: auto;
  }
  .print-clinic-name {
      font-size: 1.6rem !important; /* Slightly smaller than show view */
      margin-bottom: 0.2rem !important;
      line-height: 1.2 !important;
      font-weight: bold;
  }
  .print-document-title {
      font-size: 0.85rem !important;
      color: #555 !important;
  }
  hr { border-color: #ccc !important; }

  /* Main content container adjustments */
  .space-y-6.p-6 {
    padding: 0 !important;
    margin: 0 !important;
  }

  /* Table specific print styles */
  .overflow-x-auto.bg-white.dark\:bg-gray-900.shadow.rounded-lg {
    box-shadow: none !important;
    border-radius: 0 !important;
    background-color: transparent !important; /* No background color */
    overflow: visible !important; /* Essential to prevent clipping */
    padding: 1cm; /* Inner padding for the table */
    transform: scale(0.97); /* Slight scale down to fit wide tables */
    transform-origin: top left;
  }

  .print-table {
    width: 100% !important;
    border-collapse: collapse !important;
    font-size: 0.8rem !important; /* Adjust table body font size */
    table-layout: fixed; /* Helps with column width distribution */
  }

  .print-table-header {
    background-color: #f0f0f0 !important; /* Light grey header background */
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    text-transform: uppercase !important;
  }

  .print-table th, .print-table td {
    border: 1px solid #ddd !important; /* Subtle borders for all cells */
    padding: 0.4rem 0.6rem !important; /* Adjust cell padding */
    color: #000 !important;
    vertical-align: top !important; /* Align content to top of cell */
    word-break: break-word; /* Allow long words to break */
  }

  .print-table th {
    font-weight: bold !important;
    font-size: 0.7rem !important; /* Header font size */
    white-space: nowrap; /* Keep header text on one line if possible */
  }

  /* Adjust column widths if needed, target by nth-child or specific content */
  .print-table th:nth-child(1), .print-table td:nth-child(1) { width: 18%; } /* Name */
  .print-table th:nth-child(2), .print-table td:nth-child(2) { width: 12%; } /* Patient Code */
  .print-table th:nth-child(3), .print-table td:nth-child(3) { width: 15%; } /* Fayda ID */
  .print-table th:nth-child(4), .print-table td:nth-child(4) { width: 8%; }  /* Age */
  .print-table th:nth-child(5), .print-table td:nth-child(5) { width: 10%; } /* Gender */
  .print-table th:nth-child(6), .print-table td:nth-child(6) { width: 15%; } /* Phone */
  .print-table th:nth-child(7), .print-table td:nth-child(7) { width: 10%; } /* Source */


  .print-table tbody tr:nth-child(even) {
    background-color: #f9f9f9 !important; /* Subtle zebra striping */
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
  .print-table tbody tr:last-child {
    border-bottom: 1px solid #ddd !important;
  }

  /* Hide actions column for print */
  .print-table th:last-child,
  .print-table td:last-child {
    display: none !important;
  }

  /* Hide sort arrows on print */
  .print\:hidden {
    display: none !important;
  }

  /* Print Footer */
  .print-footer {
    display: block !important;
    text-align: center;
    margin-top: 1cm;
    font-size: 0.75rem !important;
    color: #666 !important;
  }
  .print-footer hr {
    border-color: #ccc !important;
  }
}
</style>
