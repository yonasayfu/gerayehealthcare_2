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
    isProcessing.value = true;
    try {
      const url = route(`${options.routeName}.printCurrent`, { preview: true, ...options.filters });
      window.open(url, '_blank');
    } catch (error) {
      console.error('Print failed:', error);
      alert('Failed to open print preview.');
    } finally {
      isProcessing.value = false;
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
