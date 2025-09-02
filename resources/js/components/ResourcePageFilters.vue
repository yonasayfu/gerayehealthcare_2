<script setup lang="ts">
import { ref, watch } from 'vue';
import { Search } from 'lucide-vue-next';

interface Props {
  search: string;
  perPage: number;
  placeholder?: string;
  showPerPage?: boolean;
  customFilters?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Search...',
  showPerPage: true,
  customFilters: false,
});

const emit = defineEmits<{
  'update:search': [value: string];
  'update:perPage': [value: number];
}>();

const searchValue = ref(props.search);
const perPageValue = ref(props.perPage);

watch(searchValue, (newValue) => {
  emit('update:search', newValue);
});

watch(perPageValue, (newValue) => {
  emit('update:perPage', newValue);
});

// Sync with parent changes
watch(() => props.search, (newValue) => {
  searchValue.value = newValue;
});

watch(() => props.perPage, (newValue) => {
  perPageValue.value = newValue;
});
</script>

<template>
  <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
    <!-- Search Input -->
    <div class="search-glass relative w-full md:w-1/3">
      <input
        v-model="searchValue"
        type="text"
        :placeholder="placeholder"
        class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-100 relative z-10"
      />
      <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400 z-20" />
    </div>

    <!-- Custom Filters Slot -->
    <div v-if="customFilters" class="flex items-center gap-4">
      <slot name="filters" />
    </div>

    <!-- Per Page Selector -->
    <div v-if="showPerPage">
      <label for="perPage" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
      <select 
        id="perPage" 
        v-model="perPageValue" 
        class="rounded-md border-gray-300 bg-white text-gray-900 sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700"
      >
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
    </div>
  </div>
</template>
