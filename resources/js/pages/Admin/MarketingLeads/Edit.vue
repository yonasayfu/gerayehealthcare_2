<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType, MarketingLead } from '@/types'

const props = defineProps<{
  marketingLead: MarketingLead;
  campaigns: any[];
  landingPages: any[];
  staffMembers: any[];
  patients: any[];
  statuses: string[];
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Marketing Leads', href: route('admin.marketing-leads.index') },
  { title: 'Edit', href: route('admin.marketing-leads.edit', props.marketingLead.id) },
]

const form = useForm<any>({
  first_name: props.marketingLead.first_name,
  last_name: props.marketingLead.last_name,
  email: props.marketingLead.email,
  phone: props.marketingLead.phone,
  country: props.marketingLead.country,
  utm_source: props.marketingLead.utm_source,
  utm_campaign: props.marketingLead.utm_campaign,
  utm_medium: props.marketingLead.utm_medium,
  source_campaign_id: props.marketingLead.source_campaign_id,
  landing_page_id: props.marketingLead.landing_page_id,
  lead_score: props.marketingLead.lead_score,
  status: props.marketingLead.status,
  assigned_staff_id: props.marketingLead.assigned_staff_id,
  converted_patient_id: props.marketingLead.converted_patient_id,
  conversion_date: props.marketingLead.conversion_date,
  notes: props.marketingLead.notes,
})

function submit() {
  form.put(route('admin.marketing-leads.update', props.marketingLead.id))
}
</script>

<template>
  <Head title="Edit Marketing Lead" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Edit Marketing Lead
            </h3>
            <Link :href="route('admin.marketing-leads.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <Form :form="form" :campaigns="props.campaigns" :landingPages="props.landingPages" :staffMembers="props.staffMembers" :patients="props.patients" :statuses="props.statuses" />
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <button @click="submit" :disabled="form.processing" class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
        </div>

    </div>
  </AppLayout>
</template>
