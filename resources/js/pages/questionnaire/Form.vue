<template>
    <Head :title="isNewRecord ? 'Buat Kuesioner' : 'Edit Kuesioner'" />
    <Toast />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Header -->
            <Card>
                <template #content>
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                {{ isNewRecord ? 'Buat Kuesioner Baru' : 'Edit Kuesioner' }}
                            </h1>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ isNewRecord ? 'Isi form di bawah untuk membuat kuesioner baru' : 'Perbarui informasi kuesioner' }}
                            </p>
                        </div>
                        <Button
                            label="Kembali"
                            icon="pi pi-arrow-left"
                            severity="secondary"
                            outlined
                            @click="router.visit(route('questionnaires.index'))"
                        />
                    </div>
                </template>
            </Card>

            <!-- Form -->
            <form @submit.prevent="handleSubmit">
                <div class="space-y-6">
                    <!-- Informasi Kuesioner -->
                    <Card>
                        <template #title>
                            <div class="flex items-center gap-2">
                                <i class="pi pi-file-edit text-blue-600"></i>
                                <span>Informasi Kuesioner</span>
                            </div>
                        </template>
                        <template #content>
                            <div class="space-y-4">
                                <!-- Judul -->
                                <div>
                                    <label for="title" class="mb-2 block font-medium text-gray-700">
                                        Judul Kuesioner <span class="text-red-500">*</span>
                                    </label>
                                    <InputText
                                        id="title"
                                        v-model="form.title"
                                        placeholder="Masukkan judul kuesioner"
                                        class="w-full"
                                        :class="{ 'p-invalid': form.errors.title }"
                                    />
                                    <small v-if="form.errors.title" class="text-red-500">{{ form.errors.title }}</small>
                                </div>

                                <!-- Deskripsi -->
                                <div>
                                    <label for="description" class="mb-2 block font-medium text-gray-700">
                                        Deskripsi <span class="text-red-500">*</span>
                                    </label>
                                    <Textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="4"
                                        placeholder="Masukkan deskripsi kuesioner"
                                        class="w-full"
                                        :class="{ 'p-invalid': form.errors.description }"
                                    />
                                    <small v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</small>
                                </div>

                                <!-- Batas Waktu -->
                                <div>
                                    <label for="due_at" class="mb-2 block font-medium text-gray-700">Batas Waktu Pengisian</label>
                                    <Calendar
                                        id="due_at"
                                        v-model="form.due_at"
                                        showIcon
                                        dateFormat="dd/mm/yy"
                                        placeholder="Pilih tanggal batas waktu"
                                        class="w-full"
                                        :minDate="new Date()"
                                        :class="{ 'p-invalid': form.errors.due_at }"
                                    />
                                    <small v-if="form.errors.due_at" class="text-red-500">{{ form.errors.due_at }}</small>
                                    <small class="text-gray-500">Opsional. Kosongkan jika tidak ada batas waktu</small>
                                </div>
                            </div>
                        </template>
                    </Card>

                    <!-- Pertanyaan -->
                    <Card>
                        <template #title>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="pi pi-list text-blue-600"></i>
                                    <span>Daftar Pertanyaan</span>
                                </div>
                                <Button
                                    label="Tambah Pertanyaan"
                                    icon="pi pi-plus"
                                    size="small"
                                    @click="addQuestion"
                                    :disabled="form.processing"
                                />
                            </div>
                        </template>
                        <template #content>
                            <div v-if="form.questions.length === 0" class="py-8 text-center">
                                <i class="pi pi-inbox mb-3 text-4xl text-gray-400"></i>
                                <p class="text-gray-500">Belum ada pertanyaan. Klik tombol "Tambah Pertanyaan" untuk memulai.</p>
                            </div>

                            <div v-else class="space-y-4">
                                <TransitionGroup name="list">
                                    <Card
                                        v-for="(question, qIndex) in form.questions"
                                        :key="qIndex"
                                        class="border-l-4"
                                        :class="{
                                            'border-blue-500': question.type === 'dropdown',
                                            'border-green-500': question.type === 'checkbox',
                                            'border-yellow-500': question.type === 'fillable',
                                            'border-purple-500': question.type === 'date',
                                        }"
                                    >
                                        <template #content>
                                            <div class="space-y-4">
                                                <!-- Question Header -->
                                                <div class="flex items-start justify-between gap-4">
                                                    <div class="flex-1">
                                                        <div class="mb-2 flex items-center gap-2">
                                                            <span class="rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700">
                                                                Pertanyaan {{ qIndex + 1 }}
                                                            </span>
                                                            <Tag
                                                                v-if="question.type"
                                                                :value="getQuestionTypeLabel(question.type)"
                                                                :severity="getQuestionTypeSeverity(question.type)"
                                                                :icon="getQuestionTypeIcon(question.type)"
                                                            />
                                                        </div>
                                                    </div>
                                                    <Button
                                                        icon="pi pi-trash"
                                                        severity="danger"
                                                        text
                                                        rounded
                                                        @click="removeQuestion(qIndex)"
                                                        :disabled="form.processing"
                                                        v-tooltip.left="'Hapus Pertanyaan'"
                                                    />
                                                </div>

                                                <!-- Tipe Pertanyaan -->
                                                <div>
                                                    <label class="mb-2 block font-medium text-gray-700">
                                                        Tipe Pertanyaan <span class="text-red-500">*</span>
                                                    </label>
                                                    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                                                        <div
                                                            v-for="typeOption in questionTypes"
                                                            :key="typeOption.value"
                                                            @click="selectQuestionType(qIndex, typeOption.value)"
                                                            class="cursor-pointer rounded-lg border-2 p-3 transition-all hover:border-blue-400 hover:shadow-md"
                                                            :class="{
                                                                'border-blue-500 bg-blue-50': question.type === typeOption.value,
                                                                'border-gray-200': question.type !== typeOption.value,
                                                            }"
                                                        >
                                                            <div class="text-center">
                                                                <i
                                                                    :class="[
                                                                        typeOption.icon,
                                                                        'mb-2 text-2xl',
                                                                        question.type === typeOption.value ? 'text-blue-600' : 'text-gray-400'
                                                                    ]"
                                                                ></i>
                                                                <p class="text-sm font-medium" :class="question.type === typeOption.value ? 'text-blue-900' : 'text-gray-700'">
                                                                    {{ typeOption.label }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <small v-if="(form.errors as any)[`questions.${qIndex}.type`]" class="text-red-500">
                                                        {{ (form.errors as any)[`questions.${qIndex}.type`] }}
                                                    </small>
                                                </div>

                                                <!-- Pertanyaan -->
                                                <div>
                                                    <label class="mb-2 block font-medium text-gray-700">
                                                        Pertanyaan <span class="text-red-500">*</span>
                                                    </label>
                                                    <Textarea
                                                        v-model="question.question_title"
                                                        rows="3"
                                                        placeholder="Masukkan pertanyaan"
                                                        class="w-full"
                                                        :class="{ 'p-invalid': (form.errors as any)[`questions.${qIndex}.question_title`] }"
                                                    />
                                                    <small v-if="(form.errors as any)[`questions.${qIndex}.question_title`]" class="text-red-500">
                                                        {{ (form.errors as any)[`questions.${qIndex}.question_title`] }}
                                                    </small>
                                                </div>

                                                <!-- Catatan -->
                                                <div>
                                                    <label class="mb-2 block font-medium text-gray-700">Catatan/Petunjuk</label>
                                                    <InputText
                                                        v-model="question.notes"
                                                        placeholder="Tambahkan catatan atau petunjuk (opsional)"
                                                        class="w-full"
                                                    />
                                                    <small class="text-gray-500">Contoh: "Pilih salah satu jawaban yang sesuai"</small>
                                                </div>

                                                <!-- Opsi Jawaban (untuk dropdown & checkbox) -->
                                                <div v-if="question.type === 'dropdown' || question.type === 'checkbox'">
                                                    <div class="mb-2 flex items-center justify-between">
                                                        <label class="font-medium text-gray-700">
                                                            Opsi Jawaban <span class="text-red-500">*</span>
                                                        </label>
                                                        <Button
                                                            label="Tambah Opsi"
                                                            icon="pi pi-plus"
                                                            size="small"
                                                            text
                                                            @click="addOption(qIndex)"
                                                            :disabled="form.processing"
                                                        />
                                                    </div>

                                                    <div v-if="!question.options || question.options.length === 0" class="rounded-lg border-2 border-dashed border-gray-300 p-4 text-center">
                                                        <p class="text-sm text-gray-500">Belum ada opsi. Klik "Tambah Opsi" untuk menambahkan.</p>
                                                    </div>

                                                    <div v-else class="space-y-2">
                                                        <TransitionGroup name="list">
                                                            <div
                                                                v-for="(option, oIndex) in question.options"
                                                                :key="oIndex"
                                                                class="flex items-center gap-2"
                                                            >
                                                                <i
                                                                    :class="question.type === 'checkbox' ? 'pi pi-check-square' : 'pi pi-circle'"
                                                                    class="text-gray-400"
                                                                ></i>
                                                                <InputText
                                                                    v-model="option.option_label"
                                                                    :placeholder="`Opsi ${oIndex + 1}`"
                                                                    class="flex-1"
                                                                    :class="{ 'p-invalid': (form.errors as any)[`questions.${qIndex}.options.${oIndex}.option_label`] }"
                                                                />
                                                                <Button
                                                                    icon="pi pi-times"
                                                                    severity="danger"
                                                                    text
                                                                    rounded
                                                                    @click="removeOption(qIndex, oIndex)"
                                                                    :disabled="form.processing"
                                                                    v-tooltip.left="'Hapus Opsi'"
                                                                />
                                                            </div>
                                                        </TransitionGroup>
                                                    </div>
                                                    <small v-if="(form.errors as any)[`questions.${qIndex}.options`]" class="text-red-500">
                                                        {{ (form.errors as any)[`questions.${qIndex}.options`] }}
                                                    </small>
                                                </div>

                                                <!-- Preview Jawaban -->
                                                <div v-if="question.type" class="rounded-lg bg-gray-50 p-4">
                                                    <p class="mb-2 text-sm font-medium text-gray-700">Preview:</p>
                                                    <div v-if="question.type === 'dropdown' && question.options && question.options.length > 0">
                                                        <Dropdown
                                                            :options="question.options"
                                                            optionLabel="option_label"
                                                            placeholder="Pilih jawaban"
                                                            class="w-full"
                                                            disabled
                                                        />
                                                    </div>
                                                    <div v-else-if="question.type === 'checkbox' && question.options && question.options.length > 0" class="space-y-2">
                                                        <div v-for="(option, idx) in question.options.slice(0, 3)" :key="idx" class="flex items-center gap-2">
                                                            <Checkbox :inputId="`preview-${qIndex}-${idx}`" disabled />
                                                            <label :for="`preview-${qIndex}-${idx}`" class="text-sm text-gray-600">{{ option.option_label }}</label>
                                                        </div>
                                                        <p v-if="question.options.length > 3" class="text-xs text-gray-500">+{{ question.options.length - 3 }} opsi lainnya</p>
                                                    </div>
                                                    <div v-else-if="question.type === 'fillable'">
                                                        <Textarea rows="3" placeholder="Jawaban akan diisi oleh responden" class="w-full" disabled />
                                                    </div>
                                                    <div v-else-if="question.type === 'date'">
                                                        <Calendar showIcon placeholder="DD/MM/YYYY" class="w-full" disabled />
                                                    </div>
                                                    <div v-else>
                                                        <p class="text-sm text-gray-500">Pilih tipe pertanyaan terlebih dahulu</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </Card>
                                </TransitionGroup>
                            </div>

                            <small v-if="form.errors.questions" class="text-red-500">{{ form.errors.questions }}</small>
                        </template>
                    </Card>

                    <!-- Actions -->
                    <Card>
                        <template #content>
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    <i class="pi pi-info-circle mr-1"></i>
                                    Pastikan semua informasi sudah benar sebelum menyimpan
                                </div>
                                <div class="flex gap-2">
                                    <Button
                                        label="Batal"
                                        severity="secondary"
                                        outlined
                                        @click="router.visit(route('questionnaires.index'))"
                                        :disabled="form.processing"
                                    />
                                    <Button
                                        :label="isNewRecord ? 'Simpan Kuesioner' : 'Perbarui Kuesioner'"
                                        icon="pi pi-save"
                                        type="submit"
                                        :loading="form.processing"
                                        :disabled="form.processing || !isFormValid"
                                    />
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import AppLayout from '@/layouts/AppLayout.vue';
import Button from 'primevue/button';
import Card from 'primevue/card';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Calendar from 'primevue/calendar';
import Dropdown from 'primevue/dropdown';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import Toast from 'primevue/toast';
import type { BreadcrumbItem } from '@/types';

const toast = useToast();

// Types
export type QuestionType = 'dropdown' | 'checkbox' | 'fillable' | 'date';

interface QuestionOption {
    id?: number;
    question_id?: number;
    option_label: string;
    created_at?: string;
    updated_at?: string;
}

interface QuestionnaireQuestion {
    id?: number;
    questionnaire_id?: number;
    question_title: string;
    type: QuestionType;
    type_label?: string;
    notes?: string | null;
    questionOptions?: QuestionOption[];
    created_at?: string;
    updated_at?: string;
    options?: QuestionOption[];
}

interface Questionnaire {
    id?: number;
    title: string;
    description: string;
    due_at?: string | null;
    questions?: QuestionnaireQuestion[];
}

const props = defineProps<{
    model: Questionnaire;
    isNewRecord: boolean;
}>();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Kuesioner', href: route('questionnaires.index') },
    { title: props.isNewRecord ? 'Buat Kuesioner' : 'Edit Kuesioner', href: '#' },
]);

