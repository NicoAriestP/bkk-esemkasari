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
        href: route('years.student-classes.index', props.year.id),
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
        route('years.student-classes.index', props.year.id),
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

        const routeParams = {
            year: props.year.id,
        };

        form.post(route('years.student-classes.store', routeParams), {
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

        const routeParams = {
            year: props.year.id,
            model: form.id,
        }

        form.post(route('years.student-classes.update', routeParams), {
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
    form.delete(route('years.student-classes.destroy', {
        year: props.year.id,
        model: id,
    }), {
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
    router.get(route('years.student-classes.students.index', { year: model.year_id, studentClass: model.id }));
};
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
    <Head title="Kelas" />
    <Toast />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        Manajemen Kelas - {{ props.year.year }}
                    </h1>
                    <p class="mt-1.5 text-sm text-gray-500">
                        Kelola data kelas untuk angkatan {{ props.year.year }}
                    </p>
                </div>
                <Button
                    label="Tambah Kelas"
                    @click="openCreateModal"
                    icon="pi pi-plus"
                    class="shrink-0 bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-blue-700 transition-colors duration-200"
                />
            </div>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Search Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="pi pi-search text-gray-400 text-sm"></i>
                        </div>
                        <InputText
                            v-model="filters"
                            placeholder="Cari kelas..."
                            class="pl-10 w-full sm:w-80 bg-white border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm"
                        />
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="pi pi-graduation-cap"></i>
                        <span>{{ props.models.length }} kelas terdaftar</span>
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
                @rowClick="(event: any) => openStudentsPage(event.data)"
                class="custom-datatable"
                tableStyle="min-width: 100%"
                paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                currentPageReportTemplate="Menampilkan {first} - {last} dari {totalRecords} data"
            >
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                        <i class="pi pi-graduation-cap text-4xl mb-4 text-gray-300"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada data kelas</h3>
                        <p class="text-sm text-gray-500">Belum ada kelas yang terdaftar untuk angkatan ini</p>
                    </div>
                </template>

                <Column
                    field="name"
                    sortable
                    header="Nama Kelas"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6"
                >
                    <template #body="slotProps">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <i class="pi pi-graduation-cap text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-gray-900 cursor-pointer hover:text-blue-600 transition-colors">
                                    {{ slotProps.data.name }}
                                </p>
                                <p class="text-xs text-gray-500">Kelas {{ props.year.year }}</p>
                            </div>
                        </div>
                    </template>
                </Column>

                <Column
                    field="students_count"
                    sortable
                    header="Jumlah Siswa"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6"
                    class="w-48"
                >
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <div class="flex-shrink-0 w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                                <i class="pi pi-users text-emerald-600 text-sm"></i>
                            </div>
                            <div>
                                <span class="text-lg font-semibold text-gray-900">
                                    {{ slotProps.data.students_count || 0 }}
                                </span>
                                <span class="text-sm text-gray-500 ml-1">siswa</span>
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
                                v-tooltip.top="'Edit kelas'"
                            />
                            <Button
                                icon="pi pi-trash"
                                severity="danger"
                                @click.stop="deleteStudentClass(slotProps.data.id)"
                                class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                text
                                size="small"
                                v-tooltip.top="'Hapus kelas'"
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
                    <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="pi pi-graduation-cap text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ dialogMode === 'add' ? 'Tambah Kelas Baru' : 'Edit Kelas' }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            {{ dialogMode === 'add' ? 'Masukkan nama kelas baru untuk angkatan ' + props.year.year : 'Ubah nama kelas' }}
                        </p>
                    </div>
                </div>
            </template>

            <form @submit.prevent="saveStudentClass" class="space-y-6">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Nama Kelas <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="pi pi-graduation-cap text-gray-400 text-sm"></i>
                        </div>
                        <InputText
                            v-model="form.name"
                            placeholder="Contoh: XII RPL 1, XII TKJ 2"
                            class="pl-10 w-full bg-white border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg h-12"
                            :class="{ 'border-red-300': form.errors.name }"
                        />
                    </div>
                    <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
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
                        :label="dialogMode === 'add' ? 'Tambah Kelas' : 'Simpan Perubahan'"
                        icon="pi pi-check"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-blue-700 transition-colors duration-200"
                        :loading="form.processing"
                    />
                </div>
            </form>
        </Dialog>
    </AppLayout>
</template>
