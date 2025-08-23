<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import { watch } from 'vue';
import { format } from 'date-fns';
// delete flow removed to match Patient module pattern

const props = defineProps<{
  insuranceClaim: any;
  patients: Array<any>;
  invoices: Array<any>;
  insuranceCompanies: Array<any>;
  insurancePolicies: Array<any>;
}>();

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Insurance Claims', href: route('admin.insurance-claims.index') },
  { title: 'Edit', href: route('admin.insurance-claims.edit', props.insuranceClaim.id) },
]

const form = useForm({
    patient_id: null,
    invoice_id: null,
    insurance_company_id: null,
    policy_id: null,
    claim_status: '',
    coverage_amount: null,
    paid_amount: null,
    submitted_at: '',
    processed_at: '',
    payment_due_date: '',
    payment_received_at: '',
    payment_method: '',
    reimbursement_required: false,
    receipt_number: '',
    is_pre_authorized: false,
    pre_authorization_code: '',
    denial_reason: '',
    translated_notes: '',
    email_status: '',
    email_sent_at: '',
});

watch(() => props.insuranceClaim, (newVal) => {
    if (newVal) {
        form.patient_id = newVal.patient_id;
        form.invoice_id = newVal.invoice_id;
        form.insurance_company_id = newVal.insurance_company_id;
        form.policy_id = newVal.policy_id;
        form.claim_status = newVal.claim_status;
        form.coverage_amount = newVal.coverage_amount;
        form.paid_amount = newVal.paid_amount;
        form.submitted_at = newVal.submitted_at ? format(new Date(newVal.submitted_at), 'yyyy-MM-dd') : '';
        form.processed_at = newVal.processed_at ? format(new Date(newVal.processed_at), 'yyyy-MM-dd') : '';
        form.payment_due_date = newVal.payment_due_date ? format(new Date(newVal.payment_due_date), 'yyyy-MM-dd') : '';
        form.payment_received_at = newVal.payment_received_at ? format(new Date(newVal.payment_received_at), 'yyyy-MM-dd') : '';
        form.payment_method = newVal.payment_method;
        form.reimbursement_required = newVal.reimbursement_required;
        form.receipt_number = newVal.receipt_number;
        form.is_pre_authorized = newVal.is_pre_authorized;
        form.pre_authorization_code = newVal.pre_authorization_code;
        form.denial_reason = newVal.denial_reason;
        form.translated_notes = newVal.translated_notes;
        form.email_status = newVal.email_status;
        form.email_sent_at = newVal.email_sent_at ?? '';
    }
}, { immediate: true });

const submit = () => {
    form.put(route('admin.insurance-claims.update', props.insuranceClaim.id));
};

// no inline delete per Patient module standard
</script>

<template>
  <Head title="Edit Insurance Claim" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Insurance Claim</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update details and save.</p>
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
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>
