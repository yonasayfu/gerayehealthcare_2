<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import PrimaryButton from '@/components/ui/button/Button.vue'

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Leads', href: route('admin.marketing-leads.index') },
  { title: 'Create', href: route('admin.marketing-leads.create') },
]

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  country: '',
  utm_source: '',
  utm_campaign: '',
  utm_medium: '',
  landing_page_id: '',
  lead_score: 0,
  status: 'New',
  assigned_staff_id: '',
  converted_patient_id: '',
  conversion_date: '',
  notes: '',
  source_campaign_id: '',
});

const submit = () => {
  form.post(route('admin.marketing-leads.store'));
};

// Dummy data for select options (replace with actual data from props if available)
const campaigns = [
  { id: 1, campaign_name: 'Summer Sale 2024' },
  { id: 2, campaign_name: 'New Patient Drive' },
];

const landingPages = [
  { id: 1, page_title: 'Homepage Offer' },
  { id: 2, page_title: 'Contact Us Form' },
];

const staffMembers = [
  { id: 1, full_name: 'John Doe' },
  { id: 2, full_name: 'Jane Smith' },
];

const patients = [
  { id: 1, full_name: 'Patient A' },
  { id: 2, full_name: 'Patient B' },
];

const statuses = [
  'New',
  'Contacted',
  'Qualified',
  'Converted',
  'Lost',
];
</script>

<template>
  <Head title="Create Marketing Lead" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create Marketing Lead</h1>
        <p class="text-sm text-muted-foreground">Fill in the details to create a new marketing lead.</p>
      </div>

      <form @submit.prevent="submit" class="space-y-6 bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
        <div>
          <InputLabel for="first_name" value="First Name" />
          <TextInput
            id="first_name"
            type="text"
            class="mt-1 block w-full"
            v-model="form.first_name"
            required
            autofocus
          />
          <InputError class="mt-2" :message="form.errors.first_name" />
        </div>

        <div>
          <InputLabel for="last_name" value="Last Name" />
          <TextInput
            id="last_name"
            type="text"
            class="mt-1 block w-full"
            v-model="form.last_name"
            required
          />
          <InputError class="mt-2" :message="form.errors.last_name" />
        </div>

        <div>
          <InputLabel for="email" value="Email" />
          <TextInput
            id="email"
            type="email"
            class="mt-1 block w-full"
            v-model="form.email"
          />
          <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div>
          <InputLabel for="phone" value="Phone" />
          <TextInput
            id="phone"
            type="text"
            class="mt-1 block w-full"
            v-model="form.phone"
          />
          <InputError class="mt-2" :message="form.errors.phone" />
        </div>

        <div>
          <InputLabel for="country" value="Country" />
          <TextInput
            id="country"
            type="text"
            class="mt-1 block w-full"
            v-model="form.country"
          />
          <InputError class="mt-2" :message="form.errors.country" />
        </div>

        <div>
          <InputLabel for="utm_source" value="UTM Source" />
          <TextInput
            id="utm_source"
            type="text"
            class="mt-1 block w-full"
            v-model="form.utm_source"
          />
          <InputError class="mt-2" :message="form.errors.utm_source" />
        </div>

        <div>
          <InputLabel for="utm_campaign" value="UTM Campaign" />
          <TextInput
            id="utm_campaign"
            type="text"
            class="mt-1 block w-full"
            v-model="form.utm_campaign"
          />
          <InputError class="mt-2" :message="form.errors.utm_campaign" />
        </div>

        <div>
          <InputLabel for="utm_medium" value="UTM Medium" />
          <TextInput
            id="utm_medium"
            type="text"
            class="mt-1 block w-full"
            v-model="form.utm_medium"
          />
          <InputError class="mt-2" :message="form.errors.utm_medium" />
        </div>

        <div>
          <InputLabel for="source_campaign_id" value="Source Campaign" />
          <select
            id="source_campaign_id"
            v-model="form.source_campaign_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
          >
            <option value="">Select a Campaign</option>
            <option v-for="campaign in campaigns" :key="campaign.id" :value="campaign.id">{{ campaign.campaign_name }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.source_campaign_id" />
        </div>

        <div>
          <InputLabel for="landing_page_id" value="Landing Page" />
          <select
            id="landing_page_id"
            v-model="form.landing_page_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
          >
            <option value="">Select a Landing Page</option>
            <option v-for="page in landingPages" :key="page.id" :value="page.id">{{ page.page_title }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.landing_page_id" />
        </div>

        <div>
          <InputLabel for="lead_score" value="Lead Score" />
          <TextInput
            id="lead_score"
            type="number"
            class="mt-1 block w-full"
            v-model="form.lead_score"
          />
          <InputError class="mt-2" :message="form.errors.lead_score" />
        </div>

        <div>
          <InputLabel for="status" value="Status" />
          <select
            id="status"
            v-model="form.status"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
            required
          >
            <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.status" />
        </div>

        <div>
          <InputLabel for="assigned_staff_id" value="Assigned Staff" />
          <select
            id="assigned_staff_id"
            v-model="form.assigned_staff_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
          >
            <option value="">Select Staff</option>
            <option v-for="staff in staffMembers" :key="staff.id" :value="staff.id">{{ staff.full_name }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.assigned_staff_id" />
        </div>

        <div>
          <InputLabel for="converted_patient_id" value="Converted Patient" />
          <select
            id="converted_patient_id"
            v-model="form.converted_patient_id"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
          >
            <option value="">Select Patient</option>
            <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient.full_name }}</option>
          </select>
          <InputError class="mt-2" :message="form.errors.converted_patient_id" />
        </div>

        <div>
          <InputLabel for="conversion_date" value="Conversion Date" />
          <TextInput
            id="conversion_date"
            type="date"
            class="mt-1 block w-full"
            v-model="form.conversion_date"
          />
          <InputError class="mt-2" :message="form.errors.conversion_date" />
        </div>

        <div>
          <InputLabel for="notes" value="Notes" />
          <textarea
            id="notes"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
            v-model="form.notes"
          ></textarea>
          <InputError class="mt-2" :message="form.errors.notes" />
        </div>

        <div class="flex items-center justify-end">
          <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Create Lead
          </PrimaryButton>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
