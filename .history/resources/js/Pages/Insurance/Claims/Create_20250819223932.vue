<template>
    <AppLayout title="Create Insurance Claim">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Insurance Claim
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <Form
                            :form="form"
                            :patients="patients"
                            :invoices="invoices"
                            :insuranceCompanies="insuranceCompanies"
                            :insurancePolicies="insurancePolicies"
                        />

                        <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Create Claim
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Form from './Form.vue'
import type { BreadcrumbItem, InertiaForm, InsuranceClaim } from '@/types'

const props = defineProps<{
  patients: Array<any>;
  invoices: Array<any>;
  insuranceCompanies: Array<any>;
  insurancePolicies: Array<any>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Insurance Claims', href: route('admin.insurance-claims.index') },
  { title: 'Create', href: route('admin.insurance-claims.create') },
]

const form = useForm<InsuranceClaim>({
    patient_id: null,
    invoice_id: null,
    insurance_company_id: null,
    policy_id: null,
    claim_status: 'Submitted', // Default status
    coverage_amount: null,
    paid_amount: null,
    submitted_at: new Date().toISOString().slice(0,10), // Default to today
    processed_at: null,
    payment_due_date: null,
    payment_received_at: null,
    payment_method: null,
    reimbursement_required: false,
    receipt_number: null,
    is_pre_authorized: false,
    pre_authorization_code: null,
    denial_reason: null,
    translated_notes: null,
    email_status: null,
