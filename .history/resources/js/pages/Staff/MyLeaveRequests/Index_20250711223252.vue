<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import debounce from 'lodash/debounce'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter,
} from '@/components/ui/dialog'
import { ChevronUp, ChevronDown } from 'lucide-vue-next'
import type { BreadcrumbItem, LeaveRequest, LeaveRequestsPagination } from '@/types'

// Props from the controller
const props = defineProps<{
  leaveRequests: LeaveRequestsPagination
  filters: {
    search: string | null
    sort_by: string
    sort_order: 'asc' | 'desc'
  }
}>()

// Breadcrumb trail
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Leave Requests', href: route('admin.admin-leave-requests.index') },
]

// Reactive filter & sort state
const search = ref(props.filters.search || '')
const sortField = ref(props.filters.sort_by)
const sortOrder = ref(props.filters.sort_order)

// Debounced watcher to trigger index refresh
watch([search, sortField, sortOrder], debounce(() => {
  router.get(
    route('admin.admin-leave-requests.index'),
    { search: search.value, sort_by: sortField.value, sort_order: sortOrder.value },
    { preserveState: true, replace: true }
  )
}, 500))

// Toggle sort direction or change sort field
function toggleSort(field: string) {
  if (sortField.value === field) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortOrder.value = 'asc'
  }
}

// Dialog and form state for approving/denying
const isDialogOpen = ref(false)
const selectedRequest = ref<LeaveRequest | null>(null)
const form = useForm({
  status: '',
  admin_notes: '',
})

// Open the confirm dialog
function openUpdateDialog(request: LeaveRequest, status: 'Approved' | 'Denied') {
  selectedRequest.value = request
  form.status = status
  form.admin_notes = request.admin_notes || ''
  isDialogOpen.value = true
}

// Submit the update
function updateRequestStatus() {
  if (!selectedRequest.value) return

  form.put(
    route('admin.admin-leave-requests.update', {
      leave_request: selectedRequest.value.id,
    }),
    {
      preserveScroll: true,
      onSuccess: () => {
        isDialogOpen.value = false
        form.reset()
      },
      onError: () => {
        // handle validation errors if needed
      },
    }
  )
}

// Helpers
function formatDate(dateString: string) {
  return dateString
    ? new Date(dateString).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      })
    : 'N/A'
}

function statusColor(status: string) {
  switch (status) {
    case 'Approved':
      return 'bg-green-100 text-green-800'
    case 'Denied':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-yellow-100 text-yellow-800'
  }
}
</script>

<template>
  <Head title="Leave Requests" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Header + Filters -->
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-xl font-semibold">Staff Leave Requests</h1>
          <p class="text-sm text-muted-foreground">Review and manage all time-off requests.</p>
        </div>
        <div class="w-full md:w-1/3">
          <Input
            v-model="search"
            placeholder="Search by name, reason, or status..."
            class="w-full"
          />
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-100 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('staff')">
                <div class="flex items-center">
                  Staff
                  <ChevronUp v-if="sortField==='staff' && sortOrder==='asc'" class="ml-1 h-4 w-4" />
                  <ChevronDown v-if="sortField==='staff' && sortOrder==='desc'" class="ml-1 h-4 w-4" />
                </div>
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('start_date')">
                <div class="flex items-center">
                  Dates
                  <ChevronUp v-if="sortField==='start_date' && sortOrder==='asc'" class="ml-1 h-4 w-4" />
                  <ChevronDown v-if="sortField==='start_date' && sortOrder==='desc'" class="ml-1 h-4 w-4" />
                </div>
              </th>
              <th class="px-6 py-3">Reason</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">
                <div class="flex items-center">
                  Status
                  <ChevronUp v-if="sortField==='status' && sortOrder==='asc'" class="ml-1 h-4 w-4" />
                  <ChevronDown v-if="sortField==='status' && sortOrder==='desc'" class="ml-1 h-4 w-4" />
                </div>
              </th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="req in leaveRequests.data" :key="req.id" class="border-b hover:bg-gray-50">
              <td class="px-6 py-4">{{ req.staff.first_name }} {{ req.staff.last_name }}</td>
              <td class="px-6 py-4">
                {{ formatDate(req.start_date) }} – {{ formatDate(req.end_date) }}
              </td>
              <td class="px-6 py-4">{{ req.reason || '—' }}</td>
              <td class="px-6 py-4">
                <span
                  class="rounded-full px-2 py-1 text-xs font-medium"
                  :class="statusColor(req.status)"
                >
                  {{ req.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div v-if="req.status==='Pending'" class="flex justify-end gap-2">
                  <Button
                    variant="outline"
                    size="sm"
                    @click="openUpdateDialog(req, 'Approved')"
                  >
                    Approve
                  </Button>
                  <Button
                    variant="destructive"
                    size="sm"
                    @click="openUpdateDialog(req, 'Denied')"
                  >
                    Deny
                  </Button>
                </div>
                <span v-else class="text-xs text-muted-foreground">Reviewed</span>
              </td>
            </tr>
            <tr v-if="leaveRequests.data.length===0">
              <td colspan="5" class="py-10 text-center text-muted-foreground">
                No leave requests found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination
        v-if="leaveRequests.data.length > 0"
        :links="leaveRequests.links"
        class="mt-4 flex justify-center"
      />
    </div>

    <!-- Approve/Deny Dialog -->
    <Dialog :open="isDialogOpen" @update:open="isDialogOpen = $event">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>
            {{ form.status }} Request
          </DialogTitle>
          <DialogDescription>
            You are about to
            <strong>{{ form.status.toLowerCase() }}</strong>
            the leave request for
            <em>
              {{ selectedRequest?.staff.first_name }}
              {{ selectedRequest?.staff.last_name }}
            </em>.
            You may add an optional note below.
          </DialogDescription>
        </DialogHeader>
        <div class="py-4">
          <Label for="admin_notes">Notes (optional)</Label>
          <textarea
            id="admin_notes"
            v-model="form.admin_notes"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
            placeholder="Add a note…"
          />
          <div v-if="form.errors.admin_notes" class="text-red-600 text-xs mt-1">
            {{ form.errors.admin_notes }}
          </div>
        </div>
        <DialogFooter>
          <Button variant="outline" @click="isDialogOpen = false" :disabled="form.processing">
            Cancel
          </Button>
          <Button @click="updateRequestStatus" :disabled="form.processing">
            Confirm
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>

<style scoped>
/* Consistent with your tailwind setup—no extra cruft */
</style>
