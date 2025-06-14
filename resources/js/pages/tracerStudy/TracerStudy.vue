<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

// Import komponen untuk Step 2
import ActivityDetails from '@/pages/tracerStudy/ActivityDetails.vue';
import StudentFeedback from '@/pages/tracerStudy/StudentFeedback.vue';

// Import komponen PrimeVue
import Button from 'primevue/button';
import Select from 'primevue/select';
import Fieldset from 'primevue/fieldset';
import InputText from 'primevue/inputtext';
import RadioButton from 'primevue/radiobutton';
import Steps from 'primevue/steps';

// --- State untuk Form ---

// Navigasi & Stepper
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Tracer Study', href: '/tracer-study' }];
const activeStep = ref(0);
const stepItems = ref([{ label: 'Data Lulusan' }, { label: 'Detail Aktivitas' }, { label: 'Umpan Balik' }]);

// --- State untuk Step 1 ---

// Data Pribadi
const marriageStatus = ref(0);
const province = ref('');
const city = ref('');
const email = ref('');
const phone = ref('');
const cvFile = ref<File | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

// Aktivitas Lulusan (tetap di sini, di Step 1)
const isStudying = ref<string | null>(null);
const isWorking = ref<string | null>(null);
const workType = ref('');

// --- State untuk Step 2 ---
const mainActivity = ref<string | null>(null);
const studentFeedback = ref<string | null>(null);


// --- Fungsi & Logika ---

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        cvFile.value = target.files[0];
    }
};

const getFileName = (file: File | string | null | undefined): string => {
    if (!file) return '';
    if (typeof file === 'string') return file.split('/').pop() || '';
    return file.name;
};

// Logika Kondisional untuk Step 1
watch(isStudying, () => {
    isWorking.value = null;
    workType.value = '';
});
watch(isWorking, (newValue) => {
    if (newValue !== 'Ya') {
        workType.value = '';
    }
});

// Fungsi Navigasi Stepper
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

