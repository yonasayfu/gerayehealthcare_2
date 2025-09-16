<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Printer, ArrowLeft, Edit3, Share2 } from 'lucide-vue-next'
import { format } from 'date-fns'
import ShowHeader from '@/components/ShowHeader.vue'

const props = defineProps<{ prescription: any }>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Prescriptions', href: route('admin.prescriptions.index') },
  { title: `#${props.prescription?.id ?? ''}`, href: null },
]

const formattedDate = computed(() => {
  const d = props.prescription?.prescribed_date
  return d ? format(new Date(d), 'PPP') : '-'
})

function printSingle() {
  requestAnimationFrame(() => window.print())
}

async function copyShareLink() {
  try {
    const res = await fetch(route('admin.prescriptions.shareLink', props.prescription.id), { headers: { 'Accept': 'application/json' } })
    const data = await res.json()
    await navigator.clipboard.writeText(data.url)
    alert('Share link copied to clipboard!')
  } catch (e) { alert('Failed to generate share link.') }
}

async function share(kind: 'wa'|'tw'|'tg') {
  try {
    const res = await fetch(route('admin.prescriptions.shareLink', props.prescription.id), { headers: { 'Accept': 'application/json' } })
    const data = await res.json()
    const url = data.url
    if (kind === 'wa') {
      window.open(`https://wa.me/?text=${encodeURIComponent('View your prescription: ' + url)}`, '_blank')
    } else if (kind === 'tw') {
      window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent('View your prescription')}&url=${encodeURIComponent(url)}`, '_blank')
    } else if (kind === 'tg') {
      window.open(`https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent('View your prescription')}`, '_blank')
    }
  } catch (e) { alert('Failed to share link.') }
}
</script>

<template>
  <Head title="Prescription Details" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0">
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative">
        <ShowHeader title="Prescription Details" :subtitle="`Prescription #${prescription.id}`">
          <template #actions>
            <Link :href="route('admin.prescriptions.index')" class="btn-glass btn-glass-sm">Back</Link>
          </template>
        </ShowHeader>

          <div class="hidden print:block text-center mb-4 print:mb-2">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Prescription</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
              <div class="text-sm text-gray-500">Prescribed Date</div>
              <div class="font-medium">{{ formattedDate }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Status</div>
              <div class="font-medium capitalize">{{ prescription.status }}</div>
            </div>
            <div>
              <div class="text-sm text-gray-500">Created By</div>
              <div class="font-medium">{{ prescription.created_by?.full_name || '-' }}</div>
            </div>
          </div>

          <div class="mb-6">
            <div class="text-sm text-gray-500">Instructions</div>
            <div class="whitespace-pre-wrap">{{ prescription.instructions || '-' }}</div>
          </div>

          <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Items</h2>
            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm print-table">
                <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
                  <tr>
                    <th class="px-4 py-2">Medication</th>
                    <th class="px-4 py-2">Dosage</th>
                    <th class="px-4 py-2">Frequency</th>
                    <th class="px-4 py-2">Duration</th>
                    <th class="px-4 py-2">Notes</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="it in prescription.items || []" :key="it.id" class="border-b dark:border-gray-700">
                    <td class="px-4 py-2">{{ it.medication_name }}</td>
                    <td class="px-4 py-2">{{ it.dosage }}</td>
                    <td class="px-4 py-2">{{ it.frequency }}</td>
                    <td class="px-4 py-2">{{ it.duration }}</td>
                    <td class="px-4 py-2">{{ it.notes }}</td>
                  </tr>
                  <tr v-if="!prescription.items || prescription.items.length === 0">
                    <td class="px-4 py-3 text-center text-gray-400" colspan="5">No items.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Bottom actions (hidden in print) for consistency -->
          <div class="mt-6 flex items-center justify-between gap-2 print:hidden">
            <Link :href="route('admin.prescriptions.index')" class="btn-glass btn-glass-sm">
              <ArrowLeft class="icon" />
              <span class="hidden sm:inline">Back</span>
            </Link>
            <div class="flex items-center gap-2">
              <Link :href="route('admin.prescriptions.edit', prescription.id)" class="btn-glass btn-glass-sm">
                <Edit3 class="icon" />
                <span class="hidden sm:inline">Edit</span>
              </Link>
              <button @click="printSingle" class="btn-glass btn-glass-sm">
                <Printer class="icon" />
                <span class="hidden sm:inline">Print</span>
              </button>
              <button @click="() => share('wa')" class="btn-glass btn-glass-sm">WhatsApp</button>
              <button @click="() => share('tw')" class="btn-glass btn-glass-sm">Twitter</button>
              <button @click="() => share('tg')" class="btn-glass btn-glass-sm">Telegram</button>
              <button @click="copyShareLink" class="btn-glass btn-glass-sm">
                <Share2 class="icon" />
                <span class="hidden sm:inline">Share Link</span>
              </button>
            </div>
          </div>

          <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
            <p>Generated on: {{ new Date().toLocaleString() }}</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style>
/* Professional A5 print layout for single prescription */
@page {
  size: A5 portrait;
  margin: 12mm;
}
@media print {
  html, body { background: #fff !important; }
  .print-logo { display: inline-block; margin: 0 auto 6px auto; max-width: 100%; height: auto; }
  .print-clinic-name { font-size: 16px; margin: 0; }
  .print-document-title { font-size: 12px; margin: 2px 0 0 0; }
  .print-table { font-size: 11px; border-collapse: collapse; }
  .print-table th, .print-table td { border: 1px solid #d1d5db; padding: 6px 8px; }
  /* Remove any hr that may appear as shadow in print */
  hr { display: none !important; }
}
</style>
