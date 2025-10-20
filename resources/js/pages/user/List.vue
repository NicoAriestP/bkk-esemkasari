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
import Password from 'primevue/password';
import Toast from 'primevue/toast';

const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Karyawan',
        href: '/users',
    },
];

const props = defineProps({
    models: {
        type: Array,
        required: true,
    },
});

const filters = ref();
const selectedUser = ref<any>(null);
const displayDeleteDialog = ref(false);
const dialogVisible = ref(false);
const dialogMode = ref(''); // add | edit

const form = useForm({
    id: null,
    name: '',
    email: '',
    phone: '',
    password: '',
});

const openCreateDialog = () => {
    form.reset();
    dialogMode.value = 'add';
    dialogVisible.value = true;
};

const openEditDialog = (user: any) => {
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;
    form.phone = user.phone;
    form.password = user.password;
    dialogMode.value = 'edit';
    dialogVisible.value = true;
};

const confirmDelete = (user: any) => {
    selectedUser.value = user;
    displayDeleteDialog.value = true;
};

const createUser = () => {
    form.post(route('users.store'), {
        errorBag: 'user',
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Sukses',
                detail: 'Karyawan berhasil ditambahkan!',
                life: 5000,
            });
            dialogVisible.value = false;
            form.reset();
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Terjadi kesalahan saat menambahkan karyawan',
                life: 5000,
            });
        },
    });
};

const updateUser = () => {
    form.put(
        route('users.update', {
            model: form.id,
        }),
        {
            errorBag: 'user',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Karyawan berhasil diperbarui!',
                    life: 5000,
                });
                dialogVisible.value = false;
                form.reset();
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Terjadi kesalahan saat memperbarui karyawan',
                    life: 5000,
                });
            },
        },
    );
};

const deleteUser = () => {
    if (!selectedUser.value) {
        return;
    }

    form.delete(
        route('users.destroy', {
            model: selectedUser.value.id,
        }),
        {
            errorBag: 'Karyawan',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Karyawan berhasil dihapus!',
                    life: 5000,
                });
                displayDeleteDialog.value = false;
                form.reset();
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Terjadi kesalahan saat menghapus karyawan',
                    life: 5000,
                });
            },
        },
    );
};

