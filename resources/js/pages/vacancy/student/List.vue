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
        <!-- Hero Header Section -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-8 mb-8 border border-blue-100">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-full text-sm font-medium mb-4">
                    <i class="pi pi-briefcase"></i>
                    <span>Karir Center SMKN Purwosari</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-3">
                    Temukan Peluang Karir Terbaik
                </h1>
                <p class="text-lg text-gray-600 mb-6 max-w-2xl mx-auto">
                    Jelajahi lowongan kerja dari mitra industri terpercaya dan mulai perjalanan karir profesional Anda
                </p>

                <!-- Enhanced Search Section -->
                <div class="max-w-md mx-auto relative">
                    <!-- Icon search dihide sementara karena menutupi placeholder input -->
                    <!-- <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="pi pi-search text-gray-400"></i>
                    </div> -->
                    <InputText
                        v-model="searchFilter"
                        placeholder="Cari posisi, perusahaan, atau lokasi..."
                        class="w-full pl-12 pr-4 py-3 text-base rounded-xl border-0 shadow-lg focus:shadow-xl transition-shadow duration-300 bg-white"
                    />
                </div>

                <!-- Stats Section -->
                <div class="flex items-center justify-center gap-6 mt-6 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <i class="pi pi-chart-line text-blue-600"></i>
                        <span>{{ totalRecords }} Lowongan Tersedia</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Content Header -->
            <div class="border-b border-gray-100 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Lowongan Tersedia</h2>
                        <p class="text-sm text-gray-500 mt-1">{{ loadedItems.length }} lowongan dimuat dari {{ totalRecords }} lowongan tersedia</p>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <i class="pi pi-sort-alt"></i>
                        <span>Terbaru</span>
                    </div>
                </div>
            </div>

            <!-- Job Cards Grid -->
            <template v-if="loadedItems.length > 0">
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <Card
                            v-for="item in loadedItems"
                            :key="item.id"
                            class="group relative overflow-hidden cursor-pointer !h-full transition-all duration-300 hover:shadow-xl hover:-translate-y-1 !border-gray-200 hover:!border-blue-300"
                            @click="openDetailPage(item.id)"
                        >
                            <!-- Premium Badge -->
                            <div class="absolute top-4 right-4 z-10">
                                <div class="bg-gradient-to-r from-orange-400 to-pink-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                    <i class="pi pi-star-fill mr-1"></i>
                                    Featured
                                </div>
                            </div>

                            <!-- Company Header -->
                            <template #header>
                                <div class="relative bg-gradient-to-br from-gray-50 to-blue-50 p-6 border-b border-gray-100">
                                    <!-- Background Pattern -->
                                    <div class="absolute inset-0 opacity-5">
                                        <div class="absolute top-0 right-0 w-20 h-20 bg-blue-500 rounded-full transform -translate-y-10 translate-x-10"></div>
                                        <div class="absolute bottom-0 left-0 w-16 h-16 bg-indigo-500 rounded-full transform translate-y-8 -translate-x-8"></div>
                                    </div>

                                    <div class="relative flex items-start gap-4">
                                        <div class="relative flex-shrink-0">
                                            <Avatar
                                                :label="item.created_by?.name.charAt(0) || 'P'"
                                                size="xlarge"
                                                shape="circle"
                                                class="!bg-gradient-to-br !from-blue-500 !to-indigo-600 !text-white shadow-lg ring-4 ring-white"
                                            />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-xl font-bold text-gray-900 line-clamp-2 group-hover:text-blue-600 transition-colors duration-300 leading-tight">
                                                {{ item.title }}
                                            </h3>
                                            <p class="font-semibold text-gray-600 mt-1 line-clamp-1">
                                                {{ item.created_by?.name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Job Details -->
                            <template #content>
                                <div class="px-6 py-5 space-y-4">
                                    <!-- Location -->
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="pi pi-map-marker text-blue-600"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <span class="font-medium text-gray-900 block truncate">{{ item.location }}</span>
                                        </div>
                                    </div>

                                    <!-- Applicants Count -->
                                    <div class="flex justify-start">
                                        <div class="bg-purple-50 text-purple-700 px-3 py-1 rounded-full text-xs font-medium">
                                            <i class="pi pi-users mr-1"></i>
                                            {{ item.applicants_count || 0 }} Pelamar
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Card Footer -->
                            <template #footer>
                                <div class="px-6 pb-6 mt-auto">
                                    <div class="border-t border-gray-100 pt-4 space-y-4">
                                        <!-- Dates Information -->
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center gap-2 text-gray-500">
                                                <i class="pi pi-calendar text-xs"></i>
                                                <span>Ditayangkan {{ dayjs(item.created_at).fromNow() }}</span>
                                            </div>
                                            <div class="flex items-center gap-2" :class="getDeadlineInfo(item.due_at).class.replace('dark:text-red-500', '').replace('dark:text-amber-500', '').replace('dark:text-gray-400', '')">
                                                <i class="pi pi-clock text-xs"></i>
                                                <span class="font-semibold text-xs">{{ getDeadlineInfo(item.due_at).text }}</span>
                                            </div>
                                        </div>

                                        <!-- Action Button -->
                                        <Button
                                            label="Lihat Detail & Lamar"
                                            icon="pi pi-arrow-right"
                                            icon-pos="right"
                                            class="w-full !bg-gradient-to-r !from-blue-600 !to-indigo-600 hover:!from-blue-700 hover:!to-indigo-700 !border-0 !font-semibold !py-3 shadow-lg hover:shadow-xl transition-all duration-300"
                                            @click="openDetailPage(item.id)"
                                        />
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>

                <!-- Enhanced Loading Trigger & Skeletons -->
                <div ref="loaderTrigger" class="h-4"></div>
                <div v-if="loading" class="px-6 pb-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="n in 6" :key="`skel-${n}`" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                            <div class="flex items-center gap-4 mb-4">
                                <Skeleton shape="circle" size="4rem"></Skeleton>
                                <div class="flex-1 space-y-2">
                                    <Skeleton width="100%" height="1.5rem"></Skeleton>
                                    <Skeleton width="70%" height="1rem"></Skeleton>
                                    <Skeleton width="50%" height="0.875rem"></Skeleton>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <Skeleton width="100%" height="1rem"></Skeleton>
                                <div class="flex gap-2">
                                    <Skeleton width="5rem" height="1.5rem" borderRadius="1rem"></Skeleton>
                                    <Skeleton width="6rem" height="1.5rem" borderRadius="1rem"></Skeleton>
                                </div>
                                <div class="pt-4 border-t border-gray-100">
                                    <Skeleton width="100%" height="2.5rem" borderRadius="0.5rem"></Skeleton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Enhanced Empty State -->
            <template v-else>
                <div class="p-12 text-center">
                    <!-- Animated Empty State -->
                    <div class="relative mb-8 inline-block">
                        <div class="w-32 h-32 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center">
                            <i class="pi pi-briefcase text-6xl text-blue-500"></i>
                        </div>
                        <!-- Floating Animation Elements -->
                        <div class="absolute top-2 right-4 w-4 h-4 bg-blue-300 rounded-full animate-pulse"></div>
                        <div class="absolute bottom-6 left-2 w-3 h-3 bg-indigo-300 rounded-full animate-pulse delay-300"></div>
                        <div class="absolute top-6 left-0 w-2 h-2 bg-purple-300 rounded-full animate-pulse delay-500"></div>
                    </div>

                    <!-- Empty State Content -->
                    <div class="max-w-md mx-auto space-y-4">
                        <h3 class="text-2xl font-bold text-gray-900">
                            Belum Ada Lowongan
                        </h3>
                        <p class="text-lg text-gray-600 leading-relaxed">
                            Saat ini tidak ada lowongan yang cocok dengan pencarian Anda.
                        </p>
                        <p class="text-sm text-gray-500">
                            Coba gunakan kata kunci yang berbeda atau periksa kembali nanti untuk lowongan terbaru.
                        </p>
                    </div>

                    <!-- Action Suggestions -->
                    <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                        <Button
                            label="Reset Pencarian"
                            icon="pi pi-refresh"
                            class="!bg-blue-600 hover:!bg-blue-700 !border-0 !px-6"
                            @click="searchFilter = ''"
                        />
                        <Button
                            label="Jelajahi Semua"
                            icon="pi pi-search"
                            outlined
                            class="!border-blue-600 !text-blue-600 hover:!bg-blue-50 !px-6"
                        />
                    </div>

                    <!-- Search Tips -->
                    <div class="mt-10 bg-gray-50 rounded-xl p-6 max-w-2xl mx-auto">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center justify-center gap-2">
                            <i class="pi pi-lightbulb text-yellow-500"></i>
                            Tips Pencarian Efektif
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-check-circle text-green-500"></i>
                                <span>Gunakan kata kunci spesifik</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="pi pi-check-circle text-green-500"></i>
                                <span>Coba variasi nama posisi</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="pi pi-check-circle text-green-500"></i>
                                <span>Periksa ejaan kata kunci</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="pi pi-check-circle text-green-500"></i>
                                <span>Cari berdasarkan lokasi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
