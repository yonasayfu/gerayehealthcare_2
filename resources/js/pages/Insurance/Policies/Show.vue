<template>
    <AppLayout title="View Insurance Policy">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                View Insurance Policy
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Policy Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p><strong>Insurance Company:</strong> {{ insurancePolicy.insurance_company?.name }}</p>
                            <p><strong>Corporate Client:</strong> {{ insurancePolicy.corporate_client?.organization_name }}</p>
                            <p><strong>Service Type:</strong> {{ insurancePolicy.service_type }}</p>
                            <p><strong>Service Type (Amharic):</strong> {{ insurancePolicy.service_type_amharic }}</p>
                            <p><strong>Coverage Percentage:</strong> {{ insurancePolicy.coverage_percentage }}%</p>
                        </div>
                        <div>
                            <p><strong>Coverage Type:</strong> {{ insurancePolicy.coverage_type }}</p>
                            <p><strong>Is Active:</strong> {{ insurancePolicy.is_active ? 'Yes' : 'No' }}</p>
                            <p><strong>Notes:</strong> {{ insurancePolicy.notes }}</p>
                            <p><strong>Created At:</strong> {{ new Date(insurancePolicy.created_at).toLocaleString() }}</p>
                            <p><strong>Updated At:</strong> {{ new Date(insurancePolicy.updated_at).toLocaleString() }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <Link :href="route('admin.insurance-policies.edit', insurancePolicy.id)" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Edit Policy
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
    insurancePolicy: Object,
});
</script>
