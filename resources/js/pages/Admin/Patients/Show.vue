<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItemType } from '@/types'
import { User, Mail, Phone, Calendar, MapPin, AlertTriangle, Printer } from 'lucide-vue-next'

const props = defineProps<{
  patient: {
    id: number
    full_name: string
    patient_code: string | null
    fayda_id: string | null
    date_of_birth: string | null
    gender: string | null
    address: string | null
    phone_number: string | null
    source: string | null
    emergency_contact: string | null
    geolocation: string | null
  }
}>()

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Patients', href: route('admin.patients.index') },
  { title: 'Details' },
]

const formatDate = (dateString: string | null) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString();
}

const printPatientCard = () => {
    window.print();
}
</script>

<template>
  <Head :title="`Patient: ${patient.full_name}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Patient Details</h1>
            <p class="text-sm text-muted-foreground">Read-only view of patient record #{{ patient.id }}</p>
        </div>
        <div class="flex items-center gap-2 no-print">
            <Link :href="route('admin.patients.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium">
                Back to List
            </Link>
            <button @click="printPatientCard" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md text-sm font-medium transition">
                <Printer class="w-4 h-4" />
                Print
            </button>
            <Link :href="route('admin.patients.print', patient.id)" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium transition">
                <Printer class="w-4 h-4" />
                Print PDF
            </Link>
            <Link :href="route('admin.patients.edit', patient.id)" class="inline-flex items-center px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow-sm">
                Edit Patient
            </Link>
        </div>
      </div>

      <!-- Print Header (only visible when printing) -->
      <div class="print-header">
        <div class="print-logo">
          <h1>Geraye Home-to-Home Care</h1>
          <p>Patient Record Card</p>
        </div>
      </div>

      <!-- Details Card -->
      <div class="rounded-lg border border-border bg-white dark:bg-gray-900 shadow-sm">
        <div class="p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center">
                    <User class="w-8 h-8 text-gray-500" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ patient.full_name }}</h2>
                    <p class="text-sm text-muted-foreground">Patient Code: {{ patient.patient_code ?? 'N/A' }}</p>
                    <p class="text-sm text-muted-foreground">Fayda ID: {{ patient.fayda_id ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 border-t dark:border-gray-700 pt-6">
                <div class="flex items-center gap-3">
                    <Mail class="w-5 h-5 text-muted-foreground" />
                    <div>
                        <p class="text-sm text-muted-foreground">Source</p>
                        <p class="font-medium">{{ patient.source ?? 'N/A' }}</p>
                    </div>
                </div>
                 <div class="flex items-center gap-3">
                    <Phone class="w-5 h-5 text-muted-foreground" />
                    <div>
                        <p class="text-sm text-muted-foreground">Phone Number</p>
                        <p class="font-medium">{{ patient.phone_number ?? 'N/A' }}</p>
                    </div>
                </div>
                 <div class="flex items-center gap-3">
                    <Calendar class="w-5 h-5 text-muted-foreground" />
                    <div>
                        <p class="text-sm text-muted-foreground">Date of Birth</p>
                        <p class="font-medium">{{ formatDate(patient.date_of_birth) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <User class="w-5 h-5 text-muted-foreground" />
                    <div>
                        <p class="text-sm text-muted-foreground">Gender</p>
                        <p class="font-medium">{{ patient.gender ?? 'N/A' }}</p>
                    </div>
                </div>
                 <div class="flex items-center gap-3">
                    <MapPin class="w-5 h-5 text-muted-foreground" />
                    <div>
                        <p class="text-sm text-muted-foreground">Address</p>
                        <p class="font-medium">{{ patient.address ?? 'N/A' }}</p>
                    </div>
                </div>
                 <div class="flex items-center gap-3">
                    <AlertTriangle class="w-5 h-5 text-muted-foreground" />
                    <div>
                        <p class="text-sm text-muted-foreground">Emergency Contact</p>
                        <p class="font-medium">{{ patient.emergency_contact ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.print-header {
  display: none;
}

@media print {
  .print-header {
    display: block !important;
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #333;
    padding-bottom: 20px;
  }
  
  .print-logo h1 {
    font-size: 24px;
    margin: 0;
    color: #2c3e50;
  }
  
  .print-logo p {
    font-size: 16px;
    margin: 5px 0;
    color: #666;
  }
}
@media print {
  .no-print {
    display: none !important;
  }
  
  .p-6 {
    padding: 1rem !important;
  }
  
  .space-y-6 > * + * {
    margin-top: 1rem !important;
  }
  
  .rounded-lg {
    border-radius: 0 !important;
  }
  
  .shadow-sm {
    box-shadow: none !important;
  }
  
  .border-border {
    border: 1px solid #000 !important;
  }
  
  .bg-white {
    background-color: white !important;
  }
  
  .text-gray-800, .text-gray-900 {
    color: #000 !important;
  }
  
  .text-muted-foreground {
    color: #666 !important;
  }
  
  .bg-gray-200 {
    background-color: #f0f0f0 !important;
  }
  
  .border-t {
    border-top: 1px solid #ccc !important;
  }
  
  .grid {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 1rem !important;
  }
  
  .flex {
    display: flex !important;
  }
  
  .items-center {
    align-items: center !important;
  }
  
  .gap-3, .gap-4 {
    gap: 0.75rem !important;
  }
  
  .mb-6 {
    margin-bottom: 1.5rem !important;
  }
  
  .pt-6 {
    padding-top: 1.5rem !important;
  }
  
  .font-bold {
    font-weight: bold !important;
  }
  
  .font-medium {
    font-weight: 500 !important;
  }
  
  .text-xl {
    font-size: 1.25rem !important;
  }
  
  .text-sm {
    font-size: 0.875rem !important;
  }
  
  .w-16, .h-16 {
    width: 4rem !important;
    height: 4rem !important;
  }
  
  .w-8, .h-8 {
    width: 2rem !important;
    height: 2rem !important;
  }
  
  .w-5, .h-5 {
    width: 1.25rem !important;
    height: 1.25rem !important;
  }
  
  .rounded-full {
    border-radius: 50% !important;
  }
  
  .justify-center {
    justify-content: center !important;
  }
}
</style>
