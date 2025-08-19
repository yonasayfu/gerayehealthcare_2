<script setup lang="ts">
import type { InertiaForm, InsuranceClaim } from '@/types';
import { computed, watch, ref, onMounted } from 'vue';
import { format } from 'date-fns';

interface LocalErrors {
  // Add other potential local errors if they exist
}

type PatientOption = { id: number; full_name: string; email?: string };
type InvoiceOption = { id: number; invoice_number: string; grand_total: number; patient?: PatientOption };
type InsuranceCompanyOption = { id: number; name: string };
type InsurancePolicyOption = { id: number; service_type: string; coverage_percentage?: number };

const props = defineProps<{
  form: InertiaForm<InsuranceClaim>,
  localErrors?: LocalErrors,
  patients: PatientOption[],
  invoices: InvoiceOption[],
  insuranceCompanies: InsuranceCompanyOption[],
  insurancePolicies: InsurancePolicyOption[],
}>();

const emit = defineEmits(['submit']);

const claimStatuses = ['Submitted', 'Processing', 'Approved', 'Denied', 'Paid'];
const paymentMethods = ['Bank Transfer', 'Cash', 'Credit Card', 'Mobile Money'];

const normalizeDate = (value: string | null | undefined) => {
  if (!value) return null;
  const s = String(value);
  return s.length >= 10 ? s.substring(0, 10) : s;
};

// Watch for patient_id change to pre-fill recipient_email
watch(() => props.form.patient_id, (newPatientId) => {
  const selectedPatient = props.patients.find(p => p.id === newPatientId);
  if (selectedPatient && selectedPatient.email) {
    props.form.recipient_email = selectedPatient.email;
  } else {
    props.form.recipient_email = '';
  }
});

// Watch for invoice_id change to pre-fill patient_id
watch(() => props.form.invoice_id, (newInvoiceId) => {
  const selectedInvoice = props.invoices.find(i => i.id === newInvoiceId);
  if (selectedInvoice && selectedInvoice.patient) {
    props.form.patient_id = selectedInvoice.patient.id;
  } else {
    props.form.patient_id = null;
  }
});

</script>

<template>
  <form @submit.prevent="emit('submit')">
    <div class="border-b border-gray-900/10 pb-12">
      <h2 class="text-base font-semibold text-gray-900 dark:text-white">Claim Information</h2>
      <p class="mt-1 text-sm text-muted-foreground">
        Provide details for the insurance claim.
      </p>

      <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Patient</label>
          <div class="mt-2">
            <select
              v-model="form.patient_id"
              class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
            >
              <option :value="null">Select Patient</option>
              <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient.full_name }}</option>
            </select>
            <div v-if="form.errors.patient_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.patient_id }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Invoice</label>
          <div class="mt-2">
            <select
              v-model="form.invoice_id"
              class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
            >
              <option :value="null">Select Invoice</option>
              <option v-for="invoice in invoices" :key="invoice.id" :value="invoice.id">{{ invoice.invoice_number }} ({{ invoice.grand_total }})</option>
            </select>
            <div v-if="form.errors.invoice_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.invoice_id }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Insurance Company</label>
          <div class="mt-2">
            <select
              v-model="form.insurance_company_id"
              class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
            >
              <option :value="null">Select Company</option>
              <option v-for="company in insuranceCompanies" :key="company.id" :value="company.id">{{ company.name }}</option>
            </select>
            <div v-if="form.errors.insurance_company_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.insurance_company_id }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Insurance Policy</label>
          <div class="mt-2">
            <select
              v-model="form.policy_id"
              class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
            >
              <option :value="null">Select Policy</option>
              <option v-for="policy in insurancePolicies" :key="policy.id" :value="policy.id">{{ policy.service_type }} ({{ policy.coverage_percentage }}%)</option>
            </select>
            <div v-if="form.errors.policy_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.policy_id }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Claim Status</label>
          <div class="mt-2">
            <select
              v-model="form.claim_status"
              class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
            >
              <option :value="null">Select Status</option>
              <option v-for="status in claimStatuses" :key="status" :value="status">{{ status }}</option>
            </select>
            <div v-if="form.errors.claim_status" class="text-red-500 text-sm mt-1">
              {{ form.errors.claim_status }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Coverage Amount</label>
          <div class="mt-2">
            <input
              type="number"
              v-model="form.coverage_amount"
              step="0.01"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.coverage_amount" class="text-red-500 text-sm mt-1">
              {{ form.errors.coverage_amount }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Paid Amount</label>
          <div class="mt-2">
            <input
              type="number"
              v-model="form.paid_amount"
              step="0.01"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.paid_amount" class="text-red-500 text-sm mt-1">
              {{ form.errors.paid_amount }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Submitted At</label>
          <div class="mt-2">
            <input
              type="date"
              v-model="form.submitted_at"
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
            />
            <div v-if="form.errors.submitted_at" class="text-red-500 text-sm mt-1">
              {{ form.errors.submitted_at }}
            </div>
          </div>
        </div>

        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Processed At</label>
          <div class="mt-2">
            <input
