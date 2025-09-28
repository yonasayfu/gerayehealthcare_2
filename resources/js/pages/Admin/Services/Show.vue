<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItemType } from '@/types'
import { Edit3 } from 'lucide-vue-next'
import ShowHeader from '@/components/ShowHeader.vue'

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

function printPage() {
  setTimeout(() => { try { window.print(); } catch (e) { console.error('Print failed', e); } }, 100)
}
</script>

<template>
  <Head :title="`Service: ${service?.name ?? ''}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

      <ShowHeader title="Service Details" :subtitle="`Service: ${service?.name}`">
        <template #actions>
          <Link :href="route('admin.services.index')" class="btn-glass btn-glass-sm">Back</Link>
        </template>
      </ShowHeader>

      <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
        <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
        <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
        <p class="text-gray-600 dark:text-gray-400 print-document-title">Service Details</p>
        <hr class="my-3 border-gray-300 print:my-2">
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
          
          <button @click="printPage" class="btn-glass btn-glass-sm">Print Current</button>
          <Link :href="route('admin.services.edit', service.id)" class="btn-glass btn-glass-sm">Edit</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
