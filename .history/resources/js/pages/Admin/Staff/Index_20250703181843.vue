// Step 3: Vue + Inertia Frontend Scaffolding for Staff Module
// Mirrors Patient module structure

// --- File: resources/js/Pages/Staff/Index.vue ---

<script setup>
import { ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import debounce from 'lodash.debounce'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Table from '@/Components/Table.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  staff: Object,
  filters: Object,
})

const search = ref(props.filters.search || '')
const perPage = ref(props.filters.perPage || 10)

watch([search, perPage], debounce(() => {
  router.get(route('staff.index'), {
    search: search.value,
    perPage: perPage.value
  }, {
    preserveState: true,
    replace: true,
  })
}, 300))
</script>

<template>
  <AppLayout title="Staff">
    <Head title="Staff Management" />
    <div class="p-4">
      <div class="flex justify-between items-center mb-4">
        <input v-model="search" placeholder="Search staff..." class="border px-3 py-1 rounded" />
        <select v-model="perPage" class="ml-2 border px-3 py-1 rounded">
          <option :value="10">10</option>
          <option :value="25">25</option>
          <option :value="50">50</option>
          <option :value="100">100</option>
        </select>
        <Link href="/dashboard/staff/create" class="ml-auto bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
          + Add Staff
        </Link>
      </div>

      <Table :data="staff.data" resource="staff" />
      <Pagination :links="staff.links" />
    </div>
  </AppLayout>
</template>

// --- Create/Edit/Form.vue to follow (in next steps) ---
