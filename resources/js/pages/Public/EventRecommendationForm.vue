<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { format } from 'date-fns'

const props = defineProps<{
  events: Array<{ id: number; title: string; event_date?: string }>
}>()

// Form model (matches API validation)
const form = useForm({
  event_id: '',
  source_channel: 'Web',
  recommended_by_name: '',
  recommended_by_phone: '',
  patient_name: '',
  phone_number: '',
  notes: '',
  // status forced to 'pending' on server for safety
  hp_field: '' // honeypot field for basic bot protection
})

const submitting = ref(false)
const submitted = ref(false)
const eventQuery = ref('')

const filteredEvents = computed(() => {
  const q = eventQuery.value.trim().toLowerCase()
  if (!q) return props.events
  return (props.events || []).filter(e => (e.title || '').toLowerCase().includes(q))
})

function eventLabel(e: { title: string; event_date?: string }) {
  if (!e) return ''
  const d = e.event_date ? new Date(e.event_date) : null
  const dateText = d && !isNaN(d.getTime()) ? ' — ' + format(d, 'PPP') : ''
  return `${e.title}${dateText}`
}

async function submit() {
  if (form.hp_field) return // bot detected
  submitting.value = true
  submitted.value = false
  try {
    await form.post(route('public.event-recommendations.store'), { preserveScroll: true })
    if (Object.keys(form.errors).length === 0) {
      submitted.value = true
      form.reset()
    }
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <Head title="Recommend an Event" />

  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-950">
    <div class="max-w-3xl mx-auto px-4 py-10">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Recommend an Event</h1>
        <p class="text-gray-600 dark:text-gray-300 mt-1">Submit a recommendation quickly — no login required.</p>
      </div>

      <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow p-6">
        <form @submit.prevent="submit" class="space-y-5">
          <!-- Honeypot -->
          <input type="text" v-model="form.hp_field" class="hidden" autocomplete="off" tabindex="-1" aria-hidden="true" />

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Event</label>
              <div class="relative mt-1">
                <input v-model="eventQuery" type="text" placeholder="Search events by title..." class="w-full rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm" />
                <select v-model="form.event_id" class="mt-2 w-full rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm">
                  <option value="">Select an event</option>
                  <option v-for="ev in filteredEvents" :key="ev.id" :value="ev.id">{{ eventLabel(ev) }}</option>
                </select>
              </div>
              <p class="text-xs text-red-500 mt-1" v-if="form.errors?.event_id">{{ form.errors.event_id }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Source</label>
              <select v-model="form.source_channel" class="mt-1 w-full rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm">
                <option>Web</option>
                <option>Mobile</option>
                <option>Email</option>
                <option>SMS</option>
                <option>WhatsApp</option>
                <option>Telegram</option>
                <option>Referral</option>
                <option>Other</option>
              </select>
              <p class="text-xs text-red-500 mt-1" v-if="form.errors?.source_channel">{{ form.errors.source_channel }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Your Name (optional)</label>
              <input v-model="form.recommended_by_name" type="text" class="mt-1 w-full rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm" />
              <p class="text-xs text-red-500 mt-1" v-if="form.errors?.recommended_by_name">{{ form.errors.recommended_by_name }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Your Phone (optional)</label>
              <input v-model="form.recommended_by_phone" type="tel" inputmode="tel" placeholder="e.g. +1 555 123 4567" class="mt-1 w-full rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm" />
              <p class="text-xs text-gray-500 mt-1">We’ll use this only if we need clarification.</p>
              <p class="text-xs text-red-500" v-if="form.errors?.recommended_by_phone">{{ form.errors.recommended_by_phone }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Patient Name</label>
              <input v-model="form.patient_name" type="text" class="mt-1 w-full rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm" />
              <p class="text-xs text-red-500 mt-1" v-if="form.errors?.patient_name">{{ form.errors.patient_name }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Patient Phone (optional)</label>
              <input v-model="form.phone_number" type="tel" inputmode="tel" placeholder="e.g. +1 555 123 4567" class="mt-1 w-full rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm" />
              <p class="text-xs text-red-500 mt-1" v-if="form.errors?.phone_number">{{ form.errors.phone_number }}</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Notes (optional)</label>
              <textarea v-model="form.notes" rows="3" class="mt-1 w-full rounded-md bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm" placeholder="Anything else we should know?" />
              <p class="text-xs text-red-500 mt-1" v-if="form.errors?.notes">{{ form.errors.notes }}</p>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2">
            <div class="text-sm text-gray-500 dark:text-gray-400">Submission is reviewed by our team. No login required.</div>
            <div class="flex gap-2">
              <Link :href="route('home')" class="btn-glass btn-glass-sm">Cancel</Link>
              <button type="submit" class="btn-glass btn-glass-sm" :disabled="submitting">{{ submitting ? 'Submitting…' : 'Submit Recommendation' }}</button>
            </div>
          </div>

          <div v-if="submitted" class="mt-4 rounded-md border border-green-200 bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-300 p-3">
            Thank you! Your recommendation has been submitted and is pending review.
          </div>
        </form>
      </div>

      <div class="text-center mt-6">
        <a :href="route('public.event-recommendations.form')" class="text-sm text-gray-500 dark:text-gray-400 underline">Refresh Events</a>
      </div>
    </div>
  </div>
</template>

