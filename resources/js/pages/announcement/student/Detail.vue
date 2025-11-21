<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';

// Import PrimeVue component
import Button from 'primevue/button';

// dayjs for date formatting
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import 'dayjs/locale/id';

// Set locale to Indonesian and extend with relativeTime
dayjs.extend(relativeTime);
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

/**
 * Handles sharing functionality with fallback to clipboard
 */
const handleShare = async () => {
    const shareData = {
        title: props.model.title,
        url: window.location.href
    };

    try {
        if (navigator.share) {
            await navigator.share(shareData);
        } else if (navigator.clipboard) {
            await navigator.clipboard.writeText(window.location.href);
        }
    } catch (error) {
        console.log('Share/copy failed:', error);
    }
};

</script>

<template>
    <!-- Set the document head title to the announcement's title -->
    <Head :title="props.model.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Enhanced Header Section -->
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        📢 Detail Pengumuman
                    </h1>
                    <p class="mt-1.5 text-sm text-gray-500">
                        Informasi lengkap pengumuman dari Bursa Kerja Khusus
                    </p>
                </div>
                <Button
                    label="Kembali ke Daftar"
                    @click="goBack"
                    icon="pi pi-arrow-left"
                    class="shrink-0 bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-blue-700 transition-colors duration-200"
                    outlined
                />
            </div>
        </div>

        <!-- Enhanced Content Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Article Header -->
            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="pi pi-megaphone text-white text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-2xl font-bold text-gray-900 leading-tight mb-3 sm:text-3xl">
                            {{ props.model.title }}
                        </h1>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="pi pi-calendar text-blue-600 text-xs"></i>
                                </div>
                                <span>{{ dayjs(props.model.created_at).format('DD MMMM YYYY') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="pi pi-clock text-green-600 text-xs"></i>
                                </div>
                                <span>{{ dayjs(props.model.created_at).format('HH:mm') }} WIB</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="pi pi-user text-purple-600 text-xs"></i>
                                </div>
                                <span>{{ props.model.created_by?.name || 'Administrator' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- File Attachment Section (if exists) -->
            <div v-if="props.model.file_url" class="px-8 py-4 bg-amber-50 border-b border-amber-100">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="pi pi-paperclip text-amber-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-amber-900 mb-1">Lampiran Tersedia</h4>
                        <p class="text-xs text-amber-700 truncate" :title="props.model.file_url.split('/').pop()">
                            📄 {{ props.model.file_url.split('/').pop() }}
                        </p>
                    </div>
                    <a
                        :href="props.model.file_url"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors duration-200 flex-shrink-0"
                    >
                        <i class="pi pi-download text-xs"></i>
                        <span>Unduh</span>
                    </a>
                </div>
            </div>

            <!-- Enhanced Content Section -->
            <div class="px-8 py-8">
                <div class="max-w-none">
                    <!-- Content with proper styling preservation -->
                    <div
                        class="announcement-content prose prose-lg prose-blue max-w-none"
                        v-html="props.model.content"
                    >
                    </div>
                </div>
            </div>

            <!-- Enhanced Footer -->
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-200">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <i class="pi pi-info-circle"></i>
                        <span>Diposting {{ dayjs(props.model.created_at).fromNow() }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <Button
                            label="Bagikan"
                            icon="pi pi-share-alt"
                            @click="handleShare"
                            class="text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors duration-200"
                            text
                            size="small"
                        />
                        <!-- <Button
                            label="Kembali"
                            @click="goBack"
                            icon="pi pi-arrow-left"
                            class="!bg-blue-600 hover:!bg-blue-700 border-blue-600 hover:border-blue-700 transition-colors duration-200"
                            size="small"
                        /> -->
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Enhanced content styling to preserve TinyMCE editor formatting */
.announcement-content {
    line-height: 1.75;
    color: #374151;
}

.announcement-content h1 {
    font-size: 1.875rem;
    font-weight: 700;
    color: #111827;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.announcement-content h1:first-child {
    margin-top: 0;
}

.announcement-content h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.announcement-content h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
}

.announcement-content h4 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
}

.announcement-content h5 {
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
    margin-top: 0.75rem;
    margin-bottom: 0.5rem;
}

.announcement-content h6 {
    font-size: 0.875rem;
    font-weight: 600;
    color: #111827;
    margin-top: 0.5rem;
    margin-bottom: 0.25rem;
}

.announcement-content p {
    margin-bottom: 1rem;
    color: #374151;
    line-height: 1.625;
}

.announcement-content p:last-child {
    margin-bottom: 0;
}

/* Lists */
.announcement-content ul {
    list-style-type: disc;
    list-style-position: inside;
    margin-bottom: 1rem;
}

.announcement-content ul li {
    margin-bottom: 0.25rem;
}

.announcement-content ol {
    list-style-type: decimal;
    list-style-position: inside;
    margin-bottom: 1rem;
}

.announcement-content ol li {
    margin-bottom: 0.25rem;
}

.announcement-content li {
    color: #374151;
    line-height: 1.625;
    margin-left: 1rem;
}

.announcement-content ul ul,
.announcement-content ol ol {
    margin-top: 0.5rem;
    margin-bottom: 0;
    margin-left: 1.5rem;
}

/* Links */
.announcement-content a {
    color: #2563eb;
    text-decoration: underline;
    transition: color 0.2s ease-in-out;
}

.announcement-content a:hover {
    color: #1d4ed8;
}

/* Images */
.announcement-content img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    margin: 1rem 0;
}

/* Blockquotes */
.announcement-content blockquote {
    border-left: 4px solid #3b82f6;
    padding-left: 1rem;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    margin: 1rem 0;
    background-color: #eff6ff;
    font-style: italic;
    color: #374151;
}

/* Code */
.announcement-content code {
    background-color: #f3f4f6;
    color: #1f2937;
    padding: 0.125rem 0.25rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
}

.announcement-content pre {
    background-color: #111827;
    color: #f9fafb;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin: 1rem 0;
}

.announcement-content pre code {
    background-color: transparent;
    color: #f9fafb;
    padding: 0;
}

/* Tables */
.announcement-content table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #d1d5db;
    margin: 1rem 0;
}

.announcement-content th {
    background-color: #f9fafb;
    border: 1px solid #d1d5db;
    padding: 0.5rem 1rem;
    text-align: left;
    font-weight: 600;
    color: #111827;
}

.announcement-content td {
    border: 1px solid #d1d5db;
    padding: 0.5rem 1rem;
    color: #374151;
}

/* Horizontal rule */
.announcement-content hr {
    border: 0;
    border-top: 1px solid #d1d5db;
    margin: 1.5rem 0;
}

/* Strong and emphasis */
.announcement-content strong {
    font-weight: 700;
    color: #111827;
}

.announcement-content em {
    font-style: italic;
}

/* Text alignment classes from TinyMCE */
.announcement-content .text-left {
    text-align: left;
}

.announcement-content .text-center {
    text-align: center;
}

.announcement-content .text-right {
    text-align: right;
}

.announcement-content .text-justify {
    text-align: justify;
}

/* Preserve spacing and formatting */
.announcement-content br {
    display: block;
    content: "";
    margin-top: 0.5rem;
}

/* Responsive images and media */
.announcement-content img,
.announcement-content video,
.announcement-content iframe {
    max-width: 100%;
    height: auto;
}

/* Fix for nested content spacing */
.announcement-content > *:first-child {
    margin-top: 0;
}

.announcement-content > *:last-child {
    margin-bottom: 0;
}
</style>
