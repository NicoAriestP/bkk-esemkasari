<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

import { BreadcrumbItem } from '@/types';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from "primevue/inputtext";
import Button from 'primevue/button';

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

const openCreatePage = () => {
    router.get(route('users.create'));
}

const openEditPage = (id: number) => {
    router.get(route('users.edit', id));
}

watch(filters, (newValue) => {
    router.get(route("users.index"), { search: newValue }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
});

</script>

<template>
    <Head title="Karyawan" />
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

            <Column field="name" sortable header="Nama"></Column>
            <Column field="email" sortable header="Email"></Column>
            <Column field="phone" sortable header="No. Telp"></Column>
        </DataTable>
    </AppLayout>
</template>
