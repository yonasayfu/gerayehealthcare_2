<template>
    <AppLayout title="Edit Employee Insurance Record">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Employee Insurance Record
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-4">
                            <label for="patient_id" class="block text-sm font-medium text-gray-700">Patient</label>
                            <select id="patient_id" v-model="form.patient_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Select a Patient</option>
                                <option v-for="patient in patients" :key="patient.id" :value="patient.id">{{ patient.full_name }}</option>
                            </select>
                            <div v-if="form.errors.patient_id" class="text-red-500 text-sm mt-1">{{ form.errors.patient_id }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="policy_id" class="block text-sm font-medium text-gray-700">Insurance Policy</label>
                            <select id="policy_id" v-model="form.policy_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Select an Insurance Policy</option>
                                <option v-for="policy in insurancePolicies" :key="policy.id" :value="policy.id">{{ policy.service_type }} ({{ policy.coverage_percentage }}%)</option>
                            </select>
                            <div v-if="form.errors.policy_id" class="text-red-500 text-sm mt-1">{{ form.errors.policy_id }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="kebele_id" class="block text-sm font-medium text-gray-700">Kebele ID</label>
                            <input type="text" id="kebele_id" v-model="form.kebele_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.kebele_id" class="text-red-500 text-sm mt-1">{{ form.errors.kebele_id }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="woreda" class="block text-sm font-medium text-gray-700">Woreda</label>
                            <input type="text" id="woreda" v-model="form.woreda" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.woreda" class="text-red-500 text-sm mt-1">{{ form.errors.woreda }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="region" class="block text-sm font-medium text-gray-700">Region</label>
                            <input type="text" id="region" v-model="form.region" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.region" class="text-red-500 text-sm mt-1">{{ form.errors.region }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="federal_id" class="block text-sm font-medium text-gray-700">Fayda ID</label>
                            <input type="text" id="federal_id" v-model="form.federal_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.federal_id" class="text-red-500 text-sm mt-1">{{ form.errors.federal_id }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="ministry_department" class="block text-sm font-medium text-gray-700">Ministry/Department</label>
                            <input type="text" id="ministry_department" v-model="form.ministry_department" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.ministry_department" class="text-red-500 text-sm mt-1">{{ form.errors.ministry_department }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="employee_id_number" class="block text-sm font-medium text-gray-700">Employee ID Number</label>
                            <input type="text" id="employee_id_number" v-model="form.employee_id_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.employee_id_number" class="text-red-500 text-sm mt-1">{{ form.errors.employee_id_number }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="verified" class="flex items-center">
                                <input type="checkbox" id="verified" v-model="form.verified" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Verified</span>
                            </label>
                            <div v-if="form.errors.verified" class="text-red-500 text-sm mt-1">{{ form.errors.verified }}</div>
                        </div>
                        </div>

                        <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md font-semibold text-sm text-white tracking-widest hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-300 disabled:opacity-50 transition">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    employeeInsuranceRecord: Object,
    patients: Array,
    insurancePolicies: Array,
});

const form = useForm({
    patient_id: props.employeeInsuranceRecord.patient_id,
    policy_id: props.employeeInsuranceRecord.policy_id,
    kebele_id: props.employeeInsuranceRecord.kebele_id,
    woreda: props.employeeInsuranceRecord.woreda,
    region: props.employeeInsuranceRecord.region,
    federal_id: props.employeeInsuranceRecord.federal_id,
    ministry_department: props.employeeInsuranceRecord.ministry_department,
    employee_id_number: props.employeeInsuranceRecord.employee_id_number,
    verified: props.employeeInsuranceRecord.verified,
});

const submit = () => {
    form.put(route('admin.employee-insurance-records.update', props.employeeInsuranceRecord.id));
};
</script>
