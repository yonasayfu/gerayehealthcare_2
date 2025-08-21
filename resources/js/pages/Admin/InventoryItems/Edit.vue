<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Form from './Form.vue';

const props = defineProps({
  inventoryItem: Object, // The inventory item to edit
  suppliers: {
    type: Array,
    default: () => [],
  },
});

function toDateInput(value: string | null): string | null {
  if (!value) return null;
  const d = new Date(value);
  if (isNaN(d.getTime())) return null;
  const yyyy = d.getFullYear();
  const mm = String(d.getMonth() + 1).padStart(2, '0');
  const dd = String(d.getDate()).padStart(2, '0');
  return `${yyyy}-${mm}-${dd}`;
}

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Items', href: route('admin.inventory-items.index') },
  { title: 'Edit', href: route('admin.inventory-items.edit', props.inventoryItem.id) },
];

const form = useForm({
  name: props.inventoryItem.name,
  description: props.inventoryItem.description,
  item_category: props.inventoryItem.item_category,
  item_type: props.inventoryItem.item_type,
  serial_number: props.inventoryItem.serial_number,
  purchase_date: toDateInput(props.inventoryItem.purchase_date),
  warranty_expiry: toDateInput(props.inventoryItem.warranty_expiry),
  supplier_id: props.inventoryItem.supplier_id,
  assigned_to_type: props.inventoryItem.assigned_to_type,
  assigned_to_id: props.inventoryItem.assigned_to_id,
  last_maintenance_date: toDateInput(props.inventoryItem.last_maintenance_date),
  next_maintenance_due: toDateInput(props.inventoryItem.next_maintenance_due),
  maintenance_schedule: props.inventoryItem.maintenance_schedule,
  notes: props.inventoryItem.notes,
  status: props.inventoryItem.status,
});

function submit() {
  form.put(route('admin.inventory-items.update', props.inventoryItem.id));
}
</script>

<template>
  <Head title="Edit Inventory Item" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Inventory Item</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update inventory item information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />

          <!-- Footer actions: Cancel + Save (right aligned), no logic changes -->
          <div class="flex justify-end gap-2 pt-2">
            <Link
              :href="route('admin.inventory-items.index')"
              class="btn-glass btn-glass-sm"
            >
              Cancel
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
