<script setup lang="ts">
import { ref, watch, nextTick, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { Search, X, User, UserCheck, Calendar, Users, ExternalLink, FileText, Shield, ShieldCheck, Package, Truck, Activity, CalendarDays, Target, Megaphone } from 'lucide-vue-next';
import axios from 'axios';

interface SearchResult {
  type: string;
  category: string;
  title: string;
  description: string;
  url: string;
  relevance?: number;
  icon?: string;
}

const searchQuery = ref('');
const searchResults = ref<SearchResult[]>([]);
const showModal = ref(false);
const isLoading = ref(false);
const searchInputRef = ref<HTMLInputElement | null>(null);

// Computed property to organize results by category
const categorizedResults = computed(() => {
  if (!searchResults.value.length) return [];
  
  const categories = searchResults.value.reduce((acc: Record<string, any[]>, result: SearchResult) => {
    const category = result.category || 'Other';
    if (!acc[category]) {
      acc[category] = [];
    }
    acc[category].push(result);
    return acc;
  }, {} as Record<string, any[]>);
  
  // Sort categories by priority
  const categoryOrder = ['Healthcare', 'Financial', 'Inventory', 'Services', 'Events', 'Marketing', 'Other'];
  
  return categoryOrder
    .filter(cat => categories[cat])
    .map(cat => ({
      name: cat,
      results: categories[cat].slice(0, 5) // Limit per category
    }));
});

const fetchResults = debounce(async (query: string) => {
  if (query.length < 2) {
    searchResults.value = [];
    isLoading.value = false;
    return;
  }

  isLoading.value = true;
  try {
    const response = await axios.get(route('admin.global-search'), { params: { q: query } });
    searchResults.value = response.data;
  } catch (error) {
    console.error('Global search error:', error);
    searchResults.value = [];
  } finally {
    isLoading.value = false;
  }
}, 300);

watch(searchQuery, (newQuery: string) => {
  fetchResults(newQuery);
});

const openModal = async () => {
  showModal.value = true;
  await nextTick();
  searchInputRef.value?.focus();
  // Prevent body scroll when modal is open
  document.body.style.overflow = 'hidden';
};

const closeModal = () => {
  showModal.value = false;
  searchQuery.value = '';
  searchResults.value = [];
  isLoading.value = false;
  // Re-enable body scroll
  document.body.style.overflow = '';
};

const getIcon = (iconName: string) => {
  const iconMap: Record<string, any> = {
    'user': User,
    'user-check': UserCheck,
    'calendar': Calendar,
    'users': Users,
    'external-link': ExternalLink,
    'file-text': FileText,
    'shield': Shield,
    'shield-check': ShieldCheck,
    'package': Package,
    'truck': Truck,
    'activity': Activity,
    'calendar-days': CalendarDays,
    'target': Target,
    'megaphone': Megaphone
  };
  return iconMap[iconName] || Search;
};

const getCategoryBadgeClass = (category: string) => {
  const classMap: Record<string, string> = {
    'Healthcare': 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
    'Financial': 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
    'Inventory': 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200',
    'Services': 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200',
    'Events': 'bg-pink-100 dark:bg-pink-900 text-pink-800 dark:text-pink-200',
    'Marketing': 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200'
  };
  return classMap[category] || 'bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200';
};

const navigateToResult = (url: string) => {
  router.visit(url);
  closeModal();
};

const handleKeyboardShortcut = (event: KeyboardEvent) => {
  // Cmd+K on Mac, Ctrl+K on Windows/Linux
  if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
    event.preventDefault();
    openModal();
  }
};

const handleEscape = (event: KeyboardEvent) => {
  if (event.key === 'Escape') {
    closeModal();
  }
};

const handleBackdropClick = (event: MouseEvent) => {
  if (event.target === event.currentTarget) {
    closeModal();
  }
};

