<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { useToast } from 'primevue/usetoast';

// Import komponen untuk setiap step
import ActivityDetails from '@/pages/tracerStudy/ActivityDetails.vue';
import StudentFeedback from '@/pages/tracerStudy/StudentFeedback.vue';

// Import komponen PrimeVue dan Toast
import Button from 'primevue/button';
import Select from 'primevue/select';

import InputText from 'primevue/inputtext';
import RadioButton from 'primevue/radiobutton';
import Steps from 'primevue/steps';
import Textarea from 'primevue/textarea';
import Toast from 'primevue/toast';

// Inisialisasi Toast
const toast = useToast();

// --- Props dari Controller ---
const props = defineProps({
    student: { type: Object, required: true },
    studentClass: { type: Object, required: true },
    studentYear: { type: Object, required: true },
    studentActivityAnswer: { type: Object, default: () => ({}) },
    detailActivityAnswer: { type: Object, default: () => ({}) },
    studentUniversityAnswer: { type: Object, default: () => ({}) },
    studentWorkingAnswer: { type: Object, default: () => ({}) },
    studentEntrepreneurAnswer: { type: Object, default: () => ({}) },
    feedbackAnswer: { type: Object, default: () => ({}) },
});


// --- PERBAIKAN: Helper function untuk mem-parsing JSON dengan aman ---
const parseAnswers = (prop: any) => {
    // Cek jika prop ada, memiliki properti 'answers', dan 'answers' adalah string
    if (prop && prop.answers && typeof prop.answers === 'string') {
        try {
            // Jika ya, parse string JSON menjadi objek
            return JSON.parse(prop.answers);
        } catch (e) {
            console.error("Gagal mem-parsing JSON dari prop:", prop.answers, e);
            return {}; // Kembalikan objek kosong jika parsing gagal
        }
    }
    // Jika sudah berupa objek atau tidak ada, kembalikan apa adanya
    return prop?.answers || {};
};


// --- State & Navigasi ---
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Tracer Study', href: '#'}];
const activeStep = ref(0);
const stepItems = ref([{ label: 'Data Lulusan' }, { label: 'Detail Aktivitas' }, { label: 'Umpan Balik' }]);
const fileInput = ref<HTMLInputElement | null>(null);

// --- Form State dengan useForm ---
const form = useForm({
    is_married: props.student?.is_married ?? false,
    province: props.student?.province ?? '',
    city: props.student?.city ?? '',
    email: props.student?.email ?? '',
    phone: props.student?.phone ?? '',
    address: props.student?.address ?? '',
    height: props.student?.height ?? '',
    weight: props.student?.weight ?? '',
    cv_file: props.student?.cv_file ?? null,
    student_activity_answers: '',
    detail_activity_answers: '',
    student_working_answers: '',
    student_university_answers: '',
    student_entrepreneur_answers: '',
    student_feedback_answers: '',
});

// Gunakan helper function untuk menginisialisasi state reaktif
const studentActivityData = ref({
    isStudying: null,
    isWorking: null,
    workType: '',
    ...parseAnswers(props.studentActivityAnswer)
});

const activityDetailsData = ref({
    mainActivity: parseAnswers(props.detailActivityAnswer)?.mainActivity || null,
    workingData: parseAnswers(props.studentWorkingAnswer) || {},
    universityData: parseAnswers(props.studentUniversityAnswer) || {},
    entrepreneurData: parseAnswers(props.studentEntrepreneurAnswer) || {},
});

const feedbackData = ref(parseAnswers(props.feedbackAnswer) || {});


// --- Logika & Fungsi ---
const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.cv_file = target.files[0];
    }
};

const getFileName = (file: File | string | null | undefined): string => {
    if (!file) return '';
    if (typeof file === 'string') return file.split('/').pop() || '';
    return file.name;
};

watch(() => studentActivityData.value.isStudying, () => {
    studentActivityData.value.isWorking = null;
    studentActivityData.value.workType = '';
});
watch(() => studentActivityData.value.isWorking, (newValue) => {
    if (newValue !== 'Ya') {
        studentActivityData.value.workType = '';
    }
});

watch(() => activityDetailsData.value.mainActivity, (newValue) => {
    const labelMap: Record<string, string> = {
        bekerja: 'Lulusan Bekerja',
        kuliah: 'Lulusan Kuliah',
        wirausaha: 'Lulusan Wirausaha',
    };
    stepItems.value[1].label = labelMap[newValue as string] || 'Detail Aktivitas';
}, { immediate: true });


