<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

// Import komponen PrimeVue
import Avatar from 'primevue/avatar';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';

// dayjs untuk format tanggal
import dayjs from 'dayjs';
import 'dayjs/locale/id';
dayjs.locale('id');

// Props untuk menerima data lowongan dari controller
const props = defineProps({
    model: {
        type: Object as () => any,
        required: true,
    },
});

// State untuk mengontrol dialog konfirmasi
const isConfirmDialogVisible = ref(false);

// Breadcrumbs dinamis
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Lowongan Kerja',
        href: route('students.vacancies.index'),
    },
    {
        title: `${props.model.title} - ${props.model.created_by?.name}`,
        href: '#',
    },
];

// Fungsi untuk menentukan warna tenggat waktu
const getDeadlineInfo = (dueDate: string) => {
    const now = dayjs();
    const deadline = dayjs(dueDate);
    const daysDiff = deadline.diff(now, 'day');

    if (daysDiff < 0) {
        return { text: 'Telah Berakhir', class: 'text-red-600 dark:text-red-500' };
    }
    return {
        text: deadline.format('DD MMM YYYY'),
        class: daysDiff <= 7 ? 'text-amber-600 dark:text-amber-500' : 'text-gray-600 dark:text-gray-400',
    };
};

// Fungsi untuk menentukan ikon file lampiran
const getFileIcon = (url: string) => {
    if (!url) return 'pi pi-file';
    if (url.endsWith('.pdf')) return 'pi pi-file-pdf';
    if (url.endsWith('.jpg') || url.endsWith('.jpeg') || url.endsWith('.png')) return 'pi pi-image';
    return 'pi pi-file';
};

// Fungsi untuk membuka dialog konfirmasi
const openConfirmDialog = () => {
    isConfirmDialogVisible.value = true;
};

// Fungsi untuk menangani aksi lamar pekerjaan
const handleApplyJob = () => {
    // Di sini Anda akan menambahkan logika untuk mengirim data lamaran ke backend
    console.log(`Melamar untuk lowongan ID: ${props.model.id}`);
    isConfirmDialogVisible.value = false;
    // Contoh: router.post(route('vacancies.apply', props.model.id), { ... });
};
</script>

<template>
    <Head :title="`${props.model.title} - ${props.model.created_by?.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Kolom Utama: Deskripsi Pekerjaan -->
                <div class="rounded-lg bg-white p-6 shadow md:p-8 lg:col-span-2 dark:bg-gray-800">
                    <!-- Header Lowongan -->
                    <div class="mb-6 flex items-start gap-4 border-b pb-6 dark:border-gray-700">
                        <Avatar :label="props.model.created_by?.name.charAt(0) || 'M'" size="xlarge" shape="circle" />
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ props.model.title }}</h1>
                            <p class="mt-1 text-lg text-gray-600 dark:text-gray-300">{{ props.model.created_by?.name }}</p>
                        </div>
                    </div>

                    <!-- Deskripsi dari WYSIWYG Editor -->
                    <h2 class="mb-4 text-xl font-semibold">Deskripsi Pekerjaan</h2>
                    <div class="prose prose-lg dark:prose-invert max-w-none" v-html="props.model.description"></div>
                </div>

                <!-- Kolom Samping: Info & Aksi -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 space-y-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                        <h2 class="border-b pb-3 text-xl font-semibold dark:border-gray-700">Detail Lowongan</h2>

                        <div class="space-y-4 text-sm">
                            <div class="flex items-center gap-3">
                                <i class="pi pi-map-marker text-xl text-gray-500 dark:text-gray-300"></i>
                                <div>
                                    <p class="font-medium text-gray-500 dark:text-gray-300">Lokasi</p>
                                    <p class="font-semibold">{{ props.model.location }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="pi pi-calendar-times text-xl text-gray-500 dark:text-gray-300"></i>
                                <div>
                                    <p class="font-medium text-gray-500 dark:text-gray-300">Tenggat Waktu</p>
                                    <span :class="['font-semibold', getDeadlineInfo(props.model.due_at).class]">{{
                                        getDeadlineInfo(props.model.due_at).text
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Lampiran -->
                        <div v-if="props.model.file_url">
                            <a :href="props.model.file_url" download target="_blank" class="w-full">
                                <Button label="Unduh Lampiran" :icon="getFileIcon(props.model.file)" outlined class="w-full" />
                            </a>
                        </div>

                        <!-- Tombol Lamar Pekerjaan -->
                        <div>
                            <Button label="Lamar Pekerjaan" icon="pi pi-send" class="w-full" @click="openConfirmDialog" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dialog Konfirmasi Lamar Pekerjaan -->
        <Dialog v-model:visible="isConfirmDialogVisible" modal header="Konfirmasi Lamaran Pekerjaan" :style="{ width: '25rem' }">
            <div class="flex items-start gap-4">
                <i class="pi pi-exclamation-triangle text-3xl text-amber-500"></i>
                <div>
                    <p class="font-semibold">Apakah Anda yakin ingin melamar pekerjaan ini?</p>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        Dengan melanjutkan, informasi pribadi dan CV Anda akan dibagikan kepada pihak
                        <span class="font-bold">{{ props.model.created_by?.name }}</span
                        >.
                    </p>
                </div>
            </div>
            <template #footer>
                <Button label="Batal" text severity="secondary" @click="isConfirmDialogVisible = false" />
                <Button label="Ya, Lamar" severity="info" @click="handleApplyJob" autofocus />
            </template>
        </Dialog>
    </AppLayout>
</template>

<style scoped>
.prose-content ul {
    list-style-type: disc !important;
    margin-left: 1.5rem !important;
}

.prose-content ol {
    list-style-type: decimal !important;
    margin-left: 1.5rem !important;
}

.prose-content li {
    margin-bottom: 0.25rem !important;
}
</style>
