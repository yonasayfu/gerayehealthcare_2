<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
// types removed to avoid TS errors; using inference

const props = defineProps<{
  patients: Array<any>;
  invoices: Array<any>;
  insuranceCompanies: Array<any>;
  insurancePolicies: Array<any>;
}>();

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Insurance Claims', href: route('admin.insurance-claims.index') },
  { title: 'Create', href: route('admin.insurance-claims.create') },
]

const form = useForm({
    patient_id: null,
    invoice_id: null,
    insurance_company_id: null,
    policy_id: null,
    claim_status: 'Submitted', // Default status
    coverage_amount: null,
    paid_amount: null,
    submitted_at: new Date().toISOString().slice(0,10), // Default to today
    processed_at: null,
    payment_due_date: null,
    payment_received_at: null,
    payment_method: null,
    reimbursement_required: false,
    receipt_number: null,
    is_pre_authorized: false,
    pre_authorization_code: null,
    denial_reason: null,
    translated_notes: null,
    email_status: null,
    email_sent_at: null,
});

const submit = () => {
    form.post(route('admin.insurance-claims.store'));
};
</script>

<template>
  <Head title="Create Insurance Claim" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create Insurance Claim</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill the claim details and save.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-900 overflow-hidden shadow rounded-lg">
        <div class="p-6 space-y-6">
            <Form
                :form="form"
                :patients="patients"
                :invoices="invoices"
                :insuranceCompanies="insuranceCompanies"
                :insurancePolicies="insurancePolicies"
            />
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
          <div class="flex justify-end gap-2 w-full items-center">
            <Link :href="route('admin.insurance-claims.index')" class="btn-glass btn-glass-sm">Cancel</Link>
            <button @click="submit" :disabled="form.processing" class="btn-glass btn-glass-sm" type="submit">
              {{ form.processing ? 'Creating...' : 'Create Claim' }}
            </button>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>
