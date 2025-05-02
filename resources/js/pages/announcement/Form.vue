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
        title: 'Pengumuman',
        href: '/announcements',
    },
    {
        title: props.isNewRecord ? 'Tambah Pengumuman' : 'Ubah Pengumuman',
        href: '#',
    },
];

const form = useForm({
    _method: props.isNewRecord ? 'POST' : 'PUT',
    title: props.model?.title,
    content: props.model?.content,
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

const deleteAnnouncement = () => {
    if (confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')) {
        form.delete(route('announcements.destroy', props.model?.id), {
            errorBag: 'Pengumuman',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Pengumuman berhasil dihapus!',
                    life: 5000,
                });
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Gagal',
                    detail: 'Gagal menghapus pengumuman!',
                    life: 5000,
                });
            },
        });
    }
};

const save = () => {
    if (props.isNewRecord) {
        form.post(route('announcements.store'), {
            errorBag: 'Pengumuman',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Data Pengumuman Disimpan!',
                    life: 5000,
                });
                // form.file = null;
                // router.visit(route('announcements.edit', props.model?.id));
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Gagal',
                    detail: 'Data Pengumuman Gagal Disimpan!',
                    life: 5000,
                });
            },
        });
    } else {
        form._method = 'PUT';
        form.post(route('announcements.update', props.model?.id), {
            errorBag: 'Pengumuman',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Data Pengumuman Diupdate!',
                    life: 5000,
                });
                // form.file = null;
                // router.visit(route('announcements.edit', props.model?.id));
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Gagal',
                    detail: 'Data Pengumuman Gagal Diupdate!',
                    life: 5000,
                });
            },
        });
    }
};
</script>

<template>
    <Head :title="model?.id ? 'Ubah Pengumuman' : 'Tambah Pengumuman'" />
    <Toast />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-foreground text-2xl font-bold">
                    {{ model?.id ? 'Ubah Pengumuman' : 'Tambah Pengumuman' }}
                </h1>
                <div v-if="!isNewRecord" class="flex gap-2">
                    <Button label="Hapus" severity="danger" icon="pi pi-trash" @click="deleteAnnouncement" class="px-4 py-2" size="small" />
                </div>
            </div>
            <form @submit.prevent="save">
                <div class="grid gap-6">
                    <div class="flex flex-col gap-2">
                        <label for="title" class="text-foreground block text-sm font-medium">Judul</label>
                        <InputText id="title" v-model="form.title" required class="lg:w-1/2" placeholder="Judul pengumuman" />
                        <InputError :message="form.errors.title" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="content" class="text-foreground block text-sm font-medium">Konten</label>
                        <Editor
                            v-model="form.content"
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
                                placeholder: 'Tulis konten pengumuman ...',
                                min_height: 350,
                                height: 350,
                                branding: false,
                                resize: true,
                                link_default_target: '_blank',
                            }"
                        />
                        <InputError :message="form.errors.content" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="file" class="text-foreground block text-sm font-medium">Lampiran</label>

                        <div class="relative w-full lg:w-1/2">
                            <input ref="fileInput" type="file" id="file" name="file" @change="handleFileChange" class="hidden" />

                            <button
                                type="button"
                                @click="$refs.fileInput.click()"
                                class="bg-primary hover:bg-primary/90 inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium text-white transition"
                            >
                                📁 {{ form?.file ? 'Ubah' : 'Pilih' }} File
                            </button>

                            <span class="mx-2 my-2 text-sm text-gray-600">
                                <!-- Tampilkan nama file jika ada -->
                                {{ getFileName(form?.file || model?.file) || 'Belum ada file yang dipilih' }}
                            </span>
                        </div>

                        <small class="text-gray-500"> Max 4MB (PDF, DOC, XLS, PPT, TXT, CSV, JPG, PNG, GIF) </small>

                        <InputError :message="form.errors.file" />
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-4">
                    <Button
                        type="button"
                        label="Reset"
                        severity="secondary"
                        icon="pi pi-refresh"
                        class="px-6 py-2"
                        size="small"
                        @click="form.reset()"
                    />
                    <Button type="submit" label="Simpan" severity="primary" icon="pi pi-check" class="px-6 py-2" size="small" />
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
