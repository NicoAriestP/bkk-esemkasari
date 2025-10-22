<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, useForm, } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';

// Import komponen PrimeVue
import Avatar from 'primevue/avatar';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Toast from 'primevue/toast';
import Tag from 'primevue/tag'; // PERUBAHAN: Import komponen Tag
import dayjs from 'dayjs';
import 'dayjs/locale/id';
import relativeTime from 'dayjs/plugin/relativeTime';

// Set up dayjs properly
dayjs.extend(relativeTime);
dayjs.locale('id');

const toast = useToast();

// Props untuk menerima data lowongan dari controller
const props = defineProps({
    model: {
        type: Object as () => any,
        required: true,
    },
    // PERUBAHAN: Tambahkan prop baru untuk status lamaran
    applicationStatus: {
        type: String, // Contoh: 'applied', 'qualified', 'rejected', null
        default: null,
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
        title: `${props.model.title}`,
        href: '#',
    },
];

// PERUBAHAN: Fungsi untuk menentukan gaya Tag status
const getApplicationStatusInfo = (status: string | null) => {
    if (!status) return null;

    const statuses: Record<string, { text: string; severity: any; icon: string }> = {
        applied: { text: 'Telah Dilamar', severity: 'info', icon: 'pi pi-check' },
        qualified: { text: 'Lolos Seleksi', severity: 'success', icon: 'pi pi-check-circle' },
        rejected: { text: 'Tidak Lolos', severity: 'danger', icon: 'pi pi-times-circle' },
    };
    return statuses[status] || null;
};

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
const applyVacancy = () => {
    useForm({}).post(route('students.vacancies.apply', props.model.id), {
        errorBag: 'Lamar Pekerjaan',
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Sukses',
                detail: 'Berhasil melamar pekerjaan!',
                life: 5000,
            });
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Gagal',
                detail: 'Gagal melamar pekerjaan!',
                life: 5000,
            });
        },
    });
    isConfirmDialogVisible.value = false;
};
</script>

