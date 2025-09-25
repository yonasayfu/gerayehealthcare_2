<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Calendar, Clock } from 'lucide-vue-next'
import { Link } from '@inertiajs/vue3'

interface LeaveRequest {
  id: number
  staff: {
    first_name: string
    last_name: string
  }
  start_date: string
  end_date: string
  status: string
  created_at: string
}

const pendingRequests = ref<LeaveRequest[]>([])
const loading = ref(true)

// Mock data for demonstration
const mockRequests: LeaveRequest[] = [
  {
    id: 1,
    staff: { first_name: 'John', last_name: 'Doe' },
    start_date: '2023-06-15',
    end_date: '2023-06-17',
    status: 'Pending',
    created_at: '2023-06-01'
  },
  {
    id: 2,
    staff: { first_name: 'Jane', last_name: 'Smith' },
    start_date: '2023-06-20',
    end_date: '2023-06-22',
    status: 'Pending',
    created_at: '2023-06-02'
  }
]

onMounted(() => {
  // In a real implementation, this would fetch from an API
  setTimeout(() => {
    pendingRequests.value = mockRequests
    loading.value = false
  }, 500)
})

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString(undefined, {
    month: 'short',
    day: 'numeric'
  })
}
</script>

<template>
  <Card>
    <CardHeader class="pb-2">
      <CardTitle class="text-sm font-medium flex items-center justify-between">
        <span>Pending Leave Requests</span>
        <Calendar class="h-4 w-4 text-muted-foreground" />
      </CardTitle>
    </CardHeader>
    <CardContent>
      <div v-if="loading" class="text-center py-4">
        <p class="text-sm text-muted-foreground">Loading...</p>
      </div>
      <div v-else>
        <div v-if="pendingRequests.length === 0" class="text-center py-4">
          <p class="text-sm text-muted-foreground">No pending requests</p>
        </div>
        <div v-else>
          <div 
            v-for="request in pendingRequests" 
            :key="request.id"
            class="mb-3 last:mb-0 p-3 rounded-md border border-border hover:bg-muted/50 transition-colors"
          >
            <div class="flex justify-between items-start">
              <div>
                <p class="text-sm font-medium">{{ request.staff.first_name }} {{ request.staff.last_name }}</p>
                <div class="flex items-center text-xs text-muted-foreground mt-1">
                  <Clock class="h-3 w-3 mr-1" />
                  <span>{{ formatDate(request.start_date) }} - {{ formatDate(request.end_date) }}</span>
                </div>
              </div>
              <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Pending</span>
            </div>
          </div>
        </div>
      </div>
      <Button 
        v-if="pendingRequests.length > 0"
        variant="outline" 
        size="sm" 
        class="w-full mt-2"
        as-child
      >
        <Link :href="route('leave-requests.index')">
          View All Requests
        </Link>
      </Button>
    </CardContent>
  </Card>
</template>