<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import axios from 'axios';

// Import PrimeVue components
import VirtualScroller from 'primevue/virtualscroller';
import InputText from "primevue/inputtext";
import Button from 'primevue/button';
import Skeleton from 'primevue/skeleton';

// dayjs
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import 'dayjs/locale/id';

dayjs.extend(relativeTime);
dayjs.locale('id');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pengumuman',
        href: route('announcements.student.index'),
    },
];

const props = defineProps({
    models: {
        type: Array as () => any[],
        required: true,
    },
    totalRecords: {
        type: Number,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    }
});

const loadedItems = ref<any[]>([]);
const searchFilter = ref(props.filters.search || '');
const loading = ref(false);

// --- START PERBAIKAN ---

// Hapus inisialisasi `loadedItems` dari sini.
// const loadedItems.value = Array.from({ length: props.totalRecords });
// loadedItems.value.splice(0, props.models.length, ...props.models);

// Ganti dengan watcher ini.
// Watcher untuk memuat ulang data saat props berubah
watch(() => [props.models, props.totalRecords], ([newModels, newTotalRecords]) => {
    // Inisialisasi ulang state lokal `loadedItems` dengan data baru dari props.
    loadedItems.value = Array.from({ length: newTotalRecords as number });
    loadedItems.value.splice(0, (newModels as any[]).length, ...(newModels as any[]));
}, {
    immediate: true, // Menjalankan watcher ini langsung saat komponen dimuat
    deep: true,      // Memastikan perubahan dalam array/object terdeteksi
});

// --- AKHIR PERBAIKAN ---

/**
 * Fungsi untuk lazy load saat scroll.
 */
const onLazyLoad = async (event: { first: number }) => {
    if (loading.value || loadedItems.value[event.first]) {
        return;
    }
    loading.value = true;
    try {
        const response = await axios.get(route('announcements.student.index'), {
            params: {
                lazy_load: true,
                first: event.first,
                search: searchFilter.value,
            }
        });
        const newItems = response.data.models;
        loadedItems.value.splice(event.first, newItems.length, ...newItems);
    } catch (error) {
        console.error("Gagal memuat data pengumuman:", error);
    } finally {
        loading.value = false;
    }
};

/**
 * Navigasi ke halaman detail.
 */
const navigateToDetail = (id: number) => {
    router.get(route('announcements.student.detail', id));
};

/**
 * Fungsi untuk membuat snippet konten.
 */
const getSnippet = (content: string, length: number = 150): string => {
    if (!content) return '';
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = content;
    const text = tempDiv.textContent || tempDiv.innerText || "";
    if (text.length <= length) return text;
    return text.substring(0, length) + '...';
};

// Watcher untuk input pencarian.
watch(searchFilter, (newValue) => {
    router.get(route("announcements.student.index"), { search: newValue }, {
        preserveState: true,
        preserveScroll: false,
        replace: true,
    });
});

</script>

