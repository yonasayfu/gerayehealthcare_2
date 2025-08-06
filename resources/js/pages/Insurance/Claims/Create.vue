<template>
    <AppLayout title="Create Insurance Claim">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Insurance Claim
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <Form
                            :form="form"
                            :patients="patients"
                            :invoices="invoices"
                            :insuranceCompanies="insuranceCompanies"
                            :insurancePolicies="insurancePolicies"
                        />

                        <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Create Claim
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import Form from './Form.vue';

const props = defineProps({
    patients: Array,
    invoices: Array,
    insuranceCompanies: Array,
    insurancePolicies: Array,
});

const form = useForm({
    patient_id: '',
    invoice_id: '',
    insurance_company_id: '',
    policy_id: '',
    claim_status: '',
    coverage_amount: '',
    paid_amount: '',
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
});

const submit = () => {
    form.post(route('admin.insurance-claims.store'));
};
</script>
