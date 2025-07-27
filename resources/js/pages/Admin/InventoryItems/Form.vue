<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  form: Object, // Inertia form object
});

const emit = defineEmits(['submit']);

const itemCategories = ['Medical Equipment', 'Office Supplies', 'Diagnostic Tools', 'Furniture', 'Other'];
const itemTypes = {
  'Medical Equipment': ['Stethoscope', 'Blood Pressure Monitor', 'Wheelchair', 'Crutches', 'Hospital Bed'],
  'Office Supplies': ['Laptop', 'Printer', 'Desk', 'Chair', 'Stationery'],
  'Diagnostic Tools': ['X-ray Machine', 'Ultrasound Machine', 'MRI Scanner'],
  'Furniture': ['Patient Bed', 'Waiting Chair', 'Cabinet'],
  'Other': ['Miscellaneous'],
};
const itemStatuses = ['Available', 'In Use', 'Under Maintenance', 'Lost', 'Damaged', 'Retired'];

// You would fetch suppliers from your backend and pass them as a prop
const suppliers = [
  { id: 1, name: 'MedSupply Co.' },
  { id: 2, name: 'Office Depot' },
];

// You would fetch staff, patients, departments, events for assigned_to
const assignedToOptions = [
  { type: 'staff', id: 1, name: 'Dr. Smith' },
  { type: 'patient', id: 1, name: 'John Doe' },
  { type: 'department', id: 1, name: 'Cardiology' },
  { type: 'event', id: 1, name: 'Health Fair 2025' },
];

</script>

