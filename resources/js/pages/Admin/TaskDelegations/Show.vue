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

function printPage() {
  setTimeout(() => {
    try {
      window.print()
    } catch (error) {
      console.error('Print failed:', error)
      alert('Failed to open print dialog. Please try again or check your browser settings.')
    }
  }, 100)
}
</script>

<template>
  <Head :title="`Task: ${props.taskDelegation.title}`" />
  <AppLayout :breadcrumbs="[
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Task Delegations', href: route('admin.task-delegations.index') },
    { title: props.taskDelegation.title, href: '' }
  ]">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

      <!-- compact liquid glass header (now full-width and same sizing as main card) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Task Delegation Details</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">Task Delegation: {{ props.taskDelegation.title || props.taskDelegation.id }}</p>
          </div>
          <!-- top actions intentionally removed to avoid duplication; see footer -->
        </div>
      </div>
      <!-- keep content and footer within the same card container -->

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
      <!-- footer actions (single source of actions, right aligned) -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          <Link :href="route('admin.task-delegations.index')" class="btn-glass btn-glass-sm">Back to List</Link>
          <button @click="printPage" class="btn-glass btn-glass-sm">Print Current</button>
          <Link :href="route('admin.task-delegations.edit', props.taskDelegation.id)" class="btn-glass btn-glass-sm">Edit</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
