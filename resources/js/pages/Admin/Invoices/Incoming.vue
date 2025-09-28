<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { format } from 'date-fns';
import { FilePlus2, Eye } from 'lucide-vue-next';

const props = defineProps<{
  groups: Array<{
    patient_id: number;
    patient_name: string;
    count: number;
    first_date: string | null;
    last_date: string | null;
    estimated_total: number;
    visit_ids: number[];
  }>;
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Invoices', href: route('admin.invoices.index') },
  { title: 'Incoming', href: route('admin.invoices.incoming') },
];

const formatCurrency = (value: number | string) => {
  const amount = typeof value === 'string' ? parseFloat(value) : value;
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};

const formatDate = (dateString: string | null) => {
  if (!dateString) return '-';
  return format(new Date(dateString), 'MMM dd, yyyy');
};

function useGenerateForm(patient_id: number, visit_ids: number[]) {
  const form = useForm({ patient_id, visit_ids });
  function submit() {
    form.post(route('admin.invoices.generate'));
  }
  return { form, submit };
}
</script>

<template>
  <Head title="Incoming Invoices" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-semibold">Incoming Invoices</h1>
          <p class="text-sm text-muted-foreground">Completed, billable visits not yet invoiced. Generate invoices in one click.</p>
        </div>
        <div class="flex items-center gap-2">
          <Link :href="route('admin.invoices.index')" class="text-sm text-indigo-600 hover:underline">Go to Invoices</Link>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase">
            <tr>
              <th class="px-6 py-3">Patient</th>
              <th class="px-6 py-3">Billable Visits</th>
              <th class="px-6 py-3">Date Range</th>
              <th class="px-6 py-3">Estimated Total</th>
              <th class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!groups || groups.length === 0">
              <td colspan="5" class="px-6 py-6 text-center text-gray-500">No incoming billables found. You're all caught up!</td>
            </tr>
            <tr v-for="g in groups" :key="g.patient_id" class="border-b dark:border-gray-700">
              <td class="px-6 py-4 font-medium">{{ g.patient_name || 'Unknown Patient' }}</td>
              <td class="px-6 py-4">{{ g.count }}</td>
              <td class="px-6 py-4">{{ formatDate(g.first_date) }} – {{ formatDate(g.last_date) }}</td>
              <td class="px-6 py-4">{{ formatCurrency(g.estimated_total) }}</td>
              <td class="px-6 py-4">
                <div class="flex justify-end gap-2">
                  <!-- Icon-only action: Generate Invoice -->
                  <button
                    title="Generate Invoice"
                    aria-label="Generate Invoice"
                    class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none"
                    @click.prevent="useGenerateForm(g.patient_id, g.visit_ids).submit()"
                  >
                    <FilePlus2 class="h-4 w-4" />
                  </button>
                  <!-- Optional: View patient record -->
                  <Link
                    :href="route('admin.patients.show', g.patient_id)"
                    title="View Patient"
                    aria-label="View Patient"
                    class="inline-flex items-center justify-center h-9 w-9 rounded-md border text-gray-700 hover:bg-gray-50"
                    target="_blank"
                    rel="noopener"
                  >
                    <Eye class="h-4 w-4" />
                  </Link>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
