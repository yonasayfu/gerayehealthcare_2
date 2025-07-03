<script setup>
import { ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import debounce from 'lodash/debounce'
import { Download, FileText, Printer, PlusCircle, Trash2, Pencil } from 'lucide-vue-next'

const props = defineProps({
  staff: Object,
  filters: Object,
})

const search = ref(props.filters.search || '')
const perPage = ref(props.filters.perPage || 10)
const sortBy = ref(props.filters.sortBy || 'id')
const sortOrder = ref(props.filters.sortOrder || 'desc')

watch([search, perPage], debounce(() => {
  router.get(route('staff.index'), {
    search: search.value,
    perPage: perPage.value,
    sortBy: sortBy.value,
    sortOrder: sortOrder.value,
  }, { preserveState: true, replace: true })
}, 300))

const sort = (field) => {
  if (sortBy.value === field) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = field
    sortOrder.value = 'asc'
  }
  router.get(route('staff.index'), {
    search: search.value,
    perPage: perPage.value,
    sortBy: sortBy.value,
    sortOrder: sortOrder.value,
  }, { preserveState: true, replace: true })
}

const exportData = (type) => {
  window.open(`/dashboard/staff/export?type=${type}`, '_blank')
}

const printTable = () => {
  window.print()
}
</script>

<template>
  <div class="p-4">
    <div class="flex justify-between mb-4">
      <input v-model="search" type="text" placeholder="Search staff..." class="border rounded px-2 py-1" />
      <div class="flex gap-2">
        <button @click="() => exportData('csv')" class="btn-icon"><FileText /></button>
        <button @click="() => exportData('pdf')" class="btn-icon"><Download /></button>
        <button @click="printTable" class="btn-icon"><Printer /></button>
        <a :href="route('staff.create')" class="btn btn-primary inline-flex items-center"><PlusCircle class="w-4 h-4 mr-2" />Add Staff</a>
      </div>
    </div>

    <div class="overflow-auto">
      <table class="min-w-full border">
        <thead>
          <tr>
            <th @click="sort('first_name')">First Name</th>
            <th @click="sort('last_name')">Last Name</th>
            <th @click="sort('email')">Email</th>
            <th>Phone</th>
            <th>Position</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="s in staff.data" :key="s.id">
            <td>{{ s.first_name }}</td>
            <td>{{ s.last_name }}</td>
            <td>{{ s.email }}</td>
            <td>{{ s.phone }}</td>
            <td>{{ s.position }}</td>
            <td>{{ s.status }}</td>
            <td class="flex gap-2">
              <a :href="route('staff.edit', s.id)" class="text-blue-500"><Pencil class="w-4 h-4" /></a>
              <button @click="() => router.delete(route('staff.destroy', s.id))" class="text-red-500"><Trash2 class="w-4 h-4" /></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex justify-between items-center">
      <select v-model="perPage" class="border rounded px-2 py-1">
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
      </select>
      <div v-html="staff.links" />
    </div>
  </div>
</template>

<style scoped>
.btn-icon {
  @apply px-2 py-1 border rounded hover:bg-gray-100;
}

@media print {
  body * {
    visibility: hidden;
  }
  table, table * {
    visibility: visible;
  }
  table {
    position: absolute;
    left: 0;
    top: 0;
  }
}
</style>