<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ref } from 'vue'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import Pagination from '@/components/Pagination.vue'

defineProps<{ staffList: { id: number; first_name: string; last_name: string }[] }>()

const form = useForm({
  title: '',
  assigned_to: null,
  due_date: '',
  status: 'Pending',
  notes: ''
})

function submit() {
  form.post(route('admin.task-delegations.store'))
}
</script>

<template>
  <Head title="Assign Task" />
  <AppLayout>
    <div class="p-6 bg-white rounded-lg shadow">
      <Link href="/dashboard/task-delegations" class="text-blue-600">← Back</Link>
      <h2 class="text-xl font-semibold mt-4">Assign New Task</h2>

      <div class="mt-4 space-y-4">
        <div>
          <Label for="title">Title</Label>
          <Input id="title" v-model="form.title" />
          <div v-if="form.errors.title" class="text-red-600 text-xs">{{ form.errors.title }}</div>
        </div>

        <div>
          <Label for="assigned_to">Assign To</Label>
          <select v-model="form.assigned_to" id="assigned_to" class="form-select w-full">
            <option disabled value="">Select staff…</option>
            <option v-for="s in staffList" :key="s.id" :value="s.id">
              {{ s.first_name }} {{ s.last_name }}
            </option>
          </select>
          <div v-if="form.errors.assigned_to" class="text-red-600 text-xs">{{ form.errors.assigned_to }}</div>
        </div>

        <div>
          <Label for="due_date">Due Date</Label>
          <Input type="date" id="due_date" v-model="form.due_date" />
          <div v-if="form.errors.due_date" class="text-red-600 text-xs">{{ form.errors.due_date }}</div>
        </div>

        <div>
          <Label for="status">Status</Label>
          <select v-model="form.status" id="status" class="form-select w-full">
            <option>Pending</option>
            <option>In Progress</option>
            <option>Completed</option>
          </select>
          <div v-if="form.errors.status" class="text-red-600 text-xs">{{ form.errors.status }}</div>
        </div>

        <div>
          <Label for="notes">Notes (optional)</Label>
          <textarea id="notes" v-model="form.notes" class="w-full rounded"></textarea>
          <div v-if="form.errors.notes" class="text-red-600 text-xs">{{ form.errors.notes }}</div>
        </div>

        <Button @click="submit" :disabled="form.processing">Assign</Button>
      </div>
    </div>
  </AppLayout>
</template>