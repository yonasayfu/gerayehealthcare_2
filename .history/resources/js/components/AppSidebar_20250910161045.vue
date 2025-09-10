<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import {
  Home,
  Users,
  Calendar,
  FileText,
  Settings,
  BarChart3,
  CheckSquare,
  Clock
} from 'lucide-vue-next'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const user = page.props.auth.user

// Define sidebar items with permissions
const sidebarItems = [
  {
    name: 'Dashboard',
    href: route('dashboard'),
    icon: Home,
    permission: null, // Always visible
  },
  {
    name: 'Daily Tasks',
    href: route('staff.daily-tasks.index'),
    icon: CheckSquare,
    permission: null, // Available to all staff
  },
  {
    name: 'KPI Dashboard',
    href: route('staff.kpi-dashboard'),
    icon: BarChart3,
    permission: ['Super Admin', 'Admin', 'CEO', 'COO'], // Only for supervisors
  },
  {
    name: 'My Tasks',
    href: route('staff.task-delegations.index'),
    icon: Clock,
    permission: null, // Available to all staff
  },
  {
    name: 'My To-Do',
    href: route('staff.my-todo.index'),
    icon: FileText,
    permission: null, // Available to all staff
  },
  {
    name: 'My Patients',
    href: route('staff.patients.index'),
    icon: Users,
    permission: null, // Available to all staff
  },
  {
    name: 'My Visits',
    href: route('staff.visits.index'),
    icon: Calendar,
    permission: null, // Available to all staff
  },
  {
    name: 'My Appointments',
    href: route('staff.appointments.index'),
    icon: Calendar,
    permission: null, // Available to all staff
  },
  {
    name: 'My Earnings',
    href: route('staff.my-earnings.index'),
    icon: FileText,
    permission: null, // Available to all staff
  },
  {
    name: 'My Availability',
    href: route('staff.my-availability.index'),
    icon: Users,
    permission: null, // Available to all staff
  },
  {
    name: 'My Leave Requests',
    href: route('staff.leave-requests.index'),
    icon: Calendar,
    permission: null, // Available to all staff
  },
  {
    name: 'Settings',
    href: route('profile.edit'),
    icon: Settings,
    permission: null, // Always visible
  },
]

// Function to check if user has permission for an item
const hasPermission = (item: any) => {
  // If no permission is required, user has access
  if (!item.permission) return true
  
  // If user has no roles, they don't have access
  if (!user || !user.roles || !Array.isArray(user.roles)) return false
  
  // Check if user has any of the required roles
  return user.roles.some((role: string) => {
    if (Array.isArray(item.permission)) {
      return item.permission.includes(role)
    } else {
      return item.permission === role
    }
  })
}

</script>

<template>
  <div class="flex flex-col h-full bg-white border-r border-gray-200">
    <div class="flex items-center justify-center h-16 border-b border-gray-200">
      <h1 class="text-xl font-bold text-cyan-700">Geraye</h1>
    </div>
    <nav class="flex-1 px-2 py-4 space-y-1">
      <div v-for="item in sidebarItems" :key="item.name">
        <!-- Check if user has permission or if no permission is required -->
        <Link
          v-if="hasPermission(item)"
          :href="item.href"
          :class="[
            'flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors',
            route().current(item.href.replace(window.location.origin, '')) 
              ? 'bg-cyan-100 text-cyan-900' 
              : 'text-gray-700 hover:bg-gray-100'
          ]"
        >
          <component :is="item.icon" class="w-5 h-5 mr-3" />
          {{ item.name }}
        </Link>
      </div>
    </nav>
    <div class="p-4 border-t border-gray-200">
      <Link
        :href="route('profile.edit')"
        class="flex items-center p-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100"
      >
        <img
          :src="user?.profile_photo_url"
          :alt="user?.name"
          class="w-8 h-8 rounded-full"
        />
        <span class="ml-3">{{ user?.name }}</span>
      </Link>
    </div>
  </div>
</template>