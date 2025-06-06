<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>();

const page = usePage() as any;

// Mengambil data user yang sedang login dari berbagai kemungkinan guard
const currentUser = computed(() => page.props.auth.user); // Dari guard 'web'
const currentStudent = computed(() => page.props.auth.student); // Dari guard 'student'
const currentPartner = computed(() => page.props.auth.partner); // Dari guard 'partner'

const activeGuard = computed(() => page.props.auth.activeGuard);
</script>

<template>
    <header
        class="border-sidebar-border/70 flex h-16 shrink-0 items-center justify-between gap-4 border-b px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <div class="hidden items-center lg:flex">
            <div v-if="activeGuard === 'web' && currentUser" class="text-sm text-muted-foreground">
                Selamat datang, <span class="font-semibold text-foreground">{{ currentUser.name }}</span>!
            </div>
            <div v-else-if="activeGuard === 'student' && currentStudent" class="text-sm text-muted-foreground">
                Selamat datang, <span class="font-semibold text-foreground">{{ currentStudent.name }}</span>!
            </div>
            <div v-else-if="activeGuard === 'partner' && currentPartner" class="text-sm text-muted-foreground">
                Selamat datang, <span class="font-semibold text-foreground">{{ currentPartner.name }}</span>!
            </div>
        </div>
    </header>
</template>
