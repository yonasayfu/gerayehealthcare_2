<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { type BreadcrumbItem, type LeaveRequest, type LeaveRequestsPagination, type AppPageProps } from '@/types';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter,
} from '@/components/ui/dialog';
import { ref, watch } from 'vue';
import { ChevronUp, ChevronDown } from 'lucide-vue-next';

// Define props from the controller
const props = defineProps<{
  leaveRequests: LeaveRequestsPagination;
  filters: {
    search: string | null;
    sort_by: string;
    sort_order: 'asc' | 'desc';
  };
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Leave Requests', href: route('admin.admin-leave-requests.index') },
];

// Reactive variables for search and sort
const searchInput = ref(props.filters.search || '');
const currentSortBy = ref(props.filters.sort_by);
const currentSortOrder = ref(props.filters.sort_order);

// Watch for changes in searchInput and trigger search after a delay
let searchTimeout: ReturnType<typeof setTimeout> | null = null;
watch(searchInput, (newValue) => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }
  searchTimeout = setTimeout(() => {
    applyFilters();
  }, 300); // 300ms debounce
});

// Function to apply filters (search and sort)
const applyFilters = (options?: { preserveState?: boolean; preserveScroll?: boolean }) => {
  console.log('Applying filters (manual trigger):', {
    search: searchInput.value,
    sort_by: currentSortBy.value,
    sort_order: currentSortOrder.value,
    options
  }); // Debug log for filters

  router.get(
    route('admin.admin-leave-requests.index'),
    {
      search: searchInput.value,
      sort_by: currentSortBy.value,
      sort_order: currentSortOrder.value,
    },
    {
      preserveState: options?.preserveState ?? true,
      preserveScroll: options?.preserveScroll ?? true,
      only: ['leaveRequests', 'filters'], // Only reload these props
    }
  );
};

// Function to handle column sorting
const sortColumn = (frontendColumnName: string) => {
  // Map friendly frontend column names to backend actual column names
  const backendColumnMap: Record<string, string> = {
    'staff_member': 'staff_first_name', // Backend sorts by 'staff.first_name'
    'dates_requested': 'start_date',
    'status': 'status',
  };

  const actualBackendColumn = backendColumnMap[frontendColumnName] || frontendColumnName;

  if (currentSortBy.value === actualBackendColumn) {
    // If clicking the currently sorted column, toggle the order
    currentSortOrder.value = currentSortOrder.value === 'asc' ? 'desc' : 'asc';
  } else {
    // If clicking a new column, set it as the sort column and default to ascending
    currentSortBy.value = actualBackendColumn;
    currentSortOrder.value = 'asc';
  }
  applyFilters(); // Apply the new sort
};


// Confirmation Dialog State
const isDialogOpen = ref(false);
const selectedRequest = ref<LeaveRequest | null>(null);

// Form for updating the request status
const form = useForm({
  status: '',
  admin_notes: '',
});

// Function to open the dialog and set the selected request
const openUpdateDialog = (request: LeaveRequest, status: 'Approved' | 'Denied') => {
  console.log('openUpdateDialog called for request ID:', request.id, 'New status:', status);
  console.log('Request data:', request); // Log the full request object

  selectedRequest.value = request;
  form.status = status;
  form.admin_notes = request.admin_notes || ''; // Ensure notes are pre-filled if existing
  isDialogOpen.value = true; // THIS IS KEY: Set to true to open the dialog
};

// Function to submit the update
const updateRequestStatus = () => {
  if (!selectedRequest.value) {
    console.error('ERROR: No selectedRequest.value. Dialog should not have been confirmed without one.');
    return;
  }

  console.log('Attempting to submit update for request ID:', selectedRequest.value.id);
  console.log('Form data being sent:', { status: form.status, admin_notes: form.admin_notes });

  form.put(route('admin.admin-leave-requests.update', selectedRequest.value.id), {
    preserveScroll: true,
    // When the backend responds with Inertia::location, it automatically performs a new GET request
    // and updates the page props. So, we don't need to manually trigger applyFilters() here.
    onSuccess: (page) => {
      console.log('Inertia PUT request SUCCESS! Backend instructed Inertia::location to refresh.');
      // The page props are already refreshed by the Inertia::location call from the backend.
      // We don't need to manually call applyFilters() here anymore.
      isDialogOpen.value = false; // Close the dialog
      form.reset(); // Reset form fields
      // Flash message will be available in AppLayout's usePage().props.flash.success
    },
    onError: (errors) => {
      console.error('Inertia PUT request FAILED! Errors:', errors);
      // Display specific errors if available
      if (errors && Object.keys(errors).length > 0) {
        alert('Validation failed: ' + Object.values(errors).join('\n'));
      } else {
        alert('An unknown error occurred during update. Check console and network tab.');
      }
    },
    onFinish: () => {
      console.log('Inertia PUT request finished (success or failure, and any subsequent navigation handled).');
      // Any cleanup or loading indicator hiding can go here
    }
  });
};

// Helper functions (remain unchanged)
const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

