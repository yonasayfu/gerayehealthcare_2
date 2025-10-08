<script setup lang="ts">
import { Head, useForm, Link, usePage, router } from '@inertiajs/vue3';
import type { BreadcrumbItemType } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ref, onMounted, onBeforeUnmount, nextTick, computed } from 'vue';
import { Calendar, List } from 'lucide-vue-next';

// Define pagination interface locally
interface LeaveRequest {
  id: number;
  staff_id: number;
  start_date: string;
  end_date: string;
  reason: string;
  type: string;
  status: string;
  admin_notes: string;
  created_at: string;
}

interface LeaveRequestsPagination {
  data: LeaveRequest[];
  links: any[];
  current_page: number;
  from: number;
  last_page: number;
  per_page: number;
  to: number;
  total: number;
}

// Define the props passed from the controller
const props = defineProps<{
  leaveRequests: LeaveRequestsPagination;
  leavePolicy?: { annual_days: number; used_days: number; remaining_days: number; pending_days: number; year: number };
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'My Leave Requests', href: route('staff.leave-requests.index') },
];

// View mode state
const viewMode = ref<'list' | 'calendar'>('list');

// Function to reload the page data
const reloadPageData = () => {
  router.get(route('staff.leave-requests.index'), {}, {
    preserveScroll: true,
    preserveState: true,
    only: ['leaveRequests'],
  });
};

onMounted(() => {
  // Add event listener to reload data when the window gains focus
  window.addEventListener('focus', reloadPageData);

  // Deep-link highlight support (e.g., from notification)
  try {
    const page = usePage();
    const url = new URL((page.props as any).ziggy?.location || window.location.href);
    const h = url.searchParams.get('highlight');
    if (h) {
      nextTick(() => {
        const el = document.querySelector(`[data-leave-id="${h}"]`);
        if (el) {
          (el as HTMLElement).scrollIntoView({ behavior: 'smooth', block: 'center' });
          (el as HTMLElement).classList.add('ring-2','ring-amber-400');
          setTimeout(() => (el as HTMLElement).classList.remove('ring-2','ring-amber-400'), 3000);
        }
      });
    }
  } catch {}
});

onBeforeUnmount(() => {
  // Remove event listener when the component is unmounted
  window.removeEventListener('focus', reloadPageData);
});