const questionTypes = [
    { label: 'Pilihan', value: 'dropdown' as QuestionType, icon: 'pi pi-list', requiresOptions: true },
    { label: 'Checkbox', value: 'checkbox' as QuestionType, icon: 'pi pi-check-square', requiresOptions: true },
    { label: 'Isian', value: 'fillable' as QuestionType, icon: 'pi pi-align-left', requiresOptions: false },
    { label: 'Tanggal', value: 'date' as QuestionType, icon: 'pi pi-calendar', requiresOptions: false },
];

const form = useForm<{
    title: string;
    description: string;
    due_at: Date | null;
    questions: QuestionnaireQuestion[];
}>({
    title: props.model.title || '',
    description: props.model.description || '',
    due_at: props.model.due_at ? new Date(props.model.due_at) : null,
    questions: [],
});

// Initialize questions from model
onMounted(() => {
    if (props.model.questions && props.model.questions.length > 0) {
        form.questions = props.model.questions.map((q: QuestionnaireQuestion) => ({
            question_title: q.question_title,
            type: q.type,
            notes: q.notes || '',
            options: q.questionOptions?.map((o: QuestionOption) => ({ option_label: o.option_label })) || [],
        }));
    }
});

const isFormValid = computed(() => {
    if (!form.title || !form.description) return false;
    if (form.questions.length === 0) return false;

    for (const question of form.questions) {
        if (!question.question_title || !question.type) return false;

        // Validate options for dropdown and checkbox
        if ((question.type === 'dropdown' || question.type === 'checkbox')) {
            if (!question.options || question.options.length === 0) return false;
            if (question.options.some((opt: QuestionOption) => !opt.option_label)) return false;
        }
    }

    return true;
});

