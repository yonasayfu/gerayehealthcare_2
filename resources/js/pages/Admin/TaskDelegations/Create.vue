<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { ref } from 'vue'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import Pagination from '@/components/Pagination.vue'

const props = defineProps<{
  staff: { id: number; first_name: string; last_name: string }[],
  prefill?: {
    title?: string | null,
    assigned_to?: number | null,
    due_date?: string | null,
    status?: string | null,
    notes?: string | null,
  },
  returnTo?: string | null,
  inventoryAlertId?: number | null,
}>()

const form = useForm({
  title: props.prefill?.title ?? '',
  assigned_to: props.prefill?.assigned_to ?? null,
  due_date: props.prefill?.due_date ?? '',
  status: props.prefill?.status ?? 'Pending',
  notes: props.prefill?.notes ?? '',
  // Extra meta to support linking back to an InventoryAlert
  inventory_alert_id: props.inventoryAlertId ?? null,
  return_to: props.returnTo ?? null,
})

function submit() {
  form.post(route('admin.task-delegations.store'), {
    preserveScroll: true,
    onSuccess: () => {
      if (props.returnTo) {
        window.location.href = props.returnTo as string
      }
    }
  })
}
</script>

<template>
  <Head title="Assign Task" />
  <AppLayout>
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold">
                Assign New Task
            </h3>
            <Link :href="props.returnTo || route('admin.task-delegations.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" :title="props.returnTo ? 'Back to Alerts' : 'Close'">
               <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>

        <div class="p-6 space-y-6">
            <div class="mt-4 space-y-4">
                <div>
                  <Label for="title">Title</Label>
                  <Input id="title" v-model="form.title" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
                  <div v-if="form.errors.title" class="text-red-600 text-xs">{{ form.errors.title }}</div>
                </div>

                <div>
                  <Label for="assigned_to">Assign To</Label>
                  <select v-model="form.assigned_to" id="assigned_to" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                    <option disabled :value="null">Select staff…</option>
                    <option v-for="s in staff" :key="s.id" :value="s.id">
                      {{ s.first_name }} {{ s.last_name }}
                    </option>
                  </select>
                  <div v-if="form.errors.assigned_to" class="text-red-600 text-xs">{{ form.errors.assigned_to }}</div>
                </div>

                <div>
                  <Label for="due_date">Due Date</Label>
                  <Input type="date" id="due_date" v-model="form.due_date" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" />
                  <div v-if="form.errors.due_date" class="text-red-600 text-xs">{{ form.errors.due_date }}</div>
                </div>

                <div>
                  <Label for="status">Status</Label>
                  <select v-model="form.status" id="status" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                    <option>Pending</option>
                    <option>In Progress</option>
                    <option>Completed</option>
                  </select>
                  <div v-if="form.errors.status" class="text-red-600 text-xs">{{ form.errors.status }}</div>
                </div>

                <div>
                  <Label for="notes">Notes (optional)</Label>
                  <textarea id="notes" v-model="form.notes" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"></textarea>
                  <div v-if="form.errors.notes" class="text-red-600 text-xs">{{ form.errors.notes }}</div>
                </div>
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
            <button @click="submit" :disabled="form.processing" class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">
              {{ form.processing ? 'Assigning...' : 'Assign' }}
            </button>
        </div>

    </div>
  </AppLayout>
</template>