<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { format } from 'date-fns';
import type { InsuranceClaim } from '@/types';

const props = defineProps<{
    insuranceClaim: InsuranceClaim;
}>();

const form = useForm({
    recipient_email: props.insuranceClaim?.invoice?.patient?.email || '',
    subject: `Insurance Claim #${props.insuranceClaim?.id} - ${props.insuranceClaim?.claim_status}`,
    message: '',
});

function submitEmail() {
    form.post(route('admin.insurance-claims.send-email', props.insuranceClaim.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Optionally reset form or show success message
        },
        onError: () => {
            // Handle errors
        },
    });
}
</script>

<template>
  <Head :title="`Insurance Claim: ${insuranceClaim.id}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t print:hidden">
            <h3 class="text-xl font-semibold">
                Insurance Claim Details: #{{ insuranceClaim.id }}
            </h3>
            <Link :href="route('admin.insurance-claims.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Claim Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p><strong>Claim ID:</strong> {{ insuranceClaim.id }}</p>
                            <p><strong>Claim Status:</strong> {{ insuranceClaim.claim_status }}</p>
                            <p><strong>Coverage Amount:</strong> {{ insuranceClaim.coverage_amount }}</p>
                                                        <p><strong>Submitted At:</strong> {{ insuranceClaim.submitted_at ? format(new Date(insuranceClaim.submitted_at), 'PPP') : 'N/A' }}</p>
                            <p><strong>Processed At:</strong> {{ insuranceClaim.processed_at ? format(new Date(insuranceClaim.processed_at), 'PPP') : 'N/A' }}</p>
                            <p><strong>Email Status:</strong> {{ insuranceClaim.email_status ?? 'N/A' }}</p>
                            <p v-if="insuranceClaim.email_sent_at"><strong>Email Sent At:</strong> {{ format(new Date(insuranceClaim.email_sent_at), 'PPP p') }}</p>
                        </div>
                        <div>
                            <p><strong>Invoice Number:</strong> {{ insuranceClaim.invoice?.invoice_number ?? 'N/A' }}</p>
                            <p><strong>Patient Name:</strong> {{ insuranceClaim.invoice?.patient?.full_name ?? 'N/A' }}</p>
                            <p><strong>Invoice Grand Total:</strong> {{ insuranceClaim.invoice?.grand_total ?? 'N/A' }}</p>
                            <p><strong>Reimbursement Required:</strong> {{ insuranceClaim.reimbursement_required ? 'Yes' : 'No' }}</p>
                            <p><strong>Is Pre-Authorized:</strong> {{ insuranceClaim.is_pre_authorized ? 'Yes' : 'No' }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Send Email</h3>
                        <form @submit.prevent="submitEmail">
                            <div class="mb-4">
                                <label for="recipient_email" class="block text-sm font-medium text-gray-700">Recipient Email</label>
                                <input type="email" id="recipient_email" v-model="form.recipient_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <div v-if="form.errors.recipient_email" class="text-red-500 text-sm mt-1">{{ form.errors.recipient_email }}</div>
                            </div>
                            <div class="mb-4">
                                <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                                <input type="text" id="subject" v-model="form.subject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <div v-if="form.errors.subject" class="text-red-500 text-sm mt-1">{{ form.errors.subject }}</div>
                            </div>
                            <div class="mb-4">
                                <label for="message" class="block text-sm font-medium text-gray-700">Message (Optional)</label>
                                <textarea id="message" v-model="form.message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                                <div v-if="form.errors.message" class="text-red-500 text-sm mt-1">{{ form.errors.message }}</div>
                            </div>
                            <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                Send Email
                            </button>
                        </form>

                        <div v-if="$page.props.flash?.success" class="mt-4 p-3 bg-green-100 text-green-800 rounded-md">
                            {{ $page.props.flash.success }}
                        </div>
                        <div v-if="$page.props.flash?.error" class="mt-4 p-3 bg-red-100 text-red-800 rounded-md">
                            {{ $page.props.flash.error }}
                        </div>
                    </div>
                </div>
                <div v-else class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <p>Loading Insurance Claim details...</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
