<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'
// Removed exports; using window.print for current view

interface Assignment {
  id: number;
  event_id: number;
  staff_id: number;
  role: string;
  notes?: string;
  event?: { title?: string };
  event_title?: string;
  staff?: { full_name?: string };
  staff_first_name?: string;
  staff_last_name?: string;
}

interface Filters {
  search?: string;
  sort?: string;
  direction?: 'asc' | 'desc';
  per_page?: number;
  event_id?: number | '';
  staff_id?: number | '';
  role?: string;
}

interface AssignmentsPagination {
  data: Assignment[];
  links: any[]; // Adjust with actual pagination link type if available
  // Add other pagination properties like current_page, last_page, total, etc.
}

const props = defineProps<{
    assignments: AssignmentsPagination;
    filters: Filters;
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Event Staff Assignments', href: route('admin.event-staff-assignments.index') },
];

const search = ref(props.filters.search || '');
const sortField = ref(props.filters.sort || '');
const sortDirection = ref(props.filters.direction || 'asc');
const perPage = ref(props.filters.per_page || 5);
const selectedEventId = ref<number | ''>(props.filters.event_id ?? '');
const selectedStaffId = ref<number | ''>(props.filters.staff_id ?? '');
const selectedRole = ref<string>(props.filters.role ?? '');

// Derive filter options from current page data to avoid extra props
const eventOptions = computed(() => {
  const map = new Map<number, string>();
  props.assignments.data.forEach((a: any) => {
    const id = a.event_id;
    const title = a.event?.title ?? a.event_title ?? `Event #${id}`;
    if (id != null && !map.has(id)) map.set(id, title);
  });
  return Array.from(map.entries()).map(([id, title]) => ({ id, title }));
});

const staffOptions = computed(() => {
  const map = new Map<number, string>();
  props.assignments.data.forEach((a: any) => {
    const id = a.staff_id;
    const name = a.staff?.full_name
      ?? (a.staff_first_name && a.staff_last_name ? `${a.staff_first_name} ${a.staff_last_name}` : `Staff #${id}`);
    if (id != null && !map.has(id)) map.set(id, name);
  });
  return Array.from(map.entries()).map(([id, name]) => ({ id, name }));
});

const roleOptions = computed(() => {
  const set = new Set<string>();
  props.assignments.data.forEach((a: any) => { if (a.role) set.add(a.role); });
  return Array.from(set.values());
});

const formattedGeneratedDate = computed(() => {
    return format(new Date(), 'PPP p');
});

watch([search, sortField, sortDirection, perPage, selectedEventId, selectedStaffId, selectedRole], debounce(() => {
    const params: Record<string, string | number> = { // Explicitly type params
        search: search.value,
        direction: sortDirection.value,
        per_page: perPage.value,
    };

    if (sortField.value) {
        params.sort = sortField.value;
    }

    if (selectedEventId.value !== '') params.event_id = Number(selectedEventId.value);
    if (selectedStaffId.value !== '') params.staff_id = Number(selectedStaffId.value);
    if (selectedRole.value) params.role = selectedRole.value;

    router.get(route('admin.event-staff-assignments.index'), params, {
        preserveState: true,
        replace: true,
    });
}, 500));

function destroy(id: number) { // Explicitly type id
    if (confirm('Are you sure you want to delete this event staff assignment?')) {
        router.delete(route('admin.event-staff-assignments.destroy', id));
    }
}

function printPage() {
  setTimeout(() => {
    try {
      window.print();
    } catch (error) {
      console.error('Print failed:', error);
      alert('Failed to open print dialog. Please try again.');
    }
  }, 100);
}

function toggleSort(field: string) { // Explicitly type field
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
}


function truncate(str: string | undefined, n = 60): string {
  if (!str) return '';
  return str.length > n ? `${str.slice(0, n)}…` : str;
}


</script>

