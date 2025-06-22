<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';

// Import PrimeVue component
import Button from 'primevue/button';

// dayjs for date formatting
import dayjs from 'dayjs';
import 'dayjs/locale/id';

// Set locale to Indonesian
dayjs.locale('id');

// Define props received from the controller
const props = defineProps({
    model: {
        type: Object as () => any, // Using 'any' for flexibility, can be a specific Announcement type
        required: true,
    },
});

// Breadcrumbs for navigation, dynamically showing the announcement title
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pengumuman',
        href: route('announcements.student.index'),
    },
    {
        title: props.model.title,
        href: '#',
    },
];

/**
 * Navigates back to the announcement list page.
 */
const goBack = () => {
    router.get(route('announcements.student.index'));
};

</script>

<template>
    <!-- Set the document head title to the announcement's title -->
    <Head :title="props.model.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 lg:p-8 bg-white dark:bg-gray-800 rounded-lg shadow-md">

            <!-- Header Section -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                <!-- Back Button -->
                <Button
                    label="Kembali ke Daftar"
                    icon="pi pi-arrow-left"
                    @click="goBack"
                    text
                    class="mb-4"
                />

                <!-- Announcement Title -->
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                    {{ props.model.title }}
                </h1>

                <!-- Publication Date -->
                <p class="mt-2 text-base text-gray-500 dark:text-gray-400">
                    Dipublikasikan pada {{ dayjs(props.model.created_at).format('DD MMMM YYYY, HH:mm') }}
                </p>
            </div>

            <!-- Content Section -->
            <!-- Using v-html to render content from WYSIWYG editor -->
            <div
                class="prose max-w-none"
                v-html="props.model.content"
            >
            </div>

        </div>
    </AppLayout>
</template>
