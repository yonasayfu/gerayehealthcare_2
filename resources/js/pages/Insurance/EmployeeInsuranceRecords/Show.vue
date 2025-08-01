<template>
    <AppLayout title="View Employee Insurance Record">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                View Employee Insurance Record
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Record Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p><strong>Patient:</strong> {{ employeeInsuranceRecord.patient?.full_name }}</p>
                            <p><strong>Policy:</strong> {{ employeeInsuranceRecord.insurance_policy?.service_type }} ({{ employeeInsuranceRecord.insurance_policy?.coverage_percentage }}%)</p>
                            <p><strong>Kebele ID:</strong> {{ employeeInsuranceRecord.kebele_id }}</p>
                            <p><strong>Woreda:</strong> {{ employeeInsuranceRecord.woreda }}</p>
                            <p><strong>Region:</strong> {{ employeeInsuranceRecord.region }}</p>
                        </div>
                        <div>
                            <p><strong>Federal ID:</strong> {{ employeeInsuranceRecord.federal_id }}</p>
                            <p><strong>Ministry/Department:</strong> {{ employeeInsuranceRecord.ministry_department }}</p>
                            <p><strong>Employee ID Number:</strong> {{ employeeInsuranceRecord.employee_id_number }}</p>
                            <p><strong>Verified:</strong> {{ employeeInsuranceRecord.verified ? 'Yes' : 'No' }}</p>
                            <p><strong>Verified At:</strong> {{ employeeInsuranceRecord.verified_at ? new Date(employeeInsuranceRecord.verified_at).toLocaleString() : 'N/A' }}</p>
                            <p><strong>Created At:</strong> {{ new Date(employeeInsuranceRecord.created_at).toLocaleString() }}</p>
                            <p><strong>Updated At:</strong> {{ new Date(employeeInsuranceRecord.updated_at).toLocaleString() }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <Link :href="route('admin.employee-insurance-records.edit', employeeInsuranceRecord.id)" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Edit Record
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    employeeInsuranceRecord: Object,
});
</script>
