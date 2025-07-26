
<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Upload, FileCheck2, AlertTriangle } from 'lucide-vue-next';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Suppliers', href: route('admin.suppliers.index') },
  { title: 'Import CSV', href: route('admin.suppliers.import.create') },
];

const csvFile = ref<File | null>(null);
const isUploading = ref(false);
const validationErrors = ref<string[]>([]);
const successMessage = ref<string | null>(null);

function handleFileChange(event: Event) {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    csvFile.value = target.files[0];
    validationErrors.value = [];
    successMessage.value = null;
  }
}

function uploadCsv() {
  if (!csvFile.value) {
    alert('Please select a CSV file to upload.');
    return;
  }

  isUploading.value = true;
  validationErrors.value = [];
  successMessage.value = null;

  const formData = new FormData();
  formData.append('csv_file', csvFile.value);

  router.post(route('admin.suppliers.import.store'), formData, {
    onSuccess: () => {
      successMessage.value = 'CSV file imported successfully!';
      csvFile.value = null;
    },
    onError: (errors) => {
      if (errors.csv_file) {
        validationErrors.value = [errors.csv_file];
      } else {
        validationErrors.value = Object.values(errors).flat();
      }
    },
    onFinish: () => {
      isUploading.value = false;
    },
  });
}
</script>

<template>
  <Head title="Import Suppliers from CSV" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <div class="rounded-lg bg-muted/40 p-4 shadow-sm">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Import Suppliers from CSV</h1>
        <p class="text-sm text-muted-foreground">Upload a CSV file to bulk-import supplier data.</p>
      </div>

      <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
        <div class="max-w-xl mx-auto">
          
          <div v-if="successMessage" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md flex items-center gap-2">
            <FileCheck2 class="h-5 w-5" />
            <span>{{ successMessage }}</span>
          </div>

          <div v-if="validationErrors.length > 0" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
            <div class="flex items-center gap-2 mb-2">
              <AlertTriangle class="h-5 w-5" />
              <h3 class="font-bold">Validation Errors</h3>
            </div>
            <ul class="list-disc list-inside text-sm">
              <li v-for="(error, index) in validationErrors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <form @submit.prevent="uploadCsv">
            <div class="mb-4">
              <label for="csv_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CSV File</label>
              <div class="flex items-center justify-center w-full">
                  <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                      <div class="flex flex-col items-center justify-center pt-5 pb-6">
                          <Upload class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" />
                          <p v-if="!csvFile" class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                          <p v-if="!csvFile" class="text-xs text-gray-500 dark:text-gray-400">CSV (MAX. 2MB)</p>
                          <p v-if="csvFile" class="text-sm text-gray-600 dark:text-gray-300">{{ csvFile.name }}</p>
                      </div>
                      <input id="dropzone-file" type="file" @change="handleFileChange" accept=".csv" class="hidden" />
                  </label>
              </div>
            </div>

            <div class="flex justify-end">
              <button type="submit" :disabled="isUploading || !csvFile" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md disabled:opacity-50">
                <Upload class="h-4 w-4" />
                <span>{{ isUploading ? 'Uploading...' : 'Upload CSV' }}</span>
              </button>
            </div>
          </form>

          <div class="mt-8 text-sm text-gray-600 dark:text-gray-400">
            <h4 class="font-semibold mb-2">CSV Format Requirements:</h4>
            <p>The CSV file must have the following columns in this exact order:</p>
            <p class="font-mono text-xs bg-gray-100 dark:bg-gray-800 p-2 rounded my-2">name, contact_person, email, phone, address</p>
            <ul class="list-disc list-inside space-y-1">
              <li><strong>name:</strong> Required. The name of the supplier.</li>
              <li><strong>contact_person:</strong> Optional. The contact person at the supplier.</li>
              <li><strong>email:</strong> Optional. A valid email address.</li>
              <li><strong>phone:</strong> Optional. The phone number of the supplier.</li>
              <li><strong>address:</strong> Optional. The address of the supplier.</li>
            </ul>
          </div>

        </div>
      </div>
    </div>
  </AppLayout>
</template>
