<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

import { BreadcrumbItem } from '@/types';

import Editor from '@tinymce/tinymce-vue';

import InputError from '@/components/InputError.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Toast from 'primevue/toast';
import DatePicker from 'primevue/datepicker';
import { watch } from 'vue';
import dayjs from 'dayjs';
import { ref } from 'vue';

const TINYMCE_API_KEY = (usePage().props as any).tinymce.api_key;

const toast = useToast();

const props = defineProps({
    model: Object,
    isNewRecord: {
        type: Boolean,
        required: true,
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Lowongan Kerja',
        href: '/partners/vacancies',
    },
    {
        title: props.isNewRecord ? 'Tambah Lowongan Kerja' : 'Ubah Lowongan Kerja',
        href: '#',
    },
];

const form = useForm({
    _method: props.isNewRecord ? 'POST' : 'PUT',
    title: props.model?.title,
    description: props.model?.description,
    location: props.model?.location,
    due_at: props.model?.due_at,
    file: null as File | null,
});

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    form.file = target.files?.[0] || null;
};

const getFileName = (path: string | File | null): string => {
    if (!path) return '';
    if (path instanceof File) return path.name;
    return path.split('/').pop() || path.split('\\').pop() || '';
};

// Create a reactive reference specifically for the DatePicker's modelValue.
// It should be initialized with a Date object if born_date exists, otherwise null.
const datePickerValue = ref<Date | null>(
    form.due_at ? dayjs(form.due_at).toDate() : null
);


// Watch for changes from the DatePicker (datePickerValue)
watch(datePickerValue, (newValue) => {
    if (newValue instanceof Date) { // Ensure it's a Date object before formatting
        form.due_at = dayjs(newValue).format('YYYY-MM-DD');
    } else {
        // If the DatePicker value becomes null (e.g., user clears the date)
        form.due_at = '';
    }
}, { immediate: true }); // Use immediate to run the watch on initial load if born_date is present

const deleteVacancy = () => {
    if (confirm('Apakah Anda yakin ingin menghapus lowongan kerja ini?')) {
        form.delete(route('partners.vacancies.destroy', props.model?.id), {
            errorBag: 'Lowongan Kerja',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Lowongan Kerja berhasil dihapus!',
                    life: 5000,
                });
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Gagal',
                    detail: 'Gagal menghapus lowongan kerja!',
                    life: 5000,
                });
            },
        });
    }
};

const save = () => {
    if (props.isNewRecord) {
        form.post(route('partners.vacancies.store'), {
            errorBag: 'Lowongan Kerja',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Data Lowongan Kerja Disimpan!',
                    life: 5000,
                });
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Gagal',
                    detail: 'Data Lowongan Kerja Gagal Disimpan!',
                    life: 5000,
                });
            },
        });
    } else {
        form._method = 'PUT';
        form.post(route('partners.vacancies.update', props.model?.id), {
            errorBag: 'Lowongan Kerja',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Data Lowongan Kerja Disimpan!',
                    life: 5000,
                });
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Gagal',
                    detail: 'Data Lowongan Kerja Gagal Diupdate!',
                    life: 5000,
                });
            },
        });
    }
};
</script>

