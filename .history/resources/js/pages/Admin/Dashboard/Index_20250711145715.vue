<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Users, UserCog, ClipboardList, DollarSign } from 'lucide-vue-next';

// Define the props passed from the controller
const props = defineProps({
  stats: {
    type: Object,
    required: true,
  },
  recentVisits: {
    type: Array,
    required: true,
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
  <Head title="Admin Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-4 md:p-6">
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-border bg-white p-5 shadow-sm dark:border-sidebar-border dark:bg-background">
          <div class="flex items-center gap-4">
            <Users class="h-8 w-8 text-primary" />
            <div>
              <p class="text-sm font-medium text-muted-foreground">Total Patients</p>
              <p class="text-2xl font-semibold">{{ stats.totalPatients }}</p>
            </div>
          </div>
        </div>
        <div class="rounded-xl border border-border bg-white p-5 shadow-sm dark:border-sidebar-border dark:bg-background">
          <div class="flex items-center gap-4">
            <UserCog class="h-8 w-8 text-primary" />
            <div>
              <p class="text-sm font-medium text-muted-foreground">Active Staff</p>
              <p class="text-2xl font-semibold">{{ stats.totalStaff }}</p>
            </div>
          </div>
        </div>
        <div class="rounded-xl border border-border bg-white p-5 shadow-sm dark:border-sidebar-border dark:bg-background">
          <div class="flex items-center gap-4">
            <ClipboardList class="h-8 w-8 text-primary" />
            <div>
              <p class="text-sm font-medium text-muted-foreground">Pending Visits</p>
              <p class="text-2xl font-semibold">{{ stats.pendingVisits }}</p>
            </div>
          </div>
        </div>
        <div class="rounded-xl border border-border bg-white p-5 shadow-sm dark:border-sidebar-border dark:bg-background">
          <div class="flex items-center gap-4">
            <DollarSign class="h-8 w-8 text-primary" />
            <div>
              <p class="text-sm font-medium text-muted-foreground">Total Revenue</p>
              <p class="text-2xl font-semibold">${{ stats.totalEarnings }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="rounded-xl border border-border bg-white shadow-sm dark:border-sidebar-border dark:bg-background">
        <div class="border-b p-5">
          <h2 class="text-lg font-semibold">Recent Activity</h2>
          <p class="text-sm text-muted-foreground">A log of the latest patient visits.</p>
        </div>
        <div class="p-5">
          <div v-if="recentVisits.length > 0" class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
              <thead class="font-medium">
                <tr>
                  <th class="p-2">Patient</th>
                  <th class="p-2">Staff Member</th>
                  <th class="p-2">Scheduled At</th>
                  <th class="p-2">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="visit in recentVisits" :key="visit.id" class="border-t">
                  <td class="p-2">{{ visit.patient?.full_name || 'N/A' }}</td>
                  <td class="p-2">{{ visit.staff ? `${visit.staff.first_name} ${visit.staff.last_name}` : 'Unassigned' }}</td>
                  <td class="p-2">{{ formatDate(visit.scheduled_at) }}</td>
                  <td class="p-2">
                    <span class="rounded-full px-2 py-1 text-xs font-medium"
                          :class="{
                            'bg-yellow-100 text-yellow-800': visit.status === 'Pending',
                            'bg-green-100 text-green-800': visit.status === 'Completed',
                            'bg-blue-100 text-blue-800': visit.status === 'Checked In'
                          }">
                      {{ visit.status }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="py-10 text-center text-muted-foreground">
            <p>No recent activity found.</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>