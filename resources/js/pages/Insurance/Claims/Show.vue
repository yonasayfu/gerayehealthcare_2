<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Printer, Edit3, Trash2 } from 'lucide-vue-next' // Import icons
import { confirmDialog } from '@/lib/confirm'
import type { BreadcrumbItemType } from '@/types';
import { format } from 'date-fns'; // For date formatting

interface InsuranceClaim {
  id: number;
  claim_status?: string;
  coverage_amount?: number | null;
  paid_amount?: number | null;
  submitted_at?: string | null;
  processed_at?: string | null;
  payment_due_date?: string | null;
  payment_received_at?: string | null;
  payment_method?: string | null;
  receipt_number?: string | null;
  reimbursement_required?: boolean;
  is_pre_authorized?: boolean;
  pre_authorization_code?: string | null;
  email_status?: string | null;
  email_sent_at?: string | null;
  invoice?: {
    invoice_number?: string | null;
    grand_total?: number | string | null;
    patient?: { full_name?: string; phone_number?: string; email?: string } | null;
  } | null;
}

const props = defineProps<{
  insuranceClaim: InsuranceClaim;
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Insurance Claims', href: route('admin.insurance-claims.index') },
  { title: `Claim #${props.insuranceClaim.id}`, href: route('admin.insurance-claims.show', props.insuranceClaim.id) },
]

function printSingleClaim() {
  // Print the current page using the built-in browser print dialog
  window.print();
}

async function destroy(id: number) {
  const ok = await confirmDialog({
    title: 'Delete Insurance Claim',
    message: 'Are you sure you want to delete this insurance claim?',
    confirmText: 'Delete',
    cancelText: 'Cancel',
  })
  if (!ok) return
  router.delete(route('admin.insurance-claims.destroy', id))
}
</script>

<template>
  <Head :title="`Insurance Claim: ${insuranceClaim.id}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

        <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
          <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
          <div class="liquidGlass-content flex items-center justify-between p-6">
            <div>
              <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Insurance Claim Details</h3>
              <p class="text-sm text-gray-600 dark:text-gray-300">Claim #{{ insuranceClaim.id }}</p>
            </div>
          </div>
        </div>

        <div class="p-6 space-y-6">
            <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">

                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                    <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                    <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                    <p class="text-gray-600 dark:text-gray-400 print-document-title">Insurance Claim Document</p>
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

        <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
            <div class="flex justify-end gap-2">
              <Link :href="route('admin.insurance-claims.index')" class="btn-glass btn-glass-sm">Back to List</Link>
              <Link
                :href="route('admin.insurance-claims.edit', insuranceClaim.id)"
                class="btn-glass btn-glass-sm"
              >
                Edit Claim
              </Link>
              <button @click="printSingleClaim" class="btn-glass btn-glass-sm">
                <Printer class="icon" />
                <span class="hidden sm:inline">Print Current</span>
              </button>
            </div>
        </div>

        <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
          <p>Printed on: {{ format(new Date(), 'PPP p') }}</p>
        </div>

    </div>
  </AppLayout>
</template>

<style>
/* Optimized Print Styles for A4 */
@media print {
  @page {
    size: A4 landscape; /* Set page size to A4 */
    margin: 0.5cm; /* Reduce margins significantly to give more space for content */
  }

  /* Universal print adjustments */
  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
  }

  /* Elements to hide during print */
  .print\:hidden {
    display: none !important;
  }
  /* HIDE BREADCRUMBS AND TOP NAV (from AppSidebarLayout.vue) */
  .app-sidebar-header, .app-sidebar {
      display: none !important;
  }
  /* Fallback/more general selectors if the above doesn't catch it all */
  body > header,
  body > nav,
  [role="banner"],
  [role="navigation"] {
      display: none !important;
  }


  /* Elements to show only during print */
  .hidden.print\:block {
    display: block !important;
  }

  /* Specific styles for the print header content (logo and clinic name) */
  .print-header-content {
      padding-top: 0.5cm !important;
      padding-bottom: 0.5cm !important;
      margin-bottom: 0.8cm !important;
  }

  .print-logo {
      max-width: 150px; /* Adjust as needed */
      max-height: 50px; /* Adjust as needed */
      margin-bottom: 0.5rem;
      display: block;
      margin-left: auto;
      margin-right: auto;
  }

  .print-clinic-name {
      font-size: 1.8rem !important;
      margin-bottom: 0.2rem !important;
      line-height: 1.2 !important;
  }

  .print-document-title {
      font-size: 0.9rem !important;
      color: #555 !important;
  }

  /* Target the main patient document container for scaling and layout */
  .bg-white.dark\:bg-gray-900.shadow.rounded-lg {
    box-shadow: none !important;
    border-radius: 0 !important;
    border: none !important;
    padding: 1cm !important;
    margin: 0 !important;
    width: 100% !important;
    height: auto !important;
    overflow: visible !important;

    transform: scale(0.98); /* Less aggressive scaling. Adjust if it goes to 2 pages */
    transform-origin: top left;
  }

  /* Reduce overall top-level padding/margin if the wrapper div adds too much */
  .p-6.space-y-6 {
    padding: 0 !important;
    margin: 0 !important;
  }
  
  /* Adjust spacing within sections for print */
  .space-y-8 > div:not(:first-child) {
    margin-top: 0.8rem !important;
    margin-bottom: 0.8rem !important;
  }
  .space-y-6 > div:not(:first-child) {
    margin-top: 0.6rem !important;
    margin-bottom: 0.6rem !important;
  }

  /* TYPOGRAPHY ADJUSTMENTS */
  h2 { font-size: 1.3rem !important; margin-bottom: 0.6rem !important; }
  p { font-size: 0.85rem !important; line-height: 1.4 !important; }
  .text-sm { font-size: 0.8rem !important; }
  .text-xs { font-size: 0.75rem !important; }
  .font-medium { font-weight: 600 !important; }

  /* BORDER STYLES */
  .border-b {
    border-bottom: 1px solid #ddd !important;
    padding-bottom: 0.7rem !important;
    margin-bottom: 0.7rem !important;
  }

  /* GRID LAYOUT FOR DATA FIELDS (Two-column "Label: Value" format) */
  .grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important; /* Force 2 equal columns */
    gap: 0.8rem 0 !important; /* Vertical gap, horizontal gap is now handled by padding */
    page-break-inside: avoid !important;
  }

  /* Style individual data items within the grid for visual grouping */
  .grid > div {
    display: flex !important;
    flex-direction: row !important;
    align-items: baseline !important;
    gap: 0.4rem !important; /* Gap between label and value */
    padding: 0.1rem 0 !important;
  }

  /* Add a subtle dashed vertical line between the two columns */
  .grid > div:nth-child(odd) { /* Targets items in the left column */
    border-right: 1px dashed #eee !important; /* Subtle dashed line */
    padding-right: 1.5rem !important; /* Space between content and line */
  }
  .grid > div:nth-child(even) { /* Targets items in the right column */
    padding-left: 1.5rem !important; /* Space between line and content */
  }

  .grid > div p:first-child { /* The Label */
    font-weight: 600 !important;
    color: #000 !important;
    flex-shrink: 0 !important;
  }

  .grid > div p:last-child { /* The Value */
    flex-grow: 1 !important;
    white-space: normal !important;
    word-break: break-word !important;
    color: #333 !important;
  }
}
</style>
