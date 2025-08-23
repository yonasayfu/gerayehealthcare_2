<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { Search } from 'lucide-vue-next';
import axios from 'axios';

const searchQuery = ref('');
const searchResults = ref<Array<any>>([]);
const showResults = ref(false);

const fetchResults = debounce(async (query: string) => {
  if (query.length < 2) {
    searchResults.value = [];
    showResults.value = false;
    return;
  }

  try {
    const response = await axios.get(route('admin.global-search'), { params: { q: query } });
    searchResults.value = response.data;
    showResults.value = true;
  } catch (error) {
    console.error('Global search error:', error);
    searchResults.value = [];
    showResults.value = false;
  }
}, 300);

watch(searchQuery, (newQuery) => {
  fetchResults(newQuery);
});

const navigateToResult = (url: string) => {
  router.visit(url);
  showResults.value = false;
  searchQuery.value = '';
  searchResults.value = [];
};

const handleClickOutside = (event: MouseEvent) => {
  const searchContainer = document.getElementById('global-search-container');
  if (searchContainer && !searchContainer.contains(event.target as Node)) {
    showResults.value = false;
  }
};

// Close results when clicking outside
import { onMounted, onUnmounted } from 'vue';
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div id="global-search-container" class="relative w-full max-w-md">
    <div class="relative">
      <input
        type="text"
        v-model="searchQuery"
        @focus="showResults = true"
        placeholder="Global Search..."
        class="form-input w-full rounded-md border border-gray-300 pl-4 pr-10 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none dark:bg-gray-900 dark:text-gray-100 dark:border-gray-600 sm:text-sm"
      />
      <Search class="absolute right-3 top-1/2 -translate-y-1/2 size-4 text-gray-400" />
    </div>

    <div
      v-if="showResults && searchQuery.length >= 2 && searchResults.length > 0"
      class="absolute z-50 mt-1 w-full rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-800 max-h-64 overflow-y-auto"
    >
      <div class="py-1">
        <a
          v-for="result in searchResults"
          :key="result.url"
          @click="navigateToResult(result.url)"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 cursor-pointer"
        >
          <span class="font-semibold">{{ result.title }}</span>
          <span class="text-muted-foreground"> ({{ result.type }})</span>
          <p class="text-xs text-muted-foreground">{{ result.description }}</p>
        </a>
      </div>
    </div>
    <div
      v-else-if="showResults && searchQuery.length >= 2 && searchResults.length === 0"
      class="absolute z-50 mt-1 w-full rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-800"
    >
      <div class="py-1 px-4 text-sm text-gray-700 dark:text-gray-200">
        No results found.
      </div>
    </div>
  </div>
</template>
