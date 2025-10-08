<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import type { BreadcrumbItemType } from '@/types';
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
import { ref, watch, nextTick, onMounted } from 'vue';
import { ChevronUp, ChevronDown } from 'lucide-vue-next';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';

// Define the LeaveRequest interface
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
  staff: {
    first_name: string;
    last_name: string;
  };
}

// Define the pagination interface
interface LeaveRequestsPagination {
  data: LeaveRequest[];
  current_page: number;
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  links: {
    url: string | null;
    label: string;
    active: boolean;
  }[];
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  total: number;
}

// Define props from the controller
const props = defineProps<{
  leaveRequests: LeaveRequestsPagination;
  filters: {
    search: string;
    sort_by: string;
    sort_order: string;
    per_page: number;
    department?: string;
    position?: string;
    type?: string;
  };
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Leave Requests', href: route('admin.leave-requests.index') },
];

// Reactive variables for search and sort
const searchInput = ref(props.filters.search || '');
const department = ref(props.filters.department || '');
const position = ref(props.filters.position || '');
const typeFilter = ref((props.filters as any)?.type || 'All');
const currentSortBy = ref(props.filters.sort_by);
const currentSortOrder = ref(props.filters.sort_order);
const perPage = ref<number>(Number(props.filters.per_page) || 5);

// Safely check for a named route via Ziggy
const hasRoute = (name: string): boolean => {
  try {
    const r: any = (route as any)();
    return typeof r?.has === 'function' ? !!r.has(name) : false;
  } catch {
    return false;
  }
};

// Inline Request Leave modal (HR/Admin convenience)
const requestModalOpen = ref(false);
const requestForm = useForm({
  start_date: '',
  end_date: '',
  type: 'Annual',
  reason: '',
});

const openRequestModal = () => {
  requestModalOpen.value = true;
};
const closeRequestModal = () => {
  requestModalOpen.value = false;
};

const submitRequestLeave = () => {
  requestForm.post(route('staff.leave-requests.store'), {
    preserveScroll: true,
    onSuccess: () => {
      requestForm.reset();
      requestModalOpen.value = false;
    },
  });
};

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

// Watch perPage and re-fetch with the new per_page value
watch(perPage, (newVal) => {
  const n = Number(newVal) || 5;
  if (n !== perPage.value) perPage.value = n;
  applyFilters({ preserveScroll: true });
});

// Function to apply filters (search and sort)
const applyFilters = (options?: { preserveState?: boolean; preserveScroll?: boolean }) => {
  router.get(
    route('admin.leave-requests.index'),
    {
      search: searchInput.value,
      sort_by: currentSortBy.value,
      sort_order: currentSortOrder.value,
      per_page: perPage.value,
      department: department.value,
      position: position.value,
      type: typeFilter.value === 'All' ? undefined : typeFilter.value,
    },
    {
      preserveState: options?.preserveState ?? true,
      preserveScroll: options?.preserveScroll ?? true,
      only: ['leaveRequests', 'filters'], // Only reload these props
    }
  );
};

