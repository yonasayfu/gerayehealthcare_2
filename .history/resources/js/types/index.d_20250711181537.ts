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

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    roles: string[]; // Add roles property
    permissions: string[]; // Add permissions property
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface Staff {
    id: number;
    first_name: string;
    last_name: string;
    // Add other staff properties if needed
}

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
