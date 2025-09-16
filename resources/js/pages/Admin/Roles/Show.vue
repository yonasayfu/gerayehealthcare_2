<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ShowHeader from '@/components/ShowHeader.vue'

interface Permission { id: number; name: string }
interface Role { id: number; name: string; permissions: Permission[]; created_at?: string; updated_at?: string }

const props = defineProps<{ role: Role }>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Roles', href: route('admin.roles.index') },
  { title: props.role.name, href: route('admin.roles.show', props.role.id) },
]
</script>

<template>
  <Head :title="`Role: ${role.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">
      <ShowHeader title="Role Details" :subtitle="`Role: ${role.name}`">
        <template #actions>
          <Link :href="route('admin.roles.index')" class="btn-glass btn-glass-sm">Back</Link>
        </template>
      </ShowHeader>

      <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
        <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
        <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
        <p class="text-gray-600 dark:text-gray-400 print-document-title">Role Details</p>
        <hr class="my-3 border-gray-300 print:my-2">
      </div>

      <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <p class="text-sm text-muted-foreground">Role Name</p>
            <p class="font-medium">{{ role.name }}</p>
          </div>
          <div>
            <p class="text-sm text-muted-foreground">Permissions</p>
            <div class="mt-1 flex flex-wrap gap-2">
              <span v-for="perm in role.permissions || []" :key="perm.id" class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full dark:bg-blue-900 dark:text-blue-200">
                {{ perm.name }}
              </span>
              <span v-if="!role.permissions || role.permissions.length === 0" class="text-gray-400 text-sm italic">No permissions</span>
            </div>
          </div>
        </div>
      </div>

      <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          
        </div>
      </div>
    </div>
  </AppLayout>
</template>
