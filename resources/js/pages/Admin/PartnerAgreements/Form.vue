<script setup lang="ts">
import { ref } from 'vue';

const props = defineProps<{
  form: any,
  partners: Array<{ id: number; name: string }>,
  staff: Array<{ id: number; name: string }>,
}>()

const agreementTypes = [
  'Referral Commission',
  'Priority Service',
  'Co-Marketing',
];

const agreementStatuses = [
  'Draft',
  'Active',
  'Expired',
  'Terminated',
];

const priorityServiceLevels = [
  'Standard',
  'Preferred',
  'Premium',
];

const commissionTypes = [
  'Percentage',
  'FixedAmountPerPatient',
];
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Partner -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Partner</label>
      <select v-model="form.partner_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option :value="null">Select Partner</option>
        <option v-for="partner in partners" :key="partner.id" :value="partner.id">{{ partner.name }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.partner_id">{{ form.errors.partner_id }}</span>
    </div>

    <!-- Agreement Title -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Agreement Title</label>
      <input
        v-model="form.agreement_title"
        type="text"
        placeholder="e.g., Annual Referral Agreement"
        required
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.agreement_title">{{ form.errors.agreement_title }}</span>
    </div>

    <!-- Agreement Type -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Agreement Type</label>
      <select v-model="form.agreement_type" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option value="">Select Type</option>
        <option v-for="typeOption in agreementTypes" :key="typeOption" :value="typeOption">{{ typeOption }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.agreement_type">{{ form.errors.agreement_type }}</span>
    </div>

    <!-- Status -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Status</label>
      <select v-model="form.status" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option value="">Select Status</option>
        <option v-for="statusOption in agreementStatuses" :key="statusOption" :value="statusOption">{{ statusOption }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.status">{{ form.errors.status }}</span>
    </div>

    <!-- Start Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Start Date</label>
      <input
        v-model="form.start_date"
        type="date"
        required
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.start_date">{{ form.errors.start_date }}</span>
    </div>

    <!-- End Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700">End Date</label>
      <input
        v-model="form.end_date"
        type="date"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.end_date">{{ form.errors.end_date }}</span>
    </div>

    <!-- Priority Service Level -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Priority Service Level</label>
      <select v-model="form.priority_service_level" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option :value="null">Select Level</option>
        <option v-for="level in priorityServiceLevels" :key="level" :value="level">{{ level }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.priority_service_level">{{ form.errors.priority_service_level }}</span>
    </div>

    <!-- Commission Type -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Commission Type</label>
      <select v-model="form.commission_type" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option :value="null">Select Type</option>
        <option v-for="typeOption in commissionTypes" :key="typeOption" :value="typeOption">{{ typeOption }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.commission_type">{{ form.errors.commission_type }}</span>
    </div>

    <!-- Commission Rate -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Commission Rate (%)</label>
      <input
        v-model.number="form.commission_rate"
        type="number"
        step="0.01"
        min="0"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.commission_rate">{{ form.errors.commission_rate }}</span>
    </div>

    <!-- Terms Document Path -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Terms Document Path</label>
      <input
        v-model="form.terms_document_path"
        type="text"
        placeholder="e.g., /documents/agreement_v1.pdf"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.terms_document_path">{{ form.errors.terms_document_path }}</span>
    </div>

    <!-- Signed By Staff -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Signed By Staff</label>
      <select v-model="form.signed_by_staff_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option :value="null">Select Staff</option>
        <option v-for="staffMember in staff" :key="staffMember.id" :value="staffMember.id">{{ staffMember.name }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.signed_by_staff_id">{{ form.errors.signed_by_staff_id }}</span>
    </div>
  </div>
</template>
