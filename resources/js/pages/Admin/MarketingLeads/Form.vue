<script setup lang="ts">
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  form: ReturnType<typeof useForm>;
  campaigns: any[];
  landingPages: any[];
  staffMembers: any[];
  patients: any[];
  statuses: string[];
}>();

</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label for="first_name" class="block text-sm font-medium text-gray-900 dark:text-white">First Name</label>
      <TextInput
        id="first_name"
        type="text"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.first_name"
        required
        autofocus
      />
      <InputError class="mt-2" :message="form.errors.first_name" />
    </div>

    <div>
      <label for="last_name" class="block text-sm font-medium text-gray-900 dark:text-white">Last Name</label>
      <TextInput
        id="last_name"
        type="text"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.last_name"
        required
      />
      <InputError class="mt-2" :message="form.errors.last_name" />
    </div>

    <div>
      <label for="email" class="block text-sm font-medium text-gray-900 dark:text-white">Email</label>
      <TextInput
        id="email"
        type="email"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.email"
      />
      <InputError class="mt-2" :message="form.errors.email" />
    </div>

    <div>
      <label for="phone" class="block text-sm font-medium text-gray-900 dark:text-white">Phone</label>
      <TextInput
        id="phone"
        type="text"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.phone"
      />
      <InputError class="mt-2" :message="form.errors.phone" />
    </div>

    <div>
      <label for="country" class="block text-sm font-medium text-gray-900 dark:text-white">Country</label>
      <TextInput
        id="country"
        type="text"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.country"
      />
      <InputError class="mt-2" :message="form.errors.country" />
    </div>

    <div>
      <label for="utm_source" class="block text-sm font-medium text-gray-900 dark:text-white">UTM Source</label>
      <TextInput
        id="utm_source"
        type="text"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.utm_source"
      />
      <InputError class="mt-2" :message="form.errors.utm_source" />
    </div>

    <div>
      <label for="utm_campaign" class="block text-sm font-medium text-gray-900 dark:text-white">UTM Campaign</label>
      <TextInput
        id="utm_campaign"
        type="text"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.utm_campaign"
      />
      <InputError class="mt-2" :message="form.errors.utm_campaign" />
    </div>

    <div>
      <label for="utm_medium" class="block text-sm font-medium text-gray-900 dark:text-white">UTM Medium</label>
      <TextInput
        id="utm_medium"
        type="text"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.utm_medium"
      />
      <InputError class="mt-2" :message="form.errors.utm_medium" />
    </div>

    <div>
      <label for="source_campaign_id" class="block text-sm font-medium text-gray-900 dark:text-white">Source Campaign</label>
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
      <label for="landing_page_id" class="block text-sm font-medium text-gray-900 dark:text-white">Landing Page</label>
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
      <label for="lead_score" class="block text-sm font-medium text-gray-900 dark:text-white">Lead Score</label>
      <TextInput
        id="lead_score"
        type="number"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.lead_score"
      />
      <InputError class="mt-2" :message="form.errors.lead_score" />
    </div>

    <div>
      <label for="status" class="block text-sm font-medium text-gray-900 dark:text-white">Status</label>
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
      <label for="assigned_staff_id" class="block text-sm font-medium text-gray-900 dark:text-white">Assigned Staff</label>
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
      <label for="converted_patient_id" class="block text-sm font-medium text-gray-900 dark:text-white">Converted Patient</label>
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
      <label for="conversion_date" class="block text-sm font-medium text-gray-900 dark:text-white">Conversion Date</label>
      <TextInput
        id="conversion_date"
        type="date"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.conversion_date"
      />
      <InputError class="mt-2" :message="form.errors.conversion_date" />
    </div>

    <div class="md:col-span-2">
      <label for="notes" class="block text-sm font-medium text-gray-900 dark:text-white">Notes</label>
      <textarea
        id="notes"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
        v-model="form.notes"
      ></textarea>
      <InputError class="mt-2" :message="form.errors.notes" />
    </div>
  </div>
</template>
