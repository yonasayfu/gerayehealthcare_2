<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface Props {
  permission?: string;
  role?: string;
  permissions?: string[];
  roles?: string[];
  requireAll?: boolean;
  fallback?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  requireAll: false,
  fallback: false,
});

const page = usePage();

// Get user permissions and roles from page props
const userPermissions = computed(() => {
  return page.props.auth?.user?.permissions || [];
});

const userRoles = computed(() => {
  return page.props.auth?.user?.roles || [];
});

// Check if user is super admin (has all permissions)
const isSuperAdmin = computed(() => {
  return userRoles.value.some((role: any) => 
    role.name === 'super-admin' || role.name === 'Super Admin'
  );
});

// Check single permission
const hasPermission = (permission: string): boolean => {
  if (isSuperAdmin.value) return true;
  return userPermissions.value.some((perm: any) => perm.name === permission);
};

// Check single role
const hasRole = (role: string): boolean => {
  if (isSuperAdmin.value) return true;
  return userRoles.value.some((userRole: any) => userRole.name === role);
};

// Check multiple permissions
const hasPermissions = (permissions: string[], requireAll: boolean = false): boolean => {
  if (isSuperAdmin.value) return true;
  
  if (requireAll) {
    return permissions.every(permission => hasPermission(permission));
  } else {
    return permissions.some(permission => hasPermission(permission));
  }
};

// Check multiple roles
const hasRoles = (roles: string[], requireAll: boolean = false): boolean => {
  if (isSuperAdmin.value) return true;
  
  if (requireAll) {
    return roles.every(role => hasRole(role));
  } else {
    return roles.some(role => hasRole(role));
  }
};

// Main authorization check
const isAuthorized = computed(() => {
  // If user is super admin, always authorized
  if (isSuperAdmin.value) return true;

  let authorized = true;

  // Check single permission
  if (props.permission) {
    authorized = authorized && hasPermission(props.permission);
  }

  // Check single role
  if (props.role) {
    authorized = authorized && hasRole(props.role);
  }

  // Check multiple permissions
  if (props.permissions && props.permissions.length > 0) {
    authorized = authorized && hasPermissions(props.permissions, props.requireAll);
  }

  // Check multiple roles
  if (props.roles && props.roles.length > 0) {
    authorized = authorized && hasRoles(props.roles, props.requireAll);
  }

  return authorized;
});
</script>

<template>
  <div v-if="isAuthorized">
    <slot />
  </div>
  <div v-else-if="fallback">
    <slot name="fallback">
      <div class="text-center py-8">
        <div class="text-gray-500 dark:text-gray-400">
          <p class="text-lg font-medium">Access Denied</p>
          <p class="text-sm">You don't have permission to view this content.</p>
        </div>
      </div>
    </slot>
  </div>
</template>
