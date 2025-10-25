<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import dayjs from 'dayjs'
import 'dayjs/locale/id'

dayjs.locale('id')

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pengumuman',
        href: '/announcements',
    },
]

const props = defineProps({
    models: {
        type: Array,
        required: true,
    },
})

const filters = ref()

const openCreatePage = () => {
    router.get(route('announcements.create'))
}

const openEditPage = (id: number) => {
    router.get(route('announcements.edit', id))
}

watch(filters, (newValue) => {
    router.get(route('announcements.index'), { search: newValue }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
})

</script>

<template>
    <Head title="Pengumuman" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        Pengumuman
                    </h1>
                    <p class="mt-1.5 text-sm text-gray-500">
                        Kelola semua pengumuman yang telah dibuat
                    </p>
                </div>
                <Button
                    label="Tambah Pengumuman"
                    @click="openCreatePage"
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
                        <InputText
                            v-model="filters"
                            placeholder="Cari pengumuman..."
                            class="w-full sm:w-80 bg-white border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm"
                        />
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="pi pi-info-circle"></i>
                        <span>{{ props.models.length }} pengumuman</span>
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
                @rowClick="(event: any) => openEditPage(event.data.id)"
                tableStyle="min-width: 100%"
                paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                currentPageReportTemplate="Menampilkan {first} - {last} dari {totalRecords} data"
            >
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                        <i class="pi pi-inbox text-4xl mb-4 text-gray-300"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada data</h3>
                        <p class="text-sm text-gray-500">Belum ada pengumuman yang dibuat</p>
                    </div>
                </template>

                <Column
                    field="created_at"
                    sortable
                    header="Tanggal"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6 text-sm"
                    class="w-36"
                >
                    <template #body="slotProps">
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-900">
                                {{ dayjs(slotProps.data.created_at).format('DD MMM YYYY') }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ dayjs(slotProps.data.created_at).format('HH:mm') }}
                            </span>
                        </div>
                    </template>
                </Column>

                <Column
                    field="title"
                    sortable
                    header="Judul Pengumuman"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6"
                >
                    <template #body="slotProps">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-medium text-gray-900 line-clamp-2 hover:text-blue-600 transition-colors cursor-pointer">
                                    {{ slotProps.data.title }}
                                </h3>
                                <p v-if="slotProps.data.description" class="mt-1 text-xs text-gray-500 line-clamp-1">
                                    {{ slotProps.data.description }}
                                </p>
                            </div>
                        </div>
                    </template>
                </Column>

                <Column
                    field="created_by"
                    sortable
                    header="Pembuat"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6 text-sm"
                    class="w-48"
                >
                    <template #body="slotProps">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-xs font-medium text-white">
                                    {{ slotProps.data.created_by?.name?.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ slotProps.data.created_by?.name }}
                                </p>
                            </div>
                        </div>
                    </template>
                </Column>

                <Column
                    header="Aksi"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6"
                    class="w-24"
                >
                    <template #body="slotProps">
                        <div class="flex items-center justify-center">
                            <Button
                                icon="pi pi-pencil"
                                severity="warn"
                                @click.stop="openEditPage(slotProps.data.id)"
                                class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                text
                                size="small"
                                v-tooltip.top="'Edit pengumuman'"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AppLayout>
</template>

<style>
/* Line clamp utilities - keep these as they're needed for text truncation */
.line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1;
    line-clamp: 1;
}

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    line-clamp: 2;
}
</style>
