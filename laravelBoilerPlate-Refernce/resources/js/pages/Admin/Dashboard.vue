<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import {
  Users,
  UserCheck,
  UserCog,
  Shield,
  Calendar,
  Clock,
  TrendingUp,
  Activity,
  Briefcase,
  Zap,
  MessageSquare,
  Bell,
  Settings,
  BarChart3,
  FileText,
  Globe
} from 'lucide-vue-next'

interface DashboardStats {
  totalUsers: number
  activeUsers: number
  staffMembers: number
  departments: number
  newUsersThisMonth: number
  systemHealth: number
}

const props = withDefaults(
  defineProps<{
    title?: string
    stats?: DashboardStats
  }>(),
  {
    title: 'Dashboard',
    stats: () => ({
      totalUsers: 156,
      activeUsers: 142,
      staffMembers: 24,
      departments: 6,
      newUsersThisMonth: 12,
      systemHealth: 98
    })
  }
)

const page = usePage()
const user = computed(() => page.props.auth?.user)

// Real-time clock
const currentTime = ref('')
const currentDate = ref('')

const updateTime = () => {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  })
  currentDate.value = now.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

let timeInterval: NodeJS.Timeout

onMounted(() => {
  updateTime()
  timeInterval = setInterval(updateTime, 1000)
})

onUnmounted(() => {
  if (timeInterval) {
    clearInterval(timeInterval)
  }
})

const getRoleBadgeClass = (roleName: string) => {
  const classes = {
    superadmin: 'badge-primary',
    admin: 'badge-danger',
    ceo: 'badge-warning',
    coo: 'badge-info',
    staff: 'badge-info',
    user: 'badge-success'
  }
  return classes[roleName as keyof typeof classes] || 'badge-success'
}
</script>

<template>
  <Head :title="title" />
  <AdminLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ title }}
      </h2>
    </template>

    <div class="space-y-6 p-6">
      <!-- Welcome Header with Liquid Glass Effect -->
      <div class="liquidGlass-wrapper rounded-xl">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content p-8">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="mb-6 lg:mb-0">
              <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Welcome back, {{ user?.name }}!
              </h1>
              <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                Here's what's happening with your application today.
              </p>
              <div class="mt-4 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                <span class="flex items-center">
                  <Calendar class="w-4 h-4 mr-1" />
                  {{ currentDate }}
                </span>
                <span class="flex items-center">
                  <Clock class="w-4 h-4 mr-1" />
                  {{ currentTime }}
                </span>
              </div>
            </div>
            <div class="flex items-center space-x-4">
              <div class="text-right">
                <div class="text-sm text-gray-500 dark:text-gray-400">Your Role</div>
                <div class="flex flex-wrap gap-1 mt-1">
                  <span
                    v-for="role in user?.roles"
                    :key="role.id"
                    :class="getRoleBadgeClass(role.name)"
                    class="badge"
                  >
                    <Shield class="w-3 h-3 mr-1" />
                    {{ role.name }}
                  </span>
                </div>
              </div>
              <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg">
                {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card group hover:scale-105 transition-transform duration-200">
          <div class="card-body">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.totalUsers }}</p>
                <p class="text-sm text-green-600 dark:text-green-400 mt-1">
                  <TrendingUp class="w-4 h-4 inline mr-1" />
                  +{{ stats.newUsersThisMonth }} this month
                </p>
              </div>
              <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                <Users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
              </div>
            </div>
          </div>
        </div>

        <div class="card group hover:scale-105 transition-transform duration-200">
          <div class="card-body">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Users</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.activeUsers }}</p>
                <p class="text-sm text-green-600 dark:text-green-400 mt-1">
                  <Activity class="w-4 h-4 inline mr-1" />
                  {{ ((stats.activeUsers / stats.totalUsers) * 100).toFixed(1) }}% active
                </p>
              </div>
              <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                <UserCheck class="w-6 h-6 text-green-600 dark:text-green-400" />
              </div>
            </div>
          </div>
        </div>

        <div class="card group hover:scale-105 transition-transform duration-200">
          <div class="card-body">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Staff Members</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.staffMembers }}</p>
                <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                  <Briefcase class="w-4 h-4 inline mr-1" />
                  Across {{ stats.departments }} departments
                </p>
              </div>
              <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                <UserCog class="w-6 h-6 text-purple-600 dark:text-purple-400" />
              </div>
            </div>
          </div>
        </div>

        <div class="card group hover:scale-105 transition-transform duration-200">
          <div class="card-body">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">System Health</p>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ stats.systemHealth }}%</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                  <Zap class="w-4 h-4 inline mr-1" />
                  All systems operational
                </p>
              </div>
              <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                <Activity class="w-6 h-6 text-green-600 dark:text-green-400" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
          <div class="card-header">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Actions</h3>
          </div>
          <div class="card-body">
            <div class="grid grid-cols-2 gap-4">
              <a href="/admin/users" class="group flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-400 transition-colors duration-200">
                <Users class="w-8 h-8 text-gray-400 group-hover:text-blue-500 mb-2" />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400">Manage Users</span>
              </a>

              <a href="/admin/staff" class="group flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-green-500 dark:hover:border-green-400 transition-colors duration-200">
                <UserCog class="w-8 h-8 text-gray-400 group-hover:text-green-500 mb-2" />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-green-600 dark:group-hover:text-green-400">Staff Management</span>
              </a>

              <a href="/messages" class="group flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-purple-500 dark:hover:border-purple-400 transition-colors duration-200">
                <MessageSquare class="w-8 h-8 text-gray-400 group-hover:text-purple-500 mb-2" />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-purple-600 dark:group-hover:text-purple-400">Messages</span>
              </a>

              <a href="/settings" class="group flex flex-col items-center p-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-orange-500 dark:hover:border-orange-400 transition-colors duration-200">
                <Settings class="w-8 h-8 text-gray-400 group-hover:text-orange-500 mb-2" />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-orange-600 dark:group-hover:text-orange-400">Settings</span>
              </a>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h3>
          </div>
          <div class="card-body">
            <div class="space-y-4">
              <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                  <Users class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">New user registered</p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">John Doe joined the platform</p>
                  <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">2 minutes ago</p>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                  <UserCheck class="w-4 h-4 text-green-600 dark:text-green-400" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">User verified email</p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">jane.smith@example.com verified</p>
                  <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">15 minutes ago</p>
                </div>
              </div>

              <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/20 rounded-full flex items-center justify-center">
                  <MessageSquare class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">New message received</p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">Support ticket #1234</p>
                  <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">1 hour ago</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
