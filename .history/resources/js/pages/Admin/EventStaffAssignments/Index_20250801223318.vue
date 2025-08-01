<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'
import AdminLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown, Eye, Search } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'
import { format } from 'date-fns'

const props = defineProps({
    assignments: Object,
    filters: Object,
});

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Event Staff Assignments', href: route('admin.event-staff-assignments.index') },
];

const search = ref(props.filters.search || '');
const sortField = ref(props.filters.sort || '');
const sortDirection = ref(props.filters.direction || 'asc');
const perPage = ref(props.filters.per_page || 5);

const formattedGeneratedDate = computed(() => {
    return format(new Date(), 'PPP p');
});

watch([search, sortField, sortDirection, perPage], debounce(() => {
    const params = {
        search: search.value,
        direction: sortDirection.value,
        per_page: perPage.value,
    };

    if (sortField.value) {
        params.sort = sortField.value;
    }

    router.get(route('admin.event-staff-assignments.index'), params, {
        preserveState: true,
        replace: true,
    });
}, 500));

function destroy(id) {
    if (confirm('Are you sure you want to delete this event staff assignment?')) {
        router.delete(route('admin.event-staff-assignments.destroy', id));
    }
}

function exportData(type) {
    window.open(route('admin.event-staff-assignments.export', { type }), '_blank');
}

function printCurrentView() {
    setTimeout(() => {
        try {
            window.print();
        } catch (error) {
            console.error('Print failed:', error);
            alert('Failed to open print dialog for current view. Please check your browser settings or try again.');
        }
    }, 100);
}

const printAllAssignments = () => {
    window.open(route('admin.event-staff-assignments.export', { type: 'pdf' }), '_blank');
};

function toggleSort(field) {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
}
</script>

<template>
    <Head title="Event Staff Assignments" />

    <AdminLayout :breadcrumbs="breadcrumbs">
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
                    <button @click="exportData('csv')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
                        <Download class="h-4 w-4" /> CSV
                    </button>
                    <button @click="exportData('pdf')" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
                        <FileText class="h-4 w-4" /> PDF
                    </button>
                    <button @click="printAllAssignments" class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200">
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
                        placeholder="Search assignments..."
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
                    <p class="text-gray-600 dark:text-gray-400 print-document-title">Event Staff Assignments List (Current View)</p>
                    <hr class="my-3 border-gray-300 print:my-2">
                </div>

                <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200 print-table">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground print-table-header">
                        <tr>
                            <th class="px-6 py-3 cursor-pointer" @click="toggleSort('event_id')">
                                Event ID <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
                            </th>
                            <th class="px-6 py-3 cursor-pointer" @click="toggleSort('staff_id')">
                                Staff ID <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
                            </th>
                            <th class="px-6 py-3 cursor-pointer" @click="toggleSort('role')">
                                Role <ArrowUpDown class="inline w-4 h-4 ml-1 print:hidden" />
                            </th>
                            <th class="px-6 py-3 text-right print:hidden">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="assignment in assignments.data" :key="assignment.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 print-table-row">
                            <td class="px-6 py-4">{{ assignment.event_id }}</td>
                            <td class="px-6 py-4">{{ assignment.staff_id }}</td>
                            <td class="px-6 py-4">{{ assignment.role }}</td>
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
                            <td colspan="4" class="text-center px-6 py-4 text-gray-400">No event staff assignments found.</td>
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
    </AdminLayout>
</template>

<style>
/* Print-specific styles for Index.vue (Print Current View) */
@media print {
    @page {
        size: A4 landscape;
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

    .print\:hidden {
        display: none !important;
    }

    .print-header-content {
        display: block !important;
        text-align: center;
        padding-top: 0.5cm;
        padding-bottom: 0.5cm;
        margin-bottom: 0.8cm;
    }
    .print-logo {
        max-width: 150px;
        max-height: 50px;
        margin-bottom: 0.5rem;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    .print-clinic-name {
        font-size: 1.6rem !important;
        margin-bottom: 0.2rem !important;
        line-height: 1.2 !important;
        font-weight: bold;
    }
    .print-document-title {
        font-size: 0.85rem !important;
        color: #555 !important;
    }
    hr { border-color: #ccc !important; }

    .space-y-6.p-6 {
        padding: 0 !important;
        margin: 0 !important;
    }

    .overflow-x-auto.bg-white.dark\:bg-gray-900.shadow.rounded-lg {
        box-shadow: none !important;
        border-radius: 0 !important;
        background-color: transparent !important;
        overflow: visible !important;
        padding: 1cm;
        transform: scale(0.97);
        transform-origin: top left;
    }

    .print-table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 0.8rem !important;
        table-layout: fixed;
    }

    .print-table-header {
        background-color: #f0f0f0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        text-transform: uppercase !important;
    }

    .print-table th, .print-table td {
        border: 1px solid #ddd !important;
        padding: 0.4rem 0.6rem !important;
        color: #000 !important;
        vertical-align: top !important;
        word-break: break-word;
    }

    .print-table th {
        font-weight: bold !important;
        font-size: 0.7rem !important;
        white-space: nowrap;
    }

    .print-table th:nth-child(1), .print-table td:nth-child(1) { width: 30%; }
    .print-table th:nth-child(2), .print-table td:nth-child(2) { width: 25%; }
    .print-table th:nth-child(3), .print-table td:nth-child(3) { width: 20%; }

    .print-table tbody tr:nth-child(even) {
        background-color: #f9f9f9 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .print-table tbody tr:last-child {
        border-bottom: 1px solid #ddd !important;
    }

    .print-table th:last-child,
    .print-table td:last-child {
        display: none !important;
    }

    .print\:hidden {
        display: none !important;
    }

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