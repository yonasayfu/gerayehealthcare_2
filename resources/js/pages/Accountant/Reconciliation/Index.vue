<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    pendingClaims: { type: Array, default: () => [] }, // Ensure it's always an array
});

const form = useForm({
    paid_amount: null,
    payment_received_at: null,
    payment_method: null,
    receipt_number: null,
});

const processPayment = (claimId) => {
    form.post(route('accountant.reconciliation.processClaimPayment', claimId), {
        onSuccess: () => {
            form.reset();
            // Optionally, refresh the page or remove the processed claim from the list
        },
    });
};
</script>

<template>
    <Head title="Payment Reconciliation" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Payment Reconciliation
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pending Insurance Claims</h3>

                    <div v-if="pendingClaims.length === 0" class="text-gray-600">
                        No pending claims to reconcile.
                    </div>

                    <div v-else>
                        <div v-for="claim in pendingClaims" :key="claim.id" class="border-b border-gray-200 pb-4 mb-4 last:border-b-0 last:pb-0 last:mb-0">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p><strong>Claim ID:</strong> {{ claim.id }}</p>
                                    <p><strong>Patient:</strong> {{ claim.patient?.full_name }}</p>
                                    <p><strong>Invoice ID:</strong> {{ claim.invoice?.id }}</p>
                                    <p><strong>Insurance Company:</strong> {{ claim.insurance_company?.name }}</p>
                                    <p><strong>Policy:</strong> {{ claim.policy?.service_type }} ({{ claim.policy?.coverage_percentage }}%)</p>
                                    <p><strong>Claim Amount:</strong> {{ claim.coverage_amount }} ETB</p>
                                    <p><strong>Status:</strong> {{ claim.claim_status }}</p>
                                    <p><strong>Submitted At:</strong> {{ new Date(claim.submitted_at).toLocaleDateString() }}</p>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-800 mb-2">Process Payment</h4>
                                    <div class="mb-2">
                                        <label for="paid_amount" class="block text-sm font-medium text-gray-700">Paid Amount</label>
                                        <input type="number" id="paid_amount" v-model="form.paid_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" step="0.01">
                                        <div v-if="form.errors.paid_amount" class="text-red-500 text-sm mt-1">{{ form.errors.paid_amount }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <label for="payment_received_at" class="block text-sm font-medium text-gray-700">Payment Received At</label>
                                        <input type="date" id="payment_received_at" v-model="form.payment_received_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <div v-if="form.errors.payment_received_at" class="text-red-500 text-sm mt-1">{{ form.errors.payment_received_at }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                                        <input type="text" id="payment_method" v-model="form.payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <div v-if="form.errors.payment_method" class="text-red-500 text-sm mt-1">{{ form.errors.payment_method }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <label for="receipt_number" class="block text-sm font-medium text-gray-700">Receipt Number</label>
                                        <input type="text" id="receipt_number" v-model="form.receipt_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                        <div v-if="form.errors.receipt_number" class="text-red-500 text-sm mt-1">{{ form.errors.receipt_number }}</div>
                                    </div>
                                    <button @click="processPayment(claim.id)" :disabled="form.processing" class="mt-2 inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                                        Process Payment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
