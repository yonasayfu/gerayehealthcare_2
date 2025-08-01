<template>
    <AppLayout title="Edit Insurance Policy">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Insurance Policy
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label for="insurance_company_id" class="block text-sm font-medium text-gray-700">Insurance Company</label>
                            <select id="insurance_company_id" v-model="form.insurance_company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Select an Insurance Company</option>
                                <option v-for="company in insuranceCompanies" :key="company.id" :value="company.id">{{ company.name }}</option>
                            </select>
                            <div v-if="form.errors.insurance_company_id" class="text-red-500 text-sm mt-1">{{ form.errors.insurance_company_id }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="corporate_client_id" class="block text-sm font-medium text-gray-700">Corporate Client</label>
                            <select id="corporate_client_id" v-model="form.corporate_client_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Select a Corporate Client</option>
                                <option v-for="client in corporateClients" :key="client.id" :value="client.id">{{ client.organization_name }}</option>
                            </select>
                            <div v-if="form.errors.corporate_client_id" class="text-red-500 text-sm mt-1">{{ form.errors.corporate_client_id }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="service_type" class="block text-sm font-medium text-gray-700">Service Type</label>
                            <input type="text" id="service_type" v-model="form.service_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <div v-if="form.errors.service_type" class="text-red-500 text-sm mt-1">{{ form.errors.service_type }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="service_type_amharic" class="block text-sm font-medium text-gray-700">Service Type (Amharic)</label>
                            <input type="text" id="service_type_amharic" v-model="form.service_type_amharic" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.service_type_amharic" class="text-red-500 text-sm mt-1">{{ form.errors.service_type_amharic }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="coverage_percentage" class="block text-sm font-medium text-gray-700">Coverage Percentage</label>
                            <input type="number" id="coverage_percentage" v-model="form.coverage_percentage" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" step="0.01" required>
                            <div v-if="form.errors.coverage_percentage" class="text-red-500 text-sm mt-1">{{ form.errors.coverage_percentage }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="coverage_type" class="block text-sm font-medium text-gray-700">Coverage Type</label>
                            <select id="coverage_type" v-model="form.coverage_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Select Coverage Type</option>
                                <option value="Full">Full</option>
                                <option value="Partial">Partial</option>
                            </select>
                            <div v-if="form.errors.coverage_type" class="text-red-500 text-sm mt-1">{{ form.errors.coverage_type }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="is_active" class="flex items-center">
                                <input type="checkbox" id="is_active" v-model="form.is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Is Active</span>
                            </label>
                            <div v-if="form.errors.is_active" class="text-red-500 text-sm mt-1">{{ form.errors.is_active }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea id="notes" v-model="form.notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                            <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">{{ form.errors.notes }}</div>
                        </div>

                        <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Update Policy
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
    insurancePolicy: Object,
    insuranceCompanies: Array,
    corporateClients: Array,
});

const form = useForm({
    insurance_company_id: props.insurancePolicy.insurance_company_id,
    corporate_client_id: props.insurancePolicy.corporate_client_id,
    service_type: props.insurancePolicy.service_type,
    service_type_amharic: props.insurancePolicy.service_type_amharic,
    coverage_percentage: props.insurancePolicy.coverage_percentage,
    coverage_type: props.insurancePolicy.coverage_type,
    is_active: props.insurancePolicy.is_active,
    notes: props.insurancePolicy.notes,
});

const submit = () => {
    form.put(route('admin.insurance-policies.update', props.insurancePolicy.id));
};
</script>
