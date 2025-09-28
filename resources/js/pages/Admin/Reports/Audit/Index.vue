<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ref, watch } from 'vue'
import debounce from 'lodash/debounce'

const props = defineProps<{ entries: string[]; filters?: { q?: string; level?: string } }>()

const q = ref(props.filters?.q || '')
const level = ref(props.filters?.level || '')

watch([q, level], debounce(() => {
  router.get(route('admin.reports.audit'), { q: q.value, level: level.value }, { preserveState: true, replace: true })
}, 300))

const levels = ['error', 'warning', 'info', 'debug']
</script>

<template>
  <Head title="Audit Logs" />
  <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: route('dashboard') }, { title: 'Reports', href: route('admin.reports.service-volume') }, { title: 'Audit Logs', href: route('admin.reports.audit') }]">
    <div class="p-6 space-y-4">
      <div class="flex flex-col md:flex-row gap-3 items-center">
        <input v-model="q" type="text" placeholder="Search logs..." class="w-full md:w-1/2 form-input rounded-md" />
        <select v-model="level" class="form-select rounded-md">
          <option value="">All Levels</option>
          <option v-for="lv in levels" :key="lv" :value="lv">{{ lv }}</option>
        </select>
      </div>
      <div class="rounded-md border bg-white dark:bg-gray-900 p-3 max-h-[70vh] overflow-auto font-mono text-xs">
        <div v-if="entries.length === 0" class="text-gray-500">No matching log entries.</div>
        <pre v-else class="whitespace-pre-wrap">
<code>
<template v-for="(line, idx) in entries" :key="idx">{{ line }}
</template>
</code>
        </pre>
      </div>
    </div>
  </AppLayout>
  </template>

