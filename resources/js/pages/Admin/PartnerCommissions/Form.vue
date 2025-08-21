<script setup lang="ts">
import { ref } from 'vue';

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
      <select v-model="form.agreement_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option :value="null">Select Agreement</option>
        <option v-for="agreement in partnerAgreements" :key="agreement.id" :value="agreement.id">{{ agreement.title }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.agreement_id">{{ form.errors.agreement_id }}</span>
    </div>

    <!-- Referral -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Referral</label>
      <select v-model="form.referral_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option :value="null">Select Referral</option>
        <option v-for="referral in referrals" :key="referral.id" :value="referral.id">{{ referral.referral_date }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.referral_id">{{ form.errors.referral_id }}</span>
    </div>

    <!-- Invoice -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Invoice</label>
      <select v-model="form.invoice_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option :value="null">Select Invoice</option>
        <option v-for="invoice in invoices" :key="invoice.id" :value="invoice.id">{{ invoice.invoice_number }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.invoice_id">{{ form.errors.invoice_id }}</span>
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
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.commission_amount">{{ form.errors.commission_amount }}</span>
    </div>

    <!-- Calculation Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Calculation Date</label>
      <input
        v-model="form.calculation_date"
        type="date"
        required
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.calculation_date">{{ form.errors.calculation_date }}</span>
    </div>

    <!-- Payout Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Payout Date</label>
      <input
        v-model="form.payout_date"
        type="date"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.payout_date">{{ form.errors.payout_date }}</span>
    </div>

    <!-- Status -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Status</label>
      <select v-model="form.status" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option value="">Select Status</option>
        <option v-for="statusOption in commissionStatuses" :key="statusOption" :value="statusOption">{{ statusOption }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.status">{{ form.errors.status }}</span>
    </div>
  </div>
</template>
