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
import Tag from 'primevue/tag';

// dayjs
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import 'dayjs/locale/id';

dayjs.extend(relativeTime);
dayjs.locale('id');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kuesioner',
        href: route('students.questionnaires.index'),
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

// Watcher untuk memuat ulang data saat props berubah
watch(() => [props.models, props.totalRecords], ([newModels, newTotalRecords]) => {
    loadedItems.value = Array.from({ length: newTotalRecords as number });
    loadedItems.value.splice(0, (newModels as any[]).length, ...(newModels as any[]));
}, {
    immediate: true,
    deep: true,
});

/**
 * Fungsi untuk lazy load saat scroll.
 */
const onLazyLoad = async (event: { first: number }) => {
    if (loading.value || loadedItems.value[event.first]) {
        return;
    }
    loading.value = true;
    try {
        const response = await axios.get(route('students.questionnaires.index'), {
            params: {
                lazy_load: true,
                first: event.first,
                search: searchFilter.value,
            }
        });
        const newItems = response.data.models;
        loadedItems.value.splice(event.first, newItems.length, ...newItems);
    } catch (error) {
        console.error("Gagal memuat data kuesioner:", error);
    } finally {
        loading.value = false;
    }
};

/**
 * Navigasi ke halaman detail.
 */
const navigateToDetail = (id: number) => {
    router.get(route('students.questionnaires.show', id));
};

/**
 * Fungsi untuk format deadline status
 */
const getDeadlineStatus = (dueAt: string | null) => {
    if (!dueAt) return { label: 'Tidak Ada Batas', severity: 'info' };

    const now = dayjs();
    const deadline = dayjs(dueAt);
    const daysLeft = deadline.diff(now, 'day');

    if (daysLeft < 0) {
        return { label: 'Sudah Lewat', severity: 'danger' };
    } else if (daysLeft === 0) {
        return { label: 'Hari Ini', severity: 'warning' };
    } else if (daysLeft <= 3) {
        return { label: `${daysLeft} Hari Lagi`, severity: 'warning' };
    } else if (daysLeft <= 7) {
        return { label: `${daysLeft} Hari Lagi`, severity: 'success' };
    } else {
        return { label: dayjs(dueAt).format('DD MMM YYYY'), severity: 'info' };
    }
};

// Watcher untuk input pencarian.
watch(searchFilter, (newValue) => {
    router.get(route("students.questionnaires.index"), { search: newValue }, {
        preserveState: true,
        preserveScroll: false,
        replace: true,
    });
});
</script>

<template>
    <Head title="Daftar Kuesioner" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Enhanced Header Section -->
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        📋 Kuesioner Tersedia
                    </h1>
                    <p class="mt-1.5 text-sm text-gray-500">
                        Isi kuesioner yang tersedia untuk membantu meningkatkan layanan BKK
                    </p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600 bg-purple-50 px-3 py-2 rounded-lg">
                    <i class="pi pi-info-circle text-purple-600"></i>
                    <span>{{ props.totalRecords }} kuesioner tersedia</span>
                </div>
            </div>
        </div>

        <!-- Enhanced Content Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Search Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-indigo-50">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative flex-1 max-w-md">
                        <InputText
                            v-model="searchFilter"
                            placeholder="Cari kuesioner berdasarkan judul..."
                            class="pl-10 w-full bg-white border-gray-300 focus:border-purple-500 focus:ring-purple-500 rounded-lg text-sm shadow-sm"
                        />
                    </div>
                </div>
            </div>

            <!-- Enhanced Questionnaire List -->
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
                                    <div
                                        @click="navigateToDetail(item.id)"
                                        class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 hover:border-purple-200 cursor-pointer overflow-hidden"
                                    >
                                        <!-- Card Header -->
                                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50/50 to-transparent">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex items-start gap-3 flex-1">
                                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                                        <i class="pi pi-file-edit text-white text-sm"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-purple-700 transition-colors duration-200 line-clamp-2">
                                                            {{ item.title }}
                                                        </h3>
                                                        <div class="flex items-center gap-2 mt-2 flex-wrap">
                                                            <div class="flex items-center gap-1 text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                                                <i class="pi pi-calendar text-xs"></i>
                                                                <span>{{ dayjs(item.created_at).format('DD MMM YYYY') }}</span>
                                                            </div>
                                                            <Tag
                                                                v-if="item.due_at"
                                                                :value="getDeadlineStatus(item.due_at).label"
                                                                :severity="getDeadlineStatus(item.due_at).severity as any"
                                                                class="text-xs"
                                                            >
                                                                <template #icon>
                                                                    <i class="pi pi-clock mr-1"></i>
                                                                </template>
                                                            </Tag>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <i class="pi pi-angle-right text-gray-400 group-hover:text-purple-500 transition-colors duration-200"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card Content -->
                                        <div class="px-6 py-4">
                                            <p class="text-gray-600 leading-relaxed line-clamp-3">
                                                {{ item.description }}
                                            </p>
                                        </div>

                                        <!-- Card Footer -->
                                        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                                    <i class="pi pi-clock"></i>
                                                    <span>Dibuat {{ dayjs(item.created_at).fromNow() }}</span>
                                                </div>
                                                <Button
                                                    label="Isi Kuesioner"
                                                    icon="pi pi-arrow-right"
                                                    iconPos="right"
                                                    class="!bg-purple-600 hover:!bg-purple-700 border-purple-600 hover:border-purple-700 text-white text-sm px-4 py-2 transition-colors duration-200"
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
                                                    <Skeleton width="5rem" height="1rem" borderRadius="1rem" />
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
                        <div class="w-24 h-24 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center mb-6">
                            <i class="pi pi-inbox text-4xl text-purple-500"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Kuesioner</h3>
                        <p class="text-gray-500 max-w-md leading-relaxed">
                            Saat ini belum ada kuesioner yang tersedia untuk diisi. Silakan periksa kembali nanti atau ubah kata kunci pencarian Anda.
                        </p>
                        <div class="mt-6">
                            <Button
                                label="Refresh Halaman"
                                icon="pi pi-refresh"
                                @click="() => router.reload()"
                                class="!bg-purple-600 hover:!bg-purple-700 border-purple-600 hover:border-purple-700 text-white transition-colors duration-200"
                                outlined
                            />
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Line clamp utilities */
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    line-clamp: 2;
}

.line-clamp-3 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    line-clamp: 3;
}
</style>
