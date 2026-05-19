<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types'

import Button from 'primevue/button'
import Card from 'primevue/card'
import Select from 'primevue/select'
import Checkbox from 'primevue/checkbox'
import Textarea from 'primevue/textarea'
import DatePicker from 'primevue/datepicker'

import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import 'dayjs/locale/id'

dayjs.extend(relativeTime)
dayjs.locale('id')

interface QuestionOption {
    id: number
    question_id: number
    option_label: string
}

interface QuestionnaireQuestion {
    id: number
    questionnaire_id: number
    question_title: string
    type: 'dropdown' | 'checkbox' | 'fillable' | 'date'
    notes: string | null
    question_options?: QuestionOption[]
}

interface Questionnaire {
    id: number
    title: string
    description: string
    due_at: string | null
    questions: QuestionnaireQuestion[]
}

interface QuestionnaireResponseStudent {
    id: number
    name: string
    nisn: string | null
    studentClass: {
        id: number
        name: string
        year: {
            id: number
            year: string
        }
    }
}

interface QuestionnaireResponseData {
    id: number
    submitted_at: string
    student: QuestionnaireResponseStudent
    answers: Record<number, number | string | number[] | null>
}

const props = defineProps<{
    questionnaire: Questionnaire
    response: QuestionnaireResponseData
}>()

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Kuisioner', href: route('questionnaires.index') },
    {
        title: 'Respons Kuisioner',
        href: route('questionnaires.responses.index', {
            model: props.questionnaire.id,
            year_id: props.response.student.studentClass.year.id,
            student_class_id: props.response.student.studentClass.id,
        }),
    },
    {
        title: props.questionnaire.title,
        href: route('questionnaires.responses.index', {
            model: props.questionnaire.id,
            year_id: props.response.student.studentClass.year.id,
            student_class_id: props.response.student.studentClass.id,
        }),
    },
    { title: props.response.student.name, href: '#' },
])

const backRoute = computed(() => route('questionnaires.responses.index', {
    model: props.questionnaire.id,
    year_id: props.response.student.studentClass.year.id,
    student_class_id: props.response.student.studentClass.id,
}))

const answers = ref<Record<number, any>>({})
const datePickerValues = ref<Record<number, Date | null>>({})

const resetAnswers = () => {
    answers.value = {}
    datePickerValues.value = {}

    props.questionnaire.questions.forEach((question) => {
        if (question.type === 'checkbox') {
            answers.value[question.id] = []
        } else if (question.type === 'date') {
            answers.value[question.id] = ''
            datePickerValues.value[question.id] = null
        } else {
            answers.value[question.id] = ''
        }
    })

    props.questionnaire.questions.forEach((question) => {
        const responseAnswer = props.response.answers?.[question.id]

        if (question.type === 'checkbox') {
            answers.value[question.id] = Array.isArray(responseAnswer)
                ? responseAnswer.map((value) => Number(value))
                : []
        } else if (question.type === 'date') {
            const dateValue = typeof responseAnswer === 'string' ? responseAnswer : ''
            answers.value[question.id] = dateValue
            datePickerValues.value[question.id] = dateValue ? dayjs(dateValue).toDate() : null
        } else if (question.type === 'dropdown') {
            answers.value[question.id] = responseAnswer !== null && responseAnswer !== undefined
                ? Number(responseAnswer)
                : ''
        } else {
            answers.value[question.id] = responseAnswer ?? ''
        }
    })
}

watch(
    () => [props.questionnaire.questions, props.response],
    () => {
        resetAnswers()
    },
    { immediate: true, deep: true },
)

props.questionnaire.questions.forEach((question) => {
    if (question.type === 'date') {
        watch(
            () => datePickerValues.value[question.id],
            (newValue) => {
                if (newValue instanceof Date) {
                    answers.value[question.id] = dayjs(newValue).format('YYYY-MM-DD')
                } else {
                    answers.value[question.id] = ''
                }
            },
            { immediate: true },
        )
    }
})

const getQuestionTypeLabel = (type: string) => {
    switch (type) {
        case 'dropdown': return 'Pilihan'
        case 'checkbox': return 'Kotak Centang'
        case 'fillable': return 'Isian'
        case 'date': return 'Tanggal'
        default: return 'Tidak Diketahui'
    }
}

const getQuestionTypeIcon = (type: string) => {
    switch (type) {
        case 'dropdown': return 'pi pi-list'
        case 'checkbox': return 'pi pi-check-square'
        case 'fillable': return 'pi pi-align-left'
        case 'date': return 'pi pi-calendar'
        default: return 'pi pi-question-circle'
    }
}

