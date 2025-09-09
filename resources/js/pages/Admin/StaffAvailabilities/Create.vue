<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import ShowHeader from '@/components/ShowHeader.vue'
import Form from './Form.vue'
import FormActions from '@/components/FormActions.vue'

const props = defineProps<{ staffList: Array<{ id: number; first_name: string; last_name: string }> }>()

const form = useForm({
  staff_id: null as number | null,
  start_time: '',
  end_time: '',
  status: 'Available',
})

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Staff Availabilities', href: route('admin.staff-availabilities.index') },
  { title: 'Create', href: route('admin.staff-availabilities.create') },
]

function submit() {
  form.post(route('admin.staff-availabilities.store'))
}
</script>

<template>
  <Head title="Create Staff Availability" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="m-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow">
      <ShowHeader title="Create Staff Availability" subtitle="Define a staff availability window">
        <template #actions>
          <Link :href="route('admin.staff-availabilities.index')" class="btn-glass btn-glass-sm">Back</Link>
        </template>
      </ShowHeader>
      <div class="p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" :staff-list="props.staffList" />
          <FormActions :cancel-href="route('admin.staff-availabilities.index')" submit-text="Create" :processing="form.processing" />
        </form>
      </div>
    </div>
  </AppLayout>
</template>

