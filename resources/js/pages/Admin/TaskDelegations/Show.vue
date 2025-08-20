<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps<{
  taskDelegation: {
    id: number
    title: string
    assigned_to: number
    due_date: string
    status: 'Pending' | 'In Progress' | 'Completed'
    notes: string | null
    assignee?: { first_name: string; last_name: string }
  }
}>()
</script>

<template>
  <Head :title="`Task: ${props.taskDelegation.title}`" />
  <AppLayout :breadcrumbs="[
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Task Delegations', href: route('admin.task-delegations.index') },
    { title: props.taskDelegation.title, href: '' }
  ]">
    <div class="bg-background text-foreground border border-border rounded-lg shadow m-6">
      <div class="flex items-center justify-between p-5 border-b border-border">
        <h3 class="text-xl font-semibold">Task Details</h3>
        <div class="flex gap-2">
          <Link :href="route('admin.task-delegations.index')" class="btn btn-outline">Back to List</Link>
          <Link :href="route('admin.task-delegations.edit', { task_delegation: props.taskDelegation.id })" class="btn btn-primary">Edit</Link>
        </div>
      </div>

      <div class="p-6 space-y-4">
        <div>
          <div class="text-xs text-muted-foreground">Title</div>
          <div class="text-sm font-medium text-foreground">{{ props.taskDelegation.title }}</div>
        </div>
        <div>
          <div class="text-xs text-muted-foreground">Assigned To</div>
          <div class="text-sm font-medium text-foreground">
            <template v-if="props.taskDelegation.assignee">
              {{ props.taskDelegation.assignee.first_name }} {{ props.taskDelegation.assignee.last_name }}
            </template>
            <template v-else>#{{ props.taskDelegation.assigned_to }}</template>
          </div>
        </div>
        <div>
          <div class="text-xs text-muted-foreground">Due Date</div>
          <div class="text-sm font-medium text-foreground">{{ new Date(props.taskDelegation.due_date).toLocaleDateString() }}</div>
        </div>
        <div>
          <div class="text-xs text-muted-foreground">Status</div>
          <div class="inline-flex items-center px-2 py-1 rounded text-xs"
               :class="{
                 'bg-yellow-100 text-yellow-800': props.taskDelegation.status === 'Pending',
                 'bg-blue-100 text-blue-800': props.taskDelegation.status === 'In Progress',
                 'bg-green-100 text-green-800': props.taskDelegation.status === 'Completed'
               }">
            {{ props.taskDelegation.status }}
          </div>
        </div>
        <div v-if="props.taskDelegation.notes">
          <div class="text-xs text-muted-foreground">Notes</div>
          <div class="text-sm whitespace-pre-wrap text-foreground">{{ props.taskDelegation.notes }}</div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
