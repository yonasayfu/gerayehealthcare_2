<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ArrowLeft, ClipboardList, Edit3, User2, Calendar, Eye } from 'lucide-vue-next';
import ShowHeader from '@/components/ShowHeader.vue'

const props = defineProps<{
  inventoryAlert: any;
  returnTo?: string | null;
}>();

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Alerts', href: route('admin.inventory-alerts.index') },
  { title: `Alert #${props.inventoryAlert?.id ?? ''}`, href: route('admin.inventory-alerts.show', props.inventoryAlert?.id) },
];

const isDelegated = computed(() => !!props.inventoryAlert?.delegated_task_id || !!props.inventoryAlert?.delegatedTask);
</script>

<template>
  <Head :title="`Alert #${inventoryAlert?.id}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative">
        <ShowHeader title="Inventory Alert Details" :subtitle="`Alert #${inventoryAlert?.id}`">
          <template #actions>
            <Link :href="returnTo || route('admin.inventory-alerts.index')" class="btn-glass btn-glass-sm">Back</Link>
            <Link :href="route('admin.inventory-alerts.edit', inventoryAlert.id)" class="btn-glass btn-glass-sm">Edit</Link>
          </template>
        </ShowHeader>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 rounded-lg bg-white dark:bg-gray-900 shadow p-4 space-y-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span
                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                :class="inventoryAlert?.is_active ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-200'"
              >
                {{ inventoryAlert?.is_active ? 'Active' : 'Resolved' }}
              </span>
              <span class="text-sm text-gray-500">Triggered:
                <strong>{{ inventoryAlert?.triggered_at ? new Date(inventoryAlert.triggered_at).toLocaleString() : '-' }}</strong>
              </span>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
              <div class="text-gray-500">Item</div>
              <div class="font-medium">{{ inventoryAlert?.item?.name ?? '-' }}</div>
            </div>
            <div>
              <div class="text-gray-500">Alert Type</div>
              <div class="font-medium">{{ inventoryAlert?.alert_type ?? '-' }}</div>
            </div>
            <div>
              <div class="text-gray-500">Quantity on Hand</div>
              <div class="font-medium">{{ inventoryAlert?.item?.quantity_on_hand ?? '-' }}</div>
            </div>
            <div>
              <div class="text-gray-500">Reorder Level</div>
              <div class="font-medium">{{ inventoryAlert?.item?.reorder_level ?? '-' }}</div>
            </div>
          </div>

          <div>
            <div class="text-gray-500 text-sm mb-1">Message</div>
            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ inventoryAlert?.message ?? '-' }}</p>
          </div>
        </div>

        <div class="rounded-lg bg-white dark:bg-gray-900 shadow p-4 space-y-3">
          <div class="flex items-center justify-between">
            <h2 class="font-semibold">Delegation</h2>
            <span v-if="isDelegated" class="text-xs rounded-full px-2 py-0.5 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200">Delegated</span>
            <span v-else class="text-xs rounded-full px-2 py-0.5 bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200">Not Delegated</span>
          </div>

          <template v-if="isDelegated">
            <div class="text-sm space-y-2">
              <div>
                <div class="text-gray-500">Title</div>
                <div class="font-medium">{{ inventoryAlert?.delegatedTask?.title ?? '-' }}</div>
              </div>
              <div class="flex items-center gap-2">
                <User2 class="w-4 h-4 text-gray-500" />
                <div>
                  <div class="text-gray-500 text-xs">Assigned To</div>
                  <div class="font-medium">{{ inventoryAlert?.delegatedTask?.assignee?.name ?? '#' + (inventoryAlert?.delegatedTask?.assigned_to ?? '-') }}</div>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <Calendar class="w-4 h-4 text-gray-500" />
                <div>
                  <div class="text-gray-500 text-xs">Due Date</div>
                  <div class="font-medium">{{ inventoryAlert?.delegatedTask?.due_date ? new Date(inventoryAlert.delegatedTask.due_date).toLocaleDateString() : '-' }}</div>
                </div>
              </div>
              <div>
                <div class="text-gray-500">Status</div>
                <div class="font-medium">{{ inventoryAlert?.delegatedTask?.status ?? '-' }}</div>
              </div>
            </div>
            <div class="flex gap-2 pt-2">
              <Link :href="route('admin.task-delegations.edit', inventoryAlert.delegatedTask.id)" class="btn-glass btn-glass-sm">Edit Task</Link>
            </div>
          </template>

          <template v-else>
            <p class="text-sm text-gray-600 dark:text-gray-300">You can delegate a task to handle this alert.</p>
            <Link
              :href="route('admin.task-delegations.create', {
                title: `Restock ${inventoryAlert?.item?.name ?? 'Item'} - ${inventoryAlert?.alert_type ?? 'Alert'}`,
                notes: inventoryAlert?.message ?? '',
                inventory_alert_id: inventoryAlert.id,
                return_to: route('admin.inventory-alerts.show', { inventory_alert: inventoryAlert.id })
              })"
              class="btn-glass btn-glass-sm"
            >
              <ClipboardList class="w-4 h-4" /> Delegate Task
            </Link>
          </template>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