watch(filters, (newValue) => {
    router.get(
        route('users.index'),
        { search: newValue },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
});
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
    <Toast />

    <Head title="Karyawan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        Manajemen Karyawan BKK
                    </h1>
                    <p class="mt-1.5 text-sm text-gray-500">
                        Kelola data karyawan dan staff Bursa Kerja Khusus
                    </p>
                </div>
                <Button
                    label="Tambah Karyawan"
                    @click="openCreateDialog"
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
                            placeholder="Cari karyawan berdasarkan nama, email, atau telepon..."
                            class="pl-10 w-full sm:w-96 bg-white border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm"
                        />
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="pi pi-briefcase"></i>
                        <span>{{ props.models.length }} karyawan terdaftar</span>
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
                tableStyle="min-width: 100%"
                class="custom-datatable"
                paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                currentPageReportTemplate="Menampilkan {first} - {last} dari {totalRecords} karyawan"
            >
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                        <i class="pi pi-briefcase text-4xl mb-4 text-gray-300"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada data karyawan</h3>
                        <p class="text-sm text-gray-500">Belum ada karyawan yang terdaftar dalam sistem</p>
                    </div>
                </template>

                <Column
                    field="name"
                    sortable
                    header="Nama Karyawan"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6"
                >
                    <template #body="slotProps">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">
                                    {{ slotProps.data.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                            <div>
                                <p class="text-base font-semibold text-gray-900">
                                    {{ slotProps.data.name }}
                                </p>
                                <p class="text-xs text-gray-500">{{ slotProps.data.email }}</p>
                            </div>
                        </div>
                    </template>
                </Column>

                <Column
                    field="email"
                    sortable
                    header="Email"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6"
                    class="w-64"
                >
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="pi pi-envelope text-blue-600 text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-900 truncate">
                                {{ slotProps.data.email }}
                            </span>
                        </div>
                    </template>
                </Column>

                <Column
                    field="phone"
                    sortable
                    header="No. Telepon"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6"
                    class="w-44"
                >
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="pi pi-phone text-green-600 text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-900">
                                {{ slotProps.data.phone }}
                            </span>
                        </div>
                    </template>
                </Column>

                <Column
                    header="Role"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6"
                    class="w-40"
                >
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <div :class="[
                                'flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center',
                                slotProps.data.is_leader ? 'bg-purple-100' : 'bg-gray-100'
                            ]">
                                <i :class="[
                                    'text-sm',
                                    slotProps.data.is_leader ? 'pi pi-crown text-purple-600' : 'pi pi-user text-gray-600'
                                ]"></i>
                            </div>
                            <span :class="[
                                'text-sm font-medium',
                                slotProps.data.is_leader ? 'text-purple-700' : 'text-gray-700'
                            ]">
                                {{ slotProps.data.is_leader ? 'Ketua BKK' : 'Staff' }}
                            </span>
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
                        <div v-if="slotProps.data.is_leader == false" class="flex items-center justify-center gap-1">
                            <Button
                                icon="pi pi-pencil"
                                severity="warn"
                                @click="openEditDialog(slotProps.data)"
                                class="p-2 text-amber-600 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition-colors duration-200"
                                text
                                size="small"
                                v-tooltip.top="'Edit karyawan'"
                            />
                            <Button
                                icon="pi pi-trash"
                                severity="danger"
                                @click="confirmDelete(slotProps.data)"
                                class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                text
                                size="small"
                                v-tooltip.top="'Hapus karyawan'"
                            />
                        </div>
                        <div v-else class="flex items-center justify-center">
                            <span class="text-xs text-purple-600 bg-purple-50 px-2 py-1 rounded-full font-medium">
                                Protected
                            </span>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AppLayout>

    <!-- Enhanced Employee Form Dialog -->
    <Dialog
        v-model:visible="dialogVisible"
        modal
        class="w-full max-w-2xl mx-4"
    >
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="pi pi-briefcase text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ dialogMode === 'add' ? 'Tambah Karyawan Baru' : 'Edit Data Karyawan' }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        {{ dialogMode === 'add' ? 'Masukkan data karyawan baru untuk BKK' : 'Ubah data karyawan' }}
                    </p>
                </div>
            </div>
        </template>

        <div class="p-6 space-y-6">
            <!-- Employee Information Section -->
            <div class="bg-emerald-50/50 rounded-lg p-6 border border-emerald-100">
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="pi pi-user text-emerald-600"></i>
                    </div>
                    <h5 class="text-lg font-semibold text-gray-900">Informasi Karyawan</h5>
                </div>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <InputText
                            id="name"
                            v-model="form.name"
                            class="w-full"
                            :class="{ 'border-red-300': form.errors.name }"
                            placeholder="Masukkan nama lengkap"
                        />
                        <small v-if="form.errors.name" class="text-red-600 text-xs">{{ form.errors.name }}</small>
                    </div>
                    <div class="space-y-2">
                        <label for="phone" class="block text-sm font-medium text-gray-700">
                            No. Telepon <span class="text-red-500">*</span>
                        </label>
                        <InputText
                            id="phone"
                            v-model="form.phone"
                            class="w-full"
                            :class="{ 'border-red-300': form.errors.phone }"
                            placeholder="08123456789"
                        />
                        <small v-if="form.errors.phone" class="text-red-600 text-xs">{{ form.errors.phone }}</small>
                    </div>
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <InputText
                            inputmode="email"
                            id="email"
                            v-model="form.email"
                            class="w-full"
                            :class="{ 'border-red-300': form.errors.email }"
                            placeholder="contoh@email.com"
                        />
                        <small v-if="form.errors.email" class="text-red-600 text-xs">{{ form.errors.email }}</small>
                    </div>
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <Password
                            id="password"
                            v-model="form.password"
                            class="w-full"
                            :class="{ 'border-red-300': form.errors.password }"
                            :feedback="true"
                            toggleMask
                            placeholder="Masukkan password"
                        />
                        <small v-if="form.errors.password" class="text-red-600 text-xs">{{ form.errors.password }}</small>
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                <Button
                    label="Batal"
                    @click="dialogVisible = false"
                    class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                    text
                />
                <Button
                    :label="dialogMode === 'add' ? 'Tambah Karyawan' : 'Simpan Perubahan'"
                    icon="pi pi-check"
                    @click="dialogMode === 'add' ? createUser() : updateUser()"
                    class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 border-emerald-600 hover:border-emerald-700 transition-colors duration-200"
                    :loading="form.processing"
                />
            </div>
        </template>
    </Dialog>

    <!-- Enhanced Delete Confirmation Dialog -->
    <Dialog v-model:visible="displayDeleteDialog" modal class="w-full max-w-md mx-4">
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
                            Hapus data karyawan {{ selectedUser?.name }}?
                        </p>
                        <p class="text-sm text-red-700">
                            Karyawan ini akan dihapus dari sistem dan tidak dapat mengakses akun mereka lagi.
                            Pastikan Anda telah mempertimbangkan keputusan ini dengan baik.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-3">
                <Button
                    label="Batal"
                    @click="displayDeleteDialog = false"
                    class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                    text
                />
                <Button
                    label="Hapus Karyawan"
                    icon="pi pi-trash"
                    @click="deleteUser"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700 transition-colors duration-200"
                    :loading="form.processing"
                />
            </div>
        </template>
    </Dialog>
</template>
