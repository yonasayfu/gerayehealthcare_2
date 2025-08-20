<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Printer, Edit3, Trash2 } from 'lucide-vue-next';
import { format } from 'date-fns';

const props = defineProps({
  supplier: Object, // The supplier to display
});

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Suppliers', href: route('admin.suppliers.index') },
  { title: props.supplier.name, href: route('admin.suppliers.show', props.supplier.id) },
];

function printPage() {
  window.print();
}

// Single supplier print uses in-browser preview for consistency

</script>

<template>
  <Head :title="`Supplier: ${props.supplier.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-background text-foreground border border-border rounded-lg shadow relative m-10 print:m-0 print:w-[95%] print:mx-auto print:border-0 print:shadow-none">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Supplier: {{ supplier.name }}
            </h3>
            <Link :href="route('admin.suppliers.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

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

        <div class="p-6 border-t border-border rounded-b print:hidden">
            <div class="flex flex-wrap gap-2">
              <Link :href="route('admin.suppliers.index')" class="btn btn-outline">Back to List</Link>
              <button @click="printPage()" class="btn btn-dark inline-flex items-center gap-1 text-sm">
                <Printer class="h-4 w-4" /> Print Current
              </button>
              <Link :href="route('admin.suppliers.edit', props.supplier.id)"
                class="btn btn-primary text-sm">
                Edit
              </Link>
            </div>
        </div>

    </div>
  </AppLayout>
</template>

<style>
@media print {
  html, body {
    margin: 0 !important;
    padding: 0 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  /* Ensure headers repeat and rows don't split */
  thead { display: table-header-group; }
  tfoot { display: table-footer-group; }
  tr, td, th { page-break-inside: avoid; break-inside: avoid; }

  /* Keep readable sizing */
  .print-header-content h1 { font-size: 18px !important; }
  .print-document-title { font-size: 12px !important; }

  /* Center main card in print */
  .print\:mx-auto { margin-left: auto !important; margin-right: auto !important; }
  .print\:w-\[95\%\] { width: 95% !important; }
}
</style>
