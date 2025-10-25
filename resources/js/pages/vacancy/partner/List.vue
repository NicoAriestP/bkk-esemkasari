<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'
import { Head, router } from '@inertiajs/vue3'
import dayjs from 'dayjs'
import 'dayjs/locale/id'
import relativeTime from 'dayjs/plugin/relativeTime'
import Button from 'primevue/button'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import InputText from 'primevue/inputtext'
import { ref, watch } from 'vue'

// Setup dayjs
dayjs.locale('id')
dayjs.extend(relativeTime)

const props = defineProps({
    models: {
        type: Array as () => any[],
        required: true,
    },
});

const filters = ref<string>('')

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Lowongan Kerja', href: route('partners.vacancies.index') },
]

// Watch for filter changes and perform search
watch(filters, (newValue) => {
    if (newValue.length >= 3 || newValue.length === 0) {
        router.get(
            route('partners.vacancies.index'),
            { search: newValue },
            {
                preserveState: true,
                preserveScroll: false,
                replace: true
            }
        )
    }
})

const openCreatePage = () => {
    router.get(route('partners.vacancies.create'))
}

const openEditPage = (id: number) => {
    router.get(route('partners.vacancies.edit', id))
}

const openApplicantsPage = (id: number) => {
    router.get(route('partners.vacancies.applications', id ))
}
</script>

<template>
    <Head title="Lowongan Kerja" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Hero Header Section -->
        <div class="bg-gradient-to-br from-indigo-50 to-purple-100 rounded-2xl p-8 mb-8 border border-indigo-100">
            <div class="max-w-4xl mx-auto text-center">
                <div
                    class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-full text-sm font-medium mb-4">
                    <i class="pi pi-briefcase"></i>
                    <span>Portal Rekrutmen Mitra</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-3">
                    Kelola Lowongan Kerja
                </h1>
                <p class="text-lg text-gray-600 mb-6 max-w-2xl mx-auto">
                    Kelola dan pantau lowongan kerja Anda, serta temukan kandidat terbaik dari lulusan SMKN Purwosari
                </p>

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="pi pi-list text-blue-600 text-lg"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-2xl font-bold text-gray-900">{{ props.models.length }}</p>
                                <p class="text-sm text-gray-600">Total Lowongan</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="pi pi-users text-green-600 text-lg"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-2xl font-bold text-gray-900">{{props.models.reduce((sum, item) => sum +
                                    (item.applicants_count || 0), 0) }}</p>
                                <p class="text-sm text-gray-600">Total Pelamar</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="pi pi-check-circle text-purple-600 text-lg"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-2xl font-bold text-gray-900">{{props.models.reduce((sum, item) => sum +
                                    (item.qualified_applicants_count || 0), 0) }}</p>
                                <p class="text-sm text-gray-600">Terseleksi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Content Header -->
            <div class="border-b border-gray-100 px-6 py-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Daftar Lowongan Kerja</h2>
                        <p class="text-sm text-gray-500 mt-1">Kelola dan pantau semua lowongan kerja Anda</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Search Input -->
                        <div class="relative">
                            <!-- <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="pi pi-search text-gray-400"></i>
                            </div> -->
                            <InputText class="pl-10 w-full sm:w-80" v-model="filters"
                                placeholder="Cari posisi atau lokasi..." />
                        </div>
                        <!-- Add Button -->
                        <Button label="Tambah Lowongan" @click="openCreatePage" icon="pi pi-plus"
                            class="!bg-gradient-to-r !from-indigo-600 !to-purple-600 hover:!from-indigo-700 hover:!to-purple-700 !border-0 !font-semibold whitespace-nowrap" />
                    </div>
                </div>
            </div>

            <!-- Enhanced DataTable -->
            <div class="overflow-x-auto">
                <DataTable :value="props.models" paginator removableSort row-hover :rows="10"
                    :rows-per-page-options="[5, 10, 20, 50]" tableStyle="min-width: 50rem"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown">
                    <template #empty>
                        <div class="flex flex-col items-center justify-center py-12">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="pi pi-inbox text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Lowongan</h3>
                            <p class="text-gray-500 text-center max-w-sm mb-6">
                                Mulai buat lowongan kerja pertama Anda untuk menemukan kandidat terbaik
                            </p>
                            <Button label="Buat Lowongan" @click="openCreatePage" icon="pi pi-plus"
                                class="!bg-gradient-to-r !from-indigo-600 !to-purple-600 hover:!from-indigo-700 hover:!to-purple-700 !border-0" />
                        </div>
                    </template>

                    <!-- Job Title Column -->
                    <Column field="title" sortable header="Posisi"
                        headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6" bodyClass="py-4 px-6"
                        class="min-w-64">
                        <template #body="slotProps">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="pi pi-briefcase text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 line-clamp-1">{{ slotProps.data.title }}</p>
                                    <p class="text-sm text-gray-500">{{ slotProps.data.location }}</p>
                                </div>
                            </div>
                        </template>
                    </Column>

                    <!-- Deadline Column -->
                    <Column field="due_at" sortable header="Tenggat Waktu"
                        headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6" bodyClass="py-4 px-6"
                        class="w-48">
                        <template #body="slotProps">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-calendar text-gray-400"></i>
                                <span class="font-medium text-gray-700">{{ dayjs(slotProps.data.due_at).format('DD MMM YYYY') }}</span>
                            </div>
                        </template>
                    </Column>

                    <!-- Applicants Column -->
                    <Column field="applicants_count" sortable header="Pelamar"
                        headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6" bodyClass="py-4 px-6"
                        class="w-32">
                        <template #body="slotProps">
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="pi pi-users text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <span class="text-lg font-semibold text-gray-900">
                                        {{ slotProps.data.applicants_count || 0 }}
                                    </span>
                                    <span class="text-sm text-gray-500 ml-1">orang</span>
                                </div>
                            </div>
                        </template>
                    </Column>

                    <!-- Qualified Column -->
                    <Column field="qualified_applicants_count" sortable header="Terseleksi"
                        headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6" bodyClass="py-4 px-6"
                        class="w-32">
                        <template #body="slotProps">
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="pi pi-check-circle text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <span class="text-lg font-semibold text-gray-900">
                                        {{ slotProps.data.qualified_applicants_count || 0 }}
                                    </span>
                                    <span class="text-sm text-gray-500 ml-1">orang</span>
                                </div>
                            </div>
                        </template>
                    </Column>

                    <!-- Actions Column -->
                    <Column header="Aksi" headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                        bodyClass="py-4 px-6" class="w-40">
                        <template #body="slotProps">
                            <div class="flex items-center justify-center gap-2">
                                <!-- View Applicants Button -->
                                <Button icon="pi pi-users" severity="info" text rounded
                                    v-tooltip.top="'Seleksi Pelamar'" @click="openApplicantsPage(slotProps.data.id)"
                                    class="!w-10 !h-10 !text-blue-600 hover:!bg-blue-50" />
                                <!-- Edit Button -->
                                <Button icon="pi pi-pencil" severity="warning" text rounded
                                    v-tooltip.top="'Ubah Lowongan'" @click="openEditPage(slotProps.data.id)"
                                    class="!w-10 !h-10 !text-amber-600 hover:!bg-amber-50" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
