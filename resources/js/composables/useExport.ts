import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface UseExportOptions {
  routeName: string;
  filters: Record<string, any>;
}

export function useExport(options: UseExportOptions) {
  const isProcessing = ref(false);

  const exportData = (type: 'csv' | 'pdf') => {
    const url = route(`${options.routeName}.export`, { type, ...options.filters });
    window.open(url, '_blank');
  };

  const printCurrentView = () => {
    // Use browser print for current view to ensure consistent on-page print styling
    // Toolbars should use `print:hidden` classes in the page templates
    try {
      window.print();
    } catch (error) {
      console.error('Print failed:', error);
      alert('Failed to open print dialog.');
    }
  };

  const printAllRecords = () => {
    isProcessing.value = true;
    const url = route(`${options.routeName}.printAll`, { preview: true, ...options.filters });
    window.open(url, '_blank');
    isProcessing.value = false;
  };

  return {
    exportData,
    printCurrentView,
    printAllRecords,
    isProcessing,
  };
}
