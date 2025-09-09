
<script setup>
// ... existing code
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const can = (permissions) => {
    if (typeof permissions === 'string') {
        return page.props.auth.can[permissions] || false;
    }
    if (Array.isArray(permissions)) {
        return permissions.some(permission => page.props.auth.can[permission] || false);
    }
    return false;
};

const menu = computed(() => [
    {
        label: 'Dashboard',
        icon: 'pi pi-home',
        to: '/dashboard',
        visible: can('dashboard-view'),
    },
    {
        label: 'Patient Management',
        icon: 'pi pi-users',
        visible: can(['patient-view-any', 'patient-create', 'patient-update', 'patient-delete']),
        items: [
            {
                label: 'Patients',
                icon: 'pi pi-user',
                to: '/patients',
                visible: can('patient-view-any'),
            },
            {
                label: 'Add Patient',
                icon: 'pi pi-user-plus',
                to: '/patients/create',
                visible: can('patient-create'),
            },
        ],
    },
    {
        label: 'Staff Management',
        icon: 'pi pi-id-card',
        visible: can(['staff-view-any', 'staff-create', 'staff-update', 'staff-delete']),
        items: [
            {
                label: 'Staff',
                icon: 'pi pi-users',
                to: '/staff',
                visible: can('staff-view-any'),
            },
            {
                label: 'Add Staff',
                icon: 'pi pi-user-plus',
                to: '/staff/create',
                visible: can('staff-create'),
            },
        ],
    },
    {
        label: 'Role Management',
        icon: 'pi pi-shield',
        to: '/roles',
        visible: can('role-view-any'),
    },
    {
        label: 'Settings',
        icon: 'pi pi-cog',
        to: '/settings',
        visible: can('setting-view'),
    },
]);
</script>

<template>
    <div class="sidebar">
        <div class="sidebar-header">
            <span class="app-name">Your App</span>
        </div>
        <ul class="layout-menu">
            <template v-for="(item, i) in menu" :key="i">
                <li v-if="item.visible" class="layout-root-menuitem">
                    <div v-if="item.items" class="layout-menuitem-root-text">{{ item.label }}</div>
                    <ul v-if="item.items" class="layout-submenu">
                        <li v-for="(child, j) in item.items" :key="j">
                            <router-link v-if="child.visible" :to="child.to" class="p-ripple">
                                <i :class="child.icon" class="layout-menuitem-icon"></i>
                                <span class="layout-menuitem-text">{{ child.label }}</span>
                            </router-link>
                        </li>
                    </ul>
                    <router-link v-else :to="item.to" class="p-ripple">
                        <i :class="item.icon" class="layout-menuitem-icon"></i>
                        <span class="layout-menuitem-text">{{ item.label }}</span>
                    </router-link>
                </li>
            </template>
        </ul>
    </div>
</template>

<style scoped>
/* ... existing styles ... */
</style>
