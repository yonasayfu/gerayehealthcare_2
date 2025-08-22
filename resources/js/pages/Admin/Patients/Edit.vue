<script setup lang="ts">
import { Head, useForm, Link, router } from '@inertiajs/vue3'
import { reactive, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItemType, Patient, PatientForm } from '@/types'

const props = defineProps<{
  corporateClients: Array<any>;
  insurancePolicies: Array<any>;
  patient: any;
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Patients', href: route('admin.patients.index') },
  { title: props.patient?.full_name ?? 'Patient', href: route('admin.patients.show', props.patient?.id) },
  { title: 'Edit' },
]

// Helper to coerce possible string IDs to numbers (or null)
const coerceId = (val: any) => (val === undefined || val === null || val === '' ? null : Number(val))

// Initialize form with full patient data so inputs (including selects) are pre-filled
const form = useForm<any>({
  full_name: props.patient?.full_name ?? '',
  fayda_id: props.patient?.fayda_id ?? '',
  date_of_birth: props.patient?.date_of_birth ?? '',
  ethiopian_date_of_birth: props.patient?.ethiopian_date_of_birth ?? '',
  gender: props.patient?.gender ?? null,
  address: props.patient?.address ?? '',
  phone_number: props.patient?.phone_number ?? '',
  email: props.patient?.email ?? '',
  emergency_contact: props.patient?.emergency_contact ?? '',
  source: props.patient?.source ?? null,
  geolocation: props.patient?.geolocation ?? '',
  // Ensure selects are populated from patient relationships / fields and are numeric
  corporate_client_id: coerceId(props.patient?.corporate_client_id ?? props.patient?.corporate_client?.id ?? null),
  policy_id: coerceId(props.patient?.employee_insurance_records?.[0]?.policy_id ?? props.patient?.policy_id ?? null),
  // include any other fields your Form.vue relies on
})

// Local client-side validation errors
const localErrors = reactive<{ phone_number?: string }>({})

// Normalize Ethiopian phone numbers and validate
function normalizeEthiopianPhone(input: string): string {
  const digits = (input || '').replace(/\D+/g, '')
  if (!digits) return ''
  // +2519xxxxxxxx -> 09xxxxxxxx
  if (digits.startsWith('2519') && digits.length === 12) {
    return '0' + digits.slice(3) // 09 + remaining 8
  }
  // 2519xxxxxxxxx (sometimes 13 with country code + 1 extra) -> attempt trim
  if (digits.startsWith('251') && digits.length >= 12) {
    // Keep last 9 starting with 9 and prefix 0
    const idx = digits.indexOf('9', 3)
    if (idx !== -1 && digits.length - idx >= 9) {
      return '0' + digits.slice(idx, idx + 9)
    }
  }
  // 9xxxxxxxx -> 09xxxxxxxx
  if (digits.length === 9 && digits.startsWith('9')) {
    return '0' + digits
  }
  // 09xxxxxxxx -> keep
  if (digits.length === 10 && digits.startsWith('09')) {
    return digits
  }
  // fallback: return original digits (will fail validation below)
  return digits
}

watch(() => form.phone_number, (val) => {
  const normalized = normalizeEthiopianPhone(val as string)
  if (normalized && normalized !== val) {
    form.phone_number = normalized
  }
  // Validate: must be 09xxxxxxxx (10 digits)
  if (!normalized) {
    localErrors.phone_number = undefined
    return
  }
  const valid = /^09\d{8}$/.test(normalized)
  localErrors.phone_number = valid ? undefined : 'Enter a valid Ethiopian phone number (e.g., 0912345678)'
})

function submit() {
  if (localErrors.phone_number) return
  form.put(route('admin.patients.update', props.patient.id))
}
</script>

<template>
  <Head title="Edit Patient" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Patient</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update patient information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" :localErrors="localErrors" :corporateClients="props.corporateClients" :insurancePolicies="props.insurancePolicies" />

          <!-- Footer actions: Cancel + Save (right aligned), no logic changes -->
          <div class="flex justify-end gap-2 pt-2">
            <Link
              :href="route('admin.patients.index')"
              class="btn-glass btn-glass-sm"
            >
              Cancel
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
