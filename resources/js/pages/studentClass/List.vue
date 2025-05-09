<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { ref, watch } from 'vue';

import { BreadcrumbItem } from '@/types';

import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Toast from 'primevue/toast';

const props = defineProps({
    models: {
        type: Array,
        required: true,
    },
    year: {
        type: Object,
        required: true,
    },
});

const filters = ref();

const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Angkatan',
        href: route('years.index'),
    },
    {
        title: props.year.year,
        href: '#',
    },
    {
        title: 'Kelas',
        href: route('student-classes.index', props.year.id),
    },
];

const dialogMode = ref('');
const dialogVisible = ref(false);

const form = useForm({
    _method: '',
    year_id: props.year.id,
    id: null as number | null, // Tambahkan ID untuk edit dan delete
    name: null,
});

watch(filters, (newValue) => {
    router.get(
        route('student-classes.index', props.year.id),
        { search: newValue },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
});

// Function to open modal for adding data
const openCreateModal = () => {
    dialogMode.value = 'add';
    form.reset();
    dialogVisible.value = true;
};

const openEditModal = (studentClass: any) => {
    dialogMode.value = 'edit';
    Object.assign(form, studentClass);
    dialogVisible.value = true;
};

// Save changes for add or edit
const saveStudentClass = () => {
    if (dialogMode.value === 'add') {
        form._method = 'POST';

        form.post(route('student-classes.store'), {
            errorBag: 'Kelas',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Kelas berhasil ditambahkan!',
                    life: 5000,
                });
                dialogVisible.value = false;
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Gagal',
                    detail: 'Gagal menambahkan kelas!',
                    life: 5000,
                });
            },
        });
    } else if (dialogMode.value === 'edit') {
        form._method = 'PUT';

        const routeIdParam = form.id as number;

        form.post(route('student-classes.update', routeIdParam), {
            errorBag: 'Kelas',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Kelas berhasil diubah!',
                    life: 5000,
                });
                dialogVisible.value = false;
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Gagal',
                    detail: 'Gagal mengubah kelas!',
                    life: 5000,
                });
            },
        });
    }
    form.reset();
};

// Function to delete data
const deleteStudentClass = (id: number) => {
    form.delete(route('student-classes.destroy', id), {
        errorBag: 'Kelas',
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Sukses',
                detail: 'Kelas berhasil dihapus!',
                life: 5000,
            });
            dialogVisible.value = false;
            form.reset();
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Gagal',
                detail: 'Gagal menghapus kelas!',
                life: 5000,
            });
            form.reset();
        },
    });
};

const openStudentsPage = (model: any) => {
    router.get(route('students.index', { year: model.year_id, studentClass: model.id }));
};
</script>

<template>
    <Head title="Kelas" />
    <Toast />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="lg:flex lg:justify-center">
            <DataTable
                class="w-full max-w-2xl"
                :value="props.models"
                paginator
                removableSort
                row-hover
                :rows="10"
                :rows-per-page-options="[10, 20, 50, 100]"
                @rowClick="(event: any) => openStudentsPage(event.data)"
            >
                <template #header>
                    <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                        <InputText class="w-full lg:w-72" v-model="filters" placeholder="Search ..." />
                        <Button label="Tambah" @click="openCreateModal" variant="primary" icon="pi pi-plus" />
                    </div>
                </template>
                <template #empty>
                    <span class="text-center">No data found.</span>
                </template>

                <Column field="name" sortable header="Nama Kelas" />
                <Column field="students_count" sortable header="Jumlah Siswa" />
                <Column header="Aksi" body-style="text-align: right;" header-style="text-align: center !important;" style="width: 10%">
                    <template #body="slotProps">
                        <div class="flex justify-center">
                            <Button
                                class="!text-yellow-500 hover:!text-yellow-600"
                                icon="pi pi-pencil"
                                variant="link"
                                severity="secondary"
                                @click="openEditModal(slotProps.data)"
                            />
                            <Button
                                class="!text-red-500 hover:!text-red-600"
                                icon="pi pi-trash"
                                variant="link"
                                severity="danger"
                                @click="deleteStudentClass(slotProps.data.id)"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog
            class="w-[32rem]"
            :header="dialogMode === 'create' ? 'Tambah Kelas' : 'Ubah Kelas'"
            v-model:visible="dialogVisible"
            modal
            :closable="false"
        >
            <form @submit.prevent="saveStudentClass">
                <div class="my-2 flex flex-col gap-2">
                    <InputText v-model="form.name" placeholder="Nama Kelas" />
                </div>
                <div class="my-2 flex justify-end gap-2">
                    <Button type="button" icon="pi pi-times" class="p-button-text" label="Batal" @click="dialogVisible = false" />
                    <Button type="submit" icon="pi pi-check" label="Simpan" />
                </div>
            </form>
        </Dialog>
    </AppLayout>
</template>
