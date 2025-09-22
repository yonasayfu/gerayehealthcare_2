<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Plus, Trash2 } from 'lucide-vue-next'

const props = defineProps<{
  form: ReturnType<typeof useForm>,
  patients: Array<any>,
  staff: Array<any>
}>()

function addItem() {
  props.form.items.push({ medication_name: '', dosage: '', frequency: '', duration: '', notes: '' })
}

function removeItem(index: number) {
  props.form.items.splice(index, 1)
}
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">Patient & Prescription Details</h2>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Patient</label>
      <select v-model.number="form.patient_id" class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <option :value="''">Select patient</option>
        <option v-for="p in props.patients" :key="p.id" :value="p.id">{{ p.full_name }} ({{ p.patient_code }})</option>
      </select>
      <div v-if="form.errors.patient_id" class="text-red-500 text-sm mt-1">{{ form.errors.patient_id }}</div>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prescribing Doctor</label>
      <select v-model.number="form.created_by_staff_id" class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <option :value="''">Select doctor</option>
        <option v-for="s in props.staff" :key="s.id" :value="s.id">{{ s.first_name }} {{ s.last_name }}</option>
      </select>
      <div v-if="form.errors.created_by_staff_id" class="text-red-500 text-sm mt-1">{{ form.errors.created_by_staff_id }}</div>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prescribed Date</label>
      <input v-model="form.prescribed_date" type="date" class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
      <div v-if="form.errors.prescribed_date" class="text-red-500 text-sm mt-1">{{ form.errors.prescribed_date }}</div>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
      <select v-model="form.status" class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <option value="draft">Draft</option>
        <option value="final">Final</option>
        <option value="dispensed">Dispensed</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">General Instructions</label>
      <textarea v-model="form.instructions" rows="3" class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., Take all medications as prescribed. Do not stop taking any medication without consulting your doctor."></textarea>
      <div v-if="form.errors.instructions" class="text-red-500 text-sm mt-1">{{ form.errors.instructions }}</div>
    </div>
  </div>

  <div class="mt-8">
    <div class="flex items-center justify-between mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Medication Items</h2>
      <button type="button" @click="addItem" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
        <Plus class="w-4 h-4 mr-1" />
        Add Medication
      </button>
    </div>
    
    <!-- Table Format for Medication Items -->
    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden shadow-sm">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Medication</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dosage</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Frequency</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Duration</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Notes</th>
            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="(item, idx) in form.items" :key="idx" class="hover:bg-gray-50 dark:hover:bg-gray-750">
            <td class="px-4 py-3 whitespace-nowrap">
              <input 
                v-model="item.medication_name" 
                type="text" 
                class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                placeholder="e.g., Paracetamol 500mg"
              />
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              <input 
                v-model="item.dosage" 
                type="text" 
                class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                placeholder="e.g., 1 tablet"
              />
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              <input 
                v-model="item.frequency" 
                type="text" 
                class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                placeholder="e.g., Twice a day"
              />
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              <input 
                v-model="item.duration" 
                type="text" 
                class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                placeholder="e.g., 7 days"
              />
            </td>
            <td class="px-4 py-3">
              <textarea 
                v-model="item.notes" 
                rows="1" 
                class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                placeholder="e.g., After meals"
              ></textarea>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-right">
              <button 
                type="button" 
                @click="removeItem(idx)" 
                class="inline-flex items-center text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium"
              >
                <Trash2 class="w-4 h-4 mr-1" /> Remove
              </button>
            </td>
          </tr>
          <tr v-if="form.items.length === 0">
            <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
              No medication items added yet. Click "Add Medication" to add items.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <div v-if="form.errors.items" class="text-red-500 text-sm mt-2">{{ form.errors.items }}</div>
  </div>
</template>