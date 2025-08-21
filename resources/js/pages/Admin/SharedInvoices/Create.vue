<template>
    <Head title="Create Shared Invoice" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Shared Invoice</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <SharedInvoiceForm :invoices="invoices" :partners="partners" :staff="staff" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import SharedInvoiceForm from '@/components/SharedInvoiceForm.vue';

const props = defineProps({
    invoices: Array,
    partners: Array,
    staff: Array,
});
</script>
