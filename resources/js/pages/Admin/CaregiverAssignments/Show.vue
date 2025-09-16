<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItemType } from '@/types'
import { Calendar, Clock, User, Stethoscope, BadgeCheck } from 'lucide-vue-next'
import ShowHeader from '@/components/ShowHeader.vue'

const props = defineProps<{
  assignment: {
    id: number;
    patient: { id: number; full_name: string; };
    staff: { id: number; first_name: string; last_name: string; };
    shift_start: string;
    shift_end: string;
    status: string;
  };
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Assignments', href: route('admin.assignments.index') },
  { title: 'Details', href: route('admin.assignments.show', props.assignment.id) },
]

// Helper to combine staff first and last names
const getStaffFullName = (staff) => {
    if (!staff) return 'N/A';
    return `${staff.first_name || ''} ${staff.last_name || ''}`.trim();
}

// Helper to format dates for readability
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString(undefined, {
        year: 'numeric', month: 'long', day: 'numeric'
    });
}

const formatTime = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleTimeString(undefined, {
        hour: '2-digit', minute: '2-digit'
    });
}

function printSingleAssignment() {
  // Print the current page using the built-in browser print dialog
  window.print();
}
</script>

<template>
  <Head title="Assignment Details" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">
        <ShowHeader title="Assignment Details" :subtitle="`Assignment #${assignment.id}`">
          <template #actions>
            <Link :href="route('admin.assignments.index')" class="btn-glass btn-glass-sm">Back</Link>
          </template>
        </ShowHeader>
      </div>

        <div class="p-6 space-y-6">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                    <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                    <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                    <p class="text-gray-600 dark:text-gray-400 print-document-title">Assignment Record Document</p>
                    <hr class="my-3 border-gray-300 print:my-2">
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Patient Info -->
                    <div class="space-y-4 border-b pb-4 mb-4 print:pb-2 print:mb-2">
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-white print:mb-2">Patient Information</h3>
                        <div class="flex items-center gap-3">
                            <User class="w-5 h-5 text-muted-foreground" />
                            <span>{{ assignment.patient?.full_name ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Staff Info -->
                    <div class="space-y-4 border-b pb-4 mb-4 print:pb-2 print:mb-2">
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-white print:mb-2">Assigned Staff</h3>
                         <div class="flex items-center gap-3">
                            <Stethoscope class="w-5 h-5 text-muted-foreground" />
                            <span>{{ getStaffFullName(assignment.staff) }}</span>
                        </div>
                    </div>

                    <!-- Shift Details -->
                    <div class="space-y-4 md:col-span-2 border-t dark:border-gray-700 pt-6">
                         <h3 class="font-semibold text-lg text-gray-800 dark:text-white print:mb-2">Shift Details</h3>
                         <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <Calendar class="w-5 h-5 text-muted-foreground" />
                                <div>
                                    <p class="text-sm text-muted-foreground">Shift Start Date</p>
                                    <p class="font-medium">{{ formatDate(assignment.shift_start) }}</p>
                                </div>
                            </div>
                             <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <Clock class="w-5 h-5 text-muted-foreground" />
                                <div>
                                    <p class="text-sm text-muted-foreground">Shift Start Time</p>
                                    <p class="font-medium">{{ formatTime(assignment.shift_start) }}</p>
                                </div>
                            </div>
                             <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                                <BadgeCheck class="w-5 h-5 text-muted-foreground" />
                                 <div>
                                    <p class="text-sm text-muted-foreground">Status</p>
                                    <p class="font-medium">{{ assignment.status }}</p>
                                </div>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>

      <!-- Footer actions -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          
          <Link :href="route('admin.assignments.edit', assignment.id)" class="btn-glass btn-glass-sm">Edit Assignment</Link>
          <button @click="printSingleAssignment" class="btn-glass btn-glass-sm">Print Current</button>
        </div>
      </div>

        <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
          <hr class="my-2 border-gray-300">
          <p>Printed on: {{ formatDate(new Date().toISOString()) }}</p>
        </div>

    </div>
  </AppLayout>
</template>

<style>
/* Print styles for Single Assignment */
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
  }

  .print\:hidden {
    display: none !important;
  }

  .hidden.print\:block {
    display: block !important;
  }

  /* Print header styling */
  .print-header-content {
    padding-top: 0.5cm !important;
    padding-bottom: 0.5cm !important;
    margin-bottom: 0.8cm !important;
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
    font-size: 1.8rem !important;
    margin-bottom: 0.2rem !important;
    line-height: 1.2 !important;
  }

  .print-document-title {
    font-size: 0.9rem !important;
    color: #555 !important;
  }

  /* Content adjustments */
  .space-y-8 > div:not(:first-child) {
    margin-top: 0.8rem !important;
    margin-bottom: 0.8rem !important;
  }
}
</style>
