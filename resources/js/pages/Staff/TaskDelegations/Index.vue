<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'
import { ref } from 'vue'

// Props from controller
const props = defineProps<{
  taskDelegations: {
    data: Array<{ id: number; title: string; due_date: string; status: string }>
    links: { url: string | null; label: string; active: boolean }[]
  }
  filters: { per_page: number }
  staff: Array<{ id: number; first_name: string; last_name: string }>
}>()

// Filter scope: assigned | created
const scope = ref<'assigned' | 'created'>((props as any).filters?.scope || 'assigned')

function switchScope(s: 'assigned' | 'created') {
  if (s === scope.value) return
  scope.value = s
  router.get(route('staff.task-delegations.index'), { scope: s }, { preserveState: true, replace: true })
}

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
  router.patch(
    route('staff.task-delegations.update', { task_delegation: id }),
    { assigned_to: newAssigneeId },
    { preserveScroll: true }
  )
}
</script>

<template>
  <Head title="My Tasks" />

  <AppLayout>
    <section class="p-6 space-y-6 bg-white rounded-lg shadow">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">My Tasks</h1>
        <div class="inline-flex rounded-md border overflow-hidden">
          <button @click="switchScope('assigned')" :class="['px-3 py-1 text-sm', scope === 'assigned' ? 'bg-indigo-600 text-white' : 'bg-white']">Assigned to Me</button>
          <button @click="switchScope('created')" :class="['px-3 py-1 text-sm', scope === 'created' ? 'bg-indigo-600 text-white' : 'bg-white']">Created by Me</button>
        </div>
      </div>

      <!-- New Task Form -->
      <form @submit.prevent="submitNew" class="flex flex-wrap gap-2 items-center">
        <input
          v-model="form.title"
          placeholder="New task title…"
          class="flex-1 rounded border px-3 py-2"
        />
        <input
          type="date"
          v-model="form.due_date"
          class="rounded border px-3 py-2"
        />
        <select
          v-model.number="form.assigned_to"
          class="rounded border px-3 py-2"
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
        <button
          type="submit"
          :disabled="form.processing"
          class="bg-cyan-600 text-white px-4 py-2 rounded hover:bg-cyan-700"
        >
          Add
        </button>
      </form>

      <!-- Task Table -->
      <table class="w-full text-left text-sm">
        <thead class="bg-gray-100 text-xs uppercase text-muted-foreground">
          <tr>
            <th class="px-4 py-2">Title</th>
            <th class="px-4 py-2">Due</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Transfer</th>
            <th class="px-4 py-2 text-right">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="t in props.taskDelegations.data"
            :key="t.id"
            class="border-t"
          >
            <td class="px-4 py-2">{{ t.title }}</td>
            <td class="px-4 py-2" :class="(new Date(t.due_date) < new Date() && t.status !== 'Completed') ? 'text-red-600 font-medium' : ''">
              {{ new Date(t.due_date).toLocaleDateString() }}
            </td>
            <td class="px-4 py-2">{{ t.status }}</td>
            <td class="px-4 py-2">
              <select
                :disabled="t.status === 'Completed'"
                @change="(e:any) => transferTask(t.id, parseInt(e.target.value, 10))"
                class="rounded border px-2 py-1"
              >
                <option value="" selected disabled>Transfer to…</option>
                <option
                  v-for="s in props.staff"
                  :value="s.id"
                  :key="s.id"
                >
                  {{ s.first_name }} {{ s.last_name }}
                </option>
              </select>
            </td>
            <td class="px-4 py-2 text-right">
              <template v-if="t.status !== 'Completed'">
                <button
                  @click="markDone(t.id)"
                  class="text-green-600 hover:underline"
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
            <td colspan="4" class="py-6 text-center text-muted-foreground">
              No tasks assigned.
            </td>
          </tr>
        </tbody>
      </table>

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
