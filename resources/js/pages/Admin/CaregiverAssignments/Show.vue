<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItemType } from '@/types'
import { Calendar, Clock, User, Stethoscope, BadgeCheck } from 'lucide-vue-next'

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
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Assignments', href: '/dashboard/assignments' },
  { title: 'Details', href: `/dashboard/assignments/${props.assignment.id}` },
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
</script>

<template>
  <Head title="Assignment Details" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Assignment #{{ assignment.id }}</h1>
            <p class="text-sm text-muted-foreground">Detailed view of the assignment record.</p>
        </div>
        <div class="flex items-center gap-2">
            <Link :href="route('admin.assignments.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium">
                Back to List
            </Link>
            <Link :href="route('admin.assignments.edit', assignment.id)" class="inline-flex items-center px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow-sm">
                Edit Assignment
            </Link>
            <a :href="route('admin.assignments.print', assignment.id)" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium">
                Print
            </a>
        </div>
      </div>

      <!-- Details Card -->
      <div class="rounded-lg bg-white dark:bg-gray-900 shadow-sm">
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Patient Info -->
            <div class="space-y-4">
                <h3 class="font-semibold text-lg text-gray-800 dark:text-white">Patient Information</h3>
                <div class="flex items-center gap-3">
                    <User class="w-5 h-5 text-muted-foreground" />
                    <span>{{ assignment.patient?.full_name ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Staff Info -->
            <div class="space-y-4">
                <h3 class="font-semibold text-lg text-gray-800 dark:text-white">Assigned Staff</h3>
                 <div class="flex items-center gap-3">
                    <Stethoscope class="w-5 h-5 text-muted-foreground" />
                    <span>{{ getStaffFullName(assignment.staff) }}</span>
                </div>
            </div>

            <!-- Shift Details -->
            <div class="space-y-4 md:col-span-2 border-t dark:border-gray-700 pt-6">
                 <h3 class="font-semibold text-lg text-gray-800 dark:text-white">Shift Details</h3>
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
  </AppLayout>
</template>