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
import Fieldset from 'primevue/fieldset';
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
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="card mb-8">
                <Steps :model="stepItems" :active-step="activeStep" :readonly="true" />
            </div>

            <!-- Konten Step 1 -->
            <div v-if="activeStep === 0">
                <Fieldset legend="Update Data Pribadi" :toggleable="true" class="!mb-8">
                    <div class="space-y-6 p-6">
                        <!-- Baris 1: Status dan Lokasi -->
                        <div class="grid grid-cols-1 gap-x-8 gap-y-6 md:grid-cols-3">
                            <div class="flex flex-col gap-2">
                                <label for="marriage-status">Status Pernikahan</label>
                                <Select id="marriage-status" v-model="form.is_married" :options="marriageStatusOptions" optionLabel="label" optionValue="value" placeholder="Pilih status" class="w-full" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="province">Tempat tinggal sekarang - Provinsi</label>
                                <InputText id="province" v-model="form.province" type="text" placeholder="Masukkan provinsi" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="city">Tempat tinggal sekarang - Kabupaten/Kota</label>
                                <InputText id="city" v-model="form.city" type="text" placeholder="Masukkan kab/kota" />
                            </div>
                        </div>

                        <!-- Baris 2: Email dan No. HP -->
                        <div class="grid grid-cols-1 gap-x-8 gap-y-6 md:grid-cols-2">
                            <div class="flex flex-col gap-2">
                                <label for="email">Email</label>
                                <InputText id="email" v-model="form.email" type="email" placeholder="contoh: contoh.email@gmail.com" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="phone">No. Telepon</label>
                                <InputText id="phone" v-model="form.phone" type="text" placeholder="08xxxxxxxxxx" />
                            </div>
                        </div>

                        <!-- Baris 3: Berat dan Tinggi Badan -->
                        <div class="grid grid-cols-1 gap-x-8 gap-y-6 md:grid-cols-2">
                            <div class="flex flex-col gap-2">
                                <label for="weight">Berat Badan (kg)</label>
                                <InputText id="weight" v-model="form.weight" type="number" placeholder="Contoh: 65" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="height">Tinggi Badan (cm)</label>
                                <InputText id="height" v-model="form.height" type="number" placeholder="Contoh: 170" />
                            </div>
                        </div>

                        <!-- Baris 4: Alamat -->
                        <div class="flex flex-col gap-2">
                            <label for="address">Alamat Tempat Tinggal Sekarang</label>
                            <Textarea id="address" v-model="form.address" rows="3" placeholder="Masukkan alamat lengkap..." autoResize />
                        </div>

                        <!-- Baris 5: Upload CV -->
                        <div class="flex flex-col gap-2">
                            <label for="cv" class="text-foreground block font-medium">Upload CV</label>
                            <div class="relative w-full lg:w-1/2">
                                <input ref="fileInput" type="file" id="cv" name="cv" @change="handleFileChange" class="hidden" accept=".pdf" />
                                <button type="button" @click="fileInput?.click()" class="bg-green-500 text-foreground hover:bg-green-600 inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition">
                                    📁 {{ form.cv_file ? 'Ubah' : 'Pilih' }} File CV
                                </button>
                                <span class="mx-2 my-2 text-sm text-foreground">
                                    {{ form.cv_file ? getFileName(form.cv_file) : 'Belum ada file yang dipilih' }}
                                </span>
                            </div>
                            <small class="text-foreground">Hanya file PDF, ukuran maksimal 2MB.</small>
                        </div>
                    </div>
                </Fieldset>

                <Fieldset legend="Aktivitas Lulusan" :toggleable="true" class="!mb-8">
                    <div class="space-y-8 p-6">
                        <div class="flex flex-col gap-3">
                            <p>Apakah Anda sedang melanjutkan studi di perguruan tinggi?</p>
                            <div class="flex items-center gap-6">
                                <div class="flex items-center">
                                    <RadioButton v-model="studentActivityData.isStudying" inputId="studyYes" name="isStudying" value="Ya" />
                                    <label for="studyYes" class="ml-2">Ya</label>
                                </div>
                                <div class="flex items-center">
                                    <RadioButton v-model="studentActivityData.isStudying" inputId="studyNo" name="isStudying" value="Tidak" />
                                    <label for="studyNo" class="ml-2">Tidak</label>
                                </div>
                            </div>
                        </div>
                        <template v-if="studentActivityData.isStudying === 'Tidak'">
                            <div class="flex flex-col gap-3">
                                <p>Dalam seminggu terakhir, apakah Anda sedang bekerja atau berwirausaha?</p>
                                <div class="flex items-center gap-6">
                                    <div class="flex items-center">
                                        <RadioButton v-model="studentActivityData.isWorking" inputId="workYes" name="isWorking" value="Ya" />
                                        <label for="workYes" class="ml-2">Ya</label>
                                    </div>
                                    <div class="flex items-center">
                                        <RadioButton v-model="studentActivityData.isWorking" inputId="workNo" name="isWorking" value="Tidak" />
                                        <label for="workNo" class="ml-2">Tidak</label>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-if="studentActivityData.isStudying === 'Tidak' && studentActivityData.isWorking === 'Ya'">
                            <div class="flex flex-col gap-3">
                                <p>
                                    Apa bentuk kegiatan/pekerjaan yang Anda lakukan?
                                    <br />
                                    <small class="text-foreground">(catatan: Youtuber, jual-beli online, dan usaha kreatif lainnya termasuk kategori wirausaha)</small>
                                </p>
                                <div class="mt-2 flex flex-col gap-3">
                                    <div v-for="option in workTypeOptions" :key="option" class="flex items-center">
                                        <RadioButton v-model="studentActivityData.workType" :inputId="option" name="workType" :value="option" />
                                        <label :for="option" class="ml-2">{{ option }}</label>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </Fieldset>
            </div>

            <!-- Konten Step 2 -->
            <div v-if="activeStep === 1">
                <ActivityDetails v-model="activityDetailsData" />
            </div>

            <!-- Konten Step 3 -->
            <div v-if="activeStep === 2">
                <StudentFeedback v-model="feedbackData" />
            </div>

            <!-- Tombol Aksi -->
            <div class="flex lg:flex-row flex-col lg:justify-between space-y-2 lg:space-y-0 mt-8">
                <Button v-if="activeStep > 0" label="Kembali" icon="pi pi-arrow-left" @click="prevStep" severity="secondary" />
                <div v-else></div>
                <Button v-if="activeStep < stepItems.length - 1" class="w-full lg:w-auto" label="Lanjut" icon="pi pi-arrow-right" iconPos="right" @click="nextStep" />
                <Button v-else class="w-full lg:w-auto" label="Selesai" icon="pi pi-check" severity="success" :loading="form.processing" @click="saveTracerStudy" />
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
:deep(.p-fieldset .p-fieldset-legend) {
    background-color: #3b82f6;
    color: #ffffff;
    border: none;
    padding: 0.75rem 1.25rem;
}

:deep(.p-fieldset .p-fieldset-legend):hover {
    background-color: #1d4ed8;
    color: #ffffff;
    border: none;
    padding: 0.75rem 1.25rem;
}

:deep(.p-fieldset-toggler) {
    color: #ffffff !important;
}
</style>
