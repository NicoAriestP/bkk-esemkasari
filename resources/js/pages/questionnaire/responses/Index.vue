<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'
import { useToast } from 'primevue/usetoast'
import Button from 'primevue/button'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import Select from 'primevue/select'
import Tag from 'primevue/tag'
import Toast from 'primevue/toast'
import dayjs from 'dayjs'
import 'dayjs/locale/id'

dayjs.locale('id')

interface StudentClassOption {
    id: number
    year_id: number
    name: string
}

interface YearOption {
    id: number
    year: string
    studentClasses: StudentClassOption[]
}

interface QuestionnaireResponseSummary {
    id: number
    submitted_at: string
}

interface StudentRow {
    id: number
    name: string
    nisn: string | null
    response: QuestionnaireResponseSummary | null
}

interface Filters {
    year_id: number | null
    student_class_id: number | null
}

interface Totals {
    total_students: number
    responded_students: number
    unanswered_students: number
}

interface Questionnaire {
    id: number
    title: string
    description: string | null
}

const props = defineProps<{
    questionnaire: Questionnaire
    years: YearOption[]
    students: StudentRow[]
    filters: Filters
    totals: Totals
}>()

const toast = useToast()
const selectedYearId = ref<number | null>(null)
const selectedStudentClassId = ref<number | null>(null)

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Kuisioner',
        href: route('questionnaires.index'),
    },
    {
        title: props.questionnaire.title,
        href: route('questionnaires.responses.index', props.questionnaire.id),
    },
])

const selectedYear = computed(() => {
    return props.years.find((year) => year.id === selectedYearId.value) ?? null
})

const selectedYearClasses = computed(() => selectedYear.value?.studentClasses ?? [])

const canExport = computed(() => {
    return Boolean(selectedYearId.value && selectedStudentClassId.value)
})

const syncFilters = () => {
    selectedYearId.value = props.filters.year_id ?? null
    selectedStudentClassId.value = props.filters.student_class_id ?? null
}

watch(() => props.filters, syncFilters, { immediate: true, deep: true })

