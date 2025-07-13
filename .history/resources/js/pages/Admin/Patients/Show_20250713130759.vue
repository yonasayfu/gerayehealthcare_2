<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItemType } from '@/types'
import { User, Mail, Phone, Calendar, MapPin, AlertTriangle } from 'lucide-vue-next'

const props = defineProps<{
  patient: {
    id: number
    full_name: string
    fayda_id: string | null
    date_of_birth: string | null
    gender: string | null
    address: string | null
    phone_number: string | null
    email: string | null
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
        <div class="flex items-center gap-2">
            <Link :href="route('admin.patients.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium">
                Back to List
            </Link>
            <Link :href="route('admin.patients.edit', patient.id)" class="inline-flex items-center px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow-sm">
                Edit Patient
            </Link>
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
                    <p class="text-sm text-muted-foreground">Fayda ID: {{ patient.fayda_id ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 border-t dark:border-gray-700 pt-6">
                <div class="flex items-center gap-3">
                    <Mail class="w-5 h-5 text-muted-foreground" />
                    <div>
                        <p class="text-sm text-muted-foreground">Email</p>
                        <p class="font-medium">{{ patient.email ?? 'N/A' }}</p>
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
