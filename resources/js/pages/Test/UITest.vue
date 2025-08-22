<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { confirmDialog } from '@/lib/confirm';
import { ref } from 'vue';

const showToast = () => {
  // Simulate a flash message by redirecting with flash data
  router.post(route('test.flash-message'), {}, {
    onSuccess: () => {
      console.log('Toast should appear');
    }
  });
};

const showConfirmDialog = async () => {
  const confirmed = await confirmDialog({
    title: 'Test Confirmation',
    message: 'This is a test of the confirmation dialog. Do you want to proceed?',
    confirmText: 'Yes, proceed',
    cancelText: 'Cancel'
  });
  
  if (confirmed) {
    alert('You confirmed!');
  } else {
    alert('You cancelled!');
  }
};

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'UI Test', href: '#' },
];
</script>

<template>
  <Head title="UI Test Page" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      
      <!-- Liquid Glass Card Test -->
      <div class="liquidGlass-wrapper">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Liquid Glass Effect Test</h2>
          <p class="text-gray-600 dark:text-gray-300">This card should have a liquid glass effect with a subtle shine and blur backdrop.</p>
        </div>
      </div>

      <!-- Button Tests -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border">
        <h3 class="text-lg font-semibold mb-4">Button Tests</h3>
        <div class="flex flex-wrap gap-4">
          <!-- Glass Buttons -->
          <button @click="showToast" class="btn-glass">
            Show Toast Test
          </button>
          <button @click="showConfirmDialog" class="btn-glass">
            Show Confirm Dialog
          </button>
          <button class="btn-glass btn-glass-sm">
            Small Glass Button
          </button>

          <!-- Standard Buttons -->
          <button class="btn btn-primary">Primary Button</button>
          <button class="btn btn-success">Success Button</button>
          <button class="btn btn-info">Info Button</button>
          <button class="btn btn-danger">Danger Button</button>
        </div>
      </div>

      <!-- Toast Test Instructions -->
      <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
        <h4 class="font-semibold text-blue-900 dark:text-blue-100">Test Instructions:</h4>
        <ul class="list-disc list-inside text-blue-800 dark:text-blue-200 mt-2 space-y-1">
          <li>Click "Show Toast Test" to test the toast notification system</li>
          <li>Click "Show Confirm Dialog" to test the confirmation modal</li>
          <li>Check if the liquid glass effects are visible and working</li>
          <li>Verify that the buttons have the correct styling</li>
          <li>Check the sidebar for the "Partnerships" group visibility</li>
        </ul>
      </div>

      <!-- Current Implementation Status -->
      <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
        <h4 class="font-semibold text-green-900 dark:text-green-100">✅ Confirmed Working:</h4>
        <ul class="list-disc list-inside text-green-800 dark:text-green-200 mt-2 space-y-1">
          <li>Toast component integrated into AppLayout.vue</li>
          <li>ConfirmModal component with eventBus system</li>
          <li>Liquid glass CSS classes defined in app.css</li>
          <li>Glass button styles (.btn-glass, .btn-glass-sm)</li>
          <li>Partnerships group defined in AppSidebar.vue</li>
        </ul>
      </div>

    </div>
  </AppLayout>
</template>
</script>