// Close modal when clicking outside
import { onMounted, onUnmounted } from 'vue';
onMounted(() => {
  document.addEventListener('keydown', handleEscape);
  document.addEventListener('keydown', handleKeyboardShortcut);
});
onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape);
  document.removeEventListener('keydown', handleKeyboardShortcut);
  // Ensure body scroll is re-enabled if component unmounts
  document.body.style.overflow = '';
});
</script>

<template>
  <div class="relative">
    <!-- Search Trigger Button -->
    <button
      @click="openModal"
      class="flex items-center gap-2 w-full max-w-md px-3 py-2 text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
    >
      <Search class="h-4 w-4" />
      <span class="hidden sm:inline">Search...</span>
      <kbd class="hidden sm:inline-flex ml-auto h-5 select-none items-center gap-1 rounded border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 px-1.5 font-mono text-[10px] font-medium text-gray-600 dark:text-gray-300">
        <span class="text-xs">⌘</span>K
      </kbd>
    </button>

    <!-- Modal Overlay -->
    <Teleport to="body">
      <div
        v-if="showModal"
        class="fixed inset-0 z-[99999] flex items-start justify-center pt-16 px-4 sm:pt-24"
        style="background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);"
        @click="handleBackdropClick"
      >
        <!-- Modal Content -->
        <div
          class="w-full max-w-2xl bg-white dark:bg-gray-800 rounded-lg shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden"
          @click.stop
        >
          <!-- Search Header -->
          <div class="flex items-center gap-3 p-4 border-b border-gray-200 dark:border-gray-700">
            <Search class="h-5 w-5 text-gray-400" />
            <input
              ref="searchInputRef"
              v-model="searchQuery"
              type="text"
              placeholder="Search patients, staff, services, invoices..."
              class="flex-1 text-lg bg-transparent border-none outline-none text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400"
            />
            <button
              @click="closeModal"
              class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded transition-colors"
            >
              <X class="h-5 w-5" />
            </button>
          </div>

          <!-- Search Content -->
          <div class="max-h-96 overflow-y-auto">
            <!-- Loading State -->
            <div v-if="isLoading" class="flex items-center justify-center py-8">
              <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                <div class="animate-spin rounded-full h-5 w-5 border-2 border-gray-300 border-t-blue-600"></div>
                <span>Searching...</span>
              </div>
            </div>

            <!-- No Query State -->
            <div v-else-if="!searchQuery || searchQuery.length < 2" class="p-8 text-center">
              <Search class="h-12 w-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">Global Search</h3>
              <p class="text-gray-500 dark:text-gray-400 text-sm">Search across patients, staff, services, and more...</p>
            </div>

            <!-- No Results -->
            <div v-else-if="searchResults.length === 0 && !isLoading" class="p-8 text-center">
              <div class="text-gray-400 dark:text-gray-500 mb-3">
                <Search class="h-12 w-12 mx-auto" />
              </div>
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">No Results Found</h3>
              <p class="text-gray-500 dark:text-gray-400 text-sm">Try adjusting your search query</p>
            </div>

            <!-- Search Results -->
            <div v-else class="py-2">
              <!-- Results by Category -->
              <div v-for="category in categorizedResults" :key="category.name" class="mb-4 last:mb-0">
                <div class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-100 dark:border-gray-700">
                  {{ category.name }} ({{ category.results.length }})
                </div>
                <button
                  v-for="(result, index) in category.results"
                  :key="index"
                  @click="navigateToResult(result.url)"
                  class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group flex items-start gap-3"
                >
                  <div class="flex-shrink-0 mt-1">
                    <component :is="getIcon(result.icon)" class="h-4 w-4 text-gray-400 group-hover:text-blue-500" />
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                      <h4 class="font-medium text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 truncate">
                        {{ result.title }}
                      </h4>
                      <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" :class="getCategoryBadgeClass(result.category)">
                        {{ result.type }}
                      </span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-1">
                      {{ result.description }}
                    </p>
                  </div>
                </button>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
              <span>Navigate with ↑↓ • Select with ⏎ • Close with ESC</span>
              <span>Powered by Geraye Healthcare</span>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>