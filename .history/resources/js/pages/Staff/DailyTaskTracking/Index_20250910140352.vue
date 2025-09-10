<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ref, computed } from 'vue'
import { format, parseISO } from 'date-fns'
import { 
  Calendar, 
  Clock, 
  CheckCircle, 
  Circle, 
  Play, 
  Square, 
  TrendingUp, 
  Tag,
  DollarSign,
  User,
  BarChart3
} from 'lucide-vue-next'

interface PersonalTask {
  id: number
  title: string
  notes?: string
  due_date?: string
  is_completed: boolean
  is_important: boolean
  task_date?: string
  start_time?: string
  end_time?: string
  estimated_duration_minutes?: number
  daily_notes?: string
  task_category?: string
  priority_level: number
  is_billable: boolean
  subtasks?: Array<{id:number; title:string; is_completed:boolean}>
}

interface DelegatedTask {
  id: number
  title: string
  notes?: string
  due_date: string
  status: string
  task_date?: string
  start_time?: string
  end_time?: string
  estimated_duration_minutes?: number
  daily_notes?: string
  task_category?: string
  priority_level: number
  is_billable: boolean
  progress_status: string
  assignee: {
    id: number
    first_name: string
    last_name: string
  }
}

const props = defineProps<{
  personalTasks: PersonalTask[]
  delegatedTasks: DelegatedTask[]
  date: string
  stats: {
    personalTasksCompleted: number
    personalTasksTotal: number
    delegatedTasksCompleted: number
    delegatedTasksInProgress: number
    delegatedTasksTotal: number
    totalTimeMinutes: number
    completionRate: number
  }
}>()

const selectedDate = ref(props.date)
const showKPIButton = ref(true)

// Format time in minutes to HH:MM
const formatTime = (minutes: number) => {
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  return `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}`
}

// Calculate task progress
const taskProgress = computed(() => {
  const total = props.stats.delegatedTasksTotal
  const completed = props.stats.delegatedTasksCompleted
  return total > 0 ? Math.round((completed / total) * 100) : 0
})

// Update date filter
const updateDate = () => {
  router.get(route('staff.daily-tasks.index'), { date: selectedDate.value })
}

// Update task tracking information
const updateTaskForm = useForm({
  task_type: 'personal' as 'personal' | 'delegated',
  task_id: 0,
  task_date: props.date,
  start_time: '',
  end_time: '',
  estimated_duration_minutes: null as number | null,
  daily_notes: '',
  task_category: '',
  priority_level: 1,
  is_billable: false,
  progress_status: 'not_started' as 'not_started' | 'in_progress' | 'completed' | 'blocked'
})

const openTaskModal = (taskType: 'personal' | 'delegated', task: PersonalTask | DelegatedTask) => {
  updateTaskForm.task_type = taskType
  updateTaskForm.task_id = task.id
  updateTaskForm.task_date = task.task_date || props.date
  updateTaskForm.start_time = task.start_time || ''
  updateTaskForm.end_time = task.end_time || ''
  updateTaskForm.estimated_duration_minutes = task.estimated_duration_minutes || null
  updateTaskForm.daily_notes = task.daily_notes || ''
  updateTaskForm.task_category = task.task_category || ''
  updateTaskForm.priority_level = task.priority_level || 1
  updateTaskForm.is_billable = task.is_billable || false
  updateTaskForm.progress_status = ('progress_status' in task) ? task.progress_status : 'not_started'
}

const submitTaskUpdate = () => {
  updateTaskForm.post(route('staff.daily-tasks.update'), {
    preserveScroll: true,
    onSuccess: () => {
      // Refresh the page to show updated data
      router.get(route('staff.daily-tasks.index'), { date: selectedDate.value })
    }
  })
}

// Toggle task completion
const toggleTaskCompletion = (taskType: 'personal' | 'delegated', taskId: number, isCompleted: boolean) => {
  if (taskType === 'personal') {
    router.patch(route('staff.my-todo.update', { my_todo: taskId }), { is_completed: !isCompleted })
  } else {
    router.patch(route('staff.task-delegations.update', { task_delegation: taskId }), { status: isCompleted ? 'Pending' : 'Completed' })
  }
}

// Navigate to KPI dashboard
const goToKPIDashboard = () => {
  router.get(route('staff.kpi-dashboard'))
}
</script>

