<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Printer, Edit3, Download } from 'lucide-vue-next';
import { format } from 'date-fns';
import ShowHeader from '@/components/ShowHeader.vue'

const props = defineProps({
  supplier: Object, // The supplier to display
});

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Suppliers', href: route('admin.suppliers.index') },
  { title: props.supplier.name, href: route('admin.suppliers.show', props.supplier.id) },
];

function printPage() {
  setTimeout(() => window.print(), 30);
}

// Single supplier print uses in-browser preview for consistency

</script>

<template>
  <Head :title="`Supplier: ${props.supplier.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

      <ShowHeader title="Supplier Details" :subtitle="`Supplier: ${supplier.name}`">
        <template #actions>
          <Link :href="route('admin.suppliers.index')" class="btn-glass btn-glass-sm">Back</Link>
        </template>
      </ShowHeader>

        <div class="p-6 space-y-6">
            <div class="print-document bg-card text-card-foreground shadow rounded-lg p-8 space-y-8 print:shadow-none print:rounded-none print:p-0 print:m-0 print:w-auto print:h-auto print:flex-shrink-0">
                <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
                  <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
                  <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
                  <p class="text-gray-600 dark:text-gray-400 print-document-title">Supplier Details</p>
                  <hr class="my-2 border-gray-300 print:my-2">
                </div>

                <div class="border-b pb-4 mb-4 print:pb-2 print:mb-2">
                  <h2 class="text-lg font-semibold text-foreground mb-4 print:mb-2">Supplier Information</h2>
                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-y-3 gap-x-6 print:gap-y-2 print:gap-x-4">
                    <div>
                      <p class="text-sm text-muted-foreground">Name:</p>
                      <p class="font-medium text-foreground">{{ supplier.name }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Contact Person:</p>
                      <p class="font-medium text-foreground">{{ supplier.contact_person }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Email:</p>
                      <p class="font-medium text-foreground">{{ supplier.email }}</p>
                    </div>
                    <div>
                      <p class="text-sm text-muted-foreground">Phone:</p>
                      <p class="font-medium text-foreground">{{ supplier.phone }}</p>
                    </div>
                    <div class="md:col-span-2 lg:col-span-2">
                      <p class="text-sm text-muted-foreground">Address:</p>
                      <p class="font-medium text-foreground">{{ supplier.address }}</p>
                    </div>
                  </div>
                </div>

                
                <div class="hidden print:block text-center mt-4 text-sm text-gray-500 print-footer">
                  <hr class="my-2 border-gray-300">
                  <p>Document Generated: {{ format(new Date(), 'PPP p') }}</p>
                </div>
            </div>
        </div>

              <!-- footer actions (single source of actions, right aligned) -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          <button @click="printPage" class="btn-glass btn-glass-sm">
            <Printer class="icon" />
            <span class="hidden sm:inline">Print Current</span>
          </button>
          <Link :href="route('admin.suppliers.edit', supplier.id)" class="btn-glass btn-glass-sm">Edit</Link>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<style>
@media print {
  @page {
    size: A4 landscape;
    margin: 0.5cm;
  }

  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  .app-sidebar-header, .app-sidebar,
  body > header, body > nav,
  [role="banner"], [role="navigation"] {
    display: none !important;
  }

  .hidden.print\:block { display: block !important; }

  .print-header-content {
    padding-top: 0.5cm !important;
    padding-bottom: 0.5cm !important;
    margin-bottom: 0.8cm !important;
  }

  .print-logo {
    max-width: 150px;
    max-height: 50px;
    margin-bottom: 0.5rem;
    display: block;
    margin-left: auto;
    margin-right: auto;
  }

  .print-clinic-name { font-size: 1.8rem !important; margin-bottom: 0.2rem !important; line-height: 1.2 !important; }
  .print-document-title { font-size: 0.9rem !important; color: #555 !important; }

  .bg-white.dark\:bg-gray-800.shadow.rounded-lg {
    box-shadow: none !important;
    border-radius: 0 !important;
    border: none !important;
    padding: 1cm !important;
    margin: 0 !important;
    width: 100% !important;
    height: auto !important;
    overflow: visible !important;
  }

  .p-6.space-y-6 { padding: 0 !important; margin: 0 !important; }

  h2 { font-size: 1.3rem !important; margin-bottom: 0.6rem !important; }
  p { font-size: 0.85rem !important; line-height: 1.4 !important; }
  .text-sm { font-size: 0.8rem !important; }
  .text-xs { font-size: 0.75rem !important; }
  .font-medium { font-weight: 600 !important; }

  .border-b { border-bottom: 1px solid #ddd !important; padding-bottom: 0.7rem !important; margin-bottom: 0.7rem !important; }
  
  /* Responsive grid for print */
  .grid {
    display: grid !important;
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 1rem !important;
  }
  
  /* Ensure content doesn't get cut off */
  .print-document {
    overflow: visible !important;
    height: auto !important;
    padding-bottom: 40px !important;
    box-sizing: border-box !important;
  }
  
  /* Adjust for better readability */
  .space-y-8 > div {
    break-inside: avoid !important;
  }
  
  /* Fix footer positioning */
  .print-footer {
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    text-align: center !important;
    padding: 10px !important;
    font-size: 0.8rem !important;
    color: #666 !important;
    break-inside: avoid !important;
  }
  
  /* Add page margin for footer */
  @page {
    size: A4 landscape;
    margin: 0.5cm;
    margin-bottom: 2cm !important;
  }
  
  /* Ensure the main content area doesn't overlap with footer */
  .print-document > div:not(.print-footer) {
    margin-bottom: 30px !important;
  }
}
</style>
