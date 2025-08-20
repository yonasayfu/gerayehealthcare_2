/// <reference path="./ziggy.d.ts" />

declare global {
    interface Window {
        Alpine: any; // Declare Alpine on the Window interface
    }
}

interface BreadcrumbItem {
    title: string;
    href: string;
}

interface Patient {
    id: number;
    full_name: string;
    patient_code: string | null;
    fayda_id: string | null;
    age: number | null;
    gender: string | null;
    phone_number: string | null;
    source: string | null;
    // Add other patient properties as needed
}

interface PatientPagination {
    data: Patient[];
    current_page: number;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

interface Invoice {
    id: number;
    invoice_number: string;
    grand_total: number;
    patient?: Patient;
}

interface InsuranceClaim {
    id: number;
    claim_status: string;
    coverage_amount: number | null;
    paid_amount: number | null;
    submitted_at: string | null;
    processed_at: string | null;
    payment_due_date: string | null;
    payment_received_at: string | null;
    payment_method: string | null;
    reimbursement_required: boolean;
    receipt_number: string | null;
    is_pre_authorized: boolean;
    pre_authorization_code: string | null;
    email_status: string | null;
    email_sent_at: string | null;
    invoice?: Invoice;
}

interface InsuranceClaimPagination {
    data: InsuranceClaim[];
    current_page: number;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export { BreadcrumbItem, Patient, PatientPagination, EthiopianCalendarDay, EthiopianCalendarDayPagination, InsuranceClaim, InsuranceClaimPagination };

interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    two_factor_secret: string | null;
    two_factor_recovery_codes: string | null;
    created_at: string | null;
    updated_at: string | null;
    staff?: Staff; // Add staff relationship
}

interface Staff {
    id: number;
    first_name: string;
    last_name: string;
    phone_number: string;
    role: string;
    hourly_rate: number;
}

interface EthiopianCalendarDay {
    id: number;
    gregorian_date: string;
    ethiopian_date: string;
    description: string | null;
    is_holiday: boolean;
    region: string | null;
}

import { type Errors, type Form as InertiaFormType } from '@inertiajs/vue3';

interface InertiaForm<T> extends InertiaFormType<T> {
    errors: Errors & { [key: string]: string };
}

interface EthiopianCalendarDayPagination {
    data: EthiopianCalendarDay[];
    current_page: number;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
