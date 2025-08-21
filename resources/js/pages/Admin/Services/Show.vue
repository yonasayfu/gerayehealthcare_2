<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItemType } from '@/types'
import { Edit3 } from 'lucide-vue-next'

const props = defineProps<{ service: any }>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Services', href: route('admin.services.index') },
  { title: props.service?.name || 'Service', href: route('admin.services.show', props.service?.id) },
]

function formatCurrency(amount: number | string) {
  const n = Number(amount ?? 0)
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(n)
}
</script>

<template>
  <Head :title="`Service: ${service?.name ?? ''}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

      <!-- compact liquid glass header (now full-width and same sizing as main card) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Service Details</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">Service: {{ service?.name }}</p>
          </div>
          <!-- top actions intentionally removed to avoid duplication; see footer -->
        </div>
      </div>

      <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div>
            <p class="text-sm text-gray-500">Name</p>
            <p class="font-medium">{{ service?.name ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Category</p>
            <p class="font-medium">{{ service?.category ?? '-' }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Duration</p>
            <p class="font-medium">{{ service?.duration ? service.duration + ' min' : '-' }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Price</p>
            <p class="font-medium">{{ formatCurrency(service?.price) }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Status</p>
            <span :class="['px-2 py-1 text-xs font-semibold rounded-full', service?.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
              {{ service?.is_active ? 'Active' : 'Inactive' }}
            </span>
          </div>
        </div>

        <div>
          <p class="text-sm text-gray-500 mb-1">Description</p>
          <p class="whitespace-pre-line">{{ service?.description ?? '-' }}</p>
        </div>
      </div>

            <!-- footer actions (single source of actions, right aligned) -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          <Link :href="route('admin.services.index')" class="btn-glass btn-glass-sm">Back to List</Link>
          <button @click="window.print()" class="btn-glass btn-glass-sm">Print Current</button>
          <Link :href="route('admin.services.edit', service.id)" class="btn-glass btn-glass-sm">Edit</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
