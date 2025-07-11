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
import { ref, watch } from 'vue'; // No need for computed if not using derived state
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
// Ensure these refs are initialized directly from props.filters to reflect initial state
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
// Made `options` parameter explicit for clarity
const applyFilters = (options?: { preserveState?: boolean; preserveScroll?: boolean }) => {
  // Use Inertia.js router.get to make the request
  router.get(
    route('admin.admin-leave-requests.index'),
    {
      search: searchInput.value,
      sort_by: currentSortBy.value,
      sort_order: currentSortOrder.value,
    },
    {
      // Default to true for preserveState and preserveScroll if not explicitly set
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
    // Add other sortable columns here if needed
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
  console.log('openUpdateDialog called for request:', request.id, 'status:', status); // Debug log
  selectedRequest.value = request;
  form.status = status;
  form.admin_notes = request.admin_notes || ''; // Ensure notes are pre-filled if existing
  isDialogOpen.value = true; // THIS IS KEY: Set to true to open the dialog
};

// Function to submit the update
const updateRequestStatus = () => {
  if (!selectedRequest.value) {
    console.error('No request selected for update.');
    return;
  }

  console.log('Submitting update for request ID:', selectedRequest.value.id, 'New Status:', form.status); // Debug log

  form.put(route('admin.admin-leave-requests.update', selectedRequest.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      console.log('Leave request update successful! Triggering data reload...'); // Debug log
      isDialogOpen.value = false; // Close the dialog
      form.reset(); // Reset form fields

      // Crucial: Re-fetch the data from the server to ensure the UI reflects the latest state.
      // Setting preserveState to false ensures a full re-render of props if needed,
      // but 'only' still limits which props are fetched.
      applyFilters({ preserveState: false, preserveScroll: true });
    },
    onError: (errors) => {
        console.error('Error updating leave request:', errors); // Debug log
        // You might want to display these errors to the user (e.g., using a toast notification library)
        alert('Failed to update leave request. Check console for details.'); // Simple alert for debugging
    },
    onFinish: () => {
        console.log('Update request finished.'); // Debug log
        // Any cleanup or loading indicator hiding
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
      return 'bg-yellow-100 text-yellow-800';
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
            You are about to {{ form.status.toLowerCase() }} this leave request for
            **{{ selectedRequest?.staff.first_name }} {{ selectedRequest?.staff.last_name }}**.
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
/* No changes needed here, just ensuring it's part of the full file */
.truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.custom-scrollbar::-webkit-scrollbar { width: 18px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; border: 2px solid transparent; background-clip: content-box; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
.custom-scrollbar { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
</style>