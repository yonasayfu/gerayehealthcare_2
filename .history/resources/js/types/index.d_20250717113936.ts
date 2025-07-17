import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

import { PageProps } from '@inertiajs/vue3'; // Import PageProps from Inertia

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & PageProps & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    flash: {
        success?: string;
        error?: string;
    };
};

export interface Staff {
    id: number;
    first_name: string;
    last_name: string;
    position?: string; // Added position based on ChatModal.vue usage
    // Add other staff properties if needed
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    profile_photo_url?: string; // Added profile_photo_url
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    roles: string[]; // Add roles property
    permissions: string[]; // Add permissions property
    staff?: Staff; // Add staff relationship
}

export interface Message {
    id: number;
    sender_id: number;
    receiver_id: number;
    message: string;
    read_at: string | null;
    created_at: string;
    updated_at: string;
    sender?: User; // Optional sender relationship
    receiver?: User; // Optional receiver relationship
}

export type Conversation = User; // A conversation is essentially a User object

export type BreadcrumbItemType = BreadcrumbItem;

export interface LeaveRequest {
    id: number;
    staff_id: number;
    staff: Staff; // Assuming staff relationship is loaded
    start_date: string;
    end_date: string;
    reason: string | null;
    status: 'Pending' | 'Approved' | 'Denied';
    admin_notes: string | null;
    created_at: string;
    updated_at: string;
}

export interface LeaveRequestsPaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface LeaveRequestsPagination {
    current_page: number;
    data: LeaveRequest[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: LeaveRequestsPaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export interface Patient {
    id: number;
    full_name: string;
    patient_code: string | null;
    fayda_id: string | null;
    gender: string | null;
    date_of_birth: string | null;
    age: number | null;
    phone_number: string | null;
    email: string | null;
    emergency_contact: string | null;
    address: string | null;
    source: string | null;
    geolocation: string | null;
    registered_by_staff: Staff | null;
    registered_by_caregiver: User | null; // Assuming Caregiver might be a User
    created_at: string;
    updated_at: string;
}

export interface PatientPaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PatientPagination {
    current_page: number;
    data: Patient[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: PatientPaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
