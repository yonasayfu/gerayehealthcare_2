<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Eye, Edit3, Trash2 } from 'lucide-vue-next';

interface Props {
  item: any;
  showRoute?: string;
  editRoute?: string;
  showView?: boolean;
  showEdit?: boolean;
  showDelete?: boolean;
  customActions?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  showView: true,
  showEdit: true,
  showDelete: true,
  customActions: false,
});

const emit = defineEmits<{
  delete: [id: number | string];
}>();

const handleDelete = () => {
  emit('delete', props.item.id);
};
</script>

<template>
  <div class="flex items-center gap-2 print:hidden">
    <!-- View Button -->
    <Link 
      v-if="showView && showRoute" 
      :href="showRoute"
      class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 transition-colors"
      title="View"
    >
      <Eye class="w-4 h-4" />
    </Link>

    <!-- Edit Button -->
    <Link 
      v-if="showEdit && editRoute" 
      :href="editRoute"
      class="inline-flex items-center justify-center w-8 h-8 text-green-600 bg-green-100 rounded-lg hover:bg-green-200 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800 transition-colors"
      title="Edit"
    >
      <Edit3 class="w-4 h-4" />
    </Link>

    <!-- Custom Actions Slot -->
    <slot name="custom-actions" v-if="customActions" :item="item" />

    <!-- Delete Button -->
    <button 
      v-if="showDelete"
      @click="handleDelete"
      class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-100 rounded-lg hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800 transition-colors"
      title="Delete"
    >
      <Trash2 class="w-4 h-4" />
    </button>
  </div>
</template>
