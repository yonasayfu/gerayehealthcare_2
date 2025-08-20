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
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                    <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                    <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                    <p class="text-gray-600 dark:text-gray-400 print-document-title">Insurance Claim Document</p>
                    <hr class="my-3 border-gray-300 print:my-2">
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Claim Information</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Claim ID:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.id }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Claim Status:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.claim_status }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Coverage Amount:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.coverage_amount ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Paid Amount:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.paid_amount ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Submitted At:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.submitted_at ? format(new Date(insuranceClaim.submitted_at), 'PPP') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Processed At:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.processed_at ? format(new Date(insuranceClaim.processed_at), 'PPP') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Payment Due Date:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.payment_due_date ? format(new Date(insuranceClaim.payment_due_date), 'PPP') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Payment Received At:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.payment_received_at ? format(new Date(insuranceClaim.payment_received_at), 'PPP') : '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Payment Method:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.payment_method ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Receipt Number:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.receipt_number ?? '-' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Reimbursement Required:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.reimbursement_required ? 'Yes' : 'No' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Is Pre-Authorized:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.is_pre_authorized ? 'Yes' : 'No' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Pre-Authorization Code:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.pre_authorization_code ?? '-' }}</p>
                    </div>
                  </div>
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2" v-if="insuranceClaim.invoice">
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Associated Invoice</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Invoice Number:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.invoice.invoice_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Invoice Grand Total:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.invoice.grand_total ?? 'N/A' }}</p>
                    </div>
                    <div v-if="insuranceClaim.invoice.patient">
                      <p class="text-sm text-muted-foreground">Patient Name:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.invoice.patient.full_name ?? 'N/A' }}</p>
                    </div>
                    <div v-if="insuranceClaim.invoice.patient?.phone_number">
                      <p class="text-sm text-muted-foreground">Patient Phone:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.invoice.patient.phone_number }}</p>
                    </div>
                    <div v-if="insuranceClaim.invoice.patient?.email">
                      <p class="text-sm text-muted-foreground">Patient Email:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.invoice.patient.email }}</p>
                    </div>
                  </div>
                </div>

                <div>
                  <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4 print:mb-2">Email Status</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Email Status:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.email_status ?? 'N/A' }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Email Sent At:</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ insuranceClaim.email_sent_at ? format(new Date(insuranceClaim.email_sent_at), 'PPP p') : 'N/A' }}</p>
                    </div>
                  </div>
                </div>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b print:hidden">
            <div class="flex flex-wrap gap-2">
              <Link :href="route('admin.insurance-claims.index')" class="btn btn-outline">
                Back to List
              </Link>
              <Link
                :href="route('admin.insurance-claims.edit', insuranceClaim.id)"
                class="btn btn-primary"
              >
                Edit Claim
              </Link>
              <button @click="printSingleClaim" class="btn btn-dark">
                <Printer class="h-4 w-4" /> Print Current
              </button>
            </div>
        </div>

        <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
          <hr class="my-2 border-gray-300">
          <p>Printed on: {{ format(new Date(), 'PPP p') }}</p>
        </div>

    </div>
  </AppLayout>
</template>

<style>
/* Optimized Print Styles for A4 */
@media print {
  @page {
    size: A4; /* Set page size to A4 */
    margin: 0.5cm; /* Reduce margins significantly to give more space for content */
  }

  /* Universal print adjustments */
  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
