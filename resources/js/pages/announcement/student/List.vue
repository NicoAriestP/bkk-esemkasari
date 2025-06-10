<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import axios from 'axios';

// Import PrimeVue components
import Panel from 'primevue/panel';
import VirtualScroller from 'primevue/virtualscroller';
import InputText from "primevue/inputtext";
import Button from 'primevue/button';
import Skeleton from 'primevue/skeleton';

// dayjs
import dayjs from 'dayjs';
import 'dayjs/locale/id';
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
// Watcher ini akan berjalan saat komponen pertama kali dimuat (immediate: true)
// DAN setiap kali `props.models` atau `props.totalRecords` berubah setelah pencarian.
watch(() => [props.models, props.totalRecords], ([newModels, newTotalRecords]) => {
    // Inisialisasi ulang state lokal `loadedItems` dengan data baru dari props.
    loadedItems.value = Array.from({ length: newTotalRecords });
    loadedItems.value.splice(0, newModels.length, ...newModels);
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
        <div class="p-4 sm:p-6 lg:p-8 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Daftar Pengumuman</h1>
                <p class="text-gray-500 dark:text-gray-400">Temukan informasi dan pengumuman terbaru di sini.</p>
                <div class="mt-4">
                    <span class="relative w-full">
                        <InputText v-model="searchFilter" placeholder="Cari berdasarkan judul pengumuman..." class="w-full" />
                    </span>
                </div>
            </div>

            <template v-if="loadedItems && loadedItems.length > 0">
                <VirtualScroller :items="loadedItems" :itemSize="180" lazy @lazy-load="onLazyLoad"
                    class="border border-gray-200 dark:border-gray-700 rounded-lg" style="height: calc(100vh - 320px);">

                    <template v-slot:item="{ item, options }">
                        <div class="p-2">
                            <Panel v-if="item" class="shadow-sm">
                                <template #header>
                                    <div class="flex items-center gap-2">
                                        <i class="pi pi-bell"></i>
                                        <span class="font-bold">{{ item.title }}</span>
                                    </div>
                                </template>
                                <template #icons>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ dayjs(item.created_at).format('DD MMMM YYYY') }}
                                    </span>
                                </template>
                                <div class="flex flex-col justify-between h-full">
                                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                                        {{ getSnippet(item.content) }}
                                    </p>
                                    <div class="flex justify-end mt-auto">
                                        <Button label="Baca Selengkapnya" icon="pi pi-arrow-right" icon-pos="right" text @click="navigateToDetail(item.id)" />
                                    </div>
                                </div>
                            </Panel>
                            <template v-else>
                                <slot name="loader" :options="options"></slot>
                            </template>
                        </div>
                    </template>

                    <template v-slot:loader="{ options }">
                         <div class="p-2">
                           <div class="flex flex-col p-4 border border-gray-200 dark:border-gray-700 rounded">
                                <div class="flex justify-between mb-4">
                                    <Skeleton width="60%" height="1.5rem" />
                                    <Skeleton width="25%" height="1rem" />
                                </div>
                                <Skeleton width="100%" height="4rem" />
                                <div class="flex justify-end mt-4">
                                    <Skeleton width="9rem" height="2rem" />
                                </div>
                           </div>
                        </div>
                    </template>
                </VirtualScroller>
            </template>

            <template v-else>
                 <div class="flex flex-col items-center justify-center text-center p-10 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg" style="height: calc(100vh - 320px);">
                    <i class="pi pi-inbox text-5xl text-gray-400 dark:text-gray-500 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200">Tidak Ada Pengumuman</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Saat ini tidak ada pengumuman yang cocok dengan pencarian Anda.</p>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
