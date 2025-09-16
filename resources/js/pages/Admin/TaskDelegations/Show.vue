<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ShowHeader from '@/components/ShowHeader.vue'

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

      <ShowHeader title="Task Delegation Details" :subtitle="`Task Delegation: ${props.taskDelegation.title || props.taskDelegation.id}`">
        <template #actions>
          <Link :href="route('admin.task-delegations.index')" class="btn-glass btn-glass-sm">Back</Link>
        </template>
      </ShowHeader>
      <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
        <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
        <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
        <p class="text-gray-600 dark:text-gray-400 print-document-title">Task Delegation Details</p>
        <hr class="my-3 border-gray-300 print:my-2">
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
          
          <button @click="printPage" class="btn-glass btn-glass-sm">Print Current</button>
          <Link :href="route('admin.task-delegations.edit', props.taskDelegation.id)" class="btn-glass btn-glass-sm">Edit</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
