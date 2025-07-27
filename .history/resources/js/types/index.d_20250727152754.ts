/// <reference path="./ziggy.d.ts" />

export {};

interface BreadcrumbItem {
    title: string;
    href: string;
}

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
