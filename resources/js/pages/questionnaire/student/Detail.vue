<script setup lang="ts">
import { computed, ref, watch, onMounted } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { useToast } from 'primevue/usetoast';

// Import PrimeVue components
import Button from 'primevue/button';
import Card from 'primevue/card';
import Select from 'primevue/select';
import Checkbox from 'primevue/checkbox';
import Textarea from 'primevue/textarea';
import DatePicker from 'primevue/datepicker';
import Toast from 'primevue/toast';
import Tag from 'primevue/tag';

// dayjs
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import 'dayjs/locale/id';

dayjs.extend(relativeTime);
dayjs.locale('id');

const toast = useToast();
const page = usePage();

interface QuestionOption {
    id: number;
    question_id: number;
    option_label: string;
}

interface QuestionnaireQuestion {
    id: number;
    questionnaire_id: number;
    question_title: string;
    type: 'dropdown' | 'checkbox' | 'fillable' | 'date';
    notes: string | null;
    question_options?: QuestionOption[];
}

interface Questionnaire {
    id: number;
    title: string;
    description: string;
    due_at: string | null;
    questions: QuestionnaireQuestion[];
}

const props = defineProps<{
    model: Questionnaire;
}>();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Kuesioner', href: route('students.questionnaires.index') },
    { title: props.model.title, href: '#' },
]);

// Initialize form answers
const answers = ref<Record<number, any>>({});
const datePickerValues = ref<Record<number, Date | null>>({});

props.model.questions.forEach((question) => {
    if (question.type === 'checkbox') {
        answers.value[question.id] = [];
    } else if (question.type === 'date') {
        answers.value[question.id] = '';
        datePickerValues.value[question.id] = null;
    } else {
        answers.value[question.id] = '';
    }
});

// Watch for changes from DatePicker components
props.model.questions.forEach((question) => {
    if (question.type === 'date') {
        watch(
            () => datePickerValues.value[question.id],
            (newValue) => {
                if (newValue instanceof Date) {
                    answers.value[question.id] = dayjs(newValue).format('YYYY-MM-DD');
                } else {
                    answers.value[question.id] = '';
                }
            },
            { immediate: true }
        );
    }
});

const form = useForm({
    answers: answers.value,
});

// Check if deadline has passed
const isDeadlinePassed = computed(() => {
    if (!props.model.due_at) return false;
    return dayjs().isAfter(dayjs(props.model.due_at));
});

// Get deadline status
const getDeadlineStatus = () => {
    if (!props.model.due_at) return { label: 'Tidak Ada Batas Waktu', severity: 'info' };

    const now = dayjs();
    const deadline = dayjs(props.model.due_at);
    const daysLeft = deadline.diff(now, 'day');

    if (daysLeft < 0) {
        return { label: 'Sudah Lewat', severity: 'danger' };
    } else if (daysLeft === 0) {
        return { label: 'Hari Ini', severity: 'warning' };
    } else if (daysLeft <= 3) {
        return { label: `${daysLeft} Hari Lagi`, severity: 'warning' };
    } else if (daysLeft <= 7) {
        return { label: `${daysLeft} Hari Lagi`, severity: 'success' };
    } else {
        return { label: dayjs(props.model.due_at).format('DD MMM YYYY'), severity: 'info' };
    }
};

// Validate form
const isFormValid = computed(() => {
    for (const question of props.model.questions) {
        const answer = answers.value[question.id];

        if (question.type === 'checkbox') {
            if (!answer || answer.length === 0) return false;
        } else if (question.type === 'date') {
            if (!answer) return false;
        } else {
            if (!answer || answer.trim() === '') return false;
        }
    }
    return true;
});

// Get question type label in Indonesian
const getQuestionTypeLabel = (type: string) => {
    switch (type) {
        case 'dropdown': return 'Pilihan';
        case 'checkbox': return 'Kotak Centang';
        case 'fillable': return 'Isian';
        case 'date': return 'Tanggal';
        default: return 'Tidak Diketahui';
    }
};

// Get question type icon
const getQuestionTypeIcon = (type: string) => {
    switch (type) {
        case 'dropdown': return 'pi pi-list';
        case 'checkbox': return 'pi pi-check-square';
        case 'fillable': return 'pi pi-align-left';
        case 'date': return 'pi pi-calendar';
        default: return 'pi pi-question-circle';
    }
};

// Get question type color
const getQuestionTypeColor = (type: string) => {
    switch (type) {
        case 'dropdown': return 'text-blue-600 bg-blue-50 border-blue-200';
        case 'checkbox': return 'text-green-600 bg-green-50 border-green-200';
        case 'fillable': return 'text-yellow-600 bg-yellow-50 border-yellow-200';
        case 'date': return 'text-purple-600 bg-purple-50 border-purple-200';
        default: return 'text-gray-600 bg-gray-50 border-gray-200';
    }
};

// Handle flash messages for errors (when redirect back)
onMounted(() => {
    const flash = page.props.flash as { success?: string; error?: string };

    if (flash?.error) {
        toast.add({
            severity: 'error',
            summary: 'Gagal',
            detail: flash.error,
            life: 5000,
        });
    }
});

// Submit form
const handleSubmit = () => {
    if (!isFormValid.value) {
        toast.add({
            severity: 'warn',
            summary: 'Peringatan',
            detail: 'Mohon jawab semua pertanyaan sebelum mengirim',
            life: 5000,
        });
        return;
    }

    form.transform(() => ({ answers: answers.value }))
        .post(route('students.questionnaires.submit', props.model.id), {
            preserveScroll: true,
        });
};
</script>

