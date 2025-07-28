<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Trash2, ArrowUpDown, Filter, Edit3, PlusCircle } from 'lucide-vue-next'
import debounce from 'lodash/debounce'

const props = defineProps<{
  availabilities: {
    data: Array<any>;
    links: Array<any>;
    meta: {
      current_page: number;
      from: number;
      last_page: number;
      per_page: number;
      to: number;
      total: number;
    };
  };
  filters: any;
  staffList: Array<{ id: number; first_name: string; last_name: string }>;
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Staff Availabilities', href: route('admin.staff-availabilities.index') },
]

// Reactive state for filters and sorting
const filters = ref({
  staff_id: props.filters.staff_id || '',
  status: props.filters.status || '',
  start_date: props.filters.start_date || '',
  end_date: props.filters.end_date || '',
  sort: props.filters.sort || 'start_time',
  direction: props.filters.direction || 'desc',
  per_page: props.availabilities.meta.per_page || 10,
})

// Watch for changes and trigger API request
watch(filters, debounce(() => {
  router.get(route('admin.staff-availabilities.index'), { ...filters.value }, {
    preserveState: true,
    replace: true,
  })
}, 300), { deep: true })

const showModal = ref(false);
const isEditMode = ref(false);

const form = useForm({
    id: null,
    staff_id: '',
    status: 'Available',
    start_time: '',
    end_time: '',
});

const openCreateModal = () => {
    isEditMode.value = false;
    form.reset();
    showModal.value = true;
};

const openEditModal = (availability) => {
    isEditMode.value = true;
    form.id = availability.id;
    form.staff_id = availability.staff_id;
    form.status = availability.status;
    form.start_time = availability.start_time.slice(0, 16);
    form.end_time = availability.end_time.slice(0, 16);
    showModal.value = true;
};

const submit = () => {
    const onSuccess = () => {
        showModal.value = false;
        form.reset();
    };

    if (isEditMode.value) {
        form.put(route('admin.staff-availabilities.update', form.id), { onSuccess });
    } else {
        form.post(route('admin.staff-availabilities.store'), { onSuccess });
    }
};

function destroy(id: number) {
  if (confirm('Are you sure you want to delete this availability slot?')) {
    router.delete(route('admin.staff-availabilities.destroy', id), {
        preserveScroll: true,
    })
  }
}

// Function to handle column sorting
function toggleSort(field: string) {
  if (filters.value.sort === field) {
    filters.value.direction = filters.value.direction === 'asc' ? 'desc' : 'asc'
  } else {
    filters.value.sort = field
    filters.value.direction = 'asc'
  }
}

const getStaffFullName = (staff) => {
    if (!staff) return 'N/A';
    return `${staff.first_name || ''} ${staff.last_name || ''}`.trim();
}

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleString();
}
</script>

<template>
  <Head title="Staff Availabilities" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Staff Availability Records</h1>
            <p class="text-sm text-muted-foreground">Review and manage all staff availability slots.</p>
        </div>
        <button @click="openCreateModal" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white font-semibold px-4 py-2 rounded-lg text-sm shadow-md">
          <PlusCircle class="h-4 w-4" />
          Add New Slot
        </button>
      </div>

      <div class="rounded-lg border border-border bg-white dark:bg-gray-900 p-4 shadow-sm">
        <div class="flex items-center gap-2 mb-4">
            <Filter class="h-5 w-5 text-muted-foreground" />
            <h3 class="text-lg font-semibold">Filters</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Staff Member</label>
                <select v-model="filters.staff_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                    <option value="">All Staff</option>
                    <option v-for="staff in staffList" :key="staff.id" :value="staff.id">
                        {{ getStaffFullName(staff) }}
                    </option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select v-model="filters.status" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                    <option value="">All Statuses</option>
                    <option value="Available">Available</option>
                    <option value="Unavailable">Unavailable</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Date</label>
                <input type="date" v-model="filters.start_date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">To Date</label>
                <input type="date" v-model="filters.end_date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
            </div>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('staff_id')">
                Staff Member <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">
                Status <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('start_time')">
                Start Time <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('end_time')">
                End Time <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="availability in availabilities.data" :key="availability.id" class="border-b dark:border-gray-700">
              <td class="px-6 py-4">{{ getStaffFullName(availability.staff) }}</td>
              <td class="px-6 py-4">
                 <span :class="['px-2 py-1 rounded-full text-xs font-semibold', availability.status === 'Available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">{{ availability.status }}</span>
              </td>
              <td class="px-6 py-4">{{ formatDate(availability.start_time) }}</td>
              <td class="px-6 py-4">{{ formatDate(availability.end_time) }}</td>
              <td class="px-6 py-4 text-right">
                <div class="inline-flex items-center justify-end space-x-2">
                    <button @click="openEditModal(availability)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100" title="Edit Slot">
                        <Edit3 class="w-4 h-4 text-blue-600" />
                    </button>
                    <button @click="destroy(availability.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100" title="Delete Slot">
                        <Trash2 class="w-4 h-4 text-red-600" />
                    </button>
                </div>
              </td>
            </tr>
            <tr v-if="availabilities.data.length === 0">
              <td colspan="5" class="text-center px-6 py-4 text-gray-400">No records found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50" @click.self="showModal = false">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-lg">
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">
                {{ isEditMode ? 'Edit' : 'Create' }} Availability Slot
            </h3>
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium">Staff Member</label>
                    <select v-model="form.staff_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700">
                        <option disabled value="">Please select a staff member</option>
                        <option v-for="staff in staffList" :key="staff.id" :value="staff.id">{{ getStaffFullName(staff) }}</option>
                    </select>
                    <div v-if="form.errors.staff_id" class="text-red-500 text-sm mt-1">{{ form.errors.staff_id }}</div>
                </div>
                 <div>
                    <label class="block text-sm font-medium">Status</label>
                    <select v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700">
                        <option>Available</option>
                        <option>Unavailable</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Start Time</label>
                    <input type="datetime-local" v-model="form.start_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700" />
                    <div v-if="form.errors.start_time" class="text-red-500 text-sm mt-1">{{ form.errors.start_time }}</div>
                </div>
                 <div>
                    <label class="block text-sm font-medium">End Time</label>
                    <input type="datetime-local" v-model="form.end_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700" />
                    <div v-if="form.errors.end_time" class="text-red-500 text-sm mt-1">{{ form.errors.end_time }}</div>
                </div>
                
                <!-- Error Message -->
                <div v-if="form.errors.error" class="text-red-500 text-sm mt-1 p-3 bg-red-50 border border-red-200 rounded">
                    {{ form.errors.error }}
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button @click="showModal = false" type="button" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-100">Cancel</button>
                    <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-cyan-600 text-white rounded-md disabled:opacity-50 hover:bg-cyan-700">
                        {{ form.processing ? 'Saving...' : 'Save Slot' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

  </AppLayout>
</template>
