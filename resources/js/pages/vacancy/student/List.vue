<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import axios from 'axios';

// Import komponen PrimeVue yang relevan
import Card from 'primevue/card';
import InputText from "primevue/inputtext";
import Skeleton from 'primevue/skeleton';
import Avatar from 'primevue/avatar';
import Button from 'primevue/button';

// dayjs untuk format tanggal
import dayjs from 'dayjs';
import 'dayjs/locale/id';
import relativeTime from 'dayjs/plugin/relativeTime';

dayjs.locale('id');
dayjs.extend(relativeTime);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Lowongan Kerja',
        href: route('students.vacancies.index'),
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
    },
});

// State management
const loadedItems = ref<any[]>([...props.models]);
const searchFilter = ref(props.filters.search || '');
const loading = ref(false);
const allItemsLoaded = ref(loadedItems.value.length >= props.totalRecords);

// Ref untuk elemen pemicu di bagian bawah daftar
const loaderTrigger = ref<HTMLElement | null>(null);

// PERUBAHAN: Fungsi untuk menentukan warna tenggat waktu
const getDeadlineInfo = (dueDate: string) => {
    const now = dayjs();
    const deadline = dayjs(dueDate);
    const daysDiff = deadline.diff(now, 'day');

    if (daysDiff < 0) {
        return { text: 'Telah Berakhir', class: 'text-red-600 dark:text-red-500' };
    }
    if (daysDiff <= 7) {
        return { text: `Tenggat: ${deadline.format('DD MMM YYYY')}`, class: 'text-amber-600 dark:text-amber-500' };
    }
    return { text: `Tenggat: ${deadline.format('DD MMM YYYY')}`, class: 'text-gray-600 dark:text-gray-400' };
};

// Mengisi ulang data saat ada pencarian baru
watch(() => props.models, (newModels) => {
    loadedItems.value = [...newModels];
    allItemsLoaded.value = loadedItems.value.length >= props.totalRecords;
}, { deep: true });

const openDetailPage = (id: number) => {
    // Arahkan ke halaman detail lowongan
    router.get(route('students.vacancies.show', id)); // Pastikan nama route ini benar
};

// Fungsi untuk memuat data selanjutnya saat scroll
const loadMoreItems = async () => {
    if (loading.value || allItemsLoaded.value) return;

    loading.value = true;
    try {
        const response = await axios.get(route('students.vacancies.index'), {
            params: {
                lazy_load: true,
                first: loadedItems.value.length, // Offset berdasarkan jumlah item yang sudah ada
                search: searchFilter.value,
            },
        });
        const newItems = response.data.models;
        if (newItems.length > 0) {
            loadedItems.value.push(...newItems);
        }
        // Cek jika semua item sudah dimuat
        if (loadedItems.value.length >= props.totalRecords) {
            allItemsLoaded.value = true;
        }
    } catch (error) {
        console.error('Gagal memuat data lowongan:', error);
    } finally {
        loading.value = false;
    }
};

// Watcher untuk memicu pencarian
watch(searchFilter, (newValue) => {
    router.get(route("students.vacancies.index"), { search: newValue }, {
        preserveState: true,
        preserveScroll: false,
        replace: true,
    });
});

// Logika Intersection Observer untuk lazy loading
let observer: IntersectionObserver;
onMounted(() => {
    if (loaderTrigger.value) {
        observer = new IntersectionObserver(
            (entries) => {
                if (entries[0].isIntersecting) {
                    loadMoreItems();
                }
            }, { threshold: 0.1 }
        );
        observer.observe(loaderTrigger.value);
    }
});
onUnmounted(() => {
    if (observer) {
        observer.disconnect();
    }
});
</script>

<template>
    <Head title="Lowongan Kerja" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 lg:p-8 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <!-- Header Halaman -->
            <div class="mb-8">
                <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Daftar Lowongan Kerja</h1>
                        <p class="mt-1 text-gray-500 dark:text-gray-400">Cari dan lamar pekerjaan dari mitra dunia usaha & dunia industri.</p>
                    </div>
                </div>
                <div class="mt-6">
                    <span class="relative w-full">
                        <InputText v-model="searchFilter" placeholder="Cari posisi, perusahaan, atau lokasi..." class="w-full pl-10" />
                    </span>
                </div>
            </div>

            <!-- Layout Grid untuk Daftar Lowongan -->
            <template v-if="loadedItems.length > 0">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <Card
                        v-for="item in loadedItems"
                        :key="item.id"
                        class="flex cursor-pointer flex-col !h-full transition-shadow duration-300 hover:shadow-xl"
                        @click="openDetailPage(item.id)"
                    >
                        <template #header>
                            <div class="flex items-center gap-4 border-b p-4 dark:border-gray-700">
                                <Avatar :label="item.created_by?.name.charAt(0) || 'P'" size="large" shape="circle" />
                                <div>
                                    <h3 class="text-primary-600 dark:text-primary-400 text-lg font-bold line-clamp-1">{{ item.title }}</h3>
                                    <p class="font-medium text-gray-600 dark:text-gray-300 line-clamp-1">{{ item.created_by?.name }}</p>
                                </div>
                            </div>
                        </template>
                        <template #content>
                            <div class="space-y-3 text-sm text-gray-700 dark:text-gray-200">
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-map-marker text-gray-500"></i>
                                    <span>{{ item.location }}</span>
                                </div>
                            </div>
                        </template>
                        <template #footer>
                            <!-- PERUBAHAN: Penambahan tombol dan logika warna -->
                            <div class="space-y-4 pt-4 !mt-auto">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-500 dark:text-gray-400">
                                        Ditayangkan {{ dayjs(item.created_at).fromNow() }}
                                    </span>
                                    <span :class="['font-semibold', getDeadlineInfo(item.due_at).class]">
                                        {{ getDeadlineInfo(item.due_at).text }}
                                    </span>
                                </div>
                                <Button
                                    label="Lihat Detail"
                                    icon="pi pi-arrow-right"
                                    icon-pos="right"
                                    size="small"
                                    class="w-full"
                                    outlined
                                    @click="openDetailPage(item.id)"
                                />
                            </div>
                        </template>
                    </Card>
                </div>

                <!-- Indikator Loading & Pemicu Lazy Load -->
                <div ref="loaderTrigger" class="h-10"></div>
                <div v-if="loading" class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <Skeleton v-for="n in 4" :key="`skel-${n}`" height="200px" borderRadius="8px"></Skeleton>
                </div>
            </template>

            <!-- Tampilan jika tidak ada data -->
            <template v-else>
                <div class="flex flex-col items-center justify-center rounded-lg bg-white p-10 text-center shadow dark:bg-gray-800">
                    <i class="pi pi-inbox mb-4 text-5xl text-gray-400 dark:text-gray-500"></i>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200">Tidak Ada Lowongan</h3>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Saat ini tidak ada lowongan yang cocok dengan pencarian Anda.</p>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