const getQuestionTypeColor = (type: string) => {
    switch (type) {
        case 'dropdown': return 'text-blue-600 bg-blue-50 border-blue-200'
        case 'checkbox': return 'text-green-600 bg-green-50 border-green-200'
        case 'fillable': return 'text-yellow-600 bg-yellow-50 border-yellow-200'
        case 'date': return 'text-purple-600 bg-purple-50 border-purple-200'
        default: return 'text-gray-600 bg-gray-50 border-gray-200'
    }
}

const getQuestionBorderColor = (type: string) => {
    switch (type) {
        case 'dropdown': return 'border-blue-500'
        case 'checkbox': return 'border-green-500'
        case 'fillable': return 'border-yellow-500'
        case 'date': return 'border-purple-500'
        default: return 'border-gray-400'
    }
}

const getQuestionNumberColor = (type: string) => {
    switch (type) {
        case 'dropdown': return 'bg-blue-600'
        case 'checkbox': return 'bg-green-600'
        case 'fillable': return 'bg-yellow-500'
        case 'date': return 'bg-purple-600'
        default: return 'bg-gray-500'
    }
}

const getQuestionHeaderBg = (type: string) => {
    switch (type) {
        case 'dropdown': return 'bg-blue-50/50'
        case 'checkbox': return 'bg-green-50/50'
        case 'fillable': return 'bg-yellow-50/50'
        case 'date': return 'bg-purple-50/50'
        default: return 'bg-gray-50/50'
    }
}

const getInitials = (name: string) => {
    return name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() ?? '')
        .join('')
}

const handleSubmit = () => {}
</script>

