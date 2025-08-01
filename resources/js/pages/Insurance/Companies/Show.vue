<template>
    <AppLayout title="View Insurance Company">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                View Insurance Company
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Company Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p><strong>Name:</strong> {{ insuranceCompany.name }}</p>
                            <p><strong>Name (Amharic):</strong> {{ insuranceCompany.name_amharic }}</p>
                            <p><strong>Contact Person:</strong> {{ insuranceCompany.contact_person }}</p>
                            <p><strong>Contact Email:</strong> {{ insuranceCompany.contact_email }}</p>
                            <p><strong>Contact Phone:</strong> {{ insuranceCompany.contact_phone }}</p>
                        </div>
                        <div>
                            <p><strong>Address:</strong> {{ insuranceCompany.address }}</p>
                            <p><strong>Address (Amharic):</strong> {{ insuranceCompany.address_amharic }}</p>
                            <p><strong>Created At:</strong> {{ new Date(insuranceCompany.created_at).toLocaleString() }}</p>
                            <p><strong>Updated At:</strong> {{ new Date(insuranceCompany.updated_at).toLocaleString() }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <Link :href="route('admin.insurance-companies.edit', insuranceCompany.id)" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Edit Company
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
    insuranceCompany: Object,
});
</script>