const nextStep = () => {
    if (activeStep.value < stepItems.value.length - 1) {
        activeStep.value++;
    }
};
const prevStep = () => {
    if (activeStep.value > 0) {
        activeStep.value--;
    }
};

const saveTracerStudy = () => {
    form.student_activity_answers = JSON.stringify(studentActivityData.value);
    form.detail_activity_answers = JSON.stringify({ mainActivity: activityDetailsData.value.mainActivity });
    form.student_working_answers = JSON.stringify(activityDetailsData.value.workingData);
    form.student_university_answers = JSON.stringify(activityDetailsData.value.universityData);
    form.student_entrepreneur_answers = JSON.stringify(activityDetailsData.value.entrepreneurData);
    form.student_feedback_answers = JSON.stringify(feedbackData.value);

    form.post(route('tracer-study.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Sukses',
                detail: 'Data Tracer Study berhasil disimpan!',
                life: 5000,
            });
        },
        onError: (errors) => {
            console.error("Error saving tracer study:", errors);
            toast.add({
                severity: 'error',
                summary: 'Gagal Menyimpan',
                detail: 'Terjadi kesalahan. Periksa kembali isian Anda.',
                life: 5000,
            });
        },
    });
};

const marriageStatusOptions = ref([
    { label: 'Belum Menikah', value: false },
    { label: 'Menikah', value: true }
]);
const workTypeOptions = ref([
    'Berwirausaha sendiri tanpa dibantu orang lain',
    'Berwirausaha dengan dibantu buruh/pekerja tak dibayar',
    'Berwirausaha dengan dibantu buruh/pekerja dibayar',
    'Membantu menjalankan usaha/wirausaha keluarga',
    'Buruh/karyawan/pegawai',
    'Pekerja bebas (tidak punya majikan tetap)',
]);
</script>

