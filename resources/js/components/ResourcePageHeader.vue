<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Download, Printer, Plus } from 'lucide-vue-next';

interface Props {
  title: string;
  description: string;
  createRoute?: string;
  createText?: string;
  showCreate?: boolean;
  showExport?: boolean;
  showPrint?: boolean;
  customActions?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  createText: 'Add New',
  showCreate: true,
  showExport: true,
  showPrint: true,
  customActions: false,
});

const emit = defineEmits<{
  export: [type: string];
  print: [];
}>();

const handleExport = (type: string) => {
  emit('export', type);
};

const handlePrint = () => {
  emit('print');
};
</script>

<template>
  <div class="liquidGlass-wrapper print:hidden">
    <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

    <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
      <div class="flex items-center gap-4">
        <div class="print:hidden">
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ title }}</h1>
          <p class="text-sm text-gray-600 dark:text-gray-300">{{ description }}</p>
        </div>
      </div>

      <div class="flex items-center gap-2 print:hidden">
        <!-- Create Button -->
        <Link 
          v-if="showCreate && createRoute" 
          :href="createRoute" 
          class="btn-glass"
        >
          <Plus class="icon" />
          <span>{{ createText }}</span>
        </Link>

        <!-- Custom Actions Slot -->
        <slot name="actions" v-if="customActions" />

        <!-- Export Button -->
        <button 
          v-if="showExport" 
          @click="handleExport('csv')" 
          class="btn-glass btn-glass-sm"
        >
          <Download class="icon" />
          <span class="hidden sm:inline">Export CSV</span>
        </button>

        <!-- Print Button -->
        <button 
          v-if="showPrint" 
          @click="handlePrint" 
          class="btn-glass btn-glass-sm"
        >
          <Printer class="icon" />
          <span class="hidden sm:inline">Print Current</span>
        </button>
      </div>
    </div>
  </div>
</template>
