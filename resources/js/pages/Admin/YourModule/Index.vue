<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Download, FileText, Edit3, Trash2, Printer, ArrowUpDown, Eye } from 'lucide-vue-next'
import debounce from 'lodash/debounce'
import Pagination from '@/components/Pagination.vue'

const props = defineProps<{
  // Your data prop
  yourData: any;
  // Always make filters optional with proper typing
  filters?: {
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
    per_page?: number;
  };
}>()

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Your Module', href: route('admin.your-module.index') },
]

// Always use optional chaining and provide defaults
const search = ref(props.filters?.search || '')
const sortField = ref(props.filters?.sort || '')
const sortDirection = ref(props.filters?.direction || 'asc')
const perPage = ref(props.filters?.per_page || 5)

// Trigger search, sort, pagination
watch([search, sortField, sortDirection, perPage], debounce(() => {
  const params: Record<string, string | number> = {
    search: search.value,
    direction: sortDirection.value,
    per_page: perPage.value,
  };

  if (sortField.value) {
    params.sort = sortField.value;
  }

  router.get(route('admin.your-module.index'), params, {
    preserveState: true,
    replace: true,
  })
}, 500))

// ... rest of your component logic ...
</script>

<!-- ... template ... --> 