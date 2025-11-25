<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import Toast from 'primevue/toast'
import Dialog from 'primevue/dialog'
import { useToast } from 'primevue/usetoast'
import dayjs from 'dayjs'
import 'dayjs/locale/id'

dayjs.locale('id')

const toast = useToast()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kuisioner',
        href: '/questionnaires',
    },
]

const props = defineProps({
    models: {
        type: Array,
        required: true,
    },
})

const filters = ref('')
const selectedQuestionnaire = ref<any>(null)
const displayDeleteDialog = ref(false)

const openCreatePage = () => {
    router.get(route('questionnaires.create'))
}

const openEditPage = (id: number) => {
    router.get(route('questionnaires.edit', id))
}

const confirmDelete = (questionnaire: any) => {
    selectedQuestionnaire.value = questionnaire
    displayDeleteDialog.value = true
}

const deleteQuestionnaire = () => {
    if (!selectedQuestionnaire.value) {
        return
    }

    router.delete(route('questionnaires.destroy', selectedQuestionnaire.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Berhasil',
                detail: 'Kuesioner berhasil dihapus',
                life: 5000,
            })
            displayDeleteDialog.value = false
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Gagal',
                detail: 'Terjadi kesalahan saat menghapus kuesioner',
                life: 5000,
            })
        },
    })
}

watch(filters, (newValue) => {
    router.get(route('questionnaires.index'), { search: newValue }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
})
</script>

<template>
    <Head title="Kuisioner" />
    <Toast />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        Manajemen Kuisioner
                    </h1>
                    <p class="mt-1.5 text-sm text-gray-500">
                        Kelola kuisioner untuk siswa dan alumni sekolah
                    </p>
                </div>
                <Button
                    label="Buat Kuisioner"
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
                            placeholder="Cari kuisioner..."
                            class="w-full sm:w-80 bg-white border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm"
                        />
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="pi pi-info-circle"></i>
                        <span>{{ props.models.length }} kuisioner</span>
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
                        <p class="text-sm text-gray-500">Belum ada kuisioner yang dibuat</p>
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
                    header="Judul Kuisioner"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6"
                >
                    <template #body="slotProps">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
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
                    field="due_at"
                    sortable
                    header="Batas Waktu"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6 text-sm"
                    class="w-44"
                >
                    <template #body="slotProps">
                        <div>
                            <span class="font-medium text-gray-900">
                                {{ slotProps.data.due_at ? dayjs(slotProps.data.due_at).format('DD MMM YYYY') : '-'}}
                            </span>
                            <div v-if="slotProps.data.due_at" class="text-xs text-gray-500">
                                {{ dayjs(slotProps.data.due_at).format('HH:mm') }}
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
                            <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
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
                    class="w-32"
                >
                    <template #body="slotProps">
                        <div class="flex items-center justify-center gap-1">
                            <Button
                                icon="pi pi-pencil"
                                severity="warn"
                                @click.stop="openEditPage(slotProps.data.id)"
                                class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                text
                                size="small"
                                v-tooltip.top="'Edit kuisioner'"
                            />
                            <Button
                                icon="pi pi-trash"
                                severity="danger"
                                @click.stop="confirmDelete(slotProps.data)"
                                class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                text
                                size="small"
                                v-tooltip.top="'Hapus kuisioner'"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AppLayout>

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
                            Hapus kuesioner "{{ selectedQuestionnaire?.title }}"?
                        </p>
                        <p class="text-sm text-red-700">
                            Kuesioner ini akan dihapus dari sistem beserta semua pertanyaan dan data terkait.
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
                    label="Hapus Kuesioner"
                    icon="pi pi-trash"
                    severity="danger"
                    @click="deleteQuestionnaire"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700 transition-colors duration-200"
                />
            </div>
        </template>
    </Dialog>
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
