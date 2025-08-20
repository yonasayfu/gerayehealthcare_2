<script setup>
import { useForm } from '@inertiajs/vue3';
import { defineProps, watch } from 'vue';

const props = defineProps({
    form: Object,
    patients: Array,
    invoices: Array,
    insuranceCompanies: Array,
    insurancePolicies: Array,
});

const claimStatuses = [
    'Pending',
    'Approved',
    'Denied',
    'Processing',
    'Paid',
    'Reimbursed',
];

const paymentMethods = [
    'Bank Transfer',
    'Credit Card',
    'Cash',
    'Cheque',
    'Other',
];
</script>

<template>
    <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
        <div class="sm:col-span-3">
            <label for="patient_id" class="block text-sm font-medium text-gray-700">Patient</label>
            <select id="patient_id" v-model="form.patient_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                <option value="">Select a Patient</option>
                <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient.full_name }}</option>
            </select>
            <div v-if="form.errors.patient_id" class="text-red-500 text-sm mt-1">{{ form.errors.patient_id }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="invoice_id" class="block text-sm font-medium text-gray-700">Invoice</label>
            <select id="invoice_id" v-model="form.invoice_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">Select an Invoice (Optional)</option>
                <option v-for="invoice in invoices" :key="invoice.id" :value="invoice.id">{{ invoice.invoice_number }} ({{ invoice.grand_total }})</option>
            </select>
            <div v-if="form.errors.invoice_id" class="text-red-500 text-sm mt-1">{{ form.errors.invoice_id }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="insurance_company_id" class="block text-sm font-medium text-gray-700">Insurance Company</label>
            <select id="insurance_company_id" v-model="form.insurance_company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">Select an Insurance Company (Optional)</option>
                <option v-for="company in insuranceCompanies" :key="company.id" :value="company.id">{{ company.name }}</option>
            </select>
            <div v-if="form.errors.insurance_company_id" class="text-red-500 text-sm mt-1">{{ form.errors.insurance_company_id }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="policy_id" class="block text-sm font-medium text-gray-700">Insurance Policy</label>
            <select id="policy_id" v-model="form.policy_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">Select an Insurance Policy (Optional)</option>
                <option v-for="policy in insurancePolicies" :key="policy.id" :value="policy.id">{{ policy.service_type }} ({{ policy.coverage_percentage }}%)</option>
            </select>
            <div v-if="form.errors.policy_id" class="text-red-500 text-sm mt-1">{{ form.errors.policy_id }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="claim_status" class="block text-sm font-medium text-gray-700">Claim Status</label>
            <select id="claim_status" v-model="form.claim_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                <option value="">Select Status</option>
                <option v-for="status in claimStatuses" :key="status" :value="status">{{ status }}</option>
            </select>
            <div v-if="form.errors.claim_status" class="text-red-500 text-sm mt-1">{{ form.errors.claim_status }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="coverage_amount" class="block text-sm font-medium text-gray-700">Coverage Amount</label>
            <input type="number" id="coverage_amount" v-model="form.coverage_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" step="0.01">
            <div v-if="form.errors.coverage_amount" class="text-red-500 text-sm mt-1">{{ form.errors.coverage_amount }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="paid_amount" class="block text-sm font-medium text-gray-700">Paid Amount</label>
            <input type="number" id="paid_amount" v-model="form.paid_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" step="0.01">
            <div v-if="form.errors.paid_amount" class="text-red-500 text-sm mt-1">{{ form.errors.paid_amount }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="submitted_at" class="block text-sm font-medium text-gray-700">Submitted At</label>
            <input type="date" id="submitted_at" v-model="form.submitted_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <div v-if="form.errors.submitted_at" class="text-red-500 text-sm mt-1">{{ form.errors.submitted_at }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="processed_at" class="block text-sm font-medium text-gray-700">Processed At</label>
            <input type="date" id="processed_at" v-model="form.processed_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <div v-if="form.errors.processed_at" class="text-red-500 text-sm mt-1">{{ form.errors.processed_at }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="payment_due_date" class="block text-sm font-medium text-gray-700">Payment Due Date</label>
            <input type="date" id="payment_due_date" v-model="form.payment_due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <div v-if="form.errors.payment_due_date" class="text-red-500 text-sm mt-1">{{ form.errors.payment_due_date }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="payment_received_at" class="block text-sm font-medium text-gray-700">Payment Received At</label>
            <input type="date" id="payment_received_at" v-model="form.payment_received_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <div v-if="form.errors.payment_received_at" class="text-red-500 text-sm mt-1">{{ form.errors.payment_received_at }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
            <select id="payment_method" v-model="form.payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">Select Payment Method</option>
                <option v-for="method in paymentMethods" :key="method" :value="method">{{ method }}</option>
            </select>
            <div v-if="form.errors.payment_method" class="text-red-500 text-sm mt-1">{{ form.errors.payment_method }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="reimbursement_required" class="flex items-center">
                <input type="checkbox" id="reimbursement_required" v-model="form.reimbursement_required" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-600">Reimbursement Required</span>
            </label>
            <div v-if="form.errors.reimbursement_required" class="text-red-500 text-sm mt-1">{{ form.errors.reimbursement_required }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="receipt_number" class="block text-sm font-medium text-gray-700">Receipt Number</label>
            <input type="text" id="receipt_number" v-model="form.receipt_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <div v-if="form.errors.receipt_number" class="text-red-500 text-sm mt-1">{{ form.errors.receipt_number }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="is_pre_authorized" class="flex items-center">
                <input type="checkbox" id="is_pre_authorized" v-model="form.is_pre_authorized" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-600">Is Pre-Authorized</span>
            </label>
            <div v-if="form.errors.is_pre_authorized" class="text-red-500 text-sm mt-1">{{ form.errors.is_pre_authorized }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="pre_authorization_code" class="block text-sm font-medium text-gray-700">Pre-Authorization Code</label>
            <input type="text" id="pre_authorization_code" v-model="form.pre_authorization_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <div v-if="form.errors.pre_authorization_code" class="text-red-500 text-sm mt-1">{{ form.errors.pre_authorization_code }}</div>
        </div>

        <div class="col-span-full">
            <label for="denial_reason" class="block text-sm font-medium text-gray-700">Denial Reason</label>
            <textarea id="denial_reason" v-model="form.denial_reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
            <div v-if="form.errors.denial_reason" class="text-red-500 text-sm mt-1">{{ form.errors.denial_reason }}</div>
        </div>

        <div class="col-span-full">
            <label for="translated_notes" class="block text-sm font-medium text-gray-700">Translated Notes</label>
            <textarea id="translated_notes" v-model="form.translated_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
            <div v-if="form.errors.translated_notes" class="text-red-500 text-sm mt-1">{{ form.errors.translated_notes }}</div>
        </div>
    </div>
</template>