const loadIndex = (params: Record<string, number | null>) => {
    router.get(route('questionnaires.responses.index', props.questionnaire.id), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const onYearChange = (value: number | null) => {
    selectedYearId.value = value
    selectedStudentClassId.value = null

    if (!value) {
        loadIndex({})
        return
    }

    loadIndex({ year_id: value })
}

const onClassChange = (value: number | null) => {
    selectedStudentClassId.value = value

    if (!selectedYearId.value || !value) {
        return
    }

    loadIndex({
        year_id: selectedYearId.value,
        student_class_id: value,
    })
}

const openStudentResponse = (student: StudentRow) => {
    if (!student.response || !selectedYearId.value || !selectedStudentClassId.value) {
        toast.add({
            severity: 'warn',
            summary: 'Belum Mengisi',
            detail: 'Siswa ini belum mengisi kuesioner.',
            life: 4000,
        })

        return
    }

    router.get(route('questionnaires.responses.show', {
        model: props.questionnaire.id,
        year: selectedYearId.value,
        studentClass: selectedStudentClassId.value,
        student: student.id,
    }))
}

const exportResponses = () => {
    if (!canExport.value || !selectedYearId.value || !selectedStudentClassId.value) {
        toast.add({
            severity: 'warn',
            summary: 'Pilih Kelas',
            detail: 'Pilih angkatan dan kelas terlebih dahulu.',
            life: 4000,
        })

        return
    }

    window.location.href = route('questionnaires.responses.export', {
        model: props.questionnaire.id,
        year_id: selectedYearId.value,
        student_class_id: selectedStudentClassId.value,
    })
}

const formatSubmittedAt = (submittedAt: string) => dayjs(submittedAt).format('dddd, DD MMM YYYY HH:mm')
</script>

<template>
    <Head :title="`Respons ${questionnaire.title}`" />
    <Toast />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mb-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        Respons Kuesioner
                    </h1>
                    <p class="mt-1.5 text-sm text-gray-500">
                        Pilih angkatan lalu kelas untuk melihat respons siswa per kelas.
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-3 text-center text-sm">
                    <div class="rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm">
                        <div class="text-xs uppercase tracking-wide text-gray-500">Siswa</div>
                        <div class="mt-1 text-lg font-semibold text-gray-900">{{ totals.total_students }}</div>
                    </div>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 shadow-sm">
                        <div class="text-xs uppercase tracking-wide text-emerald-700">Sudah Mengisi</div>
                        <div class="mt-1 text-lg font-semibold text-emerald-700">{{ totals.responded_students }}</div>
                    </div>
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 shadow-sm">
                        <div class="text-xs uppercase tracking-wide text-amber-700">Belum Mengisi</div>
                        <div class="mt-1 text-lg font-semibold text-amber-700">{{ totals.unanswered_students }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50/60 px-6 py-5">
                    <div class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-end">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Angkatan</label>
                                <Select
                                    :model-value="selectedYearId"
                                    :options="years"
                                    option-label="year"
                                    option-value="id"
                                    placeholder="Pilih angkatan"
                                    class="w-full"
                                    show-clear
                                    @update:model-value="onYearChange"
                                />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Kelas</label>
                                <Select
                                    :model-value="selectedStudentClassId"
                                    :options="selectedYearClasses"
                                    option-label="name"
                                    option-value="id"
                                    placeholder="Pilih kelas"
                                    class="w-full"
                                    show-clear
                                    :disabled="!selectedYearId"
                                    @update:model-value="onClassChange"
                                />
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <Button
                                label="Export"
                                icon="pi pi-file-excel"
                                severity="primary"
                                :disabled="!canExport"
                                @click="exportResponses"
                            />
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div v-if="!selectedYearId" class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-200 bg-gray-50/70 px-6 py-12 text-center text-gray-500">
                        <i class="pi pi-filter text-4xl text-gray-300"></i>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Pilih angkatan terlebih dahulu</h3>
                        <p class="mt-1 max-w-lg text-sm text-gray-500">
                            Setelah angkatan dipilih, daftar kelas akan disaring otomatis dan Anda bisa lanjut ke daftar siswa.
                        </p>
                    </div>

                    <div v-else-if="!selectedStudentClassId" class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-200 bg-gray-50/70 px-6 py-12 text-center text-gray-500">
                        <i class="pi pi-building text-4xl text-gray-300"></i>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900">Pilih kelas untuk melihat respons</h3>
                        <p class="mt-1 max-w-lg text-sm text-gray-500">
                            Hanya kelas dari angkatan yang dipilih yang akan muncul di dropdown kelas.
                        </p>
                    </div>

                    <DataTable
                        v-else
                        :value="students"
                        paginator
                        removable-sort
                        row-hover
                        :rows="10"
                        :rows-per-page-options="[10, 20, 50]"
                        table-style="min-width: 100%"
                        paginator-template="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                        current-page-report-template="Menampilkan {first} - {last} dari {totalRecords} data"
                        @row-click="(event: any) => openStudentResponse(event.data)"
                    >
                        <template #empty>
                            <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                                <i class="pi pi-inbox mb-4 text-4xl text-gray-300"></i>
                                <h3 class="mb-1 text-lg font-medium text-gray-900">Tidak ada respons</h3>
                                <p class="text-sm text-gray-500">Belum ada data respons untuk kelas ini.</p>
                            </div>
                        </template>

                        <Column header="Siswa" header-class="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6" body-class="py-4 px-6">
                            <template #body="slotProps">
                                <div>
                                    <p class="font-medium text-gray-900">{{ slotProps.data.name }}</p>
                                    <p class="text-xs text-gray-500">NISN: {{ slotProps.data.nisn ?? '-' }}</p>
                                </div>
                            </template>
                        </Column>

                        <Column header="Status" header-class="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6" body-class="py-4 px-6">
                            <template #body="slotProps">
                                <Tag
                                    :severity="slotProps.data.response ? 'success' : 'danger'"
                                    :value="slotProps.data.response ? 'Sudah Mengisi' : 'Belum Mengisi'"
                                />
                            </template>
                        </Column>

                        <Column header="Waktu Submit" header-class="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6" body-class="py-4 px-6 text-sm">
                            <template #body="slotProps">
                                <span v-if="slotProps.data.response" class="text-gray-900">
                                    {{ formatSubmittedAt(slotProps.data.response.submitted_at) }}
                                </span>
                                <span v-else class="text-gray-400 italic">-</span>
                            </template>
                        </Column>

                        <Column header="Aksi" header-class="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6" body-class="py-4 px-6">
                            <template #body="slotProps">
                                <Button
                                    icon="pi pi-eye"
                                    size="small"
                                    text
                                    rounded
                                    severity="info"
                                    :disabled="!slotProps.data.response"
                                    @click.stop="openStudentResponse(slotProps.data)"
                                    v-tooltip.top="slotProps.data.response ? 'Lihat detail respons' : 'Siswa belum mengisi'"
                                />
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