<template>
  <Head title="Daily Task Tracking" />

  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Daily Task Tracking</h1>
          <p class="text-gray-600">Track and manage your daily tasks for performance analysis</p>
        </div>
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-2">
            <Calendar class="w-5 h-5 text-gray-500" />
            <input 
              v-model="selectedDate" 
              type="date" 
              @change="updateDate"
              class="rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500 bg-white/50 backdrop-blur-sm"
            />
          </div>
          <button 
            v-if="showKPIButton"
            @click="goToKPIDashboard"
            class="btn-glass flex items-center gap-2"
          >
            <BarChart3 class="w-4 h-4" />
            KPI Dashboard
          </button>
        </div>
      </div>

      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-xl p-5 shadow-sm border border-cyan-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-cyan-700">Personal Tasks</p>
              <p class="text-2xl font-bold text-cyan-900">{{ stats.personalTasksCompleted }}/{{ stats.personalTasksTotal }}</p>
            </div>
            <Circle class="w-8 h-8 text-cyan-500" />
          </div>
        </div>
        
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 shadow-sm border border-green-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-green-700">Delegated Tasks</p>
              <p class="text-2xl font-bold text-green-900">{{ stats.delegatedTasksCompleted }}/{{ stats.delegatedTasksTotal }}</p>
            </div>
            <CheckCircle class="w-8 h-8 text-green-500" />
          </div>
        </div>
        
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 shadow-sm border border-blue-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-blue-700">Time Tracked</p>
              <p class="text-2xl font-bold text-blue-900">{{ formatTime(stats.totalTimeMinutes) }}</p>
            </div>
            <Clock class="w-8 h-8 text-blue-500" />
          </div>
        </div>
        
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 shadow-sm border border-purple-200">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-purple-700">Completion Rate</p>
              <p class="text-2xl font-bold text-purple-900">{{ stats.completionRate }}%</p>
            </div>
            <TrendingUp class="w-8 h-8 text-purple-500" />
          </div>
        </div>
      </div>

      <!-- Progress Bar -->
      <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-700">Daily Progress</span>
          <span class="text-sm font-medium text-gray-700">{{ taskProgress }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
          <div 
            class="bg-cyan-600 h-2.5 rounded-full" 
            :style="{ width: taskProgress + '%' }"
          ></div>
        </div>
      </div>

      <!-- Personal Tasks Section -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="border-b border-gray-200 p-5">
          <h2 class="text-lg font-semibold text-gray-900">My Personal Tasks</h2>
        </div>
        <div class="divide-y divide-gray-100">
          <div 
            v-for="task in personalTasks" 
            :key="task.id"
            class="p-5 hover:bg-gray-50 transition-colors"
          >
            <div class="flex items-start gap-3">
              <input 
                type="checkbox" 
                :checked="task.is_completed"
                @change="toggleTaskCompletion('personal', task.id, task.is_completed)"
                class="mt-1 rounded text-cyan-600 focus:ring-cyan-500"
              />
              <div class="flex-1">
                <div class="flex items-start justify-between">
                  <h3 class="font-medium text-gray-900" :class="{ 'line-through text-gray-500': task.is_completed }">
                    {{ task.title }}
                  </h3>
                  <div class="flex items-center gap-2">
                    <span 
                      v-if="task.priority_level > 3"
                      class="px-2 py-1 text-xs rounded-full"
                      :class="{
                        'bg-red-100 text-red-800': task.priority_level === 5,
                        'bg-orange-100 text-orange-800': task.priority_level === 4
                      }"
                    >
                      P{{ task.priority_level }}
                    </span>
                    <button 
                      @click="openTaskModal('personal', task)"
                      class="text-cyan-600 hover:text-cyan-800 text-sm"
                    >
                      Track
                    </button>
                  </div>
                </div>
                <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-500">
                  <div v-if="task.task_category" class="flex items-center gap-1">
                    <Tag class="w-4 h-4" />
                    {{ task.task_category }}
                  </div>
                  <div v-if="task.is_billable" class="flex items-center gap-1">
                    <DollarSign class="w-4 h-4" />
                    Billable
                  </div>
                  <div v-if="task.due_date" class="flex items-center gap-1">
                    <Calendar class="w-4 h-4" />
                    Due: {{ format(parseISO(task.due_date), 'MMM d, yyyy') }}
                  </div>
                </div>
                <div v-if="task.daily_notes" class="mt-2 text-sm text-gray-600">
                  {{ task.daily_notes }}
                </div>
              </div>
            </div>
          </div>
          <div v-if="personalTasks.length === 0" class="p-8 text-center text-gray-500">
            No personal tasks for today. Add some tasks to get started!
          </div>
        </div>
      </div>

      <!-- Delegated Tasks Section -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="border-b border-gray-200 p-5">
          <h2 class="text-lg font-semibold text-gray-900">Delegated Tasks</h2>
        </div>
        <div class="divide-y divide-gray-100">
          <div 
            v-for="task in delegatedTasks" 
            :key="task.id"
            class="p-5 hover:bg-gray-50 transition-colors"
          >
            <div class="flex items-start gap-3">
              <input 
                type="checkbox" 
                :checked="task.status === 'Completed'"
                @change="toggleTaskCompletion('delegated', task.id, task.status === 'Completed')"
                class="mt-1 rounded text-cyan-600 focus:ring-cyan-500"
              />
              <div class="flex-1">
                <div class="flex items-start justify-between">
                  <h3 class="font-medium text-gray-900" :class="{ 'line-through text-gray-500': task.status === 'Completed' }">
                    {{ task.title }}
                  </h3>
                  <div class="flex items-center gap-2">
                    <span 
                      v-if="task.priority_level > 3"
                      class="px-2 py-1 text-xs rounded-full"
                      :class="{
                        'bg-red-100 text-red-800': task.priority_level === 5,
                        'bg-orange-100 text-orange-800': task.priority_level === 4
                      }"
                    >
                      P{{ task.priority_level }}
                    </span>
                    <button 
                      @click="openTaskModal('delegated', task)"
                      class="text-cyan-600 hover:text-cyan-800 text-sm"
                    >
                      Track
                    </button>
                  </div>
                </div>
                <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-500">
                  <div v-if="task.task_category" class="flex items-center gap-1">
                    <Tag class="w-4 h-4" />
                    {{ task.task_category }}
                  </div>
                  <div v-if="task.is_billable" class="flex items-center gap-1">
                    <DollarSign class="w-4 h-4" />
                    Billable
                  </div>
                  <div class="flex items-center gap-1">
                    <User class="w-4 h-4" />
                    {{ task.assignee.first_name }} {{ task.assignee.last_name }}
                  </div>
                  <div v-if="task.due_date" class="flex items-center gap-1">
                    <Calendar class="w-4 h-4" />
                    Due: {{ format(parseISO(task.due_date), 'MMM d, yyyy') }}
                  </div>
                </div>
                <div v-if="task.daily_notes" class="mt-2 text-sm text-gray-600">
                  {{ task.daily_notes }}
                </div>
              </div>
            </div>
          </div>
          <div v-if="delegatedTasks.length === 0" class="p-8 text-center text-gray-500">
            No delegated tasks for today.
          </div>
        </div>
      </div>
    </div>

    <!-- Task Tracking Modal -->
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" v-if="updateTaskForm.task_id">
      <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="border-b border-gray-200 p-5 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900">Track Task Progress</h3>
          <button 
            @click="updateTaskForm.task_id = 0"
            class="text-gray-500 hover:text-gray-700"
          >
            ✕
          </button>
        </div>
        <div class="p-5 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Task Date</label>
              <input 
                v-model="updateTaskForm.task_date"
                type="date"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <input 
                v-model="updateTaskForm.task_category"
                type="text"
                placeholder="Task category"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500"
              />
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
              <input 
                v-model="updateTaskForm.start_time"
                type="time"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
              <input 
                v-model="updateTaskForm.end_time"
                type="time"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Est. Duration (min)</label>
              <input 
                v-model="updateTaskForm.estimated_duration_minutes"
                type="number"
                min="1"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500"
              />
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Priority Level</label>
            <select 
              v-model="updateTaskForm.priority_level"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500"
            >
              <option value="1">Low (1)</option>
              <option value="2">Medium (2)</option>
              <option value="3">Normal (3)</option>
              <option value="4">High (4)</option>
              <option value="5">Critical (5)</option>
            </select>
          </div>
          
          <div class="flex items-center gap-4">
            <label class="flex items-center gap-2">
              <input 
                v-model="updateTaskForm.is_billable"
                type="checkbox"
                class="rounded text-cyan-600 focus:ring-cyan-500"
              />
              <span class="text-sm text-gray-700">Billable Task</span>
            </label>
            
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-700 mb-1">Progress Status</label>
              <select 
                v-model="updateTaskForm.progress_status"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500"
              >
                <option value="not_started">Not Started</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="blocked">Blocked</option>
              </select>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Daily Notes</label>
            <textarea 
              v-model="updateTaskForm.daily_notes"
              rows="3"
              placeholder="Add notes about today's progress..."
              class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500"
            ></textarea>
          </div>
        </div>
        <div class="border-t border-gray-200 p-5 flex justify-end gap-3">
          <button 
            @click="updateTaskForm.task_id = 0"
            class="px-4 py-2 text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50"
          >
            Cancel
          </button>
          <button 
            @click="submitTaskUpdate"
            class="btn-glass"
          >
            Save Tracking
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>