<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';

const props = defineProps<{
  patients: Array<{ id: number; full_name: string }>;
  selectedPatientId: string | null;
  billableVisits: Array<any>;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Invoices', href: route('admin.invoices.index') },
  { title: 'Create', href: route('admin.invoices.create') },
];

const selectedPatient = ref(props.selectedPatientId);

const form = useForm({
  patient_id: props.selectedPatientId,
  visit_ids: [],
  invoice_date: new Date().toISOString().split('T')[0],
  due_date: new Date(new Date().setDate(new Date().getDate() + 30)).toISOString().split('T')[0], // Default due date 30 days from now
});

watch(selectedPatient, (newPatientId) => {
  router.get(route('admin.invoices.create'), { patient_id: newPatientId }, {
    preserveState: true,
    replace: true,
  });
});

function submit() {
  console.log('Submitting form with data:', form.data()); // Add this line
  form.post(route('admin.invoices.store'), {
    onSuccess: () => {
      console.log('Invoice created successfully!');
      // Optionally, redirect or show a success message
    },
    onError: (errors) => {
      console.error('Error creating invoice:', errors);
      let errorMessage = 'An unknown error occurred.';
      if (typeof errors === 'object' && errors !== null) {
        errorMessage = Object.values(errors).flat().join('\n');
      }
      alert('Error creating invoice:\n' + errorMessage);
    },
  });
}
</script>

<template>
  <Head title="Create Invoice" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Create New Invoice
            </h3>
            <Link :href="route('admin.invoices.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <div class="p-4 bg-white rounded-lg shadow">
                <label for="patient" class="block text-sm font-medium text-gray-700">Step 1: Select a Patient</label>
                <select id="patient" v-model="selectedPatient" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                  <option disabled value="">-- Select a Patient --</option>
                  <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient.full_name }}</option>
                </select>
            </div>

            <form v-if="selectedPatient" @submit.prevent="submit">
                <div class="rounded-lg bg-white dark:bg-background p-6 shadow-sm space-y-6">
                  <h2 class="text-lg font-semibold">Step 2: Select Billable Visits</h2>
                  
                  <div v-if="billableVisits.length === 0" class="text-center text-gray-500 py-4">
                    No billable visits found for this patient.
                  </div>
                  
                  <div v-else class="space-y-2 border rounded-md p-2">
                    <label v-for="visit in billableVisits" :key="visit.id" class="flex items-center gap-4 p-2 hover:bg-gray-50 rounded-md">
                      <input type="checkbox" :value="visit.id" v-model="form.visit_ids" class="rounded" />
                      <span class="flex-1">{{ visit.service_description || 'Standard Visit' }} on {{ new Date(visit.scheduled_at).toLocaleDateString() }}</span>
                      <span class="font-mono">${{ visit.cost }}</span>
                    </label>
                  </div>

                  <div class="grid md:grid-cols-2 gap-4 pt-4 border-t">
                      <div>
                          <label for="invoice_date" class="block text-sm font-medium">Invoice Date</label>
                          <input type="date" v-model="form.invoice_date" id="invoice_date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
                      </div>
                      <div>
                          <label for="due_date" class="block text-sm font-medium">Due Date</label>
                          <input type="date" v-model="form.due_date" id="due_date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
                      </div>
                  </div>
                  <div class="flex justify-end pt-4">
                    <button type="submit" :disabled="form.processing || form.visit_ids.length === 0" class="px-4 py-2 bg-green-600 text-white rounded-md disabled:opacity-50">
                      Generate Invoice
                    </button>
                  </div>
                </div>
            </form>
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <!-- The button is now inside the form, so this div can be empty or removed if no other elements are needed here -->
        </div>

    </div>
  </AppLayout>
</template>