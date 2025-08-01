<template>
    <AppLayout title="Edit Insurance Claim">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Insurance Claim
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label for="patient_id" class="block text-sm font-medium text-gray-700">Patient</label>
                            <select id="patient_id" v-model="form.patient_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Select a Patient</option>
                                <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient.full_name }}</option>
                            </select>
                            <div v-if="form.errors.patient_id" class="text-red-500 text-sm mt-1">{{ form.errors.patient_id }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="invoice_id" class="block text-sm font-medium text-gray-700">Invoice</label>
                            <select id="invoice_id" v-model="form.invoice_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Select an Invoice (Optional)</option>
                                <option v-for="invoice in invoices" :key="invoice.id" :value="invoice.id">{{ invoice.invoice_number }} ({{ invoice.grand_total }})</option>
                            </select>
                            <div v-if="form.errors.invoice_id" class="text-red-500 text-sm mt-1">{{ form.errors.invoice_id }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="insurance_company_id" class="block text-sm font-medium text-gray-700">Insurance Company</label>
                            <select id="insurance_company_id" v-model="form.insurance_company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Select an Insurance Company (Optional)</option>
                                <option v-for="company in insuranceCompanies" :key="company.id" :value="company.id">{{ company.name }}</option>
                            </select>
                            <div v-if="form.errors.insurance_company_id" class="text-red-500 text-sm mt-1">{{ form.errors.insurance_company_id }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="policy_id" class="block text-sm font-medium text-gray-700">Insurance Policy</label>
                            <select id="policy_id" v-model="form.policy_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Select an Insurance Policy (Optional)</option>
                                <option v-for="policy in insurancePolicies" :key="policy.id" :value="policy.id">{{ policy.service_type }} ({{ policy.coverage_percentage }}%)</option>
                            </select>
                            <div v-if="form.errors.policy_id" class="text-red-500 text-sm mt-1">{{ form.errors.policy_id }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="claim_status" class="block text-sm font-medium text-gray-700">Claim Status</label>
                            <input type="text" id="claim_status" v-model="form.claim_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <div v-if="form.errors.claim_status" class="text-red-500 text-sm mt-1">{{ form.errors.claim_status }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="coverage_amount" class="block text-sm font-medium text-gray-700">Coverage Amount</label>
                            <input type="number" id="coverage_amount" v-model="form.coverage_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" step="0.01">
                            <div v-if="form.errors.coverage_amount" class="text-red-500 text-sm mt-1">{{ form.errors.coverage_amount }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="paid_amount" class="block text-sm font-medium text-gray-700">Paid Amount</label>
                            <input type="number" id="paid_amount" v-model="form.paid_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" step="0.01">
                            <div v-if="form.errors.paid_amount" class="text-red-500 text-sm mt-1">{{ form.errors.paid_amount }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="submitted_at" class="block text-sm font-medium text-gray-700">Submitted At</label>
                            <input type="date" id="submitted_at" v-model="form.submitted_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.submitted_at" class="text-red-500 text-sm mt-1">{{ form.errors.submitted_at }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="processed_at" class="block text-sm font-medium text-gray-700">Processed At</label>
                            <input type="date" id="processed_at" v-model="form.processed_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.processed_at" class="text-red-500 text-sm mt-1">{{ form.errors.processed_at }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="payment_due_date" class="block text-sm font-medium text-gray-700">Payment Due Date</label>
                            <input type="date" id="payment_due_date" v-model="form.payment_due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.payment_due_date" class="text-red-500 text-sm mt-1">{{ form.errors.payment_due_date }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="payment_received_at" class="block text-sm font-medium text-gray-700">Payment Received At</label>
                            <input type="date" id="payment_received_at" v-model="form.payment_received_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.payment_received_at" class="text-red-500 text-sm mt-1">{{ form.errors.payment_received_at }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <input type="text" id="payment_method" v-model="form.payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.payment_method" class="text-red-500 text-sm mt-1">{{ form.errors.payment_method }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="reimbursement_required" class="flex items-center">
                                <input type="checkbox" id="reimbursement_required" v-model="form.reimbursement_required" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Reimbursement Required</span>
                            </label>
                            <div v-if="form.errors.reimbursement_required" class="text-red-500 text-sm mt-1">{{ form.errors.reimbursement_required }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="receipt_number" class="block text-sm font-medium text-gray-700">Receipt Number</label>
                            <input type="text" id="receipt_number" v-model="form.receipt_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.receipt_number" class="text-red-500 text-sm mt-1">{{ form.errors.receipt_number }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="is_pre_authorized" class="flex items-center">
                                <input type="checkbox" id="is_pre_authorized" v-model="form.is_pre_authorized" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Is Pre-Authorized</span>
                            </label>
                            <div v-if="form.errors.is_pre_authorized" class="text-red-500 text-sm mt-1">{{ form.errors.is_pre_authorized }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="pre_authorization_code" class="block text-sm font-medium text-gray-700">Pre-Authorization Code</label>
                            <input type="text" id="pre_authorization_code" v-model="form.pre_authorization_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.pre_authorization_code" class="text-red-500 text-sm mt-1">{{ form.errors.pre_authorization_code }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="denial_reason" class="block text-sm font-medium text-gray-700">Denial Reason</label>
                            <textarea id="denial_reason" v-model="form.denial_reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                            <div v-if="form.errors.denial_reason" class="text-red-500 text-sm mt-1">{{ form.errors.denial_reason }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="translated_notes" class="block text-sm font-medium text-gray-700">Translated Notes</label>
                            <textarea id="translated_notes" v-model="form.translated_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                            <div v-if="form.errors.translated_notes" class="text-red-500 text-sm mt-1">{{ form.errors.translated_notes }}</div>
                        </div>

                        <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Update Claim
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

const props = defineProps({
    insuranceClaim: Object,
    patients: Array,
    invoices: Array,
    insuranceCompanies: Array,
    insurancePolicies: Array,
});

const form = useForm({
    patient_id: props.insuranceClaim.patient_id,
    invoice_id: props.insuranceClaim.invoice_id,
    insurance_company_id: props.insuranceClaim.insurance_company_id,
    policy_id: props.insuranceClaim.policy_id,
    claim_status: props.insuranceClaim.claim_status,
    coverage_amount: props.insuranceClaim.coverage_amount,
    paid_amount: props.insuranceClaim.paid_amount,
    submitted_at: props.insuranceClaim.submitted_at,
    processed_at: props.insuranceClaim.processed_at,
    payment_due_date: props.insuranceClaim.payment_due_date,
    payment_received_at: props.insuranceClaim.payment_received_at,
    payment_method: props.insuranceClaim.payment_method,
    reimbursement_required: props.insuranceClaim.reimbursement_required,
    receipt_number: props.insuranceClaim.receipt_number,
    is_pre_authorized: props.insuranceClaim.is_pre_authorized,
    pre_authorization_code: props.insuranceClaim.pre_authorization_code,
    denial_reason: props.insuranceClaim.denial_reason,
    translated_notes: props.insuranceClaim.translated_notes,
});

const submit = () => {
    form.put(route('admin.insurance-claims.update', props.insuranceClaim.id));
};
</script>
