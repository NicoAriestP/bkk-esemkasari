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
        <div class="mx-auto p-2 lg:p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-foreground text-2xl font-bold">
                    {{ model?.id ? 'Ubah Lowongan Kerja' : 'Tambah Lowongan Kerja' }}
                </h1>
                <div v-if="!isNewRecord" class="flex gap-2">
                    <Button label="Hapus" severity="danger" icon="pi pi-trash" @click="deleteVacancy"/>
                </div>
            </div>
            <form @submit.prevent="save">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-y-4 gap-x-2">
                    <div class="col-span-1 flex flex-col gap-2">
                        <label for="title" class="text-foreground block text-sm font-medium">Posisi</label>
                        <InputText id="title" v-model="form.title" required class="lg:w-full" placeholder="Posisi" />
                        <InputError :message="form.errors.title" />
                    </div>
                    <div class="col-span-1 flex flex-col gap-2">
                        <label for="location" class="text-foreground block text-sm font-medium">Lokasi</label>
                        <InputText id="location" v-model="form.location" required class="lg:w-full" placeholder="Lokasi" />
                        <InputError :message="form.errors.location" />
                    </div>
                    <div class="col-span-1 flex flex-col gap-2">
                        <label for="due_at" class="text-foreground block text-sm font-medium">Tenggat Waktu</label>
                        <DatePicker id="due_at" v-model="datePickerValue" required class="lg:w-full" placeholder="Tenggat Waktu" />
                        <InputError :message="form.errors.due_at" />
                    </div>
                    <div class="col-span-3 flex flex-col gap-2">
                        <label for="description" class="text-foreground block text-sm font-medium">Deskripsi</label>
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
                                placeholder: 'Tulis deskripsi lowongan kerja ...',
                                min_height: 350,
                                height: 350,
                                branding: false,
                                resize: true,
                                link_default_target: '_blank',
                            }"
                        />
                        <InputError :message="form.errors.description" />
                    </div>
                    <div class="col-span-3 flex flex-col gap-2">
                        <label for="file" class="text-foreground block text-sm font-medium">Lampiran</label>

                        <div class="relative w-full lg:w-1/2">
                            <input ref="fileInput" type="file" id="file" name="file" @change="handleFileChange" class="hidden" />

                            <button
                                type="button"
                                @click="$refs.fileInput.click()"
                                class="bg-blue-500 hover:bg-blue-600 inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium text-white transition"
                            >
                                📁 {{ form?.file ? 'Ubah' : 'Pilih' }} File
                            </button>

                            <span class="mx-2 my-2 text-sm text-gray-600">
                                <!-- Tampilkan nama file jika ada -->
                                {{ getFileName(form?.file || model?.file) || 'Belum ada file yang dipilih' }}
                            </span>
                        </div>
                        <small class="text-gray-500"> Max 4MB (PDF, JPG, JPEG, PNG) </small>
                        <InputError :message="form.errors.file" />
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-4">
                    <Button
                        type="button"
                        label="Reset"
                        severity="secondary"
                        icon="pi pi-refresh"
                        @click="form.reset()"
                    />
                    <Button type="submit" label="Simpan" severity="primary" icon="pi pi-check" />
                </div>
            </form>
        </div>
    </AppLayout>
</template>
<style scoped>
.p-fileupload-basic {
    justify-content: start !important;
}
</style>