<template>
    <Head title="Tracer Study" />
    <Toast/>
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Hero Header Section -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-6 sm:p-8 mb-8 border border-blue-100">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-full text-sm font-medium mb-4">
                    <i class="pi pi-chart-line"></i>
                    <span>Tracer Study SMKN Purwosari</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">
                    Survey Alumni & Lulusan
                </h1>
                <p class="text-base sm:text-lg text-gray-600 mb-6 max-w-2xl mx-auto">
                    Bantu kami melacak perkembangan karir dan memberikan umpan balik untuk meningkatkan kualitas pendidikan
                </p>

                <!-- Progress Info -->
                <div class="bg-white/60 backdrop-blur-sm rounded-xl px-4 py-3 inline-flex items-center gap-3">
                    <div class="flex items-center gap-2 text-sm text-gray-700">
                        <i class="pi pi-user text-blue-600"></i>
                        <span class="font-medium">{{ props.student?.name }}</span>
                    </div>
                    <div class="w-px h-4 bg-gray-300"></div>
                    <div class="flex items-center gap-2 text-sm text-gray-700">
                        <i class="pi pi-graduation-cap text-blue-600"></i>
                        <span>{{ props.studentClass?.name }} - {{ props.studentYear?.year }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Steps Progress Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-8 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Progress Pengisian</h2>
                <p class="text-sm text-gray-600 mt-1">Langkah {{ activeStep + 1 }} dari {{ stepItems.length }}</p>
            </div>
            <div class="p-6">
                <Steps :model="stepItems" :active-step="activeStep" :readonly="true" class="!mb-0" />
            </div>
        </div>

            <!-- Step 1: Personal Data -->
            <div v-if="activeStep === 0" class="space-y-8">
                <!-- Personal Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <i class="pi pi-user"></i>
                            Update Data Pribadi
                        </h3>
                        <p class="text-blue-100 text-sm mt-1">Pastikan data pribadi Anda sudah lengkap dan terbaru</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Row 1: Marriage Status and Location -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="space-y-2">
                                <label for="marriage-status" class="block text-sm font-medium text-gray-700">Status Pernikahan</label>
                                <Select
                                    id="marriage-status"
                                    v-model="form.is_married"
                                    :options="marriageStatusOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Pilih status"
                                    class="w-full"
                                />
                            </div>
                            <div class="space-y-2">
                                <label for="province" class="block text-sm font-medium text-gray-700">Provinsi</label>
                                <InputText id="province" v-model="form.province" placeholder="Masukkan provinsi" class="w-full" />
                            </div>
                            <div class="space-y-2">
                                <label for="city" class="block text-sm font-medium text-gray-700">Kota/Kabupaten</label>
                                <InputText id="city" v-model="form.city" placeholder="Masukkan kota" class="w-full" />
                            </div>
                        </div>

                        <!-- Row 2: Contact Information -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <InputText id="email" v-model="form.email" type="email" placeholder="contoh@email.com" class="w-full" />
                            </div>
                            <div class="space-y-2">
                                <label for="phone" class="block text-sm font-medium text-gray-700">No. HP</label>
                                <InputText id="phone" v-model="form.phone" placeholder="08123456789" class="w-full" />
                            </div>
                        </div>

                        <!-- Row 3: Physical Data -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label for="weight" class="block text-sm font-medium text-gray-700">Berat Badan (kg)</label>
                                <InputText id="weight" v-model="form.weight" type="number" placeholder="60" class="w-full" />
                            </div>
                            <div class="space-y-2">
                                <label for="height" class="block text-sm font-medium text-gray-700">Tinggi Badan (cm)</label>
                                <InputText id="height" v-model="form.height" type="number" placeholder="170" class="w-full" />
                            </div>
                        </div>

                        <!-- Row 4: Address -->
                        <div class="space-y-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                            <Textarea id="address" v-model="form.address" rows="3" placeholder="Masukkan alamat lengkap..." class="w-full" autoResize />
                        </div>

                        <!-- Row 5: CV Upload -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">Upload CV Terbaru</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition-colors duration-200">
                                <input ref="fileInput" type="file" accept=".pdf,.doc,.docx" @change="handleFileChange" class="hidden" />
                                <div class="space-y-3">
                                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto">
                                        <i class="pi pi-upload text-blue-600 text-2xl"></i>
                                    </div>
                                    <div>
                                        <Button label="Pilih File CV" icon="pi pi-folder-open" outlined @click="fileInput?.click()" />
                                        <p class="text-sm text-gray-500 mt-2">Format: PDF, DOC, DOCX (Max. 5MB)</p>
                                    </div>
                                    <div v-if="form.cv_file" class="bg-green-50 border border-green-200 rounded-lg p-3 mt-3">
                                        <div class="flex items-center gap-2 text-green-700">
                                            <i class="pi pi-check-circle"></i>
                                            <span class="text-sm font-medium">{{ getFileName(form.cv_file) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graduate Activity Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <i class="pi pi-briefcase"></i>
                            Aktivitas Lulusan
                        </h3>
                        <p class="text-green-100 text-sm mt-1">Ceritakan aktivitas Anda setelah lulus dari SMK</p>
                    </div>

                    <div class="p-6 space-y-8">
                        <!-- Study Status Question -->
                        <div class="space-y-4">
                            <h4 class="font-semibold text-lg text-gray-900">Apakah Anda sedang melanjutkan studi?</h4>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-blue-300 transition-colors duration-200" :class="{ 'border-blue-500 bg-blue-50': studentActivityData.isStudying === 'Ya' }">
                                    <RadioButton v-model="studentActivityData.isStudying" inputId="studyYes" name="isStudying" value="Ya" />
                                    <label for="studyYes" class="ml-3 text-gray-700 font-medium cursor-pointer">Ya, sedang kuliah</label>
                                </div>
                                <div class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-blue-300 transition-colors duration-200" :class="{ 'border-blue-500 bg-blue-50': studentActivityData.isStudying === 'Tidak' }">
                                    <RadioButton v-model="studentActivityData.isStudying" inputId="studyNo" name="isStudying" value="Tidak" />
                                    <label for="studyNo" class="ml-3 text-gray-700 font-medium cursor-pointer">Tidak melanjutkan studi</label>
                                </div>
                            </div>
                        </div>

                        <!-- Working Status Question (shown when not studying) -->
                        <template v-if="studentActivityData.isStudying === 'Tidak'">
                            <div class="space-y-4 p-4 bg-gray-50 rounded-xl">
                                <h4 class="font-semibold text-lg text-gray-900">Apakah Anda sedang bekerja?</h4>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-green-300 transition-colors duration-200 bg-white" :class="{ 'border-green-500 bg-green-50': studentActivityData.isWorking === 'Ya' }">
                                        <RadioButton v-model="studentActivityData.isWorking" inputId="workYes" name="isWorking" value="Ya" />
                                        <label for="workYes" class="ml-3 text-gray-700 font-medium cursor-pointer">Ya, sedang bekerja</label>
                                    </div>
                                    <div class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-green-300 transition-colors duration-200 bg-white" :class="{ 'border-green-500 bg-green-50': studentActivityData.isWorking === 'Tidak' }">
                                        <RadioButton v-model="studentActivityData.isWorking" inputId="workNo" name="isWorking" value="Tidak" />
                                        <label for="workNo" class="ml-3 text-gray-700 font-medium cursor-pointer">Tidak bekerja</label>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Work Type Question (shown when working) -->
                        <template v-if="studentActivityData.isStudying === 'Tidak' && studentActivityData.isWorking === 'Ya'">
                            <div class="space-y-4 p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                                <h4 class="font-semibold text-lg text-gray-900">Jenis pekerjaan Anda saat ini?</h4>
                                <p class="text-sm text-gray-600 mb-4">
                                    <em>Youtuber, jual-beli online, dan usaha kreatif lainnya termasuk kategori wirausaha</em>
                                </p>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div v-for="option in workTypeOptions" :key="option" class="flex items-center p-3 border border-yellow-200 rounded-lg hover:border-yellow-400 transition-colors duration-200 bg-white" :class="{ 'border-yellow-500 bg-yellow-50': studentActivityData.workType === option }">
                                        <RadioButton v-model="studentActivityData.workType" :inputId="option" name="workType" :value="option" />
                                        <label :for="option" class="ml-3 text-gray-700 font-medium cursor-pointer">{{ option }}</label>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Step 2: Activity Details -->
            <div v-if="activeStep === 1" class="space-y-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-violet-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <i class="pi pi-chart-line"></i>
                            Detail Aktivitas
                        </h3>
                        <p class="text-purple-100 text-sm mt-1">Berikan detail tentang aktivitas yang sedang Anda jalani</p>
                    </div>

                    <div class="p-6">
                        <ActivityDetails v-model="activityDetailsData" />
                    </div>
                </div>
            </div>

            <!-- Step 3: Student Feedback -->
            <div v-if="activeStep === 2" class="space-y-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-600 to-red-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <i class="pi pi-comments"></i>
                            Feedback & Saran
                        </h3>
                        <p class="text-orange-100 text-sm mt-1">Berikan masukan untuk perbaikan pendidikan di SMK</p>
                    </div>

                    <div class="p-6">
                        <StudentFeedback v-model="feedbackData" />
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-8">
                <div class="flex flex-col sm:flex-row sm:justify-between gap-4">
                    <Button
                        v-if="activeStep > 0"
                        label="Kembali"
                        icon="pi pi-arrow-left"
                        @click="prevStep"
                        severity="secondary"
                        outlined
                        class="flex-1 sm:flex-none min-w-32"
                    />
                    <div v-else class="hidden sm:block"></div>

                    <div class="flex gap-3">
                        <Button
                            v-if="activeStep < stepItems.length - 1"
                            label="Lanjut"
                            icon="pi pi-arrow-right"
                            iconPos="right"
                            @click="nextStep"
                            class="flex-1 sm:flex-none min-w-32"
                        />
                        <Button
                            v-else
                            label="Selesai"
                            icon="pi pi-check"
                            severity="success"
                            :loading="form.processing"
                            @click="saveTracerStudy"
                            class="flex-1 sm:flex-none min-w-32"
                        />
                    </div>
                </div>

                <!-- Progress Indicator -->
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Langkah {{ activeStep + 1 }} dari {{ stepItems.length }}</span>
                        <span>{{ Math.round(((activeStep + 1) / stepItems.length) * 100) }}% selesai</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div
                            class="bg-gradient-to-r from-blue-500 to-indigo-500 h-2 rounded-full transition-all duration-300"
                            :style="{ width: `${((activeStep + 1) / stepItems.length) * 100}%` }"
                        ></div>
                    </div>
                </div>
            </div>
    </AppLayout>
</template>

<style scoped>
:deep(.p-steps .p-steps-item.p-highlight .p-steps-number) {
    background-color: #3b82f6;
    color: #ffffff;
}
:deep(.p-steps .p-steps-item.p-highlight .p-menuitem-link) {
    border-color: #3b82f6 !important;
}
/* Enhanced card styling for form sections */
:deep(.p-card) {
    border-radius: 1rem;
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
}

/* Custom progress bar animation */
.progress-bar-animated {
    animation: progress-fill 0.5s ease-in-out;
}

@keyframes progress-fill {
    from {
        width: 0%;
    }
    to {
        width: 100%;
    }
}
</style>
