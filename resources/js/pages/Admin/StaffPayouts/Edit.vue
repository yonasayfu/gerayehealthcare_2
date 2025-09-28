<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import ShowHeader from '@/components/ShowHeader.vue'
import InputError from '@/components/InputError.vue'
import FormActions from '@/components/FormActions.vue'

const props = defineProps<{ staffPayout: any }>()

const form = useForm({
  status: props.staffPayout?.status || 'Completed',
  notes: props.staffPayout?.notes || '',
})

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Staff Payouts', href: route('admin.staff-payouts.index') },
  { title: `Edit #${props.staffPayout.id}`, href: route('admin.staff-payouts.edit', props.staffPayout.id) },
]

function submit() {
  form.put(route('admin.staff-payouts.update', props.staffPayout.id))
}

const statuses = ['Pending', 'Completed', 'Voided']
</script>

<template>
  <Head :title="`Edit Payout #${props.staffPayout.id}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="m-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow">
      <ShowHeader title="Edit Staff Payout" :subtitle="`Payout #${props.staffPayout.id}`">
        <template #actions>
          <Link :href="route('admin.staff-payouts.show', props.staffPayout.id)" class="btn-glass btn-glass-sm">Back</Link>
        </template>
      </ShowHeader>

      <div class="p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-900 dark:text-white">Status</label>
              <select v-model="form.status" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700">
                <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
              </select>
              <InputError class="mt-1" :message="form.errors?.status" />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-900 dark:text-white">Notes</label>
              <textarea v-model="form.notes" rows="4" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700" />
              <InputError class="mt-1" :message="form.errors?.notes" />
            </div>
          </div>

          <FormActions :cancel-href="route('admin.staff-payouts.show', props.staffPayout.id)" submit-text="Update" :processing="form.processing" />
        </form>
      </div>
    </div>
  </AppLayout>
</template>

