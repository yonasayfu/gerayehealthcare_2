<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { CalendarClock, FileText, DollarSign, ArrowRight } from 'lucide-vue-next';

// Define the props passed from the Staff/DashboardController.
// They are not required, so this component can still render if no props are passed.
const props = defineProps({
  stats: {
    type: Object,
    required: false,
  },
  upcomingVisits: {
    type: Array,
    required: false,
  },
});

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: route('dashboard') },
];

// Helper function to format dates for readability
const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A';
  const options: Intl.DateTimeFormatOptions = {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  };
  return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4 md:p-6 bg-gray-800">
      <template v-if="stats">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
          <Link :href="route('staff.my-visits.index')" class="block rounded-xl border border-blue-700 bg-blue-900 p-5 shadow-lg transition hover:border-blue-500">
            <div class="flex items-center gap-4">
              <CalendarClock class="h-8 w-8 text-blue-300" />
              <div>
                <p class="text-sm font-medium text-blue-200">Upcoming Visits</p>
                <p class="text-2xl font-semibold text-white">{{ stats.upcomingVisitsCount }}</p>
              </div>
            </div>
          </Link>
          <Link :href="route('staff.my-earnings.index')" class="block rounded-xl border border-green-700 bg-green-900 p-5 shadow-lg transition hover:border-green-500">
            <div class="flex items-center gap-4">
              <FileText class="h-8 w-8 text-green-300" />
              <div>
                <p class="text-sm font-medium text-green-200">Completed & Unpaid Visits</p>
                <p class="text-2xl font-semibold text-white">{{ stats.unpaidVisitsCount }}</p>
              </div>
            </div>
          </Link>
          <Link :href="route('staff.my-earnings.index')" class="block rounded-xl border border-purple-700 bg-purple-900 p-5 shadow-lg transition hover:border-purple-500">
            <div class="flex items-center gap-4">
              <DollarSign class="h-8 w-8 text-purple-300" />
              <div>
                <p class="text-sm font-medium text-purple-200">Pending Earnings</p>
                <p class="text-2xl font-semibold text-white">${{ stats.unpaidEarnings }}</p>
              </div>
            </div>
          </Link>
        </div>

        <div class="rounded-xl border border-gray-700 bg-gray-900 shadow-lg">
          <div class="flex items-center justify-between border-b border-gray-700 p-5">
            <div>
                <h2 class="text-lg font-semibold text-white">My Upcoming Schedule</h2>
                <p class="text-sm text-gray-400">Your next 5 scheduled patient visits.</p>
            </div>
            <Link :href="route('staff.my-visits.index')" class="flex items-center gap-2 text-sm font-semibold text-blue-400 hover:underline">
                View All <ArrowRight class="h-4 w-4" />
            </Link>
          </div>
          <div class="p-5">
            <div v-if="upcomingVisits && upcomingVisits.length > 0" class="overflow-x-auto">
              <table class="min-w-full text-left text-sm">
                <thead class="font-medium">
                  <tr>
                    <th class="p-2">Patient</th>
                    <th class="p-2">Scheduled At</th>
                    <th class="p-2">Address</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="visit in upcomingVisits" :key="visit.id" class="border-t">
                    <td class="p-2">{{ visit.patient?.full_name || 'N/A' }}</td>
                    <td class="p-2">{{ formatDate(visit.scheduled_at) }}</td>
                    <td class="p-2">{{ visit.patient?.address || 'N/A' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="py-10 text-center text-muted-foreground">
              <p>You have no upcoming visits scheduled.</p>
            </div>
          </div>
        </div>
      </template>

      <template v-else>
        <div class="rounded-xl border border-border bg-white p-8 text-center text-muted-foreground shadow-sm dark:border-sidebar-border dark:bg-background">
            Welcome to your dashboard. Select a section from the sidebar to begin.
        </div>
      </template>
    </div>
  </AppLayout>
</template>