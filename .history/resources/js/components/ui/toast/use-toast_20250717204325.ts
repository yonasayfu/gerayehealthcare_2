import { ref, onUnmounted } from 'vue'

interface Toast {
  id: string
  title: string
  description: string
  variant: 'default' | 'destructive'
}

const toasts = ref<Toast[]>([])

let toastCounter = 0

export function useToast() {
  const showToast = (toast: Omit<Toast, 'id'>) => {
    const id = `toast-${toastCounter++}`
    toasts.value.push({ ...toast, id })

    setTimeout(() => {
      toasts.value = toasts.value.filter(t => t.id !== id)
    }, 5000)
  }

  onUnmounted(() => {
    toasts.value = []
  })

  return {
