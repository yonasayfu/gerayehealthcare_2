<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'

const { taskDelegations } = defineProps<{
  taskDelegations: {
    data: Array<{ id:number; title:string; due_date:string; status:string }>
    links: { url:string|null; label:string; active:boolean }[]
  }
}>()
</script>

<template>
  <Head title="My Tasks" />

  <AppLayout>
    <div class="p-6 bg-white rounded-lg shadow space-y-4">
      <h1 class="text-xl font-semibold">My Tasks</h1>
      <table class="w-full text-left text-sm">
        <thead class="bg-gray-100 text-xs uppercase text-muted-foreground">
          <tr>
            <th class="px-4 py-2">Title</th>
            <th class="px-4 py-2">Due Date</th>
            <th class="px-4 py-2">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="t in taskDelegations.data" :key="t.id" class="border-t hover:bg-gray-50">
            <td class="px-4 py-2">{{ t.title }}</td>
            <td class="px-4 py-2">{{ new Date(t.due_date).toLocaleDateString() }}</td>
            <td class="px-4 py-2">{{ t.status }}</td>
          </tr>
          <tr v-if="!taskDelegations.data.length">
            <td colspan="3" class="px-4 py-6 text-center text-muted-foreground">
              No tasks assigned.
            </td>
          </tr>
        </tbody>
      </table>

      <Pagination
        v-if="taskDelegations.data.length"
        :links="taskDelegations.links"
        class="flex justify-center"
      />
    </div>
  </AppLayout>
</template>
