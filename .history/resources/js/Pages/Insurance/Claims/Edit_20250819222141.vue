<template>
    <AppLayout title="Edit Insurance Claim">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Insurance Claim
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="insuranceClaim" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <Form
                            :form="form"
                            :patients="patients"
                            :invoices="invoices"
                            :insuranceCompanies="insuranceCompanies"
                            :insurancePolicies="insurancePolicies"
                        />

                        <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Update Claim
                        </button>
                    </form>
                </div>
                <div v-else class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <p>Loading Insurance Claim details...</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { defineProps, watch } from 'vue';
import Form from './Form.vue';
import { format } from 'date-fns';

const props = defineProps({
    insuranceClaim: Object,
    patients: Array,
    invoices: Array,
    insuranceCompanies: Array,
    insurancePolicies: Array,
});

const form = useForm({
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
    }
}, { immediate: true });

const submit = () => {
    form.put(route('admin.insurance-claims.update', props.insuranceClaim.id));
};
</script>
