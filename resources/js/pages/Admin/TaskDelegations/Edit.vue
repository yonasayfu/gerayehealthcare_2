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
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Task: {{ props.task.title }}
            </h3>
            <Link :href="route('admin.task-delegations.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <div class="mt-4 space-y-4">
                <!-- Title -->
                <div>
                  <Label for="title">Title</Label>
                  <Input id="title" v-model="form.title" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
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
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <button @click="submit" :disabled="form.processing" class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">
              {{ form.processing ? 'Saving...' : 'Save' }}
            </button>
        </div>

    </div>
  </AppLayout>
</template>

<style scoped>
/* Add any page-specific styles here */
</style>
