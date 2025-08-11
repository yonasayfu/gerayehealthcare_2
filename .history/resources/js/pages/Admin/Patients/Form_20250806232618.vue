<script setup lang="ts">
import type { PatientForm, InertiaForm } from '@/types' // Import PatientForm and InertiaForm types
import { computed } from 'vue';

interface LocalErrors {
  source?: string;
  phone_number?: string;
  corporate_client_id?: string;
  policy_id?: string;
  // Add other potential local errors if they exist
}

const props = defineProps<{
  form: InertiaForm<PatientForm>, // Use InertiaForm with PatientForm generic
  localErrors?: LocalErrors, // Use defined interface
  corporateClients: Array<any>, // Add corporateClients prop
  insurancePolicies: Array<any>, // Add insurancePolicies prop
}>()

const emit = defineEmits(['submit'])

// Define options for dropdowns here
const genders = ['Male', 'Female', 'Other', 'Prefer not to say']
const sources = ['TikTok', 'Website', 'Referral', 'Walk-in']

// Computed properties for date handling
const dateOfBirth = computed({
  get: () => props.form.date_of_birth,
  set: (value) => {
    props.form.date_of_birth = value;
    // You might want to add logic here to convert Gregorian to Ethiopian date
    // For now, we'll assume the Ethiopian date is handled separately or by backend
  }
});

const ethiopianDateOfBirth = computed({
  get: () => props.form.ethiopian_date_of_birth,
  set: (value) => {
    props.form.ethiopian_date_of_birth = value;
    // You might want to add logic here to convert Ethiopian to Gregorian date
  }
});

</script>

<template>
  <form @submit.prevent="emit('submit')">
    <div class="border-b border-gray-900/10 pb-12">
      <h2 class="text-base font-semibold text-gray-900 dark:text-white">Patient Information</h2>
      <p class="mt-1 text-sm text-muted-foreground">
        Use accurate and up-to-date details for patient registration.
      </p>

      <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Full Name</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.full_name"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.full_name" class="text-red-500 text-sm mt-1">
              {{ form.errors.full_name }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Fayda ID</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.fayda_id"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.fayda_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.fayda_id }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Source</label>
          <div class="mt-2">
            <select
              v-model="form.source"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            >
              <option :value="null">Select source</option>
              <option v-for="sourceOption in sources" :key="sourceOption" :value="sourceOption">{{ sourceOption }}</option>
            </select>
            <div v-if="form.errors.source" class="text-red-500 text-sm mt-1">
              {{ form.errors.source }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Phone Number</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.phone_number"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="props.localErrors?.phone_number" class="text-red-500 text-sm mt-1">
              {{ props.localErrors.phone_number }}
            </div>
            <div v-else-if="form.errors.phone_number" class="text-red-500 text-sm mt-1">
              {{ form.errors.phone_number }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Email Address</label>
          <div class="mt-2">
            <input
              type="email"
              v-model="form.email"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
              {{ form.errors.email }}
            </div>
          </div>
        </div>
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Gender</label>
          <div class="mt-2">
            <select
              v-model="form.gender"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            >
              <option :value="null">Select gender</option>
              <option v-for="genderOption in genders" :key="genderOption" :value="genderOption">{{ genderOption }}</option>
            </select>
            <div v-if="form.errors.gender" class="text-red-500 text-sm mt-1">
              {{ form.errors.gender }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Date of Birth (Gregorian) <span class="text-red-500">*</span></label>
          <div class="mt-2">
            <input
              type="date"
              v-model="dateOfBirth"
              required
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.date_of_birth" class="text-red-500 text-sm mt-1">
              {{ form.errors.date_of_birth }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Date of Birth (Ethiopian) <span class="text-red-500">*</span></label>
          <div class="mt-2">
            <input
              type="text"
              v-model="ethiopianDateOfBirth"
              placeholder="YYYY-MM-DD"
              required
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.ethiopian_date_of_birth" class="text-red-500 text-sm mt-1">
              {{ form.errors.ethiopian_date_of_birth }}
            </div>
          </div>
        </div>

        <div class="col-span-full">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Address</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.address"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">
              {{ form.errors.address }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Emergency Contact</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.emergency_contact"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.emergency_contact" class="text-red-500 text-sm mt-1">
              {{ form.errors.emergency_contact }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Geolocation</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.geolocation"
              placeholder="e.g., 9.012345,38.765432"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.geolocation" class="text-red-500 text-sm mt-1">
              {{ form.errors.geolocation }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Employer (Corporate Client)</label>
          <div class="mt-2">
            <select
              v-model="form.corporate_client_id"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            >
              <option :value="null" class="text-black">Select Employer</option>
              <option v-for="client in corporateClients" :key="client.id" :value="client.id" class="text-black">{{ client.name }}</option>
            </select>
            <div v-if="form.errors.corporate_client_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.corporate_client_id }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Insurance Policy</label>
          <div class="mt-2">
            <select
              v-model="form.policy_id"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            >
              <option :value="null" class="text-black">Select Policy</option>
              <option v-for="policy in insurancePolicies" :key="policy.id" :value="policy.id" class="text-black">{{ policy.service_type }} ({{ policy.coverage_percentage }}%) - {{ policy.corporate_client?.name }}</option>
            </select>
            <div v-if="form.errors.policy_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.policy_id }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
