import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { confirmDialog } from '@/lib/confirm';

interface UseResourcePageOptions {
  routeName: string;
  deleteConfirmTitle?: string;
  deleteConfirmMessage?: string;
}

export function useResourcePage(options: UseResourcePageOptions) {
  const { 
    routeName, 
    deleteConfirmTitle = 'Delete Record',
    deleteConfirmMessage = 'Are you sure you want to delete this record? This action cannot be undone.'
  } = options;

  const isLoading = ref(false);

  // Delete function with confirmation
  const deleteRecord = async (id: number | string) => {
    const confirmed = await confirmDialog({
      title: deleteConfirmTitle,
      message: deleteConfirmMessage,
      confirmText: 'Delete',
      cancelText: 'Cancel',
    });

    if (!confirmed) return;

    isLoading.value = true;
    
    router.delete(route(`${routeName}.destroy`, id), {
      preserveScroll: true,
      onFinish: () => {
        isLoading.value = false;
      },
    });
  };

  // Export function
  const exportData = (type: 'csv' | 'excel' | 'pdf' = 'csv', preview: boolean = false) => {
    const params: Record<string, string | boolean> = { type };
    if (preview) {
      params.preview = true;
    }
    window.open(route(`${routeName}.export`, params), '_blank');
  };

  // Print current view
  const printCurrentView = () => {
    window.open(route(`${routeName}.printCurrent`, { preview: true }), '_blank');
  };

  // Print all records
  const printAllRecords = () => {
    window.open(route(`${routeName}.printAll`, { preview: true }), '_blank');
  };

  // Bulk delete function
  const bulkDelete = async (ids: (number | string)[]) => {
    const confirmed = await confirmDialog({
      title: 'Delete Multiple Records',
      message: `Are you sure you want to delete ${ids.length} records? This action cannot be undone.`,
      confirmText: 'Delete All',
      cancelText: 'Cancel',
    });

    if (!confirmed) return;

    isLoading.value = true;
    
    router.post(route(`${routeName}.bulkDestroy`), { ids }, {
      preserveScroll: true,
      onFinish: () => {
        isLoading.value = false;
      },
    });
  };

  // Bulk export function
  const bulkExport = (ids: (number | string)[], type: 'csv' | 'excel' | 'pdf' = 'csv') => {
    const params = { ids, type };
    window.open(route(`${routeName}.bulkExport`, params), '_blank');
  };

  return {
    isLoading,
    deleteRecord,
    exportData,
    printCurrentView,
    printAllRecords,
    bulkDelete,
    bulkExport,
  };
}
