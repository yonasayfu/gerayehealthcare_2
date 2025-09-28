<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { ref, watch } from 'vue'

const props = defineProps<{ form: any; staffList?: Array<{ id: number; first_name: string; last_name: string }> }>()

const statuses = ['Available', 'Unavailable']

const conflictLoading = ref(false)
const visitConflictCount = ref<number | null>(null)

async function checkConflicts() {
  const staffId = props.form.staff_id
  const start = props.form.start_time
  const end = props.form.end_time
  if (!staffId || !start || !end) {
    visitConflictCount.value = null
    return
  }
  try {
    conflictLoading.value = true
    const url = route('admin.staff-availabilities.visitConflicts', { staff_id: staffId, start_time: start, end_time: end })
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } })
    if (!res.ok) throw new Error('Failed to check conflicts')
    const data = await res.json()
    visitConflictCount.value = data?.count ?? 0
  } catch (e) {
    // Swallow errors for hinting; backend will still validate on submit
    visitConflictCount.value = null
  } finally {
    conflictLoading.value = false
  }
}

watch(() => [props.form.staff_id, props.form.start_time, props.form.end_time], () => {
  // Debounce a bit
  if ((checkConflicts as any)._t) clearTimeout((checkConflicts as any)._t)
  ;(checkConflicts as any)._t = setTimeout(() => checkConflicts(), 300)
})
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-900 dark:text-white">Staff</label>
      <select v-model.number="props.form.staff_id" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700">
        <option :value="null">Select staff</option>
        <option v-for="s in props.staffList || []" :key="s.id" :value="s.id">{{ s.first_name }} {{ s.last_name }}</option>
      </select>
      <InputError class="mt-1" :message="props.form.errors?.staff_id" />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-900 dark:text-white">Start Time</label>
      <input v-model="props.form.start_time" type="datetime-local" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700" />
      <InputError class="mt-1" :message="props.form.errors?.start_time" />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-900 dark:text-white">End Time</label>
      <input v-model="props.form.end_time" type="datetime-local" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700" />
      <InputError class="mt-1" :message="props.form.errors?.end_time" />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-900 dark:text-white">Status</label>
      <select v-model="props.form.status" class="mt-1 block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700">
        <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
      </select>
      <InputError class="mt-1" :message="props.form.errors?.status" />
    </div>
    <div class="md:col-span-2">
      <div v-if="conflictLoading" class="mt-2 text-sm text-gray-500">Checking visits in this window...</div>
      <div v-else-if="visitConflictCount !== null" class="mt-2 text-sm" :class="visitConflictCount > 0 ? 'text-red-700' : 'text-green-700'">
        <template v-if="visitConflictCount > 0">
          {{ visitConflictCount }} scheduled visit(s) in this window. Setting "Unavailable" will be blocked.
        </template>
        <template v-else>
          No scheduled visits in this window.
        </template>
      </div>
    </div>
  </div>
</template>
