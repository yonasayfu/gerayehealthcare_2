<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'
import CalendarView from '@/components/CalendarView.vue'
import { toEthiopian, toGregorian } from 'ethiopian-date'; // Import toGregorian
import axios from 'axios'; // Import axios
import EthiopianDatePicker from '@/components/EthiopianDatePicker.vue'; // Correct import for the component

import type { EthiopianCalendarDayPagination } from '@/types';

const gregorianInput = ref('');
const ethiopianInput = ref(''); // This will store the Ethiopian date string from the picker
const ethiopianPickerGregorianModel = ref(''); // This will be the v-model for EthiopianDatePicker
const convertedGregorianDate = ref('');
const convertedEthiopianDate = ref('');
const conversionError = ref('');

const props = defineProps<{
  ethiopianCalendarDays: EthiopianCalendarDayPagination;
  filters: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
  };
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Insurance', href: route('admin.ethiopian-calendar-days.index') },
  { title: 'Ethiopian Calendar Days', href: route('admin.ethiopian-calendar-days.index') },
]

const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort || '')
const sortDirection = ref(props.filters.direction || 'asc')
const perPage = ref(props.filters.per_page || 5)

const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p');
});

const currentEthiopianDate = ref('');
const showEthiopianDate = ref(false); // New ref for toggling visibility

function getCurrentEthiopianDate() {
    const todayGregorian = new Date();
    const ethiopian = toEthiopian(todayGregorian.getFullYear(), todayGregorian.getMonth() + 1, todayGregorian.getDate());
    currentEthiopianDate.value = `${ethiopian[0]}-${String(ethiopian[1]).padStart(2, '0')}-${String(ethiopian[2]).padStart(2, '0')}`;
}

getCurrentEthiopianDate();

watch([search, sortField, sortDirection, perPage], debounce(() => {
  const params: Record<string, string | number> = {
    search: search.value,
    direction: sortDirection.value,
    per_page: perPage.value,
  };

  if (sortField.value) {
    params.sort = sortField.value;
  }

  router.get(route('admin.ethiopian-calendar-days.index'), params, {
    preserveState: true,
    replace: true,
  })
}, 500))

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this ethiopian calendar day?')) {
    router.delete(route('admin.ethiopian-calendar-days.destroy', id))
  }
}

function toggleSort(field: string) {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
}

async function convertDate(type: 'gregorian' | 'ethiopian') {
  conversionError.value = '';
  convertedGregorianDate.value = '';
  convertedEthiopianDate.value = '';

  try {
    if (type === 'gregorian' && gregorianInput.value) {
      try {
        const response = await axios.post('/api/v1/convert-to-ethiopian', { date: gregorianInput.value });
        convertedEthiopianDate.value = response.data.ethiopian_date;
      } catch (error: any) {
        conversionError.value = error.response?.data?.error || 'An unexpected error occurred during Gregorian to Ethiopian conversion.';
        console.error('Gregorian to Ethiopian conversion error:', error);
      }
    } else if (type === 'ethiopian' && ethiopianInput.value) {
      try {
        const dateParts = ethiopianInput.value.split('-').map(Number);
        if (dateParts.length !== 3 || isNaN(dateParts[0]) || isNaN(dateParts[1]) || isNaN(dateParts[2])) {
          throw new Error('Invalid Ethiopian date format. Please use YYYY-MM-DD.');
        }
        const gregorian = toGregorian(dateParts[0], dateParts[1], dateParts[2]);
        convertedGregorianDate.value = `${gregorian[0]}-${String(gregorian[1]).padStart(2, '0')}-${String(gregorian[2]).padStart(2, '0')}`;
      } catch (e: any) {
        conversionError.value = e.message || 'Invalid Ethiopian date for conversion.';
        console.error('Frontend conversion error:', e);
      }
    } else {
      conversionError.value = 'Please enter a date to convert.';
    }
  } catch (error: any) {
    if (error.response && error.response.data && error.response.data.message) {
      conversionError.value = error.response.data.message;
    } else if (error.response && error.response.data && error.response.data.error) {
      conversionError.value = error.response.data.error;
    }
    else {
      conversionError.value = 'An unexpected error occurred during conversion.';
    }
    console.error('Conversion error:', error);
  }
}
</script>

