<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link, usePage } from '@inertiajs/vue3';
import { type BreadcrumbItem, type LeaveRequestsPagination, type AppPageProps } from '@/types'; // Import AppPageProps
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { ref, onMounted, onBeforeUnmount } from 'vue'; // Import onMounted and onBeforeUnmount

// Define the props passed from the controller
const props = defineProps<{
  leaveRequests: LeaveRequestsPagination;
  leavePolicy?: { annual_days: number; used_days: number; remaining_days: number; pending_days: number; year: number };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'My Leave Requests', href: route('staff.leave-requests.index') },
];

// Function to reload the page data
const reloadPageData = () => {
  const page = usePage<AppPageProps>(); // Explicitly type usePage
  page.props.inertia.visit(route('staff.leave-requests.index'), {
    preserveScroll: true,
    preserveState: true,
    only: ['leaveRequests'], // Only reload the leaveRequests prop
  });
};

onMounted(() => {
  // Reload data when the component is mounted (page load or navigation)
  reloadPageData();

  // Add event listener to reload data when the window gains focus
  window.addEventListener('focus', reloadPageData);
});

onBeforeUnmount(() => {
  // Remove event listener when the component is unmounted
  window.removeEventListener('focus', reloadPageData);
});

// Form helper for submitting a new request
const form = useForm({
  start_date: '',
  end_date: '',
  reason: '',
});

const submitRequest = () => {
  form.post(route('staff.leave-requests.store'), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  });
};

// Helper function to format dates
const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

// Helper to get status badge color
const statusColor = (status: string) => {
  switch (status) {
    case 'Approved':
      return 'bg-green-100 text-green-800';
    case 'Denied':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-yellow-100 text-yellow-800';
  }
};
</script>

<template>
  <Head title="My Leave Requests" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">
      <!-- Leave Balance & Policy -->
      <div class="rounded-xl border border-border bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-background">
        <h2 class="text-lg font-semibold">Leave Balance ({{ props.leavePolicy?.year ?? new Date().getFullYear() }})</h2>
        <p class="text-sm text-muted-foreground mb-3">Annual allocation: {{ props.leavePolicy?.annual_days ?? 20 }} days</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
          <div class="p-3 rounded-md bg-gray-50 dark:bg-gray-800">
            <div class="text-muted-foreground">Used</div>
            <div class="text-lg font-semibold">{{ props.leavePolicy?.used_days ?? 0 }}</div>
          </div>
          <div class="p-3 rounded-md bg-gray-50 dark:bg-gray-800">
            <div class="text-muted-foreground">Pending</div>
            <div class="text-lg font-semibold">{{ props.leavePolicy?.pending_days ?? 0 }}</div>
          </div>
          <div class="p-3 rounded-md bg-gray-50 dark:bg-gray-800">
            <div class="text-muted-foreground">Remaining</div>
            <div class="text-lg font-semibold">{{ props.leavePolicy?.remaining_days ?? (props.leavePolicy?.annual_days || 20) }}</div>
          </div>
          <div class="p-3 rounded-md bg-gray-50 dark:bg-gray-800">
            <div class="text-muted-foreground">Policy</div>
            <div class="text-xs">Standard policy: {{ props.leavePolicy?.annual_days ?? 20 }} days/year, prorated and subject to approval.</div>
          </div>
        </div>
      </div>
      <div class="rounded-xl border border-border bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-background">
        <h2 class="text-lg font-semibold">Request Time Off</h2>
        <p class="mt-1 text-sm text-muted-foreground">Fill out the form below to submit a new leave request.</p>
        <form @submit.prevent="submitRequest" class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
          <div>
            <Label for="start_date">Start Date</Label>
            <Input id="start_date" type="date" v-model="form.start_date" />
            <InputError :message="form.errors.start_date" class="mt-2" />
          </div>
          <div>
            <Label for="end_date">End Date</Label>
            <Input id="end_date" type="date" v-model="form.end_date" />
            <InputError :message="form.errors.end_date" class="mt-2" />
          </div>
          <div class="md:col-span-2">
            <Label for="reason">Reason (Optional)</Label>
            <textarea
              id="reason"
              v-model="form.reason"
              rows="3"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            ></textarea>
            <InputError :message="form.errors.reason" class="mt-2" />
          </div>
          <div class="md:col-span-2">
            <Button type="submit" :disabled="form.processing" class="bg-cyan-600 hover:bg-cyan-700 text-white text-sm px-4 py-2 rounded-md transition">
              Submit Request
            </Button>
          </div>
        </form>
      </div>

      <div class="rounded-xl border border-border bg-white shadow-sm dark:border-sidebar-border dark:bg-background">
        <div class="border-b p-5">
          <h2 class="text-lg font-semibold">My Request History</h2>
        </div>
        <div class="p-5">
          <div v-if="leaveRequests.data.length > 0" class="overflow-x-auto">
            <table class="min-w-full text-left text-sm print-table">
              <thead>
                <tr>
                  <th class="p-2">Start Date</th>
                  <th class="p-2">End Date</th>
                  <th class="p-2">Reason</th>
                  <th class="p-2">Status</th>
                  <th class="p-2">Admin Notes</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="request in leaveRequests.data" :key="request.id" class="border-t">
                  <td class="p-2">{{ formatDate(request.start_date) }}</td>
                  <td class="p-2">{{ formatDate(request.end_date) }}</td>
                  <td class="p-2">{{ request.reason || 'N/A' }}</td>
                  <td class="p-2">
                    <span class="rounded-full px-2 py-1 text-xs font-medium" :class="statusColor(request.status)">
                      {{ request.status }}
                    </span>
                  </td>
                  <td class="p-2">{{ request.admin_notes || 'N/A' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="py-10 text-center text-muted-foreground">
            <p>You have not submitted any leave requests.</p>
          </div>
          <div v-if="leaveRequests.links.length > 3" class="mt-4 flex justify-end">
             <Link v-for="(link, index) in leaveRequests.links" :key="index" :href="link.url || '#'"
                  class="px-4 py-2 text-sm"
                  :class="{ 'font-bold text-primary': link.active, 'text-muted-foreground': !link.active }"
                  v-html="link.label" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