<template>
    <Head :title="model?.id ? 'Ubah Lowongan Kerja' : 'Tambah Lowongan Kerja'" />
    <Toast />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        {{ model?.id ? 'Ubah Lowongan Kerja' : 'Tambah Lowongan Kerja' }}
                    </h1>
                    <p class="mt-1.5 text-sm text-gray-500">
                        {{ model?.id ? 'Perbarui informasi lowongan kerja yang sudah ada' : 'Buat lowongan kerja baru untuk dipublikasikan' }}
                    </p>
                </div>
                <div v-if="!isNewRecord" class="flex gap-2">
                    <Button
                        label="Hapus Lowongan"
                        severity="danger"
                        icon="pi pi-trash"
                        @click="deleteVacancy"
                        class="shrink-0"
                        outlined
                    />
                </div>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Form Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="pi pi-briefcase text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            Informasi Lowongan Kerja
                        </h2>
                        <p class="text-sm text-gray-600">
                            Lengkapi form di bawah untuk {{ model?.id ? 'mengubah' : 'membuat' }} lowongan kerja
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <form @submit.prevent="save" class="p-6">
                <div class="space-y-8">
                    <!-- Basic Information Fields - One Row on Desktop, Stacked on Mobile -->
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-base font-semibold text-gray-900">Informasi Dasar</h3>
                            <p class="mt-1 text-sm text-gray-600">Masukkan detail utama lowongan pekerjaan</p>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <!-- Title Field -->
                            <div class="space-y-3">
                                <label for="title" class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                    <i class="pi pi-briefcase text-indigo-600"></i>
                                    Posisi Pekerjaan
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <InputText
                                        id="title"
                                        v-model="form.title"
                                        required
                                        class="w-full text-base border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg"
                                        placeholder="Software Developer"
                                        size="large"
                                    />
                                </div>
                                <InputError :message="form.errors.title" />
                                <p class="text-xs text-gray-500">Nama posisi yang jelas</p>
                            </div>

                            <!-- Location Field -->
                            <div class="space-y-3">
                                <label for="location" class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                    <i class="pi pi-map-marker text-emerald-600"></i>
                                    Lokasi Kerja
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <InputText
                                        id="location"
                                        v-model="form.location"
                                        required
                                        class="w-full text-base border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg"
                                        placeholder="Jakarta, Remote"
                                        size="large"
                                    />
                                </div>
                                <InputError :message="form.errors.location" />
                                <p class="text-xs text-gray-500">Lokasi kerja yang spesifik</p>
                            </div>

                            <!-- Due Date Field -->
                            <div class="space-y-3">
                                <label for="due_at" class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                    <i class="pi pi-calendar text-amber-600"></i>
                                    Batas Akhir
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <DatePicker
                                        id="due_at"
                                        v-model="datePickerValue"
                                        required
                                        class="w-full text-base border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg"
                                        placeholder="Pilih tanggal"
                                        size="large"
                                        show-icon
                                    />
                                </div>
                                <InputError :message="form.errors.due_at" />
                                <p class="text-xs text-gray-500">Batas penerimaan lamaran</p>
                            </div>
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="space-y-3">
                        <label for="description" class="flex items-center gap-2 text-sm font-medium text-gray-700">
                            <i class="pi pi-file-edit text-purple-600"></i>
                            Deskripsi Lowongan
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="rounded-lg border border-gray-300 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 overflow-hidden">
                            <Editor
                                v-model="form.description"
                                :api-key="TINYMCE_API_KEY"
                                :init="{
                                    plugins: 'save lists link image media table code codesample help autosave emoticons fullscreen preview',
                                    toolbar: [
                                        { name: 'history', items: ['undo', 'redo'] },
                                        { name: 'styles', items: ['styles'] },
                                        { name: 'formatting', items: ['bold', 'italic', 'underline'] },
                                        { name: 'media', items: ['link', 'emoticons', 'image'] },
                                        { name: 'lists', items: ['numlist', 'bullist'] },
                                        { name: 'alignment', items: ['alignleft', 'aligncenter', 'alignright', 'alignjustify'] },
                                        { name: 'indentation', items: ['outdent', 'indent'] },
                                        { name: 'code', items: ['codesample', 'code'] },
                                        { name: 'misc', items: ['preview', 'fullscreen'] },
                                    ],
                                    menubar: false,
                                    placeholder: 'Tulis deskripsi lengkap lowongan kerja, job desc, requirements, dan benefit yang ditawarkan...',
                                    min_height: 400,
                                    height: 400,
                                    branding: false,
                                    resize: true,
                                    link_default_target: '_blank',
                                    skin: 'oxide',
                                    content_css: 'default',
                                }"
                            />
                        </div>
                        <InputError :message="form.errors.description" />
                        <p class="text-xs text-gray-500">Gunakan editor untuk memformat teks, menambahkan daftar requirements, dan benefit</p>
                    </div>

                    <!-- File Upload Field -->
                    <div class="space-y-3">
                        <label for="file" class="flex items-center gap-2 text-sm font-medium text-gray-700">
                            <i class="pi pi-paperclip text-rose-600"></i>
                            Lampiran (Opsional)
                        </label>

                        <div class="bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 hover:border-blue-400 transition-colors duration-200 p-6">
                            <div class="text-center">
                                <input ref="fileInput" type="file" id="file" name="file" @change="handleFileChange" class="hidden" />

                                <!-- File Upload Area -->
                                <div class="space-y-4">
                                    <div class="flex justify-center">
                                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="pi pi-cloud-upload text-blue-600 text-2xl"></i>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <Button
                                            type="button"
                                            @click="$refs.fileInput.click()"
                                            :label="form?.file ? 'Ubah File' : 'Pilih File'"
                                            icon="pi pi-upload"
                                            class="bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-blue-700"
                                        />

                                        <p class="text-sm text-gray-600">
                                            atau seret dan lepas file di sini
                                        </p>
                                    </div>

                                    <!-- File Info -->
                                    <div v-if="form?.file || model?.file" class="bg-white rounded-lg border border-gray-200 p-4 mt-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                                <i class="pi pi-file text-green-600"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ getFileName(form?.file || model?.file) }}
                                                </p>
                                                <p class="text-xs text-gray-500">File berhasil dipilih</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <i class="pi pi-check-circle text-green-500"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-else class="text-center">
                                        <p class="text-sm text-gray-500">Belum ada file yang dipilih</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <i class="pi pi-info-circle"></i>
                            <span>Maksimal 4MB • Format yang didukung: PDF, JPG, JPEG, PNG</span>
                        </div>

                        <InputError :message="form.errors.file" />
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-12 pt-6 border-t border-gray-200">
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <Button
                            type="button"
                            label="Reset Form"
                            severity="secondary"
                            icon="pi pi-refresh"
                            @click="form.reset()"
                            outlined
                            class="w-full sm:w-auto"
                        />
                        <Button
                            type="submit"
                            :label="model?.id ? 'Perbarui Lowongan' : 'Buat Lowongan'"
                            icon="pi pi-check"
                            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-blue-700"
                            :loading="form.processing"
                            loadingIcon="pi pi-spinner pi-spin"
                        />
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
<style scoped>
/* TinyMCE Editor Custom Styling */
:deep(.tox-tinymce) {
    border: none !important;
    border-radius: 0 !important;
}

