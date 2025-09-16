<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import ShowHeader from '@/components/ShowHeader.vue'
import InputError from '@/components/InputError.vue'
import FormActions from '@/components/FormActions.vue'

const props = defineProps<{ staff: Array<{ id: number; first_name: string; last_name: string }> }>()

const form = useForm({
  staff_id: null as number | null,
  notes: '' as string,
})

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Staff Payouts', href: route('admin.staff-payouts.index') },
  { title: 'Create', href: route('admin.staff-payouts.create') },
]

function submit() {
  form.post(route('admin.staff-payouts.store'))
}
</script>

<template>
  <Head title="Create Staff Payout" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="m-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow">
      <ShowHeader title="Create Staff Payout" subtitle="Process payout for a staff member">
        <template #actions>
          <Link :href="route('admin.staff-payouts.index')" class="btn-glass btn-glass-sm">Back</Link>
        </template>
      </ShowHeader>

      <div class="p-6 space-y-4">
        <div class="rounded-md border border-yellow-200 bg-yellow-50 text-yellow-800 p-3 text-sm">
          This will process all unpaid visit services for the selected staff and mark them as paid.
        </div>
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Staff</label>
            <select v-model.number="form.staff_id" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700">
              <option :value="null">Select staff</option>
              <option v-for="s in props.staff || []" :key="s?.id ?? Math.random()" :value="s.id">{{ s.first_name }} {{ s.last_name }}</option>
            </select>
            <InputError class="mt-1" :message="form.errors?.staff_id" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Notes (optional)</label>
            <textarea v-model="form.notes" rows="3" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700" />
            <InputError class="mt-1" :message="form.errors?.notes" />
          </div>

          <FormActions :cancel-href="route('admin.staff-payouts.index')" submit-text="Process Payout" :processing="form.processing" />
        </form>
      </div>
    </div>
  </AppLayout>
  
</template>
