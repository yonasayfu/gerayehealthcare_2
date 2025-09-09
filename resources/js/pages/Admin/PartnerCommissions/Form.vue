<script setup lang="ts">
import { ref } from 'vue';
import InputError from '@/components/InputError.vue'

const props = defineProps<{
  form: any,
  partnerAgreements: Array<{ id: number; title: string }>,
  referrals: Array<{ id: number; referral_date: string }>,
  invoices: Array<{ id: number; invoice_number: string }>,
}>()

const commissionStatuses = [
  'Due',
  'Paid',
  'Voided',
];
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Agreement -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Agreement</label>
      <select v-model="form.agreement_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <option :value="null">Select Agreement</option>
        <option v-for="agreement in partnerAgreements" :key="agreement.id" :value="agreement.id">{{ agreement.title }}</option>
      </select>
      <InputError class="mt-1" :message="form.errors.agreement_id" />
    </div>

    <!-- Referral -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Referral</label>
      <select v-model="form.referral_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <option :value="null">Select Referral</option>
        <option v-for="referral in referrals" :key="referral.id" :value="referral.id">{{ referral.referral_date }}</option>
      </select>
      <InputError class="mt-1" :message="form.errors.referral_id" />
    </div>

    <!-- Invoice -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Invoice</label>
      <select v-model="form.invoice_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <option :value="null">Select Invoice</option>
        <option v-for="invoice in invoices" :key="invoice.id" :value="invoice.id">{{ invoice.invoice_number }}</option>
      </select>
      <InputError class="mt-1" :message="form.errors.invoice_id" />
    </div>

    <!-- Commission Amount -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Commission Amount</label>
      <input
        v-model.number="form.commission_amount"
        type="number"
        step="0.01"
        min="0"
        required
        class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
      />
      <InputError class="mt-1" :message="form.errors.commission_amount" />
    </div>

    <!-- Calculation Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Calculation Date</label>
      <input
        v-model="form.calculation_date"
        type="date"
        required
        class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
      />
      <InputError class="mt-1" :message="form.errors.calculation_date" />
    </div>

    <!-- Payout Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Payout Date</label>
      <input
        v-model="form.payout_date"
        type="date"
        class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
      />
      <InputError class="mt-1" :message="form.errors.payout_date" />
    </div>

    <!-- Status -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Status</label>
      <select v-model="form.status" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <option value="">Select Status</option>
        <option v-for="statusOption in commissionStatuses" :key="statusOption" :value="statusOption">{{ statusOption }}</option>
      </select>
      <InputError class="mt-1" :message="form.errors.status" />
    </div>
  </div>
</template>
