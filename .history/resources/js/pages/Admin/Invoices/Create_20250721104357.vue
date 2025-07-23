<script setup lang="ts">
<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import InvoiceForm from './Form.vue';
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

const handlePatientSelected = (newPatientId: string | null) => {
  selectedPatient.value = newPatientId;
  router.get(route('admin.invoices.create'), { patient_id: newPatientId }, {
    preserveState: true,
    replace: true,
  });
};

const submit = () => {
  form.post(route('admin.invoices.store'));
};
</script>

<template>
  <Head title="Create Invoice" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <h1 class="text-xl font-semibold">Create New Invoice</h1>

      <form @submit.prevent="submit">
        <InvoiceForm
          :form="form"
          :patients="patients"
          :selectedPatientId="selectedPatient"
          :billableVisits="billableVisits"
          @patient-selected="handlePatientSelected"
        />

        <div class="flex justify-end pt-4">
          <button type="submit" :disabled="form.processing || form.visit_ids.length === 0" class="px-4 py-2 bg-green-600 text-white rounded-md disabled:opacity-50">
            Generate Invoice
          </button>
        </div>
      </form>
    </div>
              <span class="flex-1">{{ visit.service_description || 'Standard Visit' }} on {{ new Date(visit.scheduled_at).toLocaleDateString() }}</span>
              <span class="font-mono">${{ visit.cost }}</span>
            </label>
          </div>

          <div class="grid md:grid-cols-2 gap-4 pt-4 border-t">
              <div>
                  <label for="invoice_date" class="block text-sm font-medium">Invoice Date</label>
                  <input type="date" v-model="form.invoice_date" id="invoice_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
              </div>
              <div>
                  <label for="due_date" class="block text-sm font-medium">Due Date</label>
                  <input type="date" v-model="form.due_date" id="due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
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
  </AppLayout>
</template>