<template>
    <Head title="Daftar Pengumuman" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Enhanced Header Section -->
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        📢 Pengumuman Terbaru
                    </h1>
                    <p class="mt-1.5 text-sm text-gray-500">
                        Temukan informasi dan pengumuman penting dari Bursa Kerja Khusus
                    </p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600 bg-blue-50 px-3 py-2 rounded-lg">
                    <i class="pi pi-info-circle text-blue-600"></i>
                    <span>{{ props.totalRecords }} pengumuman tersedia</span>
                </div>
            </div>
        </div>

        <!-- Enhanced Content Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Search Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative flex-1 max-w-md">
                        <!-- <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="pi pi-search text-gray-400 text-sm"></i>
                        </div> -->
                        <InputText
                            v-model="searchFilter"
                            placeholder="Cari pengumuman berdasarkan judul..."
                            class="pl-10 w-full bg-white border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm shadow-sm"
                        />
                    </div>
                    <!-- <div class="flex items-center gap-2 text-sm text-blue-700 font-medium">
                        <i class="pi pi-filter"></i>
                        <span>Filter Aktif</span>
                    </div> -->
                </div>
            </div>

            <!-- Enhanced Announcement List -->
            <div class="p-6">
                <template v-if="loadedItems && loadedItems.length > 0">
                    <VirtualScroller
                        :items="loadedItems"
                        :itemSize="200"
                        lazy
                        @lazy-load="onLazyLoad"
                        class="border border-gray-100 rounded-xl bg-gray-50/30"
                        style="height: calc(100vh - 380px);"
                    >
                        <template v-slot:item="{ item, options }">
                            <div class="p-3">
                                <div v-if="item" class="group">
                                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 hover:border-blue-200 cursor-pointer overflow-hidden">
                                        <!-- Card Header -->
                                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50/50 to-transparent">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex items-start gap-3 flex-1">
                                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                                        <i class="pi pi-megaphone text-white text-sm"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-700 transition-colors duration-200 line-clamp-2">
                                                            {{ item.title }}
                                                        </h3>
                                                        <div class="flex items-center gap-2 mt-2">
                                                            <div class="flex items-center gap-1 text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                                                <i class="pi pi-calendar text-xs"></i>
                                                                <span>{{ dayjs(item.created_at).format('DD MMM YYYY') }}</span>
                                                            </div>
                                                            <!-- <div class="flex items-center gap-1 text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">
                                                                <i class="pi pi-check-circle text-xs"></i>
                                                                <span>Aktif</span>
                                                            </div> -->
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <i class="pi pi-angle-right text-gray-400 group-hover:text-blue-500 transition-colors duration-200"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card Content -->
                                        <div class="px-6 py-4">
                                            <p class="text-gray-600 leading-relaxed line-clamp-3">
                                                {{ getSnippet(item.content, 200) }}
                                            </p>
                                        </div>

                                        <!-- Card Footer -->
                                        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                                    <i class="pi pi-clock"></i>
                                                    <span>Diposting {{ dayjs(item.created_at).fromNow() }}</span>
                                                </div>
                                                <Button
                                                    label="Baca Selengkapnya"
                                                    icon="pi pi-arrow-right"
                                                    iconPos="right"
                                                    @click="navigateToDetail(item.id)"
                                                    class="!bg-blue-600 hover:!bg-blue-700 border-blue-600 hover:border-blue-700 text-white text-sm px-4 py-2 transition-colors duration-200"
                                                    size="small"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <template v-else>
                                    <slot name="loader" :options="options"></slot>
                                </template>
                            </div>
                        </template>

                        <template v-slot:loader>
                            <div class="p-3">
                                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                                    <!-- Skeleton Header -->
                                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                                        <div class="flex items-start gap-3">
                                            <Skeleton width="2.5rem" height="2.5rem" borderRadius="0.5rem" />
                                            <div class="flex-1 space-y-2">
                                                <Skeleton width="75%" height="1.25rem" />
                                                <div class="flex gap-2">
                                                    <Skeleton width="6rem" height="1rem" borderRadius="1rem" />
                                                    <Skeleton width="4rem" height="1rem" borderRadius="1rem" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Skeleton Content -->
                                    <div class="px-6 py-4 space-y-2">
                                        <Skeleton width="100%" height="0.875rem" />
                                        <Skeleton width="90%" height="0.875rem" />
                                        <Skeleton width="80%" height="0.875rem" />
                                    </div>
                                    <!-- Skeleton Footer -->
                                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                                        <div class="flex justify-between">
                                            <Skeleton width="8rem" height="0.75rem" />
                                            <Skeleton width="9rem" height="2rem" borderRadius="0.375rem" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </VirtualScroller>
                </template>

                <!-- Enhanced Empty State -->
                <template v-else>
                    <div class="flex flex-col items-center justify-center text-center py-16 px-6" style="height: calc(100vh - 380px);">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mb-6">
                            <i class="pi pi-inbox text-4xl text-blue-500"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Pengumuman</h3>
                        <p class="text-gray-500 max-w-md leading-relaxed">
                            Saat ini belum ada pengumuman yang tersedia. Silakan periksa kembali nanti atau ubah kata kunci pencarian Anda.
                        </p>
                        <div class="mt-6">
                            <Button
                                label="Refresh Halaman"
                                icon="pi pi-refresh"
                                @click="() => router.reload()"
                                class="!bg-blue-600 hover:!bg-blue-700 border-blue-600 hover:border-blue-700 text-white transition-colors duration-200"
                                outlined
                            />
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
