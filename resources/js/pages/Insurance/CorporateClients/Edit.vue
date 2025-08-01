<template>
    <AppLayout title="Edit Corporate Client">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Corporate Client
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label for="organization_name" class="block text-sm font-medium text-gray-700">Organization Name</label>
                            <input type="text" id="organization_name" v-model="form.organization_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <div v-if="form.errors.organization_name" class="text-red-500 text-sm mt-1">{{ form.errors.organization_name }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="organization_name_amharic" class="block text-sm font-medium text-gray-700">Organization Name (Amharic)</label>
                            <input type="text" id="organization_name_amharic" v-model="form.organization_name_amharic" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.organization_name_amharic" class="text-red-500 text-sm mt-1">{{ form.errors.organization_name_amharic }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact Person</label>
                            <input type="text" id="contact_person" v-model="form.contact_person" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.contact_person" class="text-red-500 text-sm mt-1">{{ form.errors.contact_person }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                            <input type="email" id="contact_email" v-model="form.contact_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.contact_email" class="text-red-500 text-sm mt-1">{{ form.errors.contact_email }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                            <input type="text" id="contact_phone" v-model="form.contact_phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.contact_phone" class="text-red-500 text-sm mt-1">{{ form.errors.contact_phone }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="tin_number" class="block text-sm font-medium text-gray-700">TIN Number</label>
                            <input type="text" id="tin_number" v-model="form.tin_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.tin_number" class="text-red-500 text-sm mt-1">{{ form.errors.tin_number }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="trade_license_number" class="block text-sm font-medium text-gray-700">Trade License Number</label>
                            <input type="text" id="trade_license_number" v-model="form.trade_license_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.trade_license_number" class="text-red-500 text-sm mt-1">{{ form.errors.trade_license_number }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea id="address" v-model="form.address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                            <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address }}</div>
                        </div>

                        <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Update Client
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    corporateClient: Object,
});

const form = useForm({
    organization_name: props.corporateClient.organization_name,
    organization_name_amharic: props.corporateClient.organization_name_amharic,
    contact_person: props.corporateClient.contact_person,
    contact_email: props.corporateClient.contact_email,
    contact_phone: props.corporateClient.contact_phone,
    tin_number: props.corporateClient.tin_number,
    trade_license_number: props.corporateClient.trade_license_number,
    address: props.corporateClient.address,
});

const submit = () => {
    form.put(route('admin.corporate-clients.update', props.corporateClient.id));
};
</script>