<template>
    <Head :title="questionnaire.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        📋 Detail Respons Kuesioner
                    </h1>
                    <p class="mt-1.5 text-sm text-gray-500">
                        Lihat detail respons kuisioner yang telah dikirimkan.
                    </p>
                </div>
                <div class="hidden sm:block">
                    <Button
                        label="Kembali"
                        icon="pi pi-arrow-left"
                        severity="secondary"
                        outlined
                        @click="router.visit(backRoute)"
                    />
                </div>
            </div>
        </div>

        <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="bg-gradient-to-r from-sky-50 via-blue-50 to-indigo-50 px-8 py-6">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-600 to-indigo-600 text-lg font-bold text-white shadow-sm">
                            {{ getInitials(response.student.name) }}
                        </div>
                        <div class="min-w-0">
                            <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                                {{ response.student.name }}
                            </h2>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="rounded-full border border-sky-200 bg-white/80 px-3 py-1 text-xs font-medium text-sky-700">
                                    NISN: {{ response.student.nisn ?? '-' }}
                                </span>
                                <span class="rounded-full border border-indigo-200 bg-white/80 px-3 py-1 text-xs font-medium text-indigo-700">
                                    {{ response.student.studentClass.name }}
                                </span>
                                <span class="rounded-full border border-violet-200 bg-white/80 px-3 py-1 text-xs font-medium text-violet-700">
                                    Angkatan {{ response.student.studentClass.year.year }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:min-w-[240px] sm:items-end">
                        <div class="rounded-2xl border border-white/70 bg-white/80 px-4 py-3 shadow-sm backdrop-blur-sm">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-500">
                                Waktu Submit
                            </div>
                            <div class="mt-1 text-sm font-semibold text-gray-900">
                                {{ dayjs(response.submitted_at).format('dddd, DD MMM YYYY') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ dayjs(response.submitted_at).format('HH:mm') }} WIB
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-8 py-6 bg-gradient-to-r from-purple-50 to-indigo-50">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="pi pi-file-edit text-white text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl font-bold text-gray-900 leading-tight mb-2 sm:text-2xl">
                            {{ questionnaire.title }}
                        </h2>
                        <p class="text-gray-600 leading-relaxed text-sm">
                            {{ questionnaire.description }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- <div v-if="questionnaire.due_at">
                <div v-if="isDeadlinePassed" class="px-6 py-3 bg-red-600 flex items-center gap-2">
                    <i class="pi pi-exclamation-triangle text-white text-sm"></i>
                    <span class="text-white text-sm font-semibold">Batas waktu pengisian telah berakhir. Anda tidak dapat lagi mengirimkan jawaban.</span>
                </div>

                <div
                    class="px-8 py-4 border-t flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
                    :class="isDeadlinePassed ? 'bg-red-50 border-red-100' : 'bg-gray-50 border-gray-100'"
                >
                    <div class="flex items-center gap-2 text-sm" :class="isDeadlinePassed ? 'text-red-700' : 'text-gray-600'">
                        <i class="pi pi-clock"></i>
                        <span class="font-medium">Batas Waktu Pengisian:</span>
                        <span>{{ dayjs(questionnaire.due_at).format('dddd, DD MMMM YYYY - HH:mm') }}</span>
                    </div>
                    <Tag
                        :value="getDeadlineStatus().label"
                        :severity="getDeadlineStatus().severity as any"
                        class="self-start sm:self-auto"
                    />
                </div>
            </div> -->
        </div>

        <form @submit.prevent="handleSubmit">
            <div class="space-y-6">
                <Card
                    v-for="(question, index) in questionnaire.questions"
                    :key="question.id"
                    class="border-l-4"
                    :class="getQuestionBorderColor(question.type)"
                >
                    <template #content>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3 rounded-lg p-3 -m-3 mb-1" :class="getQuestionHeaderBg(question.type)">
                                <div class="flex-shrink-0 w-8 h-8 text-white rounded-full flex items-center justify-center font-semibold text-sm" :class="getQuestionNumberColor(question.type)">
                                    {{ index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-3 mb-2">
                                        <h3 class="text-base font-semibold text-gray-900 flex-1">
                                            {{ question.question_title }}
                                            <span class="text-red-500">*</span>
                                        </h3>
                                        <div class="flex items-center gap-2 px-3 py-1 rounded-lg border flex-shrink-0" :class="getQuestionTypeColor(question.type)">
                                            <i :class="getQuestionTypeIcon(question.type)" class="text-sm"></i>
                                            <span class="text-xs font-medium">{{ getQuestionTypeLabel(question.type) }}</span>
                                        </div>
                                    </div>
                                    <p v-if="question.notes" class="text-sm text-gray-600 italic">
                                        {{ question.notes }}
                                    </p>
                                </div>
                            </div>

                            <div v-if="question.type === 'dropdown'" class="mt-4">
                                <Select
                                    v-model="answers[question.id]"
                                    :options="question.question_options"
                                    option-label="option_label"
                                    option-value="id"
                                    placeholder="Pilih jawaban"
                                    class="w-full"
                                    disabled
                                />
                            </div>

                            <div v-if="question.type === 'checkbox'" class="mt-4 space-y-3">
                                <div
                                    v-for="option in question.question_options"
                                    :key="option.id"
                                    class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors"
                                >
                                    <Checkbox
                                        v-model="answers[question.id]"
                                        :input-id="`question-${question.id}-option-${option.id}`"
                                        :value="option.id"
                                        disabled
                                    />
                                    <label
                                        :for="`question-${question.id}-option-${option.id}`"
                                        class="text-gray-700 cursor-pointer flex-1"
                                    >
                                        {{ option.option_label }}
                                    </label>
                                </div>
                            </div>

                            <div v-if="question.type === 'fillable'" class="mt-4">
                                <Textarea
                                    v-model="answers[question.id]"
                                    rows="4"
                                    placeholder="Ketik jawaban Anda di sini..."
                                    class="w-full"
                                    disabled
                                />
                            </div>

                            <div v-if="question.type === 'date'" class="mt-4">
                                <DatePicker
                                    v-model="datePickerValues[question.id]"
                                    show-icon
                                    date-format="dd/mm/yy"
                                    placeholder="Pilih tanggal"
                                    class="w-full"
                                    disabled
                                />
                            </div>
                        </div>
                    </template>
                </Card>

                <Card v-if="false" class="bg-gradient-to-r from-purple-50 to-indigo-50">
                    <template #content>
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="pi pi-info-circle"></i>
                                <span>Pastikan semua pertanyaan telah dijawab dengan benar</span>
                            </div>
                            <div class="flex gap-3">
                                <Button
                                    label="Batal"
                                    severity="secondary"
                                    outlined
                                    @click="router.visit(route('students.questionnaires.index'))"
                                />
                                <Button
                                    label="Kirim Jawaban"
                                    icon="pi pi-send"
                                    type="submit"
                                    class="!bg-purple-600 hover:!bg-purple-700 border-purple-600 hover:border-purple-700"
                                />
                            </div>
                        </div>
                    </template>
                </Card>
            </div>
        </form>
    </AppLayout>
</template>