<template>
    <Head :title="`${props.model.title} - ${props.model.created_by?.name}`" />

    <Toast/>
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Hero Header Section -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-8 mb-8 border border-blue-100">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-start gap-6">
                    <!-- Company Avatar -->
                    <div class="relative flex-shrink-0">
                        <Avatar
                            :label="props.model.created_by?.name.charAt(0) || 'M'"
                            size="xlarge"
                            shape="circle"
                            class="!w-20 !h-20 !bg-gradient-to-br !from-blue-500 !to-indigo-600 !text-white shadow-xl ring-4 ring-white"
                        />
                        <!-- Verified Badge -->
                        <!-- <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-white flex items-center justify-center">
                            <i class="pi pi-check text-white text-sm"></i>
                        </div> -->
                    </div>

                    <!-- Job Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 flex-wrap mb-2">
                                    <h1 class="text-3xl font-bold text-gray-900 leading-tight">{{ props.model.title }}</h1>
                                    <Tag v-if="applicationStatus"
                                         :value="getApplicationStatusInfo(applicationStatus)?.text"
                                         :severity="getApplicationStatusInfo(applicationStatus)?.severity"
                                         :icon="getApplicationStatusInfo(applicationStatus)?.icon"
                                         rounded
                                         class="!text-sm !font-semibold"
                                    />
                                </div>
                                <p class="text-xl font-semibold text-gray-700 mb-4">{{ props.model.created_by?.name }}</p>

                                <!-- Quick Info -->
                                <div class="flex flex-wrap gap-4 text-sm">
                                    <div class="flex items-center gap-2 bg-white/60 backdrop-blur-sm px-3 py-2 rounded-lg">
                                        <i class="pi pi-map-marker text-blue-600"></i>
                                        <span class="font-medium text-gray-700">{{ props.model.location }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 bg-white/60 backdrop-blur-sm px-3 py-2 rounded-lg">
                                        <i class="pi pi-calendar text-blue-600"></i>
                                        <span class="font-medium text-gray-700">Ditayangkan {{ dayjs(props.model.created_at).fromNow() }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 bg-white/60 backdrop-blur-sm px-3 py-2 rounded-lg" :class="getDeadlineInfo(props.model.due_at).class.replace('text-red-600', 'text-red-700').replace('text-amber-600', 'text-amber-700').replace('text-gray-600', 'text-gray-700')">
                                        <i class="pi pi-clock text-blue-600"></i>
                                        <span class="font-medium">Berakhir {{ getDeadlineInfo(props.model.due_at).text }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Main Content: Job Description -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <!-- Content Header -->
                    <div class="border-b border-gray-100 px-8 py-6">
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                            <div class="w-2 h-8 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></div>
                            Deskripsi Pekerjaan
                        </h2>
                        <p class="text-gray-600 mt-2">Detail lengkap mengenai posisi dan persyaratan yang dibutuhkan</p>
                    </div>

                    <!-- Job Description Content -->
                    <div class="px-8 py-8">
                        <div class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-p:text-gray-700 prose-a:text-blue-600 prose-strong:text-gray-900" v-html="props.model.description"></div>
                    </div>
                </div>

                <!-- Additional Info Cards -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Company Info Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="pi pi-building text-blue-600"></i>
                            Tentang Perusahaan
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <Avatar
                                    :label="props.model.created_by?.name.charAt(0) || 'M'"
                                    size="large"
                                    shape="circle"
                                    class="!bg-gradient-to-br !from-blue-500 !to-indigo-600 !text-white"
                                />
                                <div>
                                    <p class="font-semibold text-gray-900">{{ props.model.created_by?.name }}</p>
                                    <p class="text-sm text-gray-600">Mitra Industri Terpercaya</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Application Stats Card -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="pi pi-chart-pie text-purple-600"></i>
                            Statistik Lamaran
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Total Pelamar</span>
                                <span class="font-bold text-gray-900">{{ props.model.applicants_count || 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Lolos Seleksi</span>
                                <span class="font-bold text-green-600">{{ props.model.qualified_applicants_count || 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Actions & Details -->
            <div class="lg:col-span-1">
                <div class="sticky top-8 space-y-6">
                    <!-- Action Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                                <i class="pi pi-send"></i>
                                Lamar Sekarang
                            </h3>
                            <p class="text-blue-100 text-sm mt-1">Jangan lewatkan kesempatan emas ini!</p>
                        </div>

                        <div class="px-6 py-6 space-y-4">
                            <!-- Application Button -->
                            <Button
                                label="Lamar Pekerjaan"
                                icon="pi pi-send"
                                class="w-full !bg-gradient-to-r !from-green-600 !to-emerald-600 hover:!from-green-700 hover:!to-emerald-700 !border-0 !font-semibold !py-3 shadow-lg hover:shadow-xl transition-all duration-300"
                                @click="openConfirmDialog"
                                :disabled="!!applicationStatus"
                            />

                            <!-- Download Attachment -->
                            <div v-if="props.model.file_url">
                                <a :href="props.model.file_url" download target="_blank" class="w-full block">
                                    <Button
                                        label="Unduh Lampiran"
                                        :icon="getFileIcon(props.model.file)"
                                        outlined
                                        class="w-full !border-blue-600 !text-blue-600 hover:!bg-blue-50"
                                    />
                                </a>
                            </div>

                            <!-- Status Info -->
                            <div v-if="applicationStatus" class="mt-4 p-4 bg-gray-50 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <i :class="getApplicationStatusInfo(applicationStatus)?.icon" class="text-lg"></i>
                                    <div>
                                        <p class="font-medium text-gray-900">Status Lamaran</p>
                                        <p class="text-sm text-gray-600">{{ getApplicationStatusInfo(applicationStatus)?.text }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Details Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="border-b border-gray-100 px-6 py-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                <i class="pi pi-info-circle text-blue-600"></i>
                                Informasi Lowongan
                            </h3>
                        </div>

                        <div class="px-6 py-6 space-y-6">
                            <!-- Location -->
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="pi pi-map-marker text-blue-600 text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-500 text-sm">Lokasi Kerja</p>
                                    <p class="font-semibold text-gray-900 mt-1">{{ props.model.location }}</p>
                                </div>
                            </div>

                            <!-- Deadline -->
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="pi pi-calendar-times text-red-600 text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-500 text-sm">Batas Lamaran</p>
                                    <p :class="['font-semibold mt-1', getDeadlineInfo(props.model.due_at).class.replace('dark:text-red-500', '').replace('dark:text-amber-500', '').replace('dark:text-gray-400', '')]">
                                        {{ getDeadlineInfo(props.model.due_at).text }}
                                    </p>
                                </div>
                            </div>

                            <!-- Posted Date -->
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="pi pi-calendar-plus text-green-600 text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-500 text-sm">Tanggal Posting</p>
                                    <p class="font-semibold text-gray-900 mt-1">{{ dayjs(props.model.created_at).format('DD MMM YYYY') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tips Card -->
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-2xl p-6 border border-yellow-200">
                        <h4 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="pi pi-lightbulb text-yellow-600"></i>
                            Tips Lamaran
                        </h4>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start gap-2">
                                <i class="pi pi-check-circle text-green-500 mt-0.5 flex-shrink-0"></i>
                                <span>Pastikan CV Anda sudah terbaru</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="pi pi-check-circle text-green-500 mt-0.5 flex-shrink-0"></i>
                                <span>Baca deskripsi pekerjaan dengan teliti</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="pi pi-check-circle text-green-500 mt-0.5 flex-shrink-0"></i>
                                <span>Lamar sebelum tenggat waktu</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Application Confirmation Dialog -->
        <Dialog
            class="w-full max-w-md mx-4"
            v-model:visible="isConfirmDialogVisible"
            modal
            :closable="false"
            :draggable="false"
        >
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                        <i class="pi pi-send text-blue-600 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Lamaran</h3>
                        <p class="text-sm text-gray-600">Pastikan data Anda sudah benar</p>
                    </div>
                </div>
            </template>

            <div class="space-y-6">
                <!-- Job Summary -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <Avatar
                            :label="props.model.created_by?.name.charAt(0) || 'M'"
                            size="large"
                            shape="circle"
                            class="!bg-gradient-to-br !from-blue-500 !to-indigo-600 !text-white"
                        />
                        <div>
                            <p class="font-semibold text-gray-900">{{ props.model.title }}</p>
                            <p class="text-sm text-gray-600">{{ props.model.created_by?.name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Warning Message -->
                <div class="flex items-start gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <i class="pi pi-info-circle text-amber-600 text-lg mt-0.5 flex-shrink-0"></i>
                    <div class="text-sm">
                        <p class="font-medium text-amber-800 mb-1">Informasi Penting</p>
                        <p class="text-amber-700">
                            Dengan melanjutkan, informasi pribadi dan CV Anda akan dibagikan kepada
                            <span class="font-semibold">{{ props.model.created_by?.name }}</span> untuk proses seleksi.
                        </p>
                    </div>
                </div>

                <!-- Confirmation Question -->
                <div class="text-center">
                    <p class="text-lg font-medium text-gray-900 mb-2">Yakin ingin melamar posisi ini?</p>
                    <p class="text-sm text-gray-600">Pastikan Anda telah membaca seluruh deskripsi pekerjaan</p>
                </div>
            </div>

            <template #footer>
                <div class="flex gap-3 w-full justify-end">
                    <Button
                        label="Batal"
                        text
                        severity="secondary"
                        @click="isConfirmDialogVisible = false"
                        class="!px-6"
                    />
                    <Button
                        label="Ya, Lamar Sekarang"
                        icon="pi pi-send"
                        class="!bg-gradient-to-r !from-green-600 !to-emerald-600 hover:!from-green-700 hover:!to-emerald-700 !border-0 !px-6"
                        @click="applyVacancy"
                        autofocus
                    />
                </div>
            </template>
        </Dialog>
    </AppLayout>
</template>
