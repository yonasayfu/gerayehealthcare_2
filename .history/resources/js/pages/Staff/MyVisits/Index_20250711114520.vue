<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { format } from 'date-fns';
import type { BreadcrumbItemType } from '@/types';
import { FilePenLine } from 'lucide-vue-next';

defineProps<{
  visits: any;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'My Visits', href: route('staff.my-visits.index') },
];

const processingVisitId = ref<number | null>(null);
const locationError = ref<string | null>(null);

function handleLocationAction(visit: any, action: 'check-in' | 'check-out') {
  processingVisitId.value = visit.id;
  locationError.value = null;

  if (!navigator.geolocation) {
    locationError.value = 'Geolocation is not supported by your browser.';
    processingVisitId.value = null;
    return;
  }

  navigator.geolocation.getCurrentPosition(
    (position) => {
      const { latitude, longitude } = position.coords;
      const url = route(`staff.my-visits.${action}`, visit.id);

      router.post(url, {
        latitude,
        longitude,
      }, {
        preserveScroll: true,
        onFinish: () => {
          processingVisitId.value = null;
        },
      });
    },
    (error) => {
      switch (error.code) {
        case error.PERMISSION_DENIED:
          locationError.value = "You must allow location access to check in/out.";
          break;
        case error.POSITION_UNAVAILABLE:
          locationError.value = "Location information is unavailable.";
          break;
        case error.TIMEOUT:
          locationError.value = "The request to get user location timed out.";
          break;
        default:
          locationError.value = "An unknown error occurred.";
          break;
      }
      processingVisitId.value = null;
    },
  );
}

const formatDate = (dateString: string | null) => {
    if (!dateString) return 'N/A';
    return format(new Date(dateString), 'MMM dd, yyyy, hh:mm a');
};
</script>

<template>
  <Head title="My Visits" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">My Daily Visits</h1>
        <p class="text-sm text-muted-foreground">Your scheduled visits for today and the future.</p>
      </div>

      <!-- Location Error Message -->
      <div v-if="locationError" class="rounded-md bg-red-50 p-4">
        <div class="flex">
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Location Error</h3>
            <div class="mt-2 text-sm text-red-700">
              <p>{{ locationError }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Visits List -->
      <div class="space-y-4">
        <div v-if="visits.data.length === 0" class="text-center text-gray-500 py-8">
          You have no assigned visits.
        </div>
        <div
          v-for="visit in visits.data"
          :key="visit.id"
          class="rounded-lg border bg-white dark:bg-background p-4 shadow-sm space-y-3"
        >
          <div class="flex justify-between items-start">
            <div>
              <p class="font-semibold text-primary">{{ visit.patient.full_name }}</p>
              <p class="text-sm text-muted-foreground">{{ formatDate(visit.scheduled_at) }}</p>
            </div>
            <span :class="{
              'px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full': true,
              'bg-yellow-100 text-yellow-800': visit.status === 'Pending',
              'bg-blue-100 text-blue-800': visit.status === 'In Progress',
              'bg-green-100 text-green-800': visit.status === 'Completed',
              'bg-red-100 text-red-800': visit.status === 'Cancelled',
            }">
              {{ visit.status }}
            </span>
          </div>
          <div class="border-t pt-3 flex justify-end">
            <!-- Check In Button -->
            <button
              v-if="visit.status === 'Pending'"
              @click="handleLocationAction(visit, 'check-in')"
              :disabled="processingVisitId === visit.id"
              class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-md text-sm font-medium transition"
            >
              {{ processingVisitId === visit.id ? 'Checking In...' : 'Check In' }}
            </button>
            <!-- Check Out Button -->
            <button
              v-if="visit.status === 'In Progress'"
              @click="handleLocationAction(visit, 'check-out')"
              :disabled="processingVisitId === visit.id"
              class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white rounded-md text-sm font-medium transition"
            >
              {{ processingVisitId === visit.id ? 'Checking Out...' : 'Check Out' }}
            </button>
            <!-- File Report Button -->
            <Link
              v-if="visit.status === 'Completed' && !visit.service_id"
              :href="route('staff.my-visits.report.create', { visit: visit.id })"
              class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md text-sm font-medium transition"
            >
              <FilePenLine class="h-4 w-4" />
              File Visit Report
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