<template>
  <Head title="Ethiopian Calendar Days" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div>
          <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Ethiopian Calendar Days</h1>
          <p class="text-sm text-muted-foreground">Manage all ethiopian calendar days here.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button @click="showEthiopianDate = !showEthiopianDate" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
            {{ showEthiopianDate ? 'Hide Ethiopian Date' : 'Show Ethiopian Date' }}
          </button>
          <Link :href="route('admin.ethiopian-calendar-days.create')" class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-2 rounded-md transition">
            + Add Day
          </Link>
        </div>
      </div>

      <div v-if="showEthiopianDate" class="rounded-lg bg-blue-50 p-4 shadow-md flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden mt-4">
        <p class="text-base font-semibold text-blue-800 dark:text-blue-200">Today's Date: {{ new Date().toLocaleDateString() }} (Gregorian) / {{ currentEthiopianDate }} (Ethiopian)</p>
      </div>

      <!-- Date Conversion Section -->
      <div class="rounded-lg bg-gray-50 p-4 shadow-md print:hidden mt-4">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Date Conversion</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="gregorianConvertInput" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gregorian Date to Convert</label>
            <input type="date" id="gregorianConvertInput" v-model="gregorianInput" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <button @click="convertDate('gregorian')" class="mt-2 inline-flex items-center px-3 py-1.5 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-600 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
              Convert to Ethiopian
            </button>
            <p v-if="convertedEthiopianDate" class="mt-2 text-sm text-green-600 dark:text-green-400">Converted Ethiopian: {{ convertedEthiopianDate }}</p>
          </div>
          <div>
            <label for="ethiopianConvertInput" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ethiopian Date to Convert</label>
            <EthiopianDatePicker
                id="ethiopianConvertInput"
                v-model="ethiopianPickerGregorianModel"
                @update:ethiopianDate="ethiopianInput = $event"
            />
            <button @click="convertDate('ethiopian')" class="mt-2 inline-flex items-center px-3 py-1.5 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-600 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
              Convert to Gregorian
            </button>
            <p v-if="convertedGregorianDate" class="mt-2 text-sm text-green-600 dark:text-green-400">Converted Gregorian: {{ convertedGregorianDate }}</p>
          </div>
        </div>
        <p v-if="conversionError" class="mt-4 text-sm text-red-600 dark:text-red-400">{{ conversionError }}</p>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <div class="relative w-full md:w-1/3">
          <input
            type="text"
            v-model="search"
            placeholder="Search days..."
            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
          />
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" />
        </div>

        <div>
          <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Pagination per page:</label>
          <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
            <option value="5">5</option>
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
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Ethiopian Calendar Days List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>
        
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('gregorian_date')">
                Gregorian Date <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('ethiopian_date')">
                Ethiopian Date <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('description')">
                Description <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('is_holiday')">
                Is Holiday <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('region')">
                Region <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
              </th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="day in ethiopianCalendarDays.data" :key="day.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
              <td class="px-6 py-4">{{ day.gregorian_date }}</td>
              <td class="px-6 py-4">{{ day.ethiopian_date ?? '-' }}</td>
              <td class="px-6 py-4">{{ day.description ?? '-' }}</td>
              <td class="px-6 py-4">{{ day.is_holiday ? 'Yes' : 'No' }}</td>
              <td class="px-6 py-4">{{ day.region ?? '-' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.ethiopian-calendar-days.show', day.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.ethiopian-calendar-days.edit', day.id)"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="destroy(day.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="ethiopianCalendarDays.data.length === 0">
              <td colspan="6" class="text-center px-6 py-4 text-gray-400">No ethiopian calendar days found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="ethiopianCalendarDays.data.length > 0" :links="ethiopianCalendarDays.links" class="mt-6 flex justify-center print:hidden" />
      
      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p> </div>

    </div>
  </AppLayout>
</template>