// Form helper for submitting a new request
const form = useForm({
  start_date: '',
  end_date: '',
  type: 'Annual',
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

// Computed property for calendar events
const calendarEvents = computed(() => {
  return props.leaveRequests.data.map((request) => ({
    id: request.id,
    title: `${request.type} Leave (${request.status})`,
    start: request.start_date,
    end: request.end_date,
    status: request.status,
    color: request.status === 'Approved' ? 'bg-green-500' : request.status === 'Denied' ? 'bg-red-500' : 'bg-yellow-500'
  }));
});

// Function to safely render pagination links
const renderPaginationLink = (link: any) => {
  // Create a temporary element to parse the HTML and extract text
  const temp = document.createElement('div');
  temp.innerHTML = link.label;
  return temp.textContent || temp.innerText || '';
};
</script>

<template>
  <Head title="My Leave Requests" />

  <div class="space-y-6">
    <!-- View Toggle -->
    <div class="flex justify-end">
      <div class="flex rounded-md shadow-sm">
        <Button
          type="button"
          @click="viewMode = 'list'"
          :variant="viewMode === 'list' ? 'default' : 'outline'"
          class="rounded-r-none border-r-0 px-3"
        >
          <List class="h-4 w-4 mr-2" />
          List View
        </Button>
        <Button
          type="button"
          @click="viewMode = 'calendar'"
          :variant="viewMode === 'calendar' ? 'default' : 'outline'"
          class="rounded-l-none px-3"
        >
          <Calendar class="h-4 w-4 mr-2" />
          Calendar View
        </Button>
      </div>
    </div>

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

    <!-- Request Time Off Form -->
    <div class="rounded-xl border border-border bg-white p-6 shadow-sm dark:border-sidebar-border dark:bg-background">
      <h2 class="text-lg font-semibold">Request Time Off</h2>
      <p class="mt-1 text-sm text-muted-foreground">Fill out the form below to submit a new leave request.</p>
      <form @submit.prevent="submitRequest" class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
          <Label for="start_date">Start Date</Label>
          <Input id="start_date" type="date" v-model="form.start_date" />
          <div v-if="form.errors.start_date" class="text-red-500 text-xs mt-1">
            {{ form.errors.start_date }}
          </div>
        </div>
        <div>
          <Label for="end_date">End Date</Label>
          <Input id="end_date" type="date" v-model="form.end_date" />
          <div v-if="form.errors.end_date" class="text-red-500 text-xs mt-1">
            {{ form.errors.end_date }}
          </div>
        </div>
        <div>
          <Label for="type">Leave Type</Label>
          <select id="type" v-model="form.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            <option value="Annual">Annual</option>
            <option value="Sick">Sick</option>
            <option value="Unpaid">Unpaid</option>
          </select>
          <div v-if="form.errors.type" class="text-red-500 text-xs mt-1">
            {{ form.errors.type }}
          </div>
        </div>
        <div class="md:col-span-2">
          <Label for="reason">Reason <span class="text-red-500">*</span></Label>
          <textarea
            id="reason"
            v-model="form.reason"
            rows="3"
            required
            placeholder="Please provide a reason for your leave request..."
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white"
          ></textarea>
          <div v-if="form.errors.reason" class="text-red-500 text-xs mt-1">
            {{ form.errors.reason }}
          </div>
        </div>
        <div class="md:col-span-2">
          <Button type="submit" :disabled="form.processing" class="bg-cyan-600 hover:bg-cyan-700 text-white text-sm px-4 py-2 rounded-md transition">
            Submit Request
          </Button>
        </div>
      </form>
    </div>

    <!-- Leave Requests Display -->
    <div class="rounded-xl border border-border bg-white shadow-sm dark:border-sidebar-border dark:bg-background">
      <div class="border-b p-5">
        <h2 class="text-lg font-semibold">My Request History</h2>
      </div>
      <div class="p-5">
        <!-- List View -->
        <div v-if="viewMode === 'list'">
          <div v-if="leaveRequests.data.length > 0" class="overflow-x-auto">
            <table class="min-w-full text-left text-sm print-table">
              <thead>
                <tr>
                  <th class="p-2">Start Date</th>
                  <th class="p-2">End Date</th>
                  <th class="p-2">Type</th>
                  <th class="p-2">Reason</th>
                  <th class="p-2">Status</th>
                  <th class="p-2">Admin Notes</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="request in leaveRequests.data" :key="request.id" class="border-t" :data-leave-id="request.id">
                  <td class="p-2">{{ formatDate(request.start_date) }}</td>
                  <td class="p-2">{{ formatDate(request.end_date) }}</td>
                  <td class="p-2">{{ request.type }}</td>
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
                  :class="{ 'font-bold text-primary': link.active, 'text-muted-foreground': !link.active }">
                  {{ renderPaginationLink(link) }}
             </Link>
          </div>
        </div>

        <!-- Calendar View -->
        <div v-else class="min-h-[500px]">
          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-center text-muted-foreground">Calendar view would be implemented here with a library like FullCalendar.js</p>
            <p class="text-center text-sm mt-2">Each leave request would be shown as an event on the calendar with color coding based on status.</p>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
              <div v-for="event in calendarEvents" :key="event.id" class="border rounded p-3" :class="event.color.replace('500', '100') + ' border-' + event.color.replace('bg-', '').replace('500', '300')">
                <div class="font-medium">{{ event.title }}</div>
                <div class="text-sm">{{ formatDate(event.start) }} - {{ formatDate(event.end) }}</div>
                <div class="text-xs mt-1" :class="statusColor(event.status).split(' ')[0]">
                  Status: {{ event.status }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>