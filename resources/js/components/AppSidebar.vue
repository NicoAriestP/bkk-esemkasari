<script setup lang="ts">
// import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { type SharedData } from '@/types';
// import { BookOpen, Folder, LayoutGrid } from 'lucide-vue-next';
import { Briefcase, FileText, GraduationCap, LayoutDashboard, Megaphone, ShieldCheck, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage<SharedData>();
// const user = page.props.auth.user ?? page.props.auth.student ?? page.props.auth.partner as User;
const isStaff = !!page.props.auth.user;
const isPartner = !!page.props.auth.partner;
const isStudent = !!page.props.auth.student;

// Menu yang tersedia untuk semua pengguna
const commonNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/',
        icon: LayoutDashboard,
    },
];

// Menu khusus untuk staff
const staffNavItems: NavItem[] = [
    {
        title: 'Pengumuman',
        href: '/announcements',
        icon: Megaphone,
    },
    {
        title: 'Kuisioner',
        href: '/questionnaires',
        icon: FileText,
    },
    {
        title: 'Siswa',
        href: '/years',
        icon: Users,
    },
    {
        title: 'Mitra DU/DI',
        href: '/partners',
        icon: Briefcase,
    },
    {
        title: 'Karyawan',
        href: '/users',
        icon: ShieldCheck,
    },
];

const partnerNavItems: NavItem[] = [
    {
        title: 'Lowongan Kerja',
        description: 'Halaman CRUD Lowongan Kerja untuk Mitra DU/DI',
        href: '/partners/vacancies',
        icon: Briefcase,
    },
];

const studentNavItems: NavItem[] = [
    {
        title: 'Tracer Study',
        description: 'Halaman Tracer Study untuk Siswa',
        href: '/tracer-study',
        icon: GraduationCap,
    },
    {
        title: 'Pengumuman',
        description: 'Halaman Pengumuman untuk Siswa',
        href: '/announcements/student',
        icon: Megaphone,
    },
    {
        title: 'Lowongan Kerja',
        description: 'Halaman Lowongan Kerja untuk Siswa',
        href: '/students/vacancies',
        icon: Briefcase,
    },
];

// Gabungkan menu berdasarkan role user
const mainNavItems = isStaff
    ? [...commonNavItems, ...staffNavItems]
    : isPartner
    ? [...commonNavItems, ...partnerNavItems]
    : isStudent
    ? [...commonNavItems, ...studentNavItems]
    : commonNavItems;

// const footerNavItems: NavItem[] = [
//     {
//         title: 'Github Repo',
//         href: 'https://github.com/laravel/vue-starter-kit',
//         icon: Folder,
//     },
//     {
//         title: 'Documentation',
//         href: 'https://laravel.com/docs/starter-kits',
//         icon: BookOpen,
//     },
// ];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <!-- <NavFooter :items="footerNavItems" /> -->
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
