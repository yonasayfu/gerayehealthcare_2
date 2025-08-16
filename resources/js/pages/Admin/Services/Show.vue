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
    <div class="bg-white border rounded-lg shadow relative m-10">
      <div class="flex items-start justify-between p-5 border-b rounded-t">
        <h3 class="text-xl font-semibold">Service Details</h3>
        <Link :href="route('admin.services.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
        </Link>
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

      <div class="p-6 border-t border-gray-200 rounded-b">
        <div class="flex flex-wrap gap-2">
          <Link :href="route('admin.services.index')" class="btn btn-outline">Back to List</Link>
          <Link :href="route('admin.services.edit', service?.id)" class="btn btn-primary inline-flex items-center gap-1">
            <Edit3 class="h-4 w-4" /> Edit
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
