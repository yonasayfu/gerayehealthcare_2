<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  form: Object,
  categories: Array,
  departments: Array,
});

const handlePhotoChange = (e) => {
  props.form.photo = e.target.files[0];
};
</script>

<template>
  <form @submit.prevent="emit('submit')">
    <div class="border-b border-border pb-12">
      <h2 class="text-base font-semibold text-gray-900 dark:text-white">Inventory Item Information</h2>
      <p class="mt-1 text-sm text-muted-foreground">
        Provide details for the inventory item.
      </p>

      <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Item Code</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.item_code"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.item_code" class="text-red-500 text-sm mt-1">
              {{ form.errors.item_code }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Name</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.name"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
              {{ form.errors.name }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Description</label>
          <div class="mt-2">
            <textarea
              v-model="form.description"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            ></textarea>
            <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">
              {{ form.errors.description }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Category</label>
          <div class="mt-2">
            <select
              v-model="form.category_id"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            >
              <option :value="null">Select Category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
            <div v-if="form.errors.category_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.category_id }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Department</label>
          <div class="mt-2">
            <select
              v-model="form.department_id"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            >
              <option :value="null">Select Department</option>
              <option v-for="department in departments" :key="department.id" :value="department.id">
                {{ department.name }}
              </option>
            </select>
            <div v-if="form.errors.department_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.department_id }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Serial Number</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.serial_number"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.serial_number" class="text-red-500 text-sm mt-1">
              {{ form.errors.serial_number }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Barcode</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.barcode"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.barcode" class="text-red-500 text-sm mt-1">
              {{ form.errors.barcode }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Location</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.location"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.location" class="text-red-500 text-sm mt-1">
              {{ form.errors.location }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Status</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.status"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">
              {{ form.errors.status }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Condition Status</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.condition_status"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.condition_status" class="text-red-500 text-sm mt-1">
              {{ form.errors.condition_status }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Purchase Date</label>
          <div class="mt-2">
            <input
              type="date"
              v-model="form.purchase_date"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.purchase_date" class="text-red-500 text-sm mt-1">
              {{ form.errors.purchase_date }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Purchase Cost</label>
          <div class="mt-2">
            <input
              type="number"
              step="0.01"
              v-model="form.purchase_cost"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.purchase_cost" class="text-red-500 text-sm mt-1">
              {{ form.errors.purchase_cost }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Current Value</label>
          <div class="mt-2">
            <input
              type="number"
              step="0.01"
              v-model="form.current_value"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.current_value" class="text-red-500 text-sm mt-1">
              {{ form.errors.current_value }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Warranty Expiry</label>
          <div class="mt-2">
            <input
              type="date"
              v-model="form.warranty_expiry"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.warranty_expiry" class="text-red-500 text-sm mt-1">
              {{ form.errors.warranty_expiry }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Supplier</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.supplier"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.supplier" class="text-red-500 text-sm mt-1">
              {{ form.errors.supplier }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Expiry Date</label>
          <div class="mt-2">
            <input
              type="date"
              v-model="form.expiry_date"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.expiry_date" class="text-red-500 text-sm mt-1">
              {{ form.errors.expiry_date }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Last Maintenance</label>
          <div class="mt-2">
            <input
              type="date"
              v-model="form.last_maintenance"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.last_maintenance" class="text-red-500 text-sm mt-1">
              {{ form.errors.last_maintenance }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Next Maintenance</label>
          <div class="mt-2">
            <input
              type="date"
              v-model="form.next_maintenance"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.next_maintenance" class="text-red-500 text-sm mt-1">
              {{ form.errors.next_maintenance }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Maintenance Notes</label>
          <div class="mt-2">
            <textarea
              v-model="form.maintenance_notes"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            ></textarea>
            <div v-if="form.errors.maintenance_notes" class="text-red-500 text-sm mt-1">
              {{ form.errors.maintenance_notes }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Is Assignable</label>
          <div class="mt-2">
            <input
              type="checkbox"
              v-model="form.is_assignable"
              class="h-4 w-4 rounded border-border text-primary focus:ring-primary"
            />
            <div v-if="form.errors.is_assignable" class="text-red-500 text-sm mt-1">
              {{ form.errors.is_assignable }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Minimum Stock Level</label>
          <div class="mt-2">
            <input
              type="number"
              v-model="form.minimum_stock_level"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-border placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary sm:text-sm shadow-sm"
            />
            <div v-if="form.errors.minimum_stock_level" class="text-red-500 text-sm mt-1">
              {{ form.errors.minimum_stock_level }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Photo</label>
          <div class="mt-2">
            <input
              type="file"
              @change="handlePhotoChange"
              class="block w-full text-sm text-gray-900 dark:text-white
                file:mr-4 file:py-2 file:px-4
                file:rounded-md file:border-0
                file:text-sm file:font-semibold
                file:bg-primary/10 file:text-primary
                hover:file:bg-primary/20"
            />
            <div v-if="form.errors.photo" class="text-red-500 text-sm mt-1">
              {{ form.errors.photo }}
            </div>
            <img v-if="form.photo" :src="`/images/${form.photo}`" width="100" class="mt-2" />
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
