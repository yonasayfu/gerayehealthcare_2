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

import { PageProps, Errors } from '@inertiajs/vue3'; // Import PageProps and Errors from Inertia

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

export interface InertiaForm<T> {
    data(): T;
    isDirty: boolean;
    errors: Partial<Record<keyof T, string>>;
    hasErrors: boolean;
    processing: boolean;
    progress: number | null;
    wasSuccessful: boolean;
    recentlySuccessful: boolean;
    // Methods
    submit(method: string, url: string, options?: object): void;
    get(url: string, data?: object, options?: object): void;
    post(url: string, data?: object, options?: object): void;
    put(url: string, data?: object, options?: object): void;
    patch(url: string, data?: object, options?: object): void;
    delete(url: string, options?: object): void;
    reset(...fields: (keyof T)[]): void;
    clearErrors(...fields: (keyof T)[]): void;
    // Add index signature for properties not explicitly defined in T but present in form
    [key: string]: any;
}

export interface Staff {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
    position: string | null;
    department: string | null;
    status: 'Active' | 'Inactive';
    hire_date: string | null;
    photo: string | null;
    hourly_rate: number | null;
    created_at: string;
    updated_at: string;
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
    total: number;
}

export interface PatientForm {
    full_name: string | null;
    fayda_id: string | null;
    date_of_birth: string | null;
    gender: string | null;
    address: string | null;
    phone_number: string | null;
    email: string | null;
    source: string | null;
    emergency_contact: string | null;
    geolocation: string | null;
}

export interface InventoryItem {
    id: number;
    name: string;
    description: string | null;
    item_category: string | null;
    item_type: string | null;
    serial_number: string | null;
    purchase_date: string | null;
    warranty_expiry: string | null;
    supplier_id: number | null;
    assigned_to_type: string | null;
    assigned_to_id: number | null;
    last_maintenance_date: string | null;
    next_maintenance_due: string | null;
    maintenance_schedule: string | null;
    notes: string | null;
    status: string;
    created_at: string;
    updated_at: string;
}

export interface InventoryItemPaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface InventoryItemPagination {
    current_page: number;
    data: InventoryItem[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: InventoryItemPaginationLink[];
    next_page_url: string | null;
    path: string;
