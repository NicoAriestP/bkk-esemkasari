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
});

const filters = ref();

const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Angkatan',
        href: '/years.index',
    },
];

const dialogMode = ref('');
const dialogVisible = ref(false);
const showDeleteDialog = ref(false);

const form = useForm({
    _method: '',
    id: null as number | null, // Tambahkan ID untuk edit dan delete
    year: null,
});

watch(filters, (newValue) => {
    router.get(
        route('years.index'),
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

const openEditModal = (year: any) => {
    dialogMode.value = 'edit';
    Object.assign(form, year);
    dialogVisible.value = true;
};

// Save changes for add or edit
const saveYear = () => {
    if (dialogMode.value === 'add') {
        form._method = 'POST';

        form.post(route('years.store'), {
            errorBag: 'Angkatan',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Angkatan berhasil ditambahkan!',
                    life: 5000,
                });
                dialogVisible.value = false;
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Gagal',
                    detail: 'Gagal menambahkan angkatan!',
                    life: 5000,
                });
            },
        });
    } else if (dialogMode.value === 'edit') {
        form._method = 'PUT';

        const routeIdParam = form.id as number;

        form.post(route('years.update', routeIdParam), {
            errorBag: 'Angkatan',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Angkatan berhasil diubah!',
                    life: 5000,
                });
                dialogVisible.value = false;
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Gagal',
                    detail: 'Gagal mengubah angkatan!',
                    life: 5000,
                });
            },
        });
    }
    form.reset();
};

const openDeleteDialog = (id: number) => {
    form.id = id;
    showDeleteDialog.value = true;
};

// Function to delete data
const deleteYear = () => {
    if (!form.id) {
        return;
    }

    form.delete(route('years.destroy', form.id), {
        errorBag: 'Angkatan',
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Sukses',
                detail: 'Angkatan berhasil dihapus!',
                life: 5000,
            });
            dialogVisible.value = false;
            form.reset();
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Gagal',
                detail: 'Gagal menghapus angkatan!',
                life: 5000,
            });
            form.reset();
        },
    });
};

const openStudentClassPage = (model: any) => {
    router.get(route('student-classes.index', model.id));
}
</script>

<template>
    <Head title="Angkatan" />
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
                @rowClick="(event: any) => openStudentClassPage(event.data)"
            >
                <template #header>
                    <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                        <InputText class="w-full lg:w-72" v-model="filters" placeholder="Cari angkatan ..." />
                        <Button label="Tambah" @click="openCreateModal" variant="primary" icon="pi pi-plus" />
                    </div>
                </template>
                <template #empty>
                    <span class="text-center">No data found.</span>
                </template>

                <Column field="year" sortable header="Tahun Angkatan" />
                <Column field="student_classes_count" sortable header="Jumlah Kelas" />
                <Column header="Aksi" body-style="text-align: right;" header-style="text-align: center !important;" style="width: 10%">
                    <template #body="slotProps">
                        <div class="flex justify-center">
                            <Button
                                class="!text-yellow-500 hover:!text-yellow-600"
                                icon="pi pi-pencil"
                                variant="link"
                                severity="secondary"
                                v-tooltip.top="'Ubah'"
                                @click="openEditModal(slotProps.data)"
                            />
                            <Button
                                class="!text-red-500 hover:!text-red-600"
                                icon="pi pi-trash"
                                variant="link"
                                severity="danger"
                                v-tooltip.top="'Hapus Angkatan'"
                                @click="openDeleteDialog(slotProps.data.id)"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog
            class="w-[32rem]"
            :header="dialogMode === 'create' ? 'Tambah Angkatan' : 'Ubah Angkatan'"
            v-model:visible="dialogVisible"
            modal
            :closable="false"
        >
            <form @submit.prevent="saveYear">
                <div class="my-2 flex flex-col gap-2">
                    <InputText v-model="form.year" placeholder="Tahun Angkatan" />
                </div>
                <div class="my-2 flex justify-end gap-2">
                    <Button type="button" icon="pi pi-times" class="p-button-text" label="Batal" @click="dialogVisible = false" />
                    <Button type="submit" icon="pi pi-check" label="Simpan" />
                </div>
            </form>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:visible="showDeleteDialog" modal header="Konfirmasi Hapus" :style="{ width: '450px' }">
            <div class="text-center">
                <i class="pi pi-exclamation-triangle mb-4 text-red-500" style="font-size: 3rem"></i>
                <p>Apakah Anda yakin ingin menghapus angkatan ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" @click="showDeleteDialog = false" class="p-button-text" />
                <Button label="Hapus" icon="pi pi-trash" @click="deleteYear()" severity="danger" autofocus />
            </template>
        </Dialog>
    </AppLayout>
</template>
