<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

import { BreadcrumbItem } from '@/types';

import InputText from "primevue/inputtext";
import Button from 'primevue/button';

const props = defineProps({
    model: {
        type: Object,
        required: true,
    },
    isNewRecord: {
        type: Boolean,
        required: true,
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pengumuman',
        href: '/announcements',
    },
    {
        title: props.isNewRecord ? 'Tambah' : 'Ubah',
        href: '#',
    },
];

</script>

<template>
    <Head title="Pengumuman" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <form>
            <div class="grid gap-2">
                <div class="col-12 md:col-6">
                    <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
                    <InputText id="title" v-model="model.title" required />
                </div>
                <div class="col-12 md:col-6">
                    <label for="content" class="block text-sm font-medium text-gray-700">Isi</label>
                    <InputText id="content" v-model="model.content" required />
                </div>
            </div>
        </form>
    </AppLayout>
</template>
