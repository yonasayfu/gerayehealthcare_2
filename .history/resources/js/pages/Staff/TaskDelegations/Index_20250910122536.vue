<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'
import { ref, watch } from 'vue'
import debounce from 'lodash/debounce'
import { ArrowUpDown } from 'lucide-vue-next'

// Props from controller
const props = defineProps<{
  taskDelegations: {
    data: Array<{ id: number; title: string; due_date: string; status: string; assigned_to: number }>
    links: { url: string | null; label: string; active: boolean }[]
  }
  filters: { per_page: number; search: string; sort_by: string; sort_order: string }
  staff: Array<{ id: number; first_name: string; last_name: string }>
}>()

// Filter scope: assigned | created
const scope = ref<'assigned' | 'created'>((props as any).filters?.scope || 'assigned')

// Search and pagination
const search = ref(props.filters.search || '')
const perPage = ref(props.filters.per_page || 15)
const sortBy = ref(props.filters.sort_by || 'due_date')
const sortOrder = ref(props.filters.sort_order || 'asc')

function switchScope(s: 'assigned' | 'created') {
  if (s === scope.value) return
  scope.value = s
  updateRoute()
}

// Toggle sort order
function toggleSort(field: string) {
  if (sortBy.value === field) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = field
    sortOrder.value = 'asc'
  }
  updateRoute()
}

// Update route with all parameters
function updateRoute() {
  router.get(
    route('staff.task-delegations.index'),
    { 
      scope: scope.value, 
      search: search.value, 
      per_page: perPage.value,
      sort_by: sortBy.value,
      sort_order: sortOrder.value
    },
    { preserveState: true, replace: true }
  )
}

// Watch for search and perPage changes
watch([search, perPage], debounce(([searchValue, perPageValue]) => {
  router.get(
    route('staff.task-delegations.index'),
    { 
      scope: scope.value, 
      search: searchValue, 
      per_page: perPageValue,
      sort_by: sortBy.value,
      sort_order: sortOrder.value
    },
    { preserveState: true, replace: true }
  )
}, 300))

// Form to create new task
const form = useForm({
  title: '',
  due_date: '',
  notes: '',
  assigned_to: undefined as number | undefined,
})

// Submit new task
function submitNew() {
  form.post(route('staff.task-delegations.store'), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  })
}

// Mark a task complete
function markDone(id: number) {
  router.patch(
    route('staff.task-delegations.update', { task_delegation: id }),
    { status: 'Completed' },
    {
      preserveState: false,
      preserveScroll: true,
    }
  )
}

function transferTask(id: number, newAssigneeId: number) {
  if (!newAssigneeId) return
  
  router.patch(
    route('staff.task-delegations.update', { task_delegation: id }),
    { assigned_to: newAssigneeId },
    { preserveScroll: true }
  )
}

// Update task status
function updateStatus(id: number, status: string) {
  router.patch(
    route('staff.task-delegations.update', { task_delegation: id }),
    { status: status },
    { preserveScroll: true }
  )
}
</script>