<template>
    <Head title="Event Staff Assignments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6 print:p-0 print:space-y-0">
            <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Event Staff Assignments</h1>
                    <p class="text-sm text-muted-foreground">Manage all event staff assignments here.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link :href="route('admin.event-staff-assignments.create')" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm px-4 py-2 rounded-md transition">
                        + Add Assignment
                    </Link>
                    <button @click="printPage" class="btn btn-dark inline-flex items-center gap-1">
                        <Printer class="h-4 w-4" /> Print Current
                    </button>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
                <div class="relative w-full md:w-1/3">
                    <input
                        type="text"
                        v-model="search"
                        placeholder="Search assignments..."
                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                    />
                    <Search class="absolute right-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" />
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

            <!-- Filters Row -->
            <div class="flex flex-col md:flex-row gap-4 items-center print:hidden">
              <div class="w-full md:w-1/3">
                <label class="block text-xs text-gray-600 mb-1">Event</label>
                <select v-model="selectedEventId" class="w-full rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
                  <option :value="''">All Events</option>
                  <option v-for="e in eventOptions" :key="e.id" :value="e.id">{{ e.title }}</option>
                </select>
              </div>
              <div class="w-full md:w-1/3">
                <label class="block text-xs text-gray-600 mb-1">Staff</label>
                <select v-model="selectedStaffId" class="w-full rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
                  <option :value="''">All Staff</option>
                  <option v-for="s in staffOptions" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
              </div>
              <div class="w-full md:w-1/3">
                <label class="block text-xs text-gray-600 mb-1">Role</label>
                <select v-model="selectedRole" class="w-full rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
                  <option value="">All Roles</option>
                  <option v-for="r in roleOptions" :key="r" :value="r">{{ r }}</option>
                </select>
              </div>
            </div>

            <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                    <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                    <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                    <p class="text-gray-600 dark:text-gray-400 print-document-title">Event Staff Assignments List (Current View)</p>
                    <hr class="my-3 border-gray-300 print:my-2">
                </div>

                <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
                        <tr>
                            <th class="px-6 py-3 cursor-pointer" @click="toggleSort('event_id')">
                                Event <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
                            </th>
                            <th class="px-6 py-3 cursor-pointer" @click="toggleSort('staff_id')">
                                Staff <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
                            </th>
                            <th class="px-6 py-3 cursor-pointer" @click="toggleSort('role')">
                                Role <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
                            </th>
                            <th class="px-6 py-3">Notes</th>
                            <th class="px-6 py-3 text-right print:hidden">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="assignment in assignments.data" :key="assignment.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
                            <td class="px-6 py-4">{{ assignment.event?.title ?? assignment.event_title ?? assignment.event_id }}</td>
                            <td class="px-6 py-4">
                              {{ assignment.staff?.full_name
                                  ?? (assignment.staff_first_name && assignment.staff_last_name
                                        ? `${assignment.staff_first_name} ${assignment.staff_last_name}`
                                        : assignment.staff_id) }}
                            </td>
                            <td class="px-6 py-4">{{ assignment.role }}</td>
                            <td class="px-6 py-4">{{ truncate(assignment.notes, 60) }}</td>
                            <td class="px-6 py-4 text-right print:hidden">
                                <div class="inline-flex items-center justify-end space-x-2">
                                    <Link
                                        :href="route('admin.event-staff-assignments.show', assignment.id)"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                                        title="View Details"
                                    >
                                        <Eye class="w-4 h-4" />
                                    </Link>
                                    <Link
                                        :href="route('admin.event-staff-assignments.edit', assignment.id)"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600"
                                        title="Edit"
                                    >
                                        <Edit3 class="w-4 h-4" />
                                    </Link>
                                    <button @click="destroy(assignment.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900" title="Delete">
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="assignments.data.length === 0">
                            <td colspan="5" class="text-center px-6 py-4 text-gray-400">No event staff assignments found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination v-if="assignments.data.length > 0" :links="assignments.links" class="mt-6 flex justify-center print:hidden" />

            <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
                <hr class="my-2 border-gray-300">
                <p>Document Generated: {{ formattedGeneratedDate }}</p>
            </div>
        </div>
    </AppLayout>
</template>
