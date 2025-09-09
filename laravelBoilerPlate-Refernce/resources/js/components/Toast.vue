<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 z-50 space-y-2">
      <TransitionGroup name="toast" tag="div">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="[
            'max-w-sm w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden',
            getToastClasses(toast.type)
          ]"
        >
          <div class="p-4">
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <component
                  :is="getToastIcon(toast.type)"
                  :class="[
                    'h-6 w-6',
                    getIconClasses(toast.type)
                  ]"
                />
              </div>
              <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ toast.title }}
                </p>
                <p v-if="toast.message" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                  {{ toast.message }}
                </p>
              </div>
              <div class="ml-4 flex-shrink-0 flex">
                <button
                  @click="removeToast(toast.id)"
                  class="bg-white dark:bg-gray-800 rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <span class="sr-only">Close</span>
                  <X class="h-5 w-5" />
                </button>
              </div>
            </div>
          </div>
          
          <!-- Progress bar -->
          <div
            v-if="toast.duration && toast.duration > 0"
            class="h-1 bg-gray-200 dark:bg-gray-700"
          >
            <div
              :class="[
                'h-full transition-all ease-linear',
                getProgressClasses(toast.type)
              ]"
              :style="{ 
                width: `${getProgress(toast)}%`,
                transitionDuration: `${toast.duration}ms`
              }"
            ></div>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { 
  CheckCircle, 
  XCircle, 
  AlertTriangle, 
  Info, 
  X 
} from 'lucide-vue-next'

export interface Toast {
  id: string
  type: 'success' | 'error' | 'warning' | 'info'
  title: string
  message?: string
  duration?: number
  createdAt: number
}

const toasts = ref<Toast[]>([])
let toastId = 0

const createToast = (toast: Omit<Toast, 'id' | 'createdAt'>): string => {
  const id = `toast-${++toastId}`
  const newToast: Toast = {
    ...toast,
    id,
    createdAt: Date.now(),
    duration: toast.duration ?? 5000
  }
  
  toasts.value.push(newToast)
  
  if (newToast.duration && newToast.duration > 0) {
    setTimeout(() => {
      removeToast(id)
    }, newToast.duration)
  }
  
  return id
}

const removeToast = (id: string) => {
  const index = toasts.value.findIndex(toast => toast.id === id)
  if (index > -1) {
    toasts.value.splice(index, 1)
  }
}

const clearAllToasts = () => {
  toasts.value = []
}

const getToastClasses = (type: Toast['type']) => {
  const classes = {
    success: 'border-l-4 border-green-400',
    error: 'border-l-4 border-red-400',
    warning: 'border-l-4 border-yellow-400',
    info: 'border-l-4 border-blue-400'
  }
  return classes[type]
}

const getToastIcon = (type: Toast['type']) => {
  const icons = {
    success: CheckCircle,
    error: XCircle,
    warning: AlertTriangle,
    info: Info
  }
  return icons[type]
}

const getIconClasses = (type: Toast['type']) => {
  const classes = {
    success: 'text-green-400',
    error: 'text-red-400',
    warning: 'text-yellow-400',
    info: 'text-blue-400'
  }
  return classes[type]
}

const getProgressClasses = (type: Toast['type']) => {
  const classes = {
    success: 'bg-green-400',
    error: 'bg-red-400',
    warning: 'bg-yellow-400',
    info: 'bg-blue-400'
  }
  return classes[type]
}

const getProgress = (toast: Toast) => {
  if (!toast.duration || toast.duration <= 0) return 0
  const elapsed = Date.now() - toast.createdAt
  const progress = Math.max(0, 100 - (elapsed / toast.duration) * 100)
  return progress
}

// Global toast methods
const showSuccess = (title: string, message?: string, duration?: number) => {
  return createToast({ type: 'success', title, message, duration })
}

const showError = (title: string, message?: string, duration?: number) => {
  return createToast({ type: 'error', title, message, duration })
}

const showWarning = (title: string, message?: string, duration?: number) => {
  return createToast({ type: 'warning', title, message, duration })
}

const showInfo = (title: string, message?: string, duration?: number) => {
  return createToast({ type: 'info', title, message, duration })
}

// Expose methods globally
defineExpose({
  showSuccess,
  showError,
  showWarning,
  showInfo,
  removeToast,
  clearAllToasts
})

// Listen for global toast events
onMounted(() => {
  // Listen for Inertia success/error messages
  window.addEventListener('toast', (event: any) => {
    const { type, title, message, duration } = event.detail
    createToast({ type, title, message, duration })
  })
})

onUnmounted(() => {
  window.removeEventListener('toast', () => {})
})
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.toast-move {
  transition: transform 0.3s ease;
}
</style>