const addQuestion = () => {
    form.questions.push({
        question_title: '',
        type: 'dropdown',
        notes: '',
        options: [],
    });
};

const removeQuestion = (index: number) => {
    form.questions.splice(index, 1);
};

const selectQuestionType = (qIndex: number, type: QuestionType) => {
    // Reset pertanyaan dan catatan saat tipe diubah
    form.questions[qIndex].question_title = '';
    form.questions[qIndex].notes = '';
    form.questions[qIndex].type = type;

    // Initialize options for dropdown/checkbox if empty
    if (type === 'dropdown' || type === 'checkbox') {
        form.questions[qIndex].options = [{ option_label: '' }];
    } else {
        // Clear options for fillable and date types
        form.questions[qIndex].options = [];
    }
};

const addOption = (qIndex: number) => {
    if (!form.questions[qIndex].options) {
        form.questions[qIndex].options = [];
    }
    form.questions[qIndex].options!.push({ option_label: '' });
};

const removeOption = (qIndex: number, oIndex: number) => {
    form.questions[qIndex].options?.splice(oIndex, 1);
};

const getQuestionTypeLabel = (type: QuestionType): string => {
    const typeOption = questionTypes.find((t) => t.value === type);
    return typeOption?.label || type;
};

const getQuestionTypeSeverity = (type: QuestionType): 'success' | 'info' | 'warn' | 'danger' => {
    switch (type) {
        case 'dropdown':
            return 'info';
        case 'checkbox':
            return 'success';
        case 'fillable':
            return 'warn';
        case 'date':
            return 'danger';
        default:
            return 'info';
    }
};

