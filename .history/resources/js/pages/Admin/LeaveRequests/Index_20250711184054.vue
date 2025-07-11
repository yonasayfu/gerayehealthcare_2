<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'; // Import usePage
import { type BreadcrumbItem, type LeaveRequest, type LeaveRequestsPagination, type AppPageProps } from '@/types'; // Import AppPageProps
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter,
} from '@/components/ui/dialog';
import { ref } from 'vue';

// Define props from the controller
const props = defineProps<{
  leaveRequests: LeaveRequestsPagination;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Leave Requests', href: route('admin.admin-leave-requests.index') }, // Updated route name
];

// State for the confirmation dialog
const isDialogOpen = ref(false);
const selectedRequest = ref<LeaveRequest | null>(null);

// Form for updating the request status
const form = useForm({
  status: '',
  admin_notes: '',
});

// Function to open the dialog and set the selected request
const openUpdateDialog = (request: LeaveRequest, status: 'Approved' | 'Denied') => {
  selectedRequest.value = request;
  form.status = status;
  form.admin_notes = request.admin_notes || '';
  isDialogOpen.value = true;
};

// Function to submit the update
const updateRequestStatus = () => {
  if (!selectedRequest.value) return;
  form.put(route('admin.admin-leave-requests.update', selectedRequest.value.id), { // Updated route name
    preserveScroll: true,
    onSuccess: () => {
      console.log('Leave request update successful!'); // Add console log
      isDialogOpen.value = false;
      form.reset();
      // Explicitly reload the leaveRequests prop to update the table
      usePage().props.inertia.visit(route('admin.admin-leave-requests.index'), {
        preserveScroll: true,
        preserveState: true,
        only: ['leaveRequests'],
      });
    },
  });
};

// Helper functions
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
        <div v-if="leaveRequests.data.length > 0" class="overflow-x-auto">
          <table class="min-w-full text-left text-sm">
            <thead>
              <tr>
                <th class="p-2">Staff Member</th>
                <th class="p-2">Dates Requested</th>
                <th class="p-2">Reason</th>
                <th class="p-2">Status</th>
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

    <Dialog :open="isDialogOpen" @update:open="isDialogOpen = false">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Confirm Action: {{ form.status }} Request</DialogTitle>
          <DialogDescription>
            You are about to {{ form.status.toLowerCase() }} this leave request. You can add an optional note below.
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
          <Button variant="outline" @click="isDialogOpen = false">Cancel</Button>
          <Button @click="updateRequestStatus" :disabled="form.processing">Confirm</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>