const statusColor = (status: string) => {
  switch (status) {
    case 'Approved':
      return 'bg-green-100 text-green-800';
    case 'Denied':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-yellow-100 text-yellow-800'; // Pending, or any other unhandled status
  }
};
</script>

<template>
  <Head title="Leave Requests Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="rounded-xl border border-border bg-white shadow-sm dark:border-sidebar-border dark:bg-background">
      <div class="border-b p-5">
        <h2 class="text-lg font-semibold">Staff Leave Requests</h2>
        <p class="mt-1 text-sm text-muted-foreground">Review and manage all time-off requests from staff.</p>
      </div>

      <div class="p-5">
        <div class="mb-4">
          <Input
            v-model="searchInput"
            placeholder="Search by staff name, reason, or status..."
            class="w-full md:w-1/2 lg:w-1/3"
          />
        </div>

        <div v-if="leaveRequests.data.length > 0" class="overflow-x-auto">
          <table class="min-w-full text-left text-sm">
            <thead>
              <tr>
                <th class="p-2 cursor-pointer select-none" @click="sortColumn('staff_member')">
                  <div class="flex items-center">
                    Staff Member
                    <template v-if="currentSortBy === 'staff_first_name'">
                      <ChevronUp class="h-4 w-4 ml-1" v-if="currentSortOrder === 'asc'" />
                      <ChevronDown class="h-4 w-4 ml-1" v-else />
                    </template>
                  </div>
                </th>
                <th class="p-2 cursor-pointer select-none" @click="sortColumn('dates_requested')">
                  <div class="flex items-center">
                    Dates Requested
                    <template v-if="currentSortBy === 'start_date'">
                      <ChevronUp class="h-4 w-4 ml-1" v-if="currentSortOrder === 'asc'" />
                      <ChevronDown class="h-4 w-4 ml-1" v-else />
                    </template>
                  </div>
                </th>
                <th class="p-2">Reason</th>
                <th class="p-2 cursor-pointer select-none" @click="sortColumn('status')">
                  <div class="flex items-center">
                    Status
                    <template v-if="currentSortBy === 'status'">
                      <ChevronUp class="h-4 w-4 ml-1" v-if="currentSortOrder === 'asc'" />
                      <ChevronDown class="h-4 w-4 ml-1" v-else />
                    </template>
                  </div>
                </th>
                <th class="p-2 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="request in leaveRequests.data" :key="request.id" class="border-t">
                <td class="p-2">{{ request.staff.first_name }} {{ request.staff.last_name }}</td>
                <td class="p-2">{{ formatDate(request.start_date) }} - {{ formatDate(request.end_date) }}</td>
                <td class="p-2">{{ request.reason || 'N/A' }}</td>
                <td class="p-2">
                  <span class="rounded-full px-2 py-1 text-xs font-medium" :class="statusColor(request.status)">
                    {{ request.status }}
                  </span>
                </td>
                <td class="p-2 text-right">
                  <div v-if="request.status === 'Pending'" class="flex justify-end gap-2">
                    <Button variant="outline" size="sm" @click="openUpdateDialog(request, 'Approved')">Approve</Button>
                    <Button variant="destructive" size="sm" @click="openUpdateDialog(request, 'Denied')">Deny</Button>
                  </div>
                  <span v-else class="text-xs text-muted-foreground">Reviewed</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="py-10 text-center text-muted-foreground">
          <p>There are no leave requests to review.</p>
        </div>
        <div v-if="leaveRequests.links.length > 3" class="mt-4 flex justify-end">
           <Link v-for="(link, index) in leaveRequests.links" :key="index" :href="link.url || '#'"
                class="px-4 py-2 text-sm"
                :class="{ 'font-bold text-primary': link.active, 'text-muted-foreground': !link.active }"
                v-html="link.label" />
        </div>
      </div>
    </div>

    <Dialog :open="isDialogOpen" @update:open="isDialogOpen = $event">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Confirm Action: {{ form.status }} Request</DialogTitle>
          <DialogDescription>
            <span v-if="selectedRequest">
                You are about to {{ form.status.toLowerCase() }} this leave request for
                **{{ selectedRequest.staff.first_name }} {{ selectedRequest.staff.last_name }}**.
            </span>
            <span v-else>
                You are about to {{ form.status.toLowerCase() }} a leave request.
            </span>
            You can add an optional note below.
          </DialogDescription>
        </DialogHeader>
        <div class="py-4">
          <Label for="admin_notes">Notes (Optional)</Label>
          <textarea
            id="admin_notes"
            v-model="form.admin_notes"
            placeholder="Reason for denial, etc."
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white"
          ></textarea>
           <div v-if="form.errors.admin_notes" class="text-red-500 text-xs mt-1">
            {{ form.errors.admin_notes }}
          </div>
        </div>
        <DialogFooter>
          <Button variant="outline" @click="isDialogOpen = false" :disabled="form.processing">Cancel</Button>
          <Button @click="updateRequestStatus" :disabled="form.processing">Confirm</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>

<style scoped>
/* Add any specific styles here if needed */
.truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.custom-scrollbar::-webkit-scrollbar { width: 18px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; border: 2px solid transparent; background-clip: content-box; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
.custom-scrollbar { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
</style>