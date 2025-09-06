<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'
import CalendarView from '@/components/CalendarView.vue'
import EthiopianDatePicker from '@/components/EthiopianDatePicker.vue'; // Correct import for the component
import { useEthiopianDate } from '@/composables/useEthiopianDate';
import type { EthiopianCalendarDayPagination } from '@/types';
import { confirmDialog } from '@/lib/confirm'

const gregorianInput = ref('');
const ethiopianInput = ref(''); // This will store the Ethiopian date string from the picker
const ethiopianPickerGregorianModel = ref(''); // This will be the v-model for EthiopianDatePicker
const convertedGregorianDate = ref('');
const convertedEthiopianDate = ref('');
const conversionError = ref('');

const { convertGregorianToEthiopian, convertEthiopianToGregorian, convertGregorianToEthiopianApi } = useEthiopianDate();

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

async function getCurrentEthiopianDate() {
    const todayGregorian = new Date();
    const ethiopian = await convertGregorianToEthiopian({ year: todayGregorian.getFullYear(), month: todayGregorian.getMonth() + 1, day: todayGregorian.getDate() });
    if (ethiopian) {
      currentEthiopianDate.value = `${ethiopian.year}-${String(ethiopian.month).padStart(2, '0')}-${String(ethiopian.day).padStart(2, '0')}`;
    } else {
      currentEthiopianDate.value = 'N/A'; // Or handle error appropriately
    }
}

onMounted(() => {
  getCurrentEthiopianDate();
});

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

async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete Ethiopian Calendar Day',
    message: 'Are you sure you want to delete this ethiopian calendar day?',
    confirmText: 'Delete',
    variant: 'danger',
  })
  if (!ok) return
  router.delete(route('admin.ethiopian-calendar-days.destroy', id))
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
        const ethiopianDate = await convertGregorianToEthiopianApi(gregorianInput.value);
        if (ethiopianDate) {
          convertedEthiopianDate.value = ethiopianDate;
        } else {
          conversionError.value = 'Failed to convert Gregorian date to Ethiopian.';
        }
      } catch (error: any) {
        conversionError.value = error.message || 'An unexpected error occurred during Gregorian to Ethiopian conversion.';
        console.error('Gregorian to Ethiopian conversion error:', error);
      }
    } else if (type === 'ethiopian' && ethiopianInput.value) {
      try {
        const dateParts = ethiopianInput.value.split('-').map(Number);
        if (dateParts.length !== 3 || isNaN(dateParts[0]) || isNaN(dateParts[1]) || isNaN(dateParts[2])) {
          throw new Error('Invalid Ethiopian date format. Please use YYYY-MM-DD.');
        }
        const gregorian = await convertEthiopianToGregorian({ year: dateParts[0], month: dateParts[1], day: dateParts[2] });
        if (gregorian) {
          convertedGregorianDate.value = `${gregorian.year}-${String(gregorian.month).padStart(2, '0')}-${String(gregorian.day).padStart(2, '0')}`;
        } else {
          conversionError.value = 'Failed to convert Ethiopian date to Gregorian.';
        }
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
            <button @click="convertDate('gregorian')" class="mt-2 inline-flex items-center px-3 py-1.5 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
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
            <button @click="convertDate('ethiopian')" class="mt-2 inline-flex items-center px-3 py-1.5 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
              Convert to Gregorian
            </button>
            <p v-if="convertedGregorianDate" class="mt-2 text-sm text-green-600 dark:text-green-400">Converted Gregorian: {{ convertedGregorianDate }}</p>
          </div>
        </div>
        <p v-if="conversionError" class="mt-4 text-sm text-red-600 dark:text-red-400">{{ conversionError }}</p>
      </div>

     

     
        <t
      </div>

      <Pagination v-if="ethiopianCalendarDays.data.length > 0" :links="ethiopianCalendarDays.links" class="mt-6 flex justify-center print:hidden" />
      
      <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
            <hr class="my-2 border-gray-300">
            <p>Document Generated: {{ formattedGeneratedDate }}</p> </div>

    </div>
  </AppLayout>
</template>
