<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import ShowHeader from '@/components/ShowHeader.vue'
import { format } from 'date-fns'

const props = defineProps<{
  taskDelegation: {
    id: number
    title: string
    assigned_to: number
    due_date: string
    status: 'Pending' | 'In Progress' | 'Completed'
    notes: string | null
    assignee?: { first_name: string; last_name: string }
    task_category?: string
    priority_level?: number
    created_by_user?: { name: string } | null
  }
}>()

const showDeleteModal = ref(false)

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

function confirmDelete() {
  showDeleteModal.value = true
}

function deleteTask() {
  router.delete(route('admin.task-delegations.destroy', { task_delegation: props.taskDelegation.id }), {
    onSuccess: () => {
      showDeleteModal.value = false
    }
  })
}

function cancelDelete() {
  showDeleteModal.value = false
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
      
      <div class="hidden print:block text-center mb-4 print-header-content">
        <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
        <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
        <p class="text-gray-600 dark:text-gray-400 print-document-title">Task Delegation Details</p>
        <hr class="my-3 border-gray-300 print:my-2">
      </div>
      
      <!-- Content within the same card container -->
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
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <div class="text-xs text-muted-foreground">Due Date</div>
            <div class="text-sm font-medium text-foreground">{{ new Date(props.taskDelegation.due_date).toLocaleDateString() }}</div>
          </div>
          
          <div>
            <div class="text-xs text-muted-foreground">Status</div>
            <div class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
                 :class="{
                   'bg-yellow-100 text-yellow-800': props.taskDelegation.status === 'Pending',
                   'bg-blue-100 text-blue-800': props.taskDelegation.status === 'In Progress',
                   'bg-green-100 text-green-800': props.taskDelegation.status === 'Completed'
                 }">
              {{ props.taskDelegation.status }}
            </div>
          </div>
          
          <div v-if="props.taskDelegation.task_category">
            <div class="text-xs text-muted-foreground">Category</div>
            <div class="text-sm font-medium text-foreground">{{ props.taskDelegation.task_category }}</div>
          </div>
          
          <div v-if="props.taskDelegation.priority_level">
            <div class="text-xs text-muted-foreground">Priority</div>
            <div class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
                 :class="{
                   'bg-gray-100 text-gray-800': props.taskDelegation.priority_level <= 1,
                   'bg-yellow-100 text-yellow-800': props.taskDelegation.priority_level === 2,
                   'bg-orange-100 text-orange-800': props.taskDelegation.priority_level === 3,
                   'bg-red-100 text-red-800': props.taskDelegation.priority_level === 4,
                   'bg-purple-100 text-purple-800': props.taskDelegation.priority_level === 5
                 }">
              {{ 
                props.taskDelegation.priority_level <= 1 ? 'Low' : 
                props.taskDelegation.priority_level === 2 ? 'Medium' : 
                props.taskDelegation.priority_level === 3 ? 'Normal' : 
                props.taskDelegation.priority_level === 4 ? 'High' : 'Critical' 
              }}
            </div>
          </div>
        </div>
        
        <div v-if="props.taskDelegation.notes">
          <div class="text-xs text-muted-foreground">Notes</div>
          <div class="text-sm whitespace-pre-wrap text-foreground">{{ props.taskDelegation.notes }}</div>
        </div>
      </div>
      
      <!-- Footer actions with consistent styling -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          <button @click="printPage" class="btn-glass btn-glass-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Current
          </button>
          <Link :href="route('admin.task-delegations.edit', props.taskDelegation.id)" class="btn-glass btn-glass-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit
          </Link>
          <button @click="confirmDelete" class="btn-glass btn-glass-sm bg-red-500 hover:bg-red-600 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Delete
          </button>
        </div>
      </div>
      
      <!-- Print Footer -->
      <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
        <hr class="my-2 border-gray-300 dark:border-gray-600">
        <p>Printed on: {{ format(new Date(), 'PPP p') }}</p>
      </div>
    </div>
  </AppLayout>

  <!-- Delete Confirmation Modal -->
  <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
      </div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Task</h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">Are you sure you want to delete this task? This action cannot be undone.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button @click="deleteTask" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
            Delete
          </button>
          <button @click="cancelDelete" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
/* Print Styles */
@media print {
  @page {
    size: A4 portrait;
    margin: 1cm;
  }

  /* Universal print adjustments */
  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: visible !important;
  }

  /* Elements to hide during print */
  .print\:hidden {
    display: none !important;
  }
  
  /* HIDE BREADCRUMBS AND TOP NAV */
  .app-sidebar-header, .app-sidebar {
    display: none !important;
  }
  
  /* Fallback/more general selectors */
  body > header,
  body > nav,
  [role="banner"],
  [role="navigation"] {
    display: none !important;
  }

  /* Print Header Content */
  .print-header-content {
    display: block !important;
    text-align: center;
    padding-top: 0.5cm;
    padding-bottom: 0.5cm;
    margin-bottom: 0.8cm;
  }
  
  .print-logo {
    max-width: 150px;
    max-height: 50px;
    margin-bottom: 0.5rem;
    display: block;
    margin-left: auto;
    margin-right: auto;
  }
  
  .print-clinic-name {
    font-size: 1.6rem !important;
    margin-bottom: 0.2rem !important;
    line-height: 1.2 !important;
    font-weight: bold;
  }
  
  .print-document-title {
    font-size: 0.85rem !important;
    color: #555 !important;
  }
  
  /* Status badges */
  .inline-flex.items-center.px-2.py-1.rounded.text-xs.font-medium {
    background-color: #f0f0f0 !important;
    color: #000 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    border: 1px solid #ddd !important;
  }
  
  /* Divider lines */
  hr {
    border-color: #ccc !important;
  }
}
</style>