<template>
  <Head title="My Tasks" />

  <AppLayout>
    <section class="p-6 space-y-6 bg-white rounded-lg shadow">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-xl font-semibold">My Tasks</h1>
          <p class="text-sm text-gray-600">Manage your assigned tasks and create new ones</p>
        </div>
        <div class="inline-flex rounded-md border overflow-hidden">
          <button @click="switchScope('assigned')" :class="['px-4 py-2 text-sm font-medium', scope === 'assigned' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50']">Assigned to Me</button>
          <button @click="switchScope('created')" :class="['px-4 py-2 text-sm font-medium', scope === 'created' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50']">Created by Me</button>
        </div>
      </div>

      <!-- Search and Filters -->
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
          <input
            v-model="search"
            placeholder="Search tasks..."
            class="w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>
        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-700">Per Page:</label>
          <select
            v-model="perPage"
            class="rounded border px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
        </div>
      </div>

      <!-- New Task Form -->
      <div class="border rounded-lg p-4 bg-gray-50">
        <h2 class="text-lg font-medium mb-3">Create New Task</h2>
        <form @submit.prevent="submitNew" class="grid grid-cols-1 md:grid-cols-4 gap-3">
          <div class="md:col-span-2">
            <input
              v-model="form.title"
              placeholder="Task title"
              class="w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              required
            />
          </div>
          <div>
            <input
              type="date"
              v-model="form.due_date"
              class="w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              required
            />
          </div>
          <div>
            <select
              v-model.number="form.assigned_to"
              class="w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <option :value="undefined">Assign to me</option>
              <option
                v-for="s in props.staff"
                :value="s.id"
                :key="s.id"
              >
                {{ s.first_name }} {{ s.last_name }}
              </option>
            </select>
          </div>
          <div class="md:col-span-4">
            <textarea
              v-model="form.notes"
              placeholder="Task notes (optional)"
              class="w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              rows="2"
            ></textarea>
          </div>
          <div class="md:col-span-4">
            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass"
            >
              {{ form.processing ? 'Creating...' : 'Create Task' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Task Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-100 text-xs uppercase text-muted-foreground">
            <tr>
              <th @click="toggleSort('title')" class="px-4 py-3 cursor-pointer">
                Title <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th @click="toggleSort('due_date')" class="px-4 py-3 cursor-pointer">
                Due Date <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th @click="toggleSort('status')" class="px-4 py-3 cursor-pointer">
                Status <ArrowUpDown class="inline w-4 h-4 ml-1" />
              </th>
              <th class="px-4 py-3">Assigned To</th>
              <th class="px-4 py-3">Transfer</th>
              <th class="px-4 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="t in props.taskDelegations.data"
              :key="t.id"
              class="border-t hover:bg-gray-50"
            >
              <td class="px-4 py-3 font-medium">{{ t.title }}</td>
              <td class="px-4 py-3" :class="(new Date(t.due_date) < new Date() && t.status !== 'Completed') ? 'text-red-600 font-medium' : ''">
                {{ new Date(t.due_date).toLocaleDateString() }}
              </td>
              <td class="px-4 py-3">
                <select
                  :value="t.status"
                  @change="(e: any) => updateStatus(t.id, e.target.value)"
                  class="rounded border px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  :disabled="t.status === 'Completed'"
                >
                  <option value="Pending">Pending</option>
                  <option value="In Progress">In Progress</option>
                  <option value="Completed">Completed</option>
                </select>
              </td>
              <td class="px-4 py-3">
                <span v-if="t.assigned_to === ($page.props.auth.user.staff?.id || 0)" class="text-indigo-600 font-medium">
                  Me
                </span>
                <span v-else>
                  {{ props.staff.find(s => s.id === t.assigned_to)?.first_name }} {{ props.staff.find(s => s.id === t.assigned_to)?.last_name }}
                </span>
              </td>
              <td class="px-4 py-3">
                <select
                  @change="(e: any) => transferTask(t.id, parseInt(e.target.value, 10))"
                  class="rounded border px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  :disabled="t.status === 'Completed'"
                >
                  <option value="" selected disabled>Transfer to...</option>
                  <option
                    v-for="s in props.staff.filter(staff => staff.id !== t.assigned_to)"
                    :value="s.id"
                    :key="s.id"
                  >
                    {{ s.first_name }} {{ s.last_name }}
                  </option>
                </select>
              </td>
              <td class="px-4 py-3 text-right">
                <template v-if="t.status !== 'Completed'">
                  <button
                    @click="markDone(t.id)"
                    class="btn-glass-sm"
                  >
                    Mark Done
                  </button>
                </template>
                <template v-else>
                  <span class="text-gray-500 italic">Completed</span>
                </template>
              </td>
            </tr>
            <tr v-if="!props.taskDelegations.data.length">
              <td colspan="6" class="py-8 text-center text-muted-foreground">
                No tasks found. Create a new task above or check back later.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <Pagination
        v-if="props.taskDelegations.links.length > 1"
        :links="props.taskDelegations.links"
        class="mt-4 flex justify-center"
      />
    </section>
  </AppLayout>
</template>

<style scoped>
@media print {
  body * {
    visibility: hidden;
  }
  table,
  table * {
    visibility: visible;
    position: relative;
    top: 0;
    left: 0;
    width: 100%;
  }
}
</style>