// Opsi Form
const marriageStatusOptions = ref([
    { label: 'Belum Menikah', value: 0 },
    { label: 'Menikah', value: 1 }
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

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 sm:p-6 lg:p-8">
            <!-- Stepper Navigasi -->
            <div class="card mb-8">
                <Steps :model="stepItems" :active-step="activeStep" :readonly="true" />
            </div>

            <!-- Konten Step 1: Data Pribadi & Aktivitas Lulusan -->
            <div v-if="activeStep === 0">
                <Fieldset legend="Update Data Pribadi" :toggleable="true" class="!mb-8">
                    <div class="space-y-6 p-6">
                        <!-- Baris 1: Status dan Lokasi -->
                        <div class="grid grid-cols-1 gap-x-8 gap-y-6 md:grid-cols-3">
                            <div class="flex flex-col gap-2">
                                <label for="marriage-status">Status Pernikahan</label>
                                <Select
                                    id="marriage-status"
                                    v-model="marriageStatus"
                                    :options="marriageStatusOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    placeholder="Pilih status"
                                    class="w-full"
                                />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="province">Tempat tinggal sekarang - Provinsi</label>
                                <InputText id="province" v-model="province" type="text" placeholder="Masukkan provinsi" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="city">Tempat tinggal sekarang - Kabupaten/Kota</label>
                                <InputText id="city" v-model="city" type="text" placeholder="Masukkan kab/kota" />
                            </div>
                        </div>

                        <!-- Baris 2: Email dan No. HP -->
                        <div class="grid grid-cols-1 gap-x-8 gap-y-6 md:grid-cols-2">
                            <div class="flex flex-col gap-2">
                                <label for="email">Email</label>
                                <InputText id="email" v-model="email" type="email" placeholder="contoh: contoh.email@gmail.com" />
                            </div>
                            <div class="flex flex-col gap-2">
                                <label for="phone">No. Telepon</label>
                                <InputText id="phone" v-model="phone" type="text" placeholder="08xxxxxxxxxx" />
                            </div>
                        </div>

                        <!-- Baris 3: Upload CV -->
                        <div class="flex flex-col gap-2">
                            <label for="cv" class="text-foreground block font-medium">Upload CV</label>
                            <div class="relative w-full lg:w-1/2">
                                <input
                                    ref="fileInput"
                                    type="file"
                                    id="cv"
                                    name="cv"
                                    @change="handleFileChange"
                                    class="hidden"
                                    accept=".pdf"
                                />
                                <button
                                    type="button"
                                    @click="fileInput?.click()"
                                    class="bg-green-500 text-foreground hover:bg-green-600 inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition"
                                >
                                    📁 {{ cvFile ? 'Ubah' : 'Pilih' }} File CV
                                </button>
                                <span class="mx-2 my-2 text-sm text-foreground">
                                    {{ cvFile ? getFileName(cvFile) : 'Belum ada file yang dipilih' }}
                                </span>
                            </div>
                            <small class="text-foreground">Hanya file PDF, ukuran maksimal 2MB.</small>
                        </div>
                    </div>
                </Fieldset>

                <Fieldset legend="Aktivitas Lulusan" :toggleable="true" class="!mb-8">
                    <div class="space-y-8 p-6">
                        <!-- Pertanyaan Studi -->
                        <div class="flex flex-col gap-3">
                            <p>Apakah Anda sedang melanjutkan studi di perguruan tinggi?</p>
                            <div class="flex items-center gap-6">
                                <div class="flex items-center">
                                    <RadioButton v-model="isStudying" inputId="studyYes" name="isStudying" value="Ya" />
                                    <label for="studyYes" class="ml-2">Ya</label>
                                </div>
                                <div class="flex items-center">
                                    <RadioButton v-model="isStudying" inputId="studyNo" name="isStudying" value="Tidak" />
                                    <label for="studyNo" class="ml-2">Tidak</label>
                                </div>
                            </div>
                        </div>

                        <!-- Tampilkan hanya jika TIDAK sedang studi -->
                        <template v-if="isStudying === 'Tidak'">
                            <div class="flex flex-col gap-3">
                                <p>Dalam seminggu terakhir, apakah Anda sedang bekerja atau berwirausaha?</p>
                                <div class="flex items-center gap-6">
                                    <div class="flex items-center">
                                        <RadioButton v-model="isWorking" inputId="workYes" name="isWorking" value="Ya" />
                                        <label for="workYes" class="ml-2">Ya</label>
                                    </div>
                                    <div class="flex items-center">
                                        <RadioButton v-model="isWorking" inputId="workNo" name="isWorking" value="Tidak" />
                                        <label for="workNo" class="ml-2">Tidak</label>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Tampilkan hanya jika TIDAK sedang studi DAN sedang bekerja -->
                        <template v-if="isStudying === 'Tidak' && isWorking === 'Ya'">
                            <div class="flex flex-col gap-3">
                                <p>
                                    Apa bentuk kegiatan/pekerjaan yang Anda lakukan?
                                    <br />
                                    <small class="text-foreground">(catatan: Youtuber, jual-beli online, dan usaha kreatif lainnya termasuk kategori wirausaha)</small>
                                </p>
                                <div class="mt-2 flex flex-col gap-3">
                                    <div v-for="option in workTypeOptions" :key="option" class="flex items-center">
                                        <RadioButton v-model="workType" :inputId="option" name="workType" :value="option" />
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
                <ActivityDetails v-model="mainActivity" />
            </div>

            <!-- Konten Step 3 -->
            <div v-if="activeStep === 2">
                <StudentFeedback v-model="studentFeedback" />
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-between mt-8">
                <Button
                    v-if="activeStep > 0"
                    label="Kembali"
                    icon="pi pi-arrow-left"
                    @click="prevStep"
                    severity="secondary"
                />
                <div v-else></div>
                <Button
                    class="w-full lg:w-auto"
                    :label="activeStep === stepItems.length - 1 ? 'Selesai' : 'Lanjut'"
                    icon="pi pi-arrow-right"
                    iconPos="right"
                    @click="nextStep"
                />
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
:deep(.p-fieldset-toggler) {
    color: #ffffff !important;
}
</style>
