<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItem, InertiaForm, InsuranceClaim } from '@/types'
import { watch } from 'vue';
import { format } from 'date-fns';

const props = defineProps<{
  insuranceClaim: InsuranceClaim;
  patients: Array<any>;
  invoices: Array<any>;
  insuranceCompanies: Array<any>;
  insurancePolicies: Array<any>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Insurance Claims', href: route('admin.insurance-claims.index') },
  { title: 'Edit', href: route('admin.insurance-claims.edit', props.insuranceClaim.id) },
]

const form = useForm<InsuranceClaim>({
    patient_id: null,
    invoice_id: null,
    insurance_company_id: null,
    policy_id: null,
    claim_status: null,
    coverage_amount: null,
    paid_amount: null,
    submitted_at: null,
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

watch(() => props.insuranceClaim, (newVal) => {
    if (newVal) {
        form.patient_id = newVal.patient_id;
        form.invoice_id = newVal.invoice_id;
        form.insurance_company_id = newVal.insurance_company_id;
        form.policy_id = newVal.policy_id;
        form.claim_status = newVal.claim_status;
        form.coverage_amount = newVal.coverage_amount;
        form.paid_amount = newVal.paid_amount;
        form.submitted_at = newVal.submitted_at ? format(new Date(newVal.submitted_at), 'yyyy-MM-dd') : null;
        form.processed_at = newVal.processed_at ? format(new Date(newVal.processed_at), 'yyyy-MM-dd') : null;
        form.payment_due_date = newVal.payment_due_date ? format(new Date(newVal.payment_due_date), 'yyyy-MM-dd') : null;
        form.payment_received_at = newVal.payment_received_at ? format(new Date(newVal.payment_received_at), 'yyyy-MM-dd') : null;
        form.payment_method = newVal.payment_method;
        form.reimbursement_required = newVal.reimbursement_required;
        form.receipt_number = newVal.receipt_number;
        form.is_pre_authorized = newVal.is_pre_authorized;
        form.pre_authorization_code = newVal.pre_authorization_code;
        form.denial_reason = newVal.denial_reason;
        form.translated_notes = newVal.translated_notes;
        form.email_status = newVal.email_status;
        form.email_sent_at = newVal.email_sent_at;
    }
}, { immediate: true });

const submit = () => {
    form.put(route('admin.insurance-claims.update', props.insuranceClaim.id));
};
</script>

<template>
  <Head title="Edit Insurance Claim" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Insurance Claim
            </h3>
            <Link :href="route('admin.insurance-claims.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

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
            <div class="flex flex-wrap gap-2">
              <Link :href="route('admin.insurance-claims.index')" class="btn btn-outline">Cancel</Link>
              <button @click="submit" :disabled="form.processing" class="btn btn-primary" type="submit">
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
              </button>
              <button
                class="btn btn-danger ml-auto"
                @click="() => { if (confirm('Are you sure you want to delete this insurance claim? This action cannot be undone.')) { router.delete(route('admin.insurance-claims.destroy', props.insuranceClaim.id)) } }"
              >
                Delete
              </button>
            </div>
        </div>

    </div>
  </AppLayout>
