<script setup lang="ts">
import { ref } from 'vue';
import InputError from '@/components/InputError.vue'

const props = defineProps<{
  form: any,
  partners: Array<{ id: number; name: string }>,
  partnerAgreements: Array<{ id: number; title: string }>,
  patients: Array<{ id: number; name: string }>,
}>()

const referralStatuses = [
  'Pending',
  'Converted',
  'Rejected',
];
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Partner -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Partner</label>
      <select v-model="form.partner_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <option :value="null">Select Partner</option>
        <option v-for="partner in partners" :key="partner.id" :value="partner.id">{{ partner.name }}</option>
      </select>
      <InputError class="mt-1" :message="form.errors.partner_id" />
    </div>

    <!-- Partner Agreement -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Partner Agreement</label>
      <select v-model="form.agreement_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <option :value="null">Select Agreement</option>
        <option v-for="agreement in partnerAgreements" :key="agreement.id" :value="agreement.id">{{ agreement.title }}</option>
      </select>
      <InputError class="mt-1" :message="form.errors.agreement_id" />
    </div>

    <!-- Referred Patient -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Referred Patient</label>
      <select v-model="form.referred_patient_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <option :value="null">Select Patient</option>
        <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient.name }}</option>
      </select>
      <InputError class="mt-1" :message="form.errors.referred_patient_id" />
    </div>

    <!-- Referral Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Referral Date</label>
      <input
        v-model="form.referral_date"
        type="date"
        required
        class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
      />
      <InputError class="mt-1" :message="form.errors.referral_date" />
    </div>

    <!-- Status -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Status</label>
      <select v-model="form.status" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <option value="">Select Status</option>
        <option v-for="statusOption in referralStatuses" :key="statusOption" :value="statusOption">{{ statusOption }}</option>
      </select>
      <InputError class="mt-1" :message="form.errors.status" />
    </div>

    <!-- Notes -->
    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700">Notes</label>
      <textarea
        v-model="form.notes"
        placeholder="Any additional notes about the referral"
        rows="5"
        class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
      ></textarea>
      <InputError class="mt-1" :message="form.errors.notes" />
    </div>
  </div>
</template>
