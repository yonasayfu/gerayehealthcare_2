<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { format } from 'date-fns';
import { MapPin, Paperclip, Calendar, Clock, User, Stethoscope, FileText } from 'lucide-vue-next';

const props = defineProps<{
  visitService: any;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Visit Services', href: route('admin.visit-services.index') },
  { title: `Visit #${props.visitService.id}` },
];

const formatCurrency = (value: number | string | null) => {
  const amount = parseFloat(value?.toString() || '0');
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};

const formatDate = (dateString: string | null) => {
  if (!dateString) return 'N/A';
  return format(new Date(dateString), 'MMM dd, yyyy');
};

const formatTime = (dateString: string | null) => {
  if (!dateString) return 'N/A';
  return format(new Date(dateString), 'hh:mm a');
};
</script>

<template>
  <Head :title="`Visit Details #${visitService.id}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Visit Details #{{ visitService.id }}
            </h3>
            <Link :href="route('admin.visit-services.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left Column: Key Info -->
                <div class="md:col-span-1 space-y-4">
                  <div class="flex items-center gap-3">
                    <User class="w-5 h-5 text-muted-foreground" />
                    <div>
                      <p class="text-sm text-muted-foreground">Patient</p>
                      <p class="font-semibold">{{ visitService.patient.full_name }}</p>
                    </div>
                  </div>
                  <div class="flex items-center gap-3">
                    <Stethoscope class="w-5 h-5 text-muted-foreground" />
                    <div>
                      <p class="text-sm text-muted-foreground">Assigned Staff</p>
                      <p class="font-semibold">{{ visitService.staff.first_name }} {{ visitService.staff.last_name }}</p>
                    </div>
                  </div>
                  <div class="flex items-center gap-3">
                    <Calendar class="w-5 h-5 text-muted-foreground" />
                    <div>
                      <p class="text-sm text-muted-foreground">Scheduled Date</p>
                      <p class="font-semibold">{{ formatDate(visitService.scheduled_at) }} at {{ formatTime(visitService.scheduled_at) }}</p>
                    </div>
                  </div>
                   <div class="flex items-center gap-3">
                    <Clock class="w-5 h-5 text-muted-foreground" />
                    <div>
                      <p class="text-sm text-muted-foreground">Status</p>
                      <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="{
                          'bg-yellow-100 text-yellow-800': visitService.status === 'Pending',
                          'bg-green-100 text-green-800': visitService.status === 'Completed',
                          'bg-red-100 text-red-800': visitService.status === 'Cancelled',
                          'bg-blue-100 text-blue-800': visitService.status === 'In Progress'
                        }">
                          {{ visitService.status }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Right Column: Details & Notes -->
                <div class="md:col-span-2 space-y-4 border-t md:border-t-0 md:border-l md:pl-6 pt-6 md:pt-0">
                  <div>
                    <h4 class="font-semibold mb-1">Service Description</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ visitService.service_description || 'No description provided.' }}</p>
                  </div>
                  <div>
                    <h4 class="font-semibold mb-1">Visit Notes</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ visitService.visit_notes || 'No notes provided.' }}</p>
                  </div>
                  <div>
                    <h4 class="font-semibold mb-1">Attachments & Location</h4>
                    <div class="flex items-center gap-4 mt-2">
                        <a v-if="visitService.prescription_file_url" :href="visitService.prescription_file_url" target="_blank" class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:underline">
                            <Paperclip class="w-4 h-4" /> Prescription
                        </a>
                        <a v-if="visitService.vitals_file_url" :href="visitService.vitals_file_url" target="_blank" class="inline-flex items-center gap-2 text-sm text-teal-600 hover:underline">
                            <Paperclip class="w-4 h-4" /> Vitals
                        </a>
                        <a v-if="visitService.check_in_latitude" :href="`https://www.google.com/maps/search/?api=1&query=${visitService.check_in_latitude},${visitService.check_in_longitude}`" target="_blank" class="inline-flex items-center gap-2 text-sm text-blue-600 hover:underline">
                            <MapPin class="w-4 h-4" /> Check-in Location
                        </a>
                    </div>
                  </div>
                </div>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <Link :href="route('admin.visit-services.edit', visitService.id)" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-md transition">
              Edit Visit
            </Link>
        </div>

    </div>
  </AppLayout>
</template>