:deep(.tox-edit-area) {
    border: none !important;
}

:deep(.tox-toolbar-overlord) {
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
}

/* File Upload Drag and Drop Effects */
.file-upload-area {
    transition: all 0.2s ease-in-out;
}

.file-upload-area:hover {
    background-color: rgb(239 246 255);
    border-color: rgb(59 130 246);
}

/* Form Field Focus Effects */
:deep(.p-inputtext:focus) {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
    border-color: rgb(59 130 246) !important;
}

:deep(.p-datepicker:not(.p-disabled).p-focus) {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
    border-color: rgb(59 130 246) !important;
}

/* Button Loading Animation */
:deep(.p-button .pi-spin) {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Card Shadow on Hover */
.main-form-card {
    transition: box-shadow 0.3s ease-in-out;
}

.main-form-card:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Gradient Background for Form Header */
.form-header-gradient {
    background: linear-gradient(135deg, rgb(239 246 255) 0%, rgb(224 242 254) 100%);
}

/* File Preview Card Animation */
.file-preview-card {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Input Icon Positioning */
.input-with-icon {
    position: relative;
}

.input-with-icon .p-inputtext {
    padding-right: 2.5rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    /* Stack grid items on mobile */
    .grid.md\\:grid-cols-3 {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    :deep(.tox-toolbar__group) {
        flex-wrap: wrap;
    }

    .form-actions {
        flex-direction: column-reverse;
        gap: 0.75rem;
    }

    .form-actions .p-button {
        width: 100%;
        justify-content: center;
    }

    /* Mobile input adjustments */
    :deep(.p-inputtext) {
        padding-right: 0.75rem !important;
    }

    /* Mobile DatePicker adjustments */
    :deep(.p-datepicker) {
        width: 100% !important;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    /* Tablet adjustments - keep 3 columns but adjust spacing */
    .grid.md\\:grid-cols-3 {
        gap: 1rem;
    }
}

/* Enhanced focus states for accessibility */
:deep(.p-button:focus) {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5) !important;
}

:deep(.p-button:focus:not(:active)) {
    outline: 2px solid transparent;
    outline-offset: 2px;
}
</style>
