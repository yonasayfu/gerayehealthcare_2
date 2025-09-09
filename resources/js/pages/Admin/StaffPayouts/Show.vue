<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import ShowHeader from '@/components/ShowHeader.vue'
import { format } from 'date-fns'

const props = defineProps<{ staffPayout: any }>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Staff Payouts', href: route('admin.staff-payouts.index') },
  { title: `Payout #${props.staffPayout.id}`, href: route('admin.staff-payouts.show', props.staffPayout.id) },
]

const formatCurrency = (value: unknown) => {
  const amount = parseFloat(String(value ?? '0'));
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};
</script>

<template>
  <Head :title="`Payout #${props.staffPayout.id}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="m-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow">
      <ShowHeader title="Staff Payout Details" :subtitle="`Payout #${props.staffPayout.id}`">
        <template #actions>
          <Link :href="route('admin.staff-payouts.index')" class="btn-glass btn-glass-sm">Back</Link>
        </template>
      </ShowHeader>

      <div class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div>
            <p class="text-sm text-muted-foreground">Staff</p>
            <p class="font-medium">{{ props.staffPayout.staff?.first_name }} {{ props.staffPayout.staff?.last_name }}</p>
          </div>
          <div>
            <p class="text-sm text-muted-foreground">Total Amount</p>
            <p class="font-medium text-green-600">{{ formatCurrency(props.staffPayout.total_amount) }}</p>
          </div>
          <div>
            <p class="text-sm text-muted-foreground">Payout Date</p>
            <p class="font-medium">{{ props.staffPayout.payout_date ? format(new Date(props.staffPayout.payout_date), 'PPP') : '-' }}</p>
          </div>
          <div>
            <p class="text-sm text-muted-foreground">Status</p>
            <p class="font-medium">{{ props.staffPayout.status }}</p>
          </div>
          <div class="md:col-span-2 lg:col-span-3">
            <p class="text-sm text-muted-foreground">Notes</p>
            <p class="font-medium whitespace-pre-line">{{ props.staffPayout.notes || '-' }}</p>
          </div>
        </div>

        <div v-if="props.staffPayout.visit_services?.length" class="pt-4">
          <h3 class="font-semibold mb-2">Included Visit Services</h3>
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
                <tr>
                  <th class="px-4 py-2">ID</th>
                  <th class="px-4 py-2">Patient</th>
                  <th class="px-4 py-2">Service</th>
                  <th class="px-4 py-2">Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(vs, idx) in props.staffPayout.visit_services || []" :key="vs?.id ?? idx" class="border-b dark:border-gray-700">
                  <td class="px-4 py-2">{{ vs.id }}</td>
                  <td class="px-4 py-2">{{ vs.patient?.first_name }} {{ vs.patient?.last_name }}</td>
                  <td class="px-4 py-2">{{ vs.service?.name || '-' }}</td>
                  <td class="px-4 py-2">{{ formatCurrency(vs.cost) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          <Link :href="route('admin.staff-payouts.index')" class="btn-glass btn-glass-sm">Back to List</Link>
          <Link :href="route('admin.staff-payouts.edit', props.staffPayout.id)" class="btn-glass btn-glass-sm">Edit</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

