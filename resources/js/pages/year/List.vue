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
    router.get(route('years.student-classes.index', model.id));
}
</script>

<style scoped>
:deep(.custom-datatable) {
    .p-datatable-header {
        display: none;
    }

    .p-datatable-tbody > tr {
        transition: background-color 0.2s ease;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
    }

    .p-datatable-tbody > tr:hover {
        background-color: rgba(239, 246, 255, 0.5);
    }

    .p-datatable-tbody > tr:last-child {
        border-bottom: none;
    }

    .p-paginator {
        background-color: rgba(249, 250, 251, 0.5);
        border-top: 1px solid #e5e7eb;
        padding: 1rem 1.5rem;
    }

    .p-paginator .p-paginator-pages .p-paginator-page {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        color: #374151;
        transition: all 0.2s ease;
    }

    .p-paginator .p-paginator-pages .p-paginator-page:hover {
        background-color: #eff6ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
    }

    .p-paginator .p-paginator-pages .p-paginator-page.p-highlight {
        background-color: #2563eb;
        border-color: #2563eb;
        color: white;
    }

    .p-paginator .p-paginator-pages .p-paginator-page.p-highlight:hover {
        background-color: #1d4ed8;
        border-color: #1d4ed8;
    }

    .p-paginator .p-paginator-first,
    .p-paginator .p-paginator-prev,
    .p-paginator .p-paginator-next,
    .p-paginator .p-paginator-last {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        color: #374151;
        transition: all 0.2s ease;
    }

    .p-paginator .p-paginator-first:hover,
    .p-paginator .p-paginator-prev:hover,
    .p-paginator .p-paginator-next:hover,
    .p-paginator .p-paginator-last:hover {
        background-color: #f9fafb;
        border-color: #9ca3af;
    }
}
</style>

<template>
    <Head title="Angkatan" />
    <Toast />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        Manajemen Angkatan
                    </h1>
                    <p class="mt-1.5 text-sm text-gray-500">
                        Kelola data angkatan alumni dan siswa
                    </p>
                </div>
                <Button
                    label="Tambah Angkatan"
                    @click="openCreateModal"
                    icon="pi pi-plus"
                    class="shrink-0 bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-blue-700 transition-colors duration-200"
                />
            </div>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Search Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative">
                        <!-- <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="pi pi-search text-gray-400 text-sm"></i>
                        </div> -->
                        <InputText
                            v-model="filters"
                            placeholder="Cari angkatan..."
                            class="pl-10 w-full sm:w-80 bg-white border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm"
                        />
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="pi pi-calendar"></i>
                        <span>{{ props.models.length }} angkatan terdaftar</span>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <DataTable
                :value="props.models"
                paginator
                removableSort
                row-hover
                :rows="10"
                :rows-per-page-options="[10, 20, 50, 100]"
                @rowClick="(event: any) => openStudentClassPage(event.data)"
                class="custom-datatable"
                tableStyle="min-width: 100%"
                paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                currentPageReportTemplate="Menampilkan {first} - {last} dari {totalRecords} data"
            >
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                        <i class="pi pi-calendar-times text-4xl mb-4 text-gray-300"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada data angkatan</h3>
                        <p class="text-sm text-gray-500">Belum ada angkatan yang terdaftar dalam sistem</p>
                    </div>
                </template>

                <Column
                    field="year"
                    sortable
                    header="Tahun Angkatan"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6"
                >
                    <template #body="slotProps">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                <i class="pi pi-calendar text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-gray-900 cursor-pointer hover:text-blue-600 transition-colors">
                                    {{ slotProps.data.year }}
                                </p>
                                <p class="text-xs text-gray-500">Tahun Angkatan</p>
                            </div>
                        </div>
                    </template>
                </Column>

                <Column
                    field="student_classes_count"
                    sortable
                    header="Jumlah Kelas"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6"
                    class="w-48"
                >
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="pi pi-users text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <span class="text-lg font-semibold text-gray-900">
                                    {{ slotProps.data.student_classes_count || 0 }}
                                </span>
                                <span class="text-sm text-gray-500 ml-1">kelas</span>
                            </div>
                        </div>
                    </template>
                </Column>

                <Column
                    header="Aksi"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6"
                    class="w-32"
                >
                    <template #body="slotProps">
                        <div class="flex items-center justify-center gap-1">
                            <Button
                                icon="pi pi-pencil"
                                severity="warn"
                                @click.stop="openEditModal(slotProps.data)"
                                class="p-2 text-amber-600 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition-colors duration-200"
                                text
                                size="small"
                                v-tooltip.top="'Edit angkatan'"
                            />
                            <Button
                                icon="pi pi-trash"
                                severity="danger"
                                @click.stop="openDeleteDialog(slotProps.data.id)"
                                class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                text
                                size="small"
                                v-tooltip.top="'Hapus angkatan'"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Form Modal -->
        <Dialog
            v-model:visible="dialogVisible"
            modal
            :closable="false"
            class="w-full max-w-md mx-4"
        >
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="pi pi-calendar text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ dialogMode === 'add' ? 'Tambah Angkatan Baru' : 'Edit Angkatan' }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            {{ dialogMode === 'add' ? 'Masukkan tahun angkatan baru' : 'Ubah tahun angkatan' }}
                        </p>
                    </div>
                </div>
            </template>

            <form @submit.prevent="saveYear" class="space-y-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Tahun Angkatan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <!-- <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="pi pi-calendar text-gray-400 text-sm"></i>
                        </div> -->
                        <InputText
                            v-model="form.year"
                            placeholder="Contoh: 2024"
                            class="pl-10 w-full bg-white border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg"
                            :class="{ 'border-red-300': form.errors.year }"
                        />
                    </div>
                    <p v-if="form.errors.year" class="text-sm text-red-600">{{ form.errors.year }}</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <Button
                        type="button"
                        label="Batal"
                        @click="dialogVisible = false"
                        class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                        text
                    />
                    <Button
                        type="submit"
                        :label="dialogMode === 'add' ? 'Tambah Angkatan' : 'Simpan Perubahan'"
                        icon="pi pi-check"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-blue-700 transition-colors duration-200"
                        :loading="form.processing"
                    />
                </div>
            </form>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog
            v-model:visible="showDeleteDialog"
            modal
            class="w-full max-w-md mx-4"
        >
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="pi pi-exclamation-triangle text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
            </template>

            <div class="py-4">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <i class="pi pi-exclamation-triangle text-red-500 text-xl mt-0.5"></i>
                        <div>
                            <p class="text-sm text-red-800 font-medium mb-1">
                                Hapus angkatan ini?
                            </p>
                            <p class="text-sm text-red-700">
                                Semua data yang terkait dengan angkatan ini akan ikut terhapus.
                                Pastikan Anda telah membackup data yang diperlukan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end gap-3">
                    <Button
                        label="Batal"
                        @click="showDeleteDialog = false"
                        class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                        text
                    />
                    <Button
                        label="Hapus Angkatan"
                        icon="pi pi-trash"
                        @click="deleteYear()"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700 transition-colors duration-200"
                        :loading="form.processing"
                    />
                </div>
            </template>
        </Dialog>
    </AppLayout>
</template>