const getQuestionTypeIcon = (type: QuestionType): string => {
    const typeOption = questionTypes.find((t) => t.value === type);
    return typeOption?.icon || 'pi pi-question-circle';
};

const handleSubmit = () => {
    if (!isFormValid.value) {
        return;
    }

    const submitData = {
        title: form.title,
        description: form.description,
        due_at: form.due_at ? form.due_at.toISOString().split('T')[0] : null,
        questions: form.questions.map((q) => ({
            question_title: q.question_title,
            type: q.type,
            notes: q.notes || null,
            options: (q.type === 'dropdown' || q.type === 'checkbox') ? q.options : [],
        })),
    };

    if (props.isNewRecord) {
        form.transform(() => submitData).post(route('questionnaires.store'), {
            errorBag: 'questionnaire',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Kuesioner berhasil dibuat!',
                    life: 5000,
                });
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Gagal',
                    detail: 'Terjadi kesalahan saat menyimpan kuesioner',
                    life: 5000,
                });
            },
        });
    } else {
        form.transform(() => submitData).put(route('questionnaires.update', props.model.id!), {
            errorBag: 'questionnaire',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Kuesioner berhasil diperbarui!',
                    life: 5000,
                });
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Gagal',
                    detail: 'Terjadi kesalahan saat memperbarui kuesioner',
                    life: 5000,
                });
            },
        });
    }
};
</script>

<style scoped>
/* List transition animations */
.list-enter-active,
.list-leave-active {
    transition: all 0.3s ease;
}

.list-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}

.list-leave-to {
    opacity: 0;
    transform: translateX(-10px);
}

.list-move {
    transition: transform 0.3s ease;
}
</style>
