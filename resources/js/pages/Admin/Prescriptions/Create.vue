<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Plus, Trash2, Save, ArrowLeft } from 'lucide-vue-next'

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Prescriptions', href: route('admin.prescriptions.index') },
  { title: 'Create', href: null },
]

const props = defineProps<{ patients: Array<any>; staff: Array<any> }>()

const form = useForm({
  patient_id: '',
  created_by_staff_id: '',
  prescribed_date: '',
  status: 'draft',
  instructions: '',
  items: [
    { medication_name: '', dosage: '', frequency: '', duration: '', notes: '' },
  ],
})

function addItem() {
  form.items.push({ medication_name: '', dosage: '', frequency: '', duration: '', notes: '' })
}

function removeItem(index: number) {
  form.items.splice(index, 1)
}

function submit() {
  form.post(route('admin.prescriptions.store'))
}
</script>

<template>
  <Head title="Create Prescription" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="liquidGlass-wrapper">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content p-4">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Create Prescription</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Enter details and items</p>
            </div>
            <div class="flex items-center gap-2">
              <Link :href="route('admin.prescriptions.index')" class="btn-glass btn-glass-sm">
                <ArrowLeft class="icon" />
                <span class="hidden sm:inline">Back</span>
              </Link>
              <button @click="submit" :disabled="form.processing" class="btn-glass">
                <Save class="icon" />
                <span class="hidden sm:inline">Save</span>
              </button>
            </div>
          </div>

          <form @submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="form-label">Patient</label>
                <select v-model.number="form.patient_id" class="form-input">
                  <option :value="''">Select patient</option>
                  <option v-for="p in props.patients" :key="p.id" :value="p.id">{{ p.full_name }} ({{ p.patient_code }})</option>
                </select>
                <div v-if="form.errors.patient_id" class="form-error">{{ form.errors.patient_id }}</div>
              </div>
              <div>
                <label class="form-label">Prescribing Doctor</label>
                <select v-model.number="form.created_by_staff_id" class="form-input">
                  <option :value="''">Select doctor</option>
                  <option v-for="s in props.staff" :key="s.id" :value="s.id">{{ s.first_name }} {{ s.last_name }}</option>
                </select>
                <div v-if="form.errors.created_by_staff_id" class="form-error">{{ form.errors.created_by_staff_id }}</div>
              </div>
              <div>
                <label class="form-label">Prescribed Date</label>
                <input v-model="form.prescribed_date" type="date" class="form-input" />
                <div v-if="form.errors.prescribed_date" class="form-error">{{ form.errors.prescribed_date }}</div>
              </div>
              <div>
                <label class="form-label">Status</label>
                <select v-model="form.status" class="form-input">
                  <option value="draft">Draft</option>
                  <option value="final">Final</option>
                  <option value="dispensed">Dispensed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
                <div v-if="form.errors.status" class="form-error">{{ form.errors.status }}</div>
              </div>
              <div class="md:col-span-2">
                <label class="form-label">Instructions</label>
                <textarea v-model="form.instructions" rows="3" class="form-input" placeholder="General instructions"></textarea>
                <div v-if="form.errors.instructions" class="form-error">{{ form.errors.instructions }}</div>
              </div>
            </div>

            <div>
              <div class="flex items-center justify-between mb-2">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Items</h2>
                <button type="button" @click="addItem" class="btn-glass btn-glass-sm">
                  <Plus class="icon" />
                  Add Item
                </button>
              </div>
              <div class="space-y-3">
                <div v-for="(item, idx) in form.items" :key="idx" class="rounded-md border border-gray-200 dark:border-gray-700 p-3 bg-white/60 dark:bg-gray-900/60">
                  <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <input v-model="item.medication_name" class="form-input" placeholder="Medication" />
                    <input v-model="item.dosage" class="form-input" placeholder="Dosage" />
                    <input v-model="item.frequency" class="form-input" placeholder="Frequency" />
                    <input v-model="item.duration" class="form-input" placeholder="Duration" />
                    <input v-model="item.notes" class="form-input" placeholder="Notes" />
                  </div>
                  <div class="mt-2 flex justify-end">
                    <button type="button" @click="removeItem(idx)" class="inline-flex items-center px-2 py-1 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-800 rounded-md">
                      <Trash2 class="w-4 h-4 mr-1" /> Remove
                    </button>
                  </div>
                </div>
                <div v-if="form.errors.items" class="form-error">{{ form.errors.items }}</div>
              </div>
            </div>

            <div class="flex justify-end">
              <button type="submit" :disabled="form.processing" class="btn-glass">
                <Save class="icon" />
                Save Prescription
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
