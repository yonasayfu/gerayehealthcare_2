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
    setTimeout(() => {
      try {
        window.print();
      } catch (error) {
        console.error('Print failed:', error);
        alert('Failed to open print dialog. Please check your browser settings or try again.');
      } finally {
        isProcessing.value = false;
      }
    }, 100);
  };

  const printAllRecords = () => {
    isProcessing.value = true;
    const url = route(`${options.routeName}.printAll`, { ...options.filters });
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
