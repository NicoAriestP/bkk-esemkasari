<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

import { BreadcrumbItem } from '@/types';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from "primevue/inputtext";
import Button from 'primevue/button';

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
        type: Array,
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
            tableStyle="min-width: 50rem"
            @rowClick="(event: any) => openEditPage(event.data.id)">
            <template #header>
                <div class="flex justify-between items-center">
                    <InputText class="w-72" v-model="filters" placeholder="Search ..." />
                    <Button label="Tambah" @click="openCreatePage" variant="primary" icon="pi pi-plus" />
                </div>
            </template>
            <template #empty>
                <span class="text-center">No data found.</span>
            </template>

            <Column field="created_by" sortable header="Pembuat">
                <template #body="slotProps">
                    {{ slotProps.data.created_by?.name }}
                </template>
            </Column>
            <Column field="title" sortable header="Posisi"></Column>
            <Column field="location" sortable header="Lokasi"></Column>
            <Column field="due_at" sortable header="Tenggat Waktu">
                <template #body="slotProps">
                    {{ dayjs(slotProps.data.due_at).format('DD MMMM YYYY') }}
                </template>
            </Column>
        </DataTable>
    </AppLayout>
</template>
