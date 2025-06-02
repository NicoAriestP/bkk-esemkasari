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
import Textarea from 'primevue/textarea';
import Toast from 'primevue/toast';

const toast = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Mitra DU/DI',
        href: '/partners',
    },
];

const props = defineProps({
    models: {
        type: Array,
        required: true,
    },
});

const filters = ref();
const selectedPartner = ref<any>(null);
const displayDeleteDialog = ref(false);
const dialogVisible = ref(false);
const dialogMode = ref(''); // add | edit

const form = useForm({
    id: null,
    name: '',
    email: '',
    phone: '',
    address: '',
    password: '',
});

const openCreateDialog = () => {
    form.reset();
    dialogMode.value = 'add';
    dialogVisible.value = true;
};

const openEditDialog = (partner: any) => {
    form.id = partner.id;
    form.name = partner.name;
    form.email = partner.email;
    form.phone = partner.phone;
    form.address = partner.address;
    form.password = partner.password;
    dialogMode.value = 'edit';
    dialogVisible.value = true;
};

const confirmDelete = (partner: any) => {
    selectedPartner.value = partner;
    displayDeleteDialog.value = true;
};

const createPartner = () => {
    form.post(route('partners.store'), {
        errorBag: 'partner',
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Sukses',
                detail: 'Mitra DU/DI berhasil ditambahkan!',
                life: 5000,
            });
            dialogVisible.value = false;
            form.reset();
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Terjadi kesalahan saat menambahkan mitra DU/DI',
                life: 5000,
            });
        },
    });
};

const updatePartner = () => {
    form.put(
        route('partners.update', {
            model: form.id,
        }),
        {
            errorBag: 'partner',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Mitra DU/DI berhasil diperbarui!',
                    life: 5000,
                });
                dialogVisible.value = false;
                form.reset();
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Terjadi kesalahan saat memperbarui mitra DU/DI',
                    life: 5000,
                });
            },
        },
    );
};

const deletePartner = () => {
    if (!selectedPartner.value) {
        return;
    }

    form.delete(
        route('partners.destroy', {
            model: selectedPartner.value.id,
        }),
        {
            errorBag: 'Partner',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Mitra DU/DI berhasil dihapus!',
                    life: 5000,
                });
                displayDeleteDialog.value = false;
                form.reset();
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Terjadi kesalahan saat menghapus mitra DU/DI',
                    life: 5000,
                });
            },
        },
    );
};

watch(filters, (newValue) => {
    router.get(
        route('partners.index'),
        { search: newValue },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
});
</script>

<template>
    <Toast />
    <Head title="Mitra DU/DI" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable
            :value="props.models"
            paginator
            removableSort
            row-hover
            :rows="10"
            :rows-per-page-options="[10, 20, 50, 100]"
            tableStyle="min-width: 50rem"
        >
            <template #header>
                <div class="flex items-center justify-between">
                    <InputText class="w-72" v-model="filters" placeholder="Search ..." />
                    <Button label="Tambah" @click="openCreateDialog" variant="primary" icon="pi pi-plus" />
                </div>
            </template>
            <template #empty>
                <span class="text-center">No data found.</span>
            </template>

            <Column field="name" sortable header="Nama"></Column>
            <Column field="email" sortable header="Email"></Column>
            <Column field="phone" sortable header="No. Telp"></Column>
            <Column field="address" sortable header="Alamat"></Column>
            <Column style="width: 10%">
                <template #body="slotProps">
                    <div class="flex justify-center">
                        <Button
                            style="color: #eab308 !important"
                            icon="pi pi-pencil"
                            variant="link"
                            severity="warn"
                            @click="openEditDialog(slotProps.data)"
                        />
                        <Button
                            style="color: #dc2626 !important"
                            icon="pi pi-trash"
                            variant="link"
                            severity="danger"
                            @click="confirmDelete(slotProps.data)"
                        />
                    </div>
                </template>
            </Column>
        </DataTable>

        <Dialog
            v-model:visible="dialogVisible"
            modal
            :header="dialogMode === 'add' ? 'Tambah Mitra DU/DI' : 'Ubah Mitra DU/DI'"
            class="w-full max-w-lg sm:max-w-xl md:max-w-2xl"
        >
            <div class="flex flex-col gap-y-4 p-2 lg:p-4">
                <div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 lg:col-span-1">
                            <label for="name" class="text-foreground block text-sm font-medium">Nama</label>
                            <InputText id="name" v-model="form.name" class="mt-1 w-full" :class="{ 'p-invalid': form.errors.name }" />
                            <small v-if="form.errors.name" class="p-error text-xs">{{ form.errors.name }}</small>
                        </div>
                        <div class="col-span-2 lg:col-span-1">
                            <label for="phone" class="text-foreground block text-sm font-medium">No. Telepon</label>
                            <InputText id="phone" v-model="form.phone" class="mt-1 w-full" :class="{ 'p-invalid': form.errors.phone }" />
                            <small v-if="form.errors.phone" class="p-error text-xs">{{ form.errors.phone }}</small>
                        </div>
                        <div class="col-span-2">
                            <label for="address" class="text-foreground block text-sm font-medium">Alamat</label>
                            <Textarea
                                id="address"
                                v-model="form.address"
                                class="mt-1 w-full"
                                rows="3"
                                :class="{ 'p-invalid': form.errors.address }"
                            />
                            <small v-if="form.errors.address" class="p-error text-xs">{{ form.errors.address }}</small>
                        </div>
                        <div class="col-span-2 lg:col-span-1">
                            <label for="email" class="text-foreground block text-sm font-medium">Email</label>
                            <InputText
                                inputmode="email"
                                id="email"
                                v-model="form.email"
                                class="mt-1 w-full"
                                :class="{ 'p-invalid': form.errors.email }"
                            />
                            <small v-if="form.errors.email" class="p-error text-xs">{{ form.errors.email }}</small>
                        </div>
                        <div class="col-span-2 lg:col-span-1">
                            <label for="password" class="text-foreground block text-sm font-medium">Password</label>
                            <Password
                                id="password"
                                v-model="form.password"
                                class="mt-1"
                                :class="{ 'p-invalid': form.errors.password }"
                                :feedback="true"
                                min-length="8"
                                toggleMask
                            />
                            <small v-if="form.errors.password" class="p-error text-xs">{{ form.errors.password }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <template #footer>
                <Button label="Batal" icon="pi pi-times" class="p-button-text p-button-secondary" @click="dialogVisible = false" />
                <Button
                    :label="dialogMode === 'add' ? 'Simpan' : 'Update'"
                    icon="pi pi-check"
                    class="p-button-success"
                    @click="dialogMode === 'add' ? createPartner() : updatePartner()"
                />
            </template>
        </Dialog>

        <Dialog v-model:visible="displayDeleteDialog" modal header="Konfirmasi Hapus" :style="{ width: '450px' }">
            <div class="text-center">
                <i class="pi pi-exclamation-triangle mb-4 text-red-500" style="font-size: 3rem"></i>
                <p>Apakah Anda yakin ingin menghapus mitra DU/DI ini?</p>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" text @click="displayDeleteDialog = false" />
                <Button label="Hapus" icon="pi pi-check" severity="danger" @click="deletePartner" />
            </template>
        </Dialog>
    </AppLayout>
</template>
