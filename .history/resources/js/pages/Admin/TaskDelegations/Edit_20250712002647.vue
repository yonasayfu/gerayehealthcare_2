<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'

// Only one defineProps call
const props = defineProps<{
  task: {
    id: number
    title: string
    assigned_to: number
    due_date: string
    status: 'Pending' | 'In Progress' | 'Completed'
    notes: string | null
  }
  staffList: { id: number; first_name: string; last_name: string }[]
}>()

// Initialize the form with the existing values
const form = useForm({
  title:       props.task.title,
  assigned_to: props.task.assigned_to,
  due_date:    props.task.due_date,
  status:      props.task.status,
  notes:       props.task.notes || '',
})

function submit() {
  form.put(
    route('admin.task-delegations.update', { task_delegation: props.task.id })
  )
}
</script>

<template>
  <Head :title="`Edit Task: ${props.task.title}`" />

  <AppLayout :breadcrumbs="[
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Task Delegations', href: route('admin.task-delegations.index') },
    { title: `Edit: ${props.task.title}`, href: '' }
  ]">
    <div class="p-6 bg-white rounded-lg shadow">
      <Link :href="route('admin.task-delegations.index')" class="text-blue-600">← Back</Link>
      <h2 class="text-xl font-semibold mt-4">Edit Task</h2>

      <div class="mt-4 space-y-4">
        <!-- Title -->
        <div>
          <Label for="title">Title</Label>
          <Input id="title" v-model="form.title" />
          <div v-if="form.errors.title" class="text-red-600 text-xs">{{ form.errors.title }}</div>
        </div>

        <!-- Assigned To -->
        <div>
          <Label for="assigned_to">Assign To</Label>
          <select id="assigned_to" v-model="form.assigned_to" class="form-select w-full rounded-md border-gray-300">
            <option disabled value="">Select staff…</option>
            <option v-for="s in props.staffList" :key="s.id" :value="s.id">
              {{ s.first_name }} {{ s.last_name }}
            </option>
          </select>
          <div v-if="form.errors.assigned_to" class="text-red-600 text-xs">{{ form.errors.assigned_to }}</div>
        </div>

        <!-- Due Date -->
        <div>
          <Label for="due_date">Due Date</Label>
          <Input id="due_date" type="date" v-model="form.due_date" />
          <div v-if="form.errors.due_date" class="text-red-600 text-xs">{{ form.errors.due_date }}</div>
        </div>

        <!-- Status -->
        <div>
          <Label for="status">Status</Label>
          <select id="status" v-model="form.status" class="form-select w-full rounded-md border-gray-300">
            <option>Pending</option>
            <option>In Progress</option>
            <option>Completed</option>
          </select>
          <div v-if="form.errors.status" class="text-red-600 text-xs">{{ form.errors.status }}</div>
        </div>

        <!-- Notes -->
        <div>
          <Label for="notes">Notes (optional)</Label>
          <textarea id="notes" v-model="form.notes" class="w-full rounded-md border-gray-300"></textarea>
          <div v-if="form.errors.notes" class="text-red-600 text-xs">{{ form.errors.notes }}</div>
        </div>

        <!-- Save Button -->
        <Button @click="submit" :disabled="form.processing">Save</Button>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Add any page-specific styles here */
</style>
