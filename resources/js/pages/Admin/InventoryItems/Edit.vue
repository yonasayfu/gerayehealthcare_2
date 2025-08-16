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
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Inventory Item
            </h3>
            <Link :href="route('admin.inventory-items.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <div v-if="Object.keys(form.errors).length" class="rounded-md bg-red-50 p-4 border border-red-200 text-red-800 text-sm">
              Please correct the highlighted errors and try again.
            </div>
            <Form :form="form" :suppliers="props.suppliers" @submit="submit" />
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b flex gap-2">
            <button @click="submit" :disabled="form.processing" class="btn btn-primary" type="button">
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
            <Link :href="route('admin.inventory-items.index')" class="btn btn-outline">Cancel</Link>
        </div>

    </div>
  </AppLayout>
</template>
