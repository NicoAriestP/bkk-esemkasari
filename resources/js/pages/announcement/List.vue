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
        title: 'Pengumuman',
        href: '/announcements',
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
    router.get(route('announcements.create'));
}

const openEditPage = (id: number) => {
    router.get(route('announcements.edit', id));
}

watch(filters, (newValue) => {
    router.get(route("announcements.index"), { search: newValue }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

</script>

<template>
    <Head title="Pengumuman" />
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
            <template #empty> No data found. </template>

            <Column field="created_at" sortable header="Tanggal Dibuat">
                <template #body="slotProps">
                    {{ dayjs(slotProps.data.created_at).format('DD MMMM YYYY') }}
                </template>
            </Column>
            <Column field="title" sortable header="Judul"></Column>
            <Column field="created_by" sortable header="Pembuat">
                <template #body="slotProps">
                    {{ slotProps.data.created_by?.name }}
                </template>
            </Column>
        </DataTable>
    </AppLayout>
</template>
