<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { defineProps } from 'vue'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'

// Destructure incoming props exactly once
const props = defineProps<{ task: any; staffList: any[] }>()

const form = useForm({
  title:        props.task.title,
  assigned_to:  props.task.assigned_to,
  due_date:     props.task.due_date,
  status:       props.task.status,
  notes:        props.task.notes || '',
})

function submit() {
  form.put(route('admin.task-delegations.update', { task_delegation: props.task.id }))
}
</script>

<template>
  <Head :title="`Edit Task: ${props.task.title}`" />
  <AppLayout>
    <div class="p-6 bg-white rounded-lg shadow">
      <Link href="/dashboard/task-delegations" class="text-blue-600">← Back</Link>
      <h2 class="text-xl font-semibold mt-4">Edit Task</h2>

      <div class="mt-4 space-y-4">
        <!-- Similar fields as Create.vue, but v-model bound to form -->
        <!-- Title, assigned_to, due_date, status, notes -->
        <Button @click="submit" :disabled="form.processing">Save</Button>
      </div>
    </div>
  </AppLayout>
</template>