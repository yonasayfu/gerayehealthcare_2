<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import AppLayout from '@/layouts/AppLayout.vue';
import ResourcePageHeader from './ResourcePageHeader.vue';
import ResourcePageFilters from './ResourcePageFilters.vue';
import ResourceTable from './ResourceTable.vue';
import Pagination from './Pagination.vue';

interface Column {
  key: string;
  label: string;
  sortable?: boolean;
  class?: string;
}

interface Breadcrumb {
  title: string;
  href: string;
}

interface Props {
  title: string;
  description: string;
  breadcrumbs?: Breadcrumb[];
  createRoute?: string;
  createText?: string;
  searchPlaceholder?: string;
  printTitle?: string;
  routeName: string;
  columns: Column[];
  data: any;
  filters: any;
  showCreate?: boolean;
  showExport?: boolean;
  showPrint?: boolean;
  customHeaderActions?: boolean;
  customFilters?: boolean;
  customTable?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  createText: 'Add New',
  searchPlaceholder: 'Search...',
  showCreate: true,
  showExport: true,
  showPrint: true,
  customHeaderActions: false,
  customFilters: false,
  customTable: false,
});

const emit = defineEmits<{
  delete: [id: number | string];
  export: [type: string];
  print: [];
}>();

// Reactive filter values
const search = ref(props.filters.search || '');
const sortField = ref(props.filters.sort || '');
const sortDirection = ref(props.filters.direction || 'asc');
const perPage = ref(props.filters.per_page || 10);

// Watch for changes and update URL
watch([search, sortField, sortDirection, perPage], debounce(() => {
  const params: Record<string, string | number> = {
    search: search.value,
    direction: sortDirection.value,
    per_page: perPage.value,
  };

  if (sortField.value) {
    params.sort = sortField.value;
  }

  router.get(route(`${props.routeName}.index`), params, {
    preserveState: true,
    replace: true,
  });
}, 500));

// Handle sorting
const handleSort = (field: string) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
};

// Handle export
const handleExport = (type: string) => {
  emit('export', type);
};

// Handle print
const handlePrint = () => {
  emit('print');
};

// Handle delete
const handleDelete = (id: number | string) => {
  emit('delete', id);
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">
      <!-- Header -->
      <ResourcePageHeader
        :title="title"
        :description="description"
        :create-route="createRoute"
        :create-text="createText"
        :show-create="showCreate"
        :show-export="showExport"
        :show-print="showPrint"
        :custom-actions="customHeaderActions"
        @export="handleExport"
        @print="handlePrint"
      >
        <template #actions v-if="customHeaderActions">
          <slot name="header-actions" />
        </template>
      </ResourcePageHeader>

      <!-- Filters -->
      <ResourcePageFilters
        v-model:search="search"
        v-model:per-page="perPage"
        :placeholder="searchPlaceholder"
        :custom-filters="customFilters"
      >
        <template #filters v-if="customFilters">
          <slot name="filters" />
        </template>
      </ResourcePageFilters>

      <!-- Table -->
      <ResourceTable
        v-if="!customTable"
        :columns="columns"
        :data="data.data"
        :sort-field="sortField"
        :sort-direction="sortDirection"
        :print-title="printTitle || title"
        @sort="handleSort"
      >
        <template #rows="{ data: tableData }">
          <slot name="table-rows" :data="tableData" :handle-delete="handleDelete" />
        </template>
        <template #empty-state>
          <slot name="empty-state" />
        </template>
      </ResourceTable>

      <!-- Custom Table Slot -->
      <slot v-else name="custom-table" :data="data" :handle-delete="handleDelete" />

      <!-- Pagination -->
      <Pagination :links="data.links" />
    </div>
  </AppLayout>
</template>
