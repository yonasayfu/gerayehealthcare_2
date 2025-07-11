<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'
import { ref } from 'vue'

const props = defineProps<{
  taskDelegations: {
    data: Array<{ id:number; title:string; due_date:string; status:string }>
    links: { url:string|null; label:string; active:boolean }[]
  }
  filters: { per_page: number }
}>()

// Form to create new task
const form = useForm({
  title: '',
  due_date: '',
  notes: '',
})

// Submit new task
function submitNew() {
  form.post(route('staff.task-delegations.store'), {
    preserveScroll: true,
    onSuccess: () => form.reset()
  })
}

// Mark a task complete
function markDone(id: number) {
  useForm({ status: 'Completed' })
    .patch(route('staff.task-delegations.update', { task_delegation: id }))
}
</script>

<template>
  <Head title="My Tasks" />

  <AppLayout>
    <section class="p-6 space-y-6 bg-white rounded-lg shadow">
      <h1 class="text-xl font-semibold">My Tasks</h1>

      <!-- New Task Form -->
      <form @submit.prevent="submitNew" class="flex gap-2">
        <input v-model="form.title" placeholder="New task title…" class="flex-1 rounded border px-3 py-2" />
        <input type="date" v-model="form.due_date" class="rounded border px-3 py-2" />
        <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white px-4 py-2 rounded">
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
            <th class="px-4 py-2 text-right">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="t in props.taskDelegations.data" :key="t.id" class="border-t">
            <td class="px-4 py-2">{{ t.title }}</td>
            <td class="px-4 py-2">{{ new Date(t.due_date).toLocaleDateString() }}</td>
            <td class="px-4 py-2">{{ t.status }}</td>
            <td class="px-4 py-2 text-right">
              <button
                v-if="t.status !== 'Completed'"
                @click="markDone(t.id)"
                class="text-green-600 hover:underline"
              >
                Mark Done
              </button>
            </td>
            
          </tr>
        </tbody>
      </table>

      <Pagination :links="props.taskDelegations.links" class="mt-4 flex justify-center" />
    </section>
  </AppLayout>
</template>
