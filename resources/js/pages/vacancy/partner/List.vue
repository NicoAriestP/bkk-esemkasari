<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

import { BreadcrumbItem } from '@/types';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from "primevue/inputtext";
import Button from 'primevue/button';
import Tooltip from 'primevue/tooltip';

import dayjs from 'dayjs';
import 'dayjs/locale/id';

dayjs.locale('id');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Lowongan Kerja',
        href: '/partners/vacancies',
    },
];

const props = defineProps({
    models: {
        type: Array as () => any[], // Diubah agar lebih spesifik
        required: true,
    },
});

const filters = ref();

const openCreatePage = () => {
    router.get(route('partners.vacancies.create'));
}

const openEditPage = (id: number) => {
    router.get(route('partners.vacancies.edit', id));
}

// PERUBAHAN: Fungsi baru untuk navigasi ke halaman pelamar
const openApplicantsPage = (id: number) => {
    // Asumsi nama route Anda adalah 'partners.vacancies.applicants'
    router.get(route('partners.vacancies.applicants', id));
}


watch(filters, (newValue) => {
    router.get(route("partners.vacancies.index"), { search: newValue }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

</script>

<template>
    <Head title="Lowongan Kerja" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <DataTable :value="props.models" paginator removableSort row-hover :rows="10" :rows-per-page-options="[10, 20, 50, 100]"
            tableStyle="min-width: 50rem">

            <!-- PERUBAHAN: Event @rowClick dihapus dari DataTable untuk menghindari konflik -->

            <template #header>
                <div class="flex justify-between items-center">
                    <InputText class="w-72" v-model="filters" placeholder="Cari posisi atau lokasi..." />
                    <Button label="Tambah" @click="openCreatePage" icon="pi pi-plus" />
                </div>
            </template>
            <template #empty>
                <span class="text-center">No data found.</span>
            </template>

            <Column field="title" sortable header="Posisi"></Column>
            <Column field="location" sortable header="Lokasi"></Column>
            <Column field="due_at" sortable header="Tenggat Waktu">
                <template #body="slotProps">
                    {{ dayjs(slotProps.data.due_at).format('DD MMMM YYYY') }}
                </template>
            </Column>

            <!-- PERUBAHAN: Penambahan kolom baru untuk Aksi -->
            <Column header="Aksi" bodyClass="!text-center" style="width: 10rem">
                <template #body="slotProps">
                    <div class="flex justify-center items-center gap-2">
                         <!-- Tombol untuk melihat pelamar -->
                        <Button
                            class="!text-blue-500 hover:!text-blue-600"
                            icon="pi pi-users"
                            severity="info"
                            variant="link"
                            v-tooltip.top="'Lihat Pelamar'"
                            @click="openApplicantsPage(slotProps.data.id)"
                        />
                        <!-- Tombol untuk mengedit -->
                        <Button
                            class="!text-yellow-500 hover:!text-yellow-600"
                            icon="pi pi-pencil"
                            severity="secondary"
                            variant="link"
                            v-tooltip.top="'Ubah Lowongan'"
                            @click="openEditPage(slotProps.data.id)"
                        />
                    </div>
                </template>
            </Column>

        </DataTable>
    </AppLayout>
</template>
