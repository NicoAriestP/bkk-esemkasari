import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
    student: User;
    partner: User;
    activeGuard: string;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    description?: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    tinymce: { api_key: string };
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface UserForm {
    name: string;
    email: string;
    phone?: string;
    password?: string;
}

export interface Partner {
    id: number;
    name: string;
    email: string;
    created_at: string;
    updated_at: string;
}

export interface VacancyType {
    id: number;
    title: string;
    description: string;
    due_at: string;
    location: string;
    file?: string;
    file_url?: string;
    applicants_count: number;
    qualified_applicants_count: number;
    created_at: string;
    updated_at: string;
    created_by?: Partner;
    updated_by?: Partner;
}

export type BreadcrumbItemType = BreadcrumbItem;
