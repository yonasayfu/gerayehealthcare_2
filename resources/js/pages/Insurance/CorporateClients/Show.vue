<template>
    <AppLayout title="View Corporate Client">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                View Corporate Client
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Client Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p><strong>Organization Name:</strong> {{ corporateClient.organization_name }}</p>
                            <p><strong>Organization Name (Amharic):</strong> {{ corporateClient.organization_name_amharic }}</p>
                            <p><strong>Contact Person:</strong> {{ corporateClient.contact_person }}</p>
                            <p><strong>Contact Email:</strong> {{ corporateClient.contact_email }}</p>
                            <p><strong>Contact Phone:</strong> {{ corporateClient.contact_phone }}</p>
                        </div>
                        <div>
                            <p><strong>TIN Number:</strong> {{ corporateClient.tin_number }}</p>
                            <p><strong>Trade License Number:</strong> {{ corporateClient.trade_license_number }}</p>
                            <p><strong>Address:</strong> {{ corporateClient.address }}</p>
                            <p><strong>Created At:</strong> {{ new Date(corporateClient.created_at).toLocaleString() }}</p>
                            <p><strong>Updated At:</strong> {{ new Date(corporateClient.updated_at).toLocaleString() }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <Link :href="route('admin.corporate-clients.edit', corporateClient.id)" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Edit Client
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
    corporateClient: Object,
});
</script>
