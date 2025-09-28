<template>
    <AppLayout title="Create Employee Insurance Record">
        <div class="space-y-6 p-6">

            <div class="liquidGlass-wrapper relative overflow-hidden rounded-xl p-5">
                <div class="absolute inset-0 pointer-events-none bg-gradient-to-br from-white/10 to-white/5"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create Employee Insurance Record</h1>
                        <p class="text-sm text-muted-foreground">Fill the form and save.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    </form>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 rounded-b">
                <div class="flex items-center justify-end flex-wrap gap-2">
                    <Link :href="route('admin.employee-insurance-records.index')" class="btn-glass btn-glass-sm">Cancel</Link>
                    <button type="submit" @click="submit" :disabled="form.processing" class="btn-glass btn-glass-sm">
                        {{ form.processing ? 'Creating...' : 'Create Record' }}
                    </button>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    patients: Array,
    insurancePolicies: Array,
});

const form = useForm({
    patient_id: '',
    policy_id: '',
    kebele_id: '',
    woreda: '',
    region: '',
    federal_id: '',
    ministry_department: '',
    employee_id_number: '',
    verified: false,
});

const submit = () => {
    form.post(route('admin.employee-insurance-records.store'));
};
</script>
