<template>
  <AppLayout title="RBAC Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        RBAC Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="mb-6">
              <h3 class="text-lg font-medium mb-2">Your Current Role</h3>
              <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-lg">
                <p class="text-blue-800 dark:text-blue-200">
                  <span class="font-semibold">Role:</span> 
                  {{ userRole ? userRole.name : 'No role assigned' }}
                </p>
                <p class="text-blue-800 dark:text-blue-200 mt-2">
                  <span class="font-semibold">User:</span> 
                  {{ user.name }} ({{ user.email }})
                </p>
              </div>
            </div>

            <div class="mb-6">
              <h3 class="text-lg font-medium mb-2">Available Roles</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div 
                  v-for="role in roles" 
                  :key="role.id"
                  class="border rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer"
                  @click="viewRole(role.name)"
                >
                  <h4 class="font-semibold text-lg">{{ role.name }}</h4>
                  <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ role.permissions_count }} permissions
                  </p>
                </div>
              </div>
            </div>

            <div v-if="selectedRole" class="mt-8">
              <h3 class="text-lg font-medium mb-2">Permissions for {{ selectedRole.name }}</h3>
              <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                <ul class="list-disc pl-5">
                  <li 
                    v-for="permission in selectedRolePermissions" 
                    :key="permission.id"
                    class="py-1"
                  >
                    {{ permission.name }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  roles: Array,
  userRole: Object,
  user: Object
})

const selectedRole = ref(null)
const selectedRolePermissions = ref([])

const viewRole = (roleName) => {
  router.visit(route('rbac.roles.show', roleName), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      selectedRole.value = page.props.role
      selectedRolePermissions.value = page.props.permissions
    }
  })
}

onMounted(() => {
  // Initialize with user's role if available
  if (props.userRole) {
    viewRole(props.userRole.name)
  }
})
</script>