// Deep link highlight support
const page = usePage();
const highlightId = ref<number | null>(null);
onMounted(() => {
  try {
    const url = new URL((page.props as any).ziggy?.location || window.location.href);
    const h = url.searchParams.get('highlight');
    if (h) {
      highlightId.value = Number(h);
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

// Function to handle column sorting
const sortColumn = (frontendColumnName: string) => {
  // Map friendly frontend column names to backend actual column names
  const backendColumnMap: Record<string, string> = {
    'staff_member': 'staff_first_name', // Backend sorts by 'staff.first_name'
    'dates_requested': 'start_date',
    'status': 'status',
    'created_at': 'created_at',
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
  status: '', // This is the status we want to submit
  admin_notes: '',
});

// Function to open the dialog and set the selected request (presetting status if provided)
const openUpdateDialog = (request: LeaveRequest, status?: 'Approved' | 'Denied' | 'Pending') => {
  selectedRequest.value = request;
  form.status = status ?? (request.status as 'Approved' | 'Denied' | 'Pending');
  form.admin_notes = request.admin_notes || ''; // Ensure notes are pre-filled if existing
  isDialogOpen.value = true; // THIS IS KEY: Set to true to open the dialog
};

// Function to submit the update
const updateRequestStatus = () => {
  if (!selectedRequest.value) {
    console.error('ERROR: No selectedRequest.value. Dialog should not have been confirmed without one.');
    isDialogOpen.value = false; // Close dialog if somehow opened without request
    return;
  }

  form.put(route('admin.leave-requests.update', selectedRequest.value.id), {
    preserveScroll: true,
    
    // When the backend responds with Inertia::location, it automatically performs a new GET request
    // and updates the page props. So, we don't need to manually trigger applyFilters() here.
    onSuccess: (page) => {
      isDialogOpen.value = false; // Close the dialog
      form.reset(); // Reset form fields
    },
    onError: (errors) => {
      console.error('--- Inertia PUT request FAILED! ---');
      console.error('Errors:', errors);
      // Display specific errors if available
      if (errors && Object.keys(errors).length > 0) {
        alert('Validation failed: ' + Object.values(errors).join('\n'));
      } else {
        alert('An unknown error occurred during update. Check console and network tab.');
      }
    },
    onFinish: () => {
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

// Calculate the number of days for a leave request
const calculateLeaveDays = (startDate: string, endDate: string) => {
  const start = new Date(startDate);
  const end = new Date(endDate);
  const diffTime = Math.abs(end.getTime() - start.getTime());
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
  return diffDays;
};
</script>

<template>
  <Head title="Leave Requests Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">
      <!-- Header Section -->
      <div class="rounded-xl bg-white shadow-sm dark:border-sidebar-border dark:bg-background">
        <div class="border-b p-5 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
          <div>
            <h2 class="text-lg font-semibold">Leave Requests Management</h2>
            <p class="mt-1 text-sm text-muted-foreground">Review and manage all staff leave requests.</p>
          </div>
          <div class="flex items-center gap-2">
            <!-- Quick action: Request Leave (inline modal) -->
            <Button v-if="hasRoute('staff.leave-requests.store')" @click="openRequestModal" variant="default" size="sm">
              Request Leave
            </Button>
            <Button as-child variant="secondary" size="sm">
              <Link :href="route('dashboard')">
                Back to Dashboard
              </Link>
            </Button>
          </div>
        </div>
      </div>

      <!-- Context hint for admins submitting leave without a staff profile -->
      <div class="rounded-md border border-amber-300 bg-amber-50 p-3 text-amber-900 text-sm">
        HR tip: You can submit a leave request from here. If your account doesn’t have a staff profile yet, one will be created automatically using your user details.
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium">Pending Requests</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">
              {{ leaveRequests.data.filter(r => r.status === 'Pending').length }}
            </div>
            <p class="text-xs text-muted-foreground">Requests awaiting review</p>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium">Approved Requests</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">
              {{ leaveRequests.data.filter(r => r.status === 'Approved').length }}
            </div>
            <p class="text-xs text-muted-foreground">Requests approved</p>
          </CardContent>
        </Card>
        
        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-sm font-medium">Denied Requests</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-2xl font-bold">
              {{ leaveRequests.data.filter(r => r.status === 'Denied').length }}
            </div>
            <p class="text-xs text-muted-foreground">Requests denied</p>
          </CardContent>
        </Card>
      </div>

      <!-- Search and Filters -->
      <div class="rounded-xl bg-white shadow-sm dark:border-sidebar-border dark:bg-background p-5">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
          <div class="relative w-full md:w-1/3">
            <input
              type="text"
              v-model="searchInput"
              placeholder="Search by staff name, reason, or status..."
              class="form-input w-full rounded-md border border-gray-300 pl-10 pr-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100"
            />
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1012 19.5a7.5 7.5 0 004.65-1.85z" /></svg>
          </div>
          <div class="flex items-center gap-2 w-full md:w-auto">
            <div class="w-full md:w-48">
              <label for="department" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Department</label>
              <input id="department" type="text" v-model="department" @keyup.enter="applyFilters()" placeholder="e.g. Administration" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700" />
            </div>
            <div class="w-full md:w-48">
              <label for="position" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Position</label>
              <input id="position" type="text" v-model="position" @keyup.enter="applyFilters()" placeholder="e.g. Nurse" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700" />
            </div>
            <div class="w-full md:w-40">
              <label for="typeFilter" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Type</label>
              <select id="typeFilter" @change="applyFilters()" v-model="typeFilter" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700">
                <option value="All">All</option>
                <option value="Annual">Annual</option>
                <option value="Sick">Sick</option>
                <option value="Unpaid">Unpaid</option>
              </select>
            </div>
            <div>
            <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
            <PerPageSelect v-model="perPage" id="perPage" />
          </div>
            <div>
              <Button class="ml-2" size="sm" @click="applyFilters({ preserveScroll: true })">Apply</Button>
            </div>
          </div>
        </div>
      </div>

      <!-- Leave Requests Table -->
      <div class="rounded-xl bg-white shadow-sm dark:border-sidebar-border dark:bg-background">
        <div class="border-b p-5">
          <h3 class="text-md font-semibold">Leave Requests</h3>
        </div>
        <div class="p-5">
          <div v-if="leaveRequests.data.length > 0" class="overflow-x-auto">
            <table class="min-w-full text-left text-sm print-table">
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
                  <th class="p-2">Days</th>
                  <th class="p-2">Type</th>
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
                  <th class="p-2 cursor-pointer select-none" @click="sortColumn('created_at')">
                    <div class="flex items-center">
                      Submitted
                      <template v-if="currentSortBy === 'created_at'">
                        <ChevronUp class="h-4 w-4 ml-1" v-if="currentSortOrder === 'asc'" />
                        <ChevronDown class="h-4 w-4 ml-1" v-else />
                      </template>
                    </div>
                  </th>
                  <th class="p-2 text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="request in leaveRequests.data" :key="request.id" class="border-t" :data-leave-id="request.id">
                  <td class="p-2">{{ request.staff.first_name }} {{ request.staff.last_name }}</td>
                  <td class="p-2">{{ formatDate(request.start_date) }} - {{ formatDate(request.end_date) }}</td>
                  <td class="p-2">{{ calculateLeaveDays(request.start_date, request.end_date) }}</td>
                  <td class="p-2">{{ request.type || '—' }}</td>
                  <td class="p-2 max-w-xs truncate">{{ request.reason || 'N/A' }}</td>
                  <td class="p-2">
                    <span class="rounded-full px-2 py-1 text-xs font-medium" :class="statusColor(request.status)">
                      {{ request.status }}
                    </span>
                  </td>
                  <td class="p-2">{{ formatDate(request.created_at) }}</td>
                  <td class="p-2 text-right">
                    <div class="flex justify-end gap-2">
                      <template v-if="request.status === 'Pending'">
                        <Button variant="outline" size="sm" @click="openUpdateDialog(request, 'Approved')">Approve</Button>
                        <Button variant="destructive" size="sm" @click="openUpdateDialog(request, 'Denied')">Deny</Button>
                      </template>
                      <Button variant="secondary" size="sm" @click="openUpdateDialog(request)">Edit</Button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="py-10 text-center text-muted-foreground">
            <p>There are no leave requests to review.</p>
          </div>
          <div class="flex justify-between items-center mt-4">
            <Pagination v-if="leaveRequests.data.length > 0" :links="leaveRequests.links" />
          </div>
        </div>
      </div>
    </div>

    <!-- Request Leave Dialog (inline) -->
    <Dialog :open="requestModalOpen" @update:open="requestModalOpen = $event">
      <DialogContent class="sm:max-w-lg">
        <DialogHeader>
          <DialogTitle>Request Leave</DialogTitle>
          <DialogDescription>Fill in the dates and reason to submit your leave request.</DialogDescription>
        </DialogHeader>
        <div class="grid grid-cols-1 gap-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label for="request_start_date">Start Date</Label>
              <Input id="request_start_date" type="date" v-model="requestForm.start_date" />
            </div>
            <div>
              <Label for="request_end_date">End Date</Label>
              <Input id="request_end_date" type="date" v-model="requestForm.end_date" />
            </div>
          </div>
          <div>
            <Label for="request_type">Leave Type</Label>
            <select id="request_type" v-model="requestForm.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white">
              <option value="Annual">Annual</option>
              <option value="Sick">Sick</option>
              <option value="Unpaid">Unpaid</option>
            </select>
          </div>
          <div>
            <Label for="request_reason">Reason <span class="text-red-500">*</span></Label>
            <textarea
              id="request_reason"
              v-model="requestForm.reason"
              rows="3"
              required
              placeholder="Briefly describe the reason for your leave..."
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            ></textarea>
          </div>
        </div>
        <DialogFooter class="gap-2">
          <Button variant="outline" @click="closeRequestModal">Cancel</Button>
          <Button :disabled="requestForm.processing" @click="submitRequestLeave">Submit</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Update Status Dialog -->
    <Dialog :open="isDialogOpen" @update:open="isDialogOpen = $event">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Update Leave Request Status</DialogTitle>
          <DialogDescription v-if="selectedRequest">
            Updating leave request for <strong>{{ selectedRequest.staff.first_name }} {{ selectedRequest.staff.last_name }}</strong>
            from {{ formatDate(selectedRequest.start_date) }} to {{ formatDate(selectedRequest.end_date) }}
          </DialogDescription>
        </DialogHeader>
        <div class="py-4">
          <div class="mb-4">
            <Label for="status">Status</Label>
            <select
              id="status"
              v-model="form.status"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            >
              <option value="Pending">Pending</option>
              <option value="Approved">Approved</option>
              <option value="Denied">Denied</option>
            </select>
            <div v-if="form.errors.status" class="text-red-500 text-xs mt-1">
              {{ form.errors.status }}
            </div>
          </div>

          <Label for="admin_notes">Admin Notes</Label>
          <textarea
            id="admin_notes"
            v-model="form.admin_notes"
            placeholder="Add notes about this decision..."
            rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white"
          ></textarea>
           <div v-if="form.errors.admin_notes" class="text-red-500 text-xs mt-1">
            {{ form.errors.admin_notes }}
          </div>
        </div>
        <DialogFooter>
          <Button variant="outline" @click="isDialogOpen = false" :disabled="form.processing">Cancel</Button>
          <Button @click="updateRequestStatus" :disabled="form.processing">Update Status</Button>
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
