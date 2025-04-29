import type { PageProps } from '@inertiajs/core';
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

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
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

export interface Donation {
    id: number;
    amount: number;
    created_at: string | null;
    donor: User;
}

export interface Campaign {
    id: number;
    title: string;
    description: string | null;
    goal_amount: number;
    created_at: string | null;
    creator: User;
    donations: Donation[] | null;
}

export interface CampaignsResponse {
    data: Campaign[];
}

export type BreadcrumbItemType = BreadcrumbItem;