<template>
  <form @submit.prevent="emit('submit')">
    <div class="space-y-6">
      <!-- Basic Information -->
      <div class="border-b border-gray-900/10 pb-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Basic Information</h2>
        <p class="mt-1 text-sm text-muted-foreground">Essential details about the inventory item.</p>

        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
          <div class="sm:col-span-3">
            <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white">Item Name</label>
            <input type="text" id="name" v-model="form.name" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required />
            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="serial_number" class="block text-sm font-medium text-gray-900 dark:text-white">Serial Number</label>
            <input type="text" id="serial_number" v-model="form.serial_number" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
            <div v-if="form.errors.serial_number" class="text-red-500 text-sm mt-1">{{ form.errors.serial_number }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="item_category" class="block text-sm font-medium text-gray-900 dark:text-white">Category</label>
            <select id="item_category" v-model="form.item_category" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
              <option :value="null">Select a category</option>
              <option v-for="category in itemCategories" :key="category" :value="category">{{ category }}</option>
            </select>
            <div v-if="form.errors.item_category" class="text-red-500 text-sm mt-1">{{ form.errors.item_category }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="item_type" class="block text-sm font-medium text-gray-900 dark:text-white">Type</label>
            <select id="item_type" v-model="form.item_type" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" :disabled="!form.item_category">
              <option :value="null">Select a type</option>
              <option v-for="type in itemTypes[form.item_category] || []" :key="type" :value="type">{{ type }}</option>
            </select>
            <div v-if="form.errors.item_type" class="text-red-500 text-sm mt-1">{{ form.errors.item_type }}</div>
          </div>

          <div class="col-span-full">
            <label for="description" class="block text-sm font-medium text-gray-900 dark:text-white">Description</label>
            <textarea id="description" v-model="form.description" rows="3" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"></textarea>
            <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
          </div>
        </div>
      </div>

      <!-- Acquisition & Assignment -->
      <div class="border-b border-gray-900/10 pb-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Acquisition & Assignment</h2>
        <p class="mt-1 text-sm text-muted-foreground">Details about when and how the item was acquired, and its current assignment.</p>

        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
          <div class="sm:col-span-3">
            <label for="purchase_date" class="block text-sm font-medium text-gray-900 dark:text-white">Purchase Date</label>
            <input type="date" id="purchase_date" v-model="form.purchase_date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
            <div v-if="form.errors.purchase_date" class="text-red-500 text-sm mt-1">{{ form.errors.purchase_date }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="warranty_expiry" class="block text-sm font-medium text-gray-900 dark:text-white">Warranty Expiry</label>
            <input type="date" id="warranty_expiry" v-model="form.warranty_expiry" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
            <div v-if="form.errors.warranty_expiry" class="text-red-500 text-sm mt-1">{{ form.errors.warranty_expiry }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="supplier_id" class="block text-sm font-medium text-gray-900 dark:text-white">Supplier</label>
            <select id="supplier_id" v-model="form.supplier_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
              <option :value="null">Select a supplier</option>
              <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
            </select>
            <div v-if="form.errors.supplier_id" class="text-red-500 text-sm mt-1">{{ form.errors.supplier_id }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="status" class="block text-sm font-medium text-gray-900 dark:text-white">Status</label>
            <select id="status" v-model="form.status" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
              <option v-for="statusOption in itemStatuses" :key="statusOption" :value="statusOption">{{ statusOption }}</option>
            </select>
            <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="assigned_to_type" class="block text-sm font-medium text-gray-900 dark:text-white">Assigned To Type</label>
            <select id="assigned_to_type" v-model="form.assigned_to_type" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
              <option :value="null">Select assignment type</option>
              <option value="staff">Staff</option>
              <option value="patient">Patient</option>
              <option value="department">Department</option>
              <option value="event">Event</option>
            </select>
            <div v-if="form.errors.assigned_to_type" class="text-red-500 text-sm mt-1">{{ form.errors.assigned_to_type }}</div>
          </div>

          <div class="sm:col-span-3" v-if="form.assigned_to_type">
            <label for="assigned_to_id" class="block text-sm font-medium text-gray-900 dark:text-white">Assigned To</label>
            <select id="assigned_to_id" v-model="form.assigned_to_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
              <option :value="null">Select {{ form.assigned_to_type }}</option>
              <option v-for="option in assignedToOptions.filter(o => o.type === form.assigned_to_type)" :key="option.id" :value="option.id">{{ option.name }}</option>
            </select>
            <div v-if="form.errors.assigned_to_id" class="text-red-500 text-sm mt-1">{{ form.errors.assigned_to_id }}</div>
          </div>
        </div>
      </div>

      <!-- Maintenance -->
      <div class="border-b border-gray-900/10 pb-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Maintenance Details</h2>
        <p class="mt-1 text-sm text-muted-foreground">Information regarding the item's maintenance schedule and history.</p>

        <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
          <div class="sm:col-span-3">
            <label for="last_maintenance_date" class="block text-sm font-medium text-gray-900 dark:text-white">Last Maintenance Date</label>
            <input type="date" id="last_maintenance_date" v-model="form.last_maintenance_date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
            <div v-if="form.errors.last_maintenance_date" class="text-red-500 text-sm mt-1">{{ form.errors.last_maintenance_date }}</div>
          </div>

          <div class="sm:col-span-3">
            <label for="next_maintenance_due" class="block text-sm font-medium text-gray-900 dark:text-white">Next Maintenance Due</label>
            <input type="date" id="next_maintenance_due" v-model="form.next_maintenance_due" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
            <div v-if="form.errors.next_maintenance_due" class="text-red-500 text-sm mt-1">{{ form.errors.next_maintenance_due }}</div>
          </div>

          <div class="col-span-full">
            <label for="maintenance_schedule" class="block text-sm font-medium text-gray-900 dark:text-white">Maintenance Schedule</label>
            <textarea id="maintenance_schedule" v-model="form.maintenance_schedule" rows="2" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"></textarea>
            <div v-if="form.errors.maintenance_schedule" class="text-red-500 text-sm mt-1">{{ form.errors.maintenance_schedule }}</div>
          </div>
        </div>
      </div>

      <!-- Additional Notes -->
      <div>
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Additional Notes</h2>
        <p class="mt-1 text-sm text-muted-foreground">Any other relevant information about the item.</p>
        <div class="mt-4">
          <textarea id="notes" v-model="form.notes" rows="3" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"></textarea>
          <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">{{ form.errors.notes }}</div>
        </div>
      </div>
    </div>
  </form>
</template>
