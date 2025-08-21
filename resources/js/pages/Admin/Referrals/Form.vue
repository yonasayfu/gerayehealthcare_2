<script setup lang="ts">
import { ref } from 'vue';

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
      <select v-model.number="form.partner_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option :value="null">Select Partner</option>
        <option v-for="partner in partners" :key="partner.id" :value="partner.id">{{ partner.name }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.partner_id">{{ form.errors.partner_id }}</span>
    </div>

    <!-- Partner Agreement -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Partner Agreement</label>
      <select v-model.number="form.agreement_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option :value="null">Select Agreement</option>
        <option v-for="agreement in partnerAgreements" :key="agreement.id" :value="agreement.id">{{ agreement.title }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.agreement_id">{{ form.errors.agreement_id }}</span>
    </div>

    <!-- Referred Patient -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Referred Patient</label>
      <select v-model.number="form.referred_patient_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option :value="null">Select Patient</option>
        <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient.name }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.referred_patient_id">{{ form.errors.referred_patient_id }}</span>
    </div>

    <!-- Referral Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Referral Date</label>
      <input
        v-model="form.referral_date"
        type="date"
        required
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.referral_date">{{ form.errors.referral_date }}</span>
    </div>

    <!-- Status -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Status</label>
      <select v-model="form.status" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option value="">Select Status</option>
        <option v-for="statusOption in referralStatuses" :key="statusOption" :value="statusOption">{{ statusOption }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.status">{{ form.errors.status }}</span>
    </div>

    <!-- Notes -->
    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700">Notes</label>
      <textarea
        v-model="form.notes"
        placeholder="Any additional notes about the referral"
        rows="5"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      ></textarea>
      <span class="text-red-500 text-xs" v-if="form.errors.notes">{{ form.errors.notes }}</span>
    </div>
  </div>
</template>