<template>
    <Head :title="model.title" />
    <Toast />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl mb-2">
                        {{ model.title }}
                    </h1>
                    <p class="text-gray-600 leading-relaxed">
                        {{ model.description }}
                    </p>
                </div>
                <Button
                    label="Kembali"
                    icon="pi pi-arrow-left"
                    severity="secondary"
                    outlined
                    @click="router.visit(route('students.questionnaires.index'))"
                />
            </div>
        </div>

        <!-- Deadline Info Card -->
        <Card v-if="model.due_at" class="mb-6 border-l-4" :class="isDeadlinePassed ? 'border-red-500' : 'border-purple-500'">
            <template #content>
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center"
                        :class="isDeadlinePassed ? 'bg-red-100' : 'bg-purple-100'">
                        <i class="pi pi-clock text-xl" :class="isDeadlinePassed ? 'text-red-600' : 'text-purple-600'"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-medium text-gray-700">Batas Waktu Pengisian:</span>
                            <Tag
                                :value="getDeadlineStatus().label"
                                :severity="getDeadlineStatus().severity as any"
                            />
                        </div>
                        <p class="text-sm text-gray-600">
                            {{ dayjs(model.due_at).format('dddd, DD MMMM YYYY - HH:mm') }}
                        </p>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Deadline Passed Warning -->
        <Card v-if="isDeadlinePassed" class="mb-6 bg-red-50 border-red-200">
            <template #content>
                <div class="flex items-start gap-3">
                    <i class="pi pi-exclamation-triangle text-red-600 text-xl mt-0.5"></i>
                    <div>
                        <h3 class="font-semibold text-red-800 mb-1">Batas Waktu Telah Lewat</h3>
                        <p class="text-sm text-red-700">
                            Batas waktu pengisian kuesioner ini telah berakhir. Anda tidak dapat lagi mengirimkan jawaban.
                        </p>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Questions Form -->
        <form @submit.prevent="handleSubmit">
            <div class="space-y-6">
                <Card
                    v-for="(question, index) in model.questions"
                    :key="question.id"
                    class="border-l-4 border-purple-500"
                >
                    <template #content>
                        <div class="space-y-4">
                            <!-- Question Header -->
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center font-semibold text-sm">
                                    {{ index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900 flex-1">
                                            {{ question.question_title }}
                                            <span class="text-red-500">*</span>
                                        </h3>
                                        <div class="flex items-center gap-2 px-3 py-1 rounded-lg border" :class="getQuestionTypeColor(question.type)">
                                            <i :class="getQuestionTypeIcon(question.type)" class="text-sm"></i>
                                            <span class="text-xs font-medium">{{ getQuestionTypeLabel(question.type) }}</span>
                                        </div>
                                    </div>
                                    <p v-if="question.notes" class="text-sm text-gray-600 italic">
                                        {{ question.notes }}
                                    </p>
                                </div>
                            </div>

                            <!-- Answer Input - Dropdown -->
                            <div v-if="question.type === 'dropdown'" class="mt-4">
                                <Select
                                    v-model="answers[question.id]"
                                    :options="question.question_options"
                                    optionLabel="option_label"
                                    optionValue="option_label"
                                    placeholder="Pilih jawaban"
                                    class="w-full"
                                    :disabled="isDeadlinePassed"
                                />
                            </div>

                            <!-- Answer Input - Checkbox -->
                            <div v-if="question.type === 'checkbox'" class="mt-4 space-y-3">
                                <div
                                    v-for="option in question.question_options"
                                    :key="option.id"
                                    class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors"
                                >
                                    <Checkbox
                                        v-model="answers[question.id]"
                                        :inputId="`question-${question.id}-option-${option.id}`"
                                        :value="option.option_label"
                                        :disabled="isDeadlinePassed"
                                    />
                                    <label
                                        :for="`question-${question.id}-option-${option.id}`"
                                        class="text-gray-700 cursor-pointer flex-1"
                                    >
                                        {{ option.option_label }}
                                    </label>
                                </div>
                            </div>

                            <!-- Answer Input - Fillable -->
                            <div v-if="question.type === 'fillable'" class="mt-4">
                                <Textarea
                                    v-model="answers[question.id]"
                                    rows="4"
                                    placeholder="Ketik jawaban Anda di sini..."
                                    class="w-full"
                                    :disabled="isDeadlinePassed"
                                />
                            </div>

                            <!-- Answer Input - Date -->
                            <div v-if="question.type === 'date'" class="mt-4">
                                <DatePicker
                                    v-model="datePickerValues[question.id]"
                                    show-icon
                                    date-format="dd/mm/yy"
                                    placeholder="Pilih tanggal"
                                    class="w-full"
                                    :disabled="isDeadlinePassed"
                                />
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Submit Section -->
                <Card class="bg-gradient-to-r from-purple-50 to-indigo-50">
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
                                    :disabled="form.processing"
                                />
                                <Button
                                    label="Kirim Jawaban"
                                    icon="pi pi-send"
                                    type="submit"
                                    :loading="form.processing"
                                    :disabled="form.processing || !isFormValid || isDeadlinePassed"
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

<style scoped>
/* Smooth transitions */
.transition-colors {
    transition: background-color 0.2s ease, border-color 0.2s ease;
}
</style>
