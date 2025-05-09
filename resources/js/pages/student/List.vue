<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { useToast } from 'primevue/usetoast';

import { BreadcrumbItem } from '@/types';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from "primevue/inputtext";
import Button from 'primevue/button';
import Drawer from 'primevue/drawer';
import Dialog from 'primevue/dialog';
import Toast from 'primevue/toast';

const toast = useToast();

const props = defineProps({
    year: {
        type: Object,
        required: true,
    },
    studentClass: {
        type: Object,
        required: true,
    },
    models: {
        type: Array,
        required: true,
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Angkatan' ,
        href: '/years',
    },
    {
        title: props.year.year,
        href: '/years',
    },
    {
        title: 'Kelas',
        href: route('student-classes.index', props.year.id),
    },
    {
        title: props.studentClass.name,
        href: route('student-classes.index', props.year.id),
    },
    {
        title: 'Siswa',
        href: '#',
    },
];

const filters = ref();
const selectedStudent = ref<any>(null);
const displayDrawer = ref(false);
const displayDeleteDialog = ref(false);

const form = useForm({
    id: null,
});

const formatGender = (gender: string) => {
    return gender === 'laki-laki' ? 'Laki-laki' : 'Perempuan';
};

const openDrawer = (student: any) => {
    selectedStudent.value = student;
    displayDrawer.value = true;
};

const closeDrawer = () => {
    selectedStudent.value = null;
    displayDrawer.value = false;
};

const confirmDelete = () => {
    displayDeleteDialog.value = true;
};

const deleteStudent = () => {
    if (!selectedStudent.value) {
        return;
    }

    form.delete(route('students.destroy', selectedStudent.value.id), {
        errorBag: 'Siswa',
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Sukses',
                detail: 'Siswa berhasil dihapus!',
                life: 5000,
            });
            displayDeleteDialog.value = false;
            form.reset();
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Gagal',
                detail: 'Gagal menghapus siswa!',
                life: 5000,
            });
            form.reset();
        },
    });
};

const openCreatePage = () => {
    router.get(route('students.create', props.studentClass.id));
};

watch(filters, (newValue) => {
    router.get(route("students.index", props.studentClass.id), { search: newValue }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});
</script>

<template>
    <Head title="Siswa" />
    <Toast />
    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable :value="props.models" paginator removableSort row-hover :rows="10" :rows-per-page-options="[10, 20, 50, 100]"
            tableStyle="min-width: 50rem"
            @rowClick="(event: any) => openDrawer(event.data)">
            <template #header>
                <div class="flex justify-between items-center">
                    <InputText class="w-72" v-model="filters" placeholder="Search ..." />
                    <Button label="Tambah" @click="openCreatePage" variant="primary" icon="pi pi-plus" />
                </div>
            </template>
            <template #empty>
                <span class="text-center">No data found.</span>
            </template>

            <Column field="name" sortable header="Nama"></Column>
            <Column field="nisn" sortable header="NISN"></Column>
            <Column field="phone" sortable header="No. Telepon"></Column>
            <Column field="gender" sortable header="Jenis Kelamin">
                <template #body="{ data }">
                    {{ formatGender(data.gender) }}
                </template>
            </Column>
        </DataTable>

        <Drawer v-model:visible="displayDrawer" :style="{ width: '40vw' }" position="right" :modal="true" @hide="closeDrawer">
            <template #header>
                <h3 class="m-0">Detail Siswa</h3>
            </template>

            <div class="grid">
                <div class="col-12">
                    <h4>Informasi Pribadi</h4>
                    <div class="grid">
                        <div class="col-6">
                            <strong>Nama:</strong> {{ selectedStudent?.name }}
                        </div>
                        <div class="col-6">
                            <strong>NISN:</strong> {{ selectedStudent?.nisn }}
                        </div>
                        <div class="col-6">
                            <strong>Jenis Kelamin:</strong> {{ formatGender(selectedStudent?.gender) }}
                        </div>
                        <div class="col-6">
                            <strong>Tanggal Lahir:</strong> {{ selectedStudent?.born_date }}
                        </div>
                        <div class="col-6">
                            <strong>Telepon:</strong> {{ selectedStudent?.phone }}
                        </div>
                        <div class="col-6">
                            <strong>Email:</strong> {{ selectedStudent?.email }}
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <h4>Informasi Fisik</h4>
                    <div class="grid">
                        <div class="col-6">
                            <strong>Tinggi Badan:</strong> {{ selectedStudent?.height }} cm
                        </div>
                        <div class="col-6">
                            <strong>Berat Badan:</strong> {{ selectedStudent?.weight }} kg
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <h4>Alamat</h4>
                    <div class="grid">
                        <div class="col-12">
                            <strong>Alamat:</strong> {{ selectedStudent?.address }}
                        </div>
                        <div class="col-6">
                            <strong>Provinsi:</strong> {{ selectedStudent?.province }}
                        </div>
                        <div class="col-6">
                            <strong>Kota:</strong> {{ selectedStudent?.city }}
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <h4>Status</h4>
                    <div class="grid">
                        <div class="col-6">
                            <strong>Lulus:</strong> {{ selectedStudent?.is_graduated ? 'Ya' : 'Tidak' }}
                        </div>
                        <div class="col-6">
                            <strong>Menikah:</strong> {{ selectedStudent?.is_married ? 'Ya' : 'Tidak' }}
                        </div>
                    </div>
                </div>
            </div>

            <template #footer>
                <Button label="Hapus" icon="pi pi-trash" severity="danger" @click="confirmDelete" />
            </template>
        </Drawer>

        <Dialog v-model:visible="displayDeleteDialog" modal header="Konfirmasi Hapus" :style="{ width: '500px' }">
            <div class="flex align-items-center justify-content-center">
                <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                <span>Apakah Anda yakin ingin menghapus siswa ini?</span>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" text @click="displayDeleteDialog = false" />
                <Button label="Hapus" icon="pi pi-check" severity="danger" @click="deleteStudent" />
            </template>
        </Dialog>
    </AppLayout>
</template>
