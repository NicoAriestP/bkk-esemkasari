<script setup lang="ts">
import { watch, computed } from 'vue';

// Import komponen PrimeVue
import Fieldset from 'primevue/fieldset';
import Checkbox from 'primevue/checkbox';
import RadioButton from 'primevue/radiobutton';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';

// --- Props & Emits ---
const props = defineProps<{
    modelValue: Record<string, any>
    feedbackOptions?: Record<string, Array<{ key: string; text: string }>>
}>();
const emit = defineEmits(['update:modelValue']);

const smkReasonOptions = computed(() => props.feedbackOptions?.smkReasonOptions || []);
const pklDurationOptions = computed(() => props.feedbackOptions?.pklDurationOptions || []);
const qualityRatingOptions = computed(() => props.feedbackOptions?.qualityRatingOptions || []);
const certificateOwnershipOptions = computed(() => props.feedbackOptions?.certificateOwnershipOptions || []);

// Inisialisasi data secara defensif untuk mencegah error
watch(() => props.modelValue, (currentValue) => {
    const needsUpdate = !currentValue || !currentValue.pklQuality || !currentValue.certificates;

    if (needsUpdate) {
        const completeData = {
            smkReasons: currentValue?.smkReasons || [],
            pklDuration: currentValue?.pklDuration || null,
            pklQuality: {
                location: null,
                taskRelevance: null,
                guidance: null,
                monitoring: null,
                ...currentValue?.pklQuality,
            },
            hasCertificate: currentValue?.hasCertificate || null,
            certificates: currentValue?.certificates && currentValue.certificates.length > 0
                ? currentValue.certificates
                : [{ id: 1, name: '' }],
        };
        emit('update:modelValue', completeData);
    }
}, { immediate: true, deep: true });

// Data dikelola melalui computed property
const formData = computed({
    get: () => props.modelValue || {},
    set: (value) => {
        emit('update:modelValue', value);
    }
});


// --- Fungsi untuk menambah & menghapus sertifikat ---
const addCertificate = () => {
    if (!Array.isArray(formData.value.certificates)) {
        formData.value.certificates = [];
    }
    formData.value.certificates.push({ id: Date.now(), name: '' });
};

const removeCertificate = (id: number) => {
    formData.value.certificates = formData.value.certificates.filter((cert: { id: number }) => cert.id !== id);
    if (formData.value.certificates.length === 0) {
        addCertificate();
    }
};

// Watcher untuk mereset daftar sertifikat jika user memilih "Tidak"
watch(() => formData.value.hasCertificate, (newValue) => {
    if (newValue === 'Tidak') {
        formData.value.certificates = [{ id: 1, name: '' }];
    }
});

</script>

<template>
    <Fieldset toggleable legend="Umpan Balik" class="!mb-8">
        <div class="p-6 space-y-8">
            <!-- Pertanyaan 1: Alasan memilih SMK -->
            <div class="flex flex-col gap-3">
                <p>Apa alasan Anda memilih pendidikan di SMK? <small class="text-gray-500">(boleh pilih lebih dari satu)</small></p>
                <div class="flex flex-col gap-3 mt-1">
                    <div v-for="option in smkReasonOptions" :key="option.key" class="flex items-center">
                        <Checkbox v-model="formData.smkReasons" :inputId="option.key" name="smkReason" :value="option.key" />
                        <label :for="option.key" class="ml-2">{{ option.text }}</label>
                    </div>
                     <div class="flex items-center">
                        <Checkbox disabled inputId="r-other" />
                        <label for="r-other" class="ml-2 text-gray-400">Lainnya</label>
                    </div>
                </div>
            </div>

            <!-- Pertanyaan 2: Durasi PKL -->
            <div class="flex flex-col gap-3">
                <p>Berapa total durasi/lama Anda melaksanakan Prakerin/PKL sewaktu di SMK?</p>
                <div class="flex flex-col gap-3 mt-1">
                    <div v-for="option in pklDurationOptions" :key="option.key" class="flex items-center">
                        <RadioButton v-model="formData.pklDuration" :inputId="option.key" name="pklDuration" :value="option.key" />
                        <label :for="option.key" class="ml-2">{{ option.text }}</label>
                    </div>
                </div>
            </div>

            <!-- Pertanyaan 3: Kualitas PKL -->
            <div class="flex flex-col gap-4" v-if="formData.pklQuality">
                <p>Kualitas program Prakerin/PKL yang diselenggarakan di SMK:</p>
                <div class="pl-4 border-l-2">
                    <p class="mb-3">a. Kualitas tempat Prakerin/PKL</p>
                    <div class="flex flex-wrap gap-x-6 gap-y-3">
                        <div v-for="option in qualityRatingOptions" :key="option.key" class="flex items-center">
                            <RadioButton v-model="formData.pklQuality.location" :inputId="`loc-${option.key}`" name="pklLocation" :value="option.key" />
                            <label :for="`loc-${option.key}`" class="ml-2">{{ option.text }}</label>
                        </div>
                    </div>
                </div>
                <div class="pl-4 border-l-2">
                    <p class="mb-3">b. Kesesuaian tugas Prakerin/PKL dengan bidang/program keahlian</p>
                    <div class="flex flex-wrap gap-x-6 gap-y-3">
                        <div v-for="option in qualityRatingOptions" :key="option.key" class="flex items-center">
                            <RadioButton v-model="formData.pklQuality.taskRelevance" :inputId="`task-${option.key}`" name="pklTask" :value="option.key" />
                            <label :for="`task-${option.key}`" class="ml-2">{{ option.text }}</label>
                        </div>
                    </div>
                </div>
                <div class="pl-4 border-l-2">
                    <p class="mb-3">c. Bimbingan selama Prakerin/PKL</p>
                    <div class="flex flex-wrap gap-x-6 gap-y-3">
                        <div v-for="option in qualityRatingOptions" :key="option.key" class="flex items-center">
                            <RadioButton v-model="formData.pklQuality.guidance" :inputId="`guid-${option.key}`" name="pklGuidance" :value="option.key" />
                            <label :for="`guid-${option.key}`" class="ml-2">{{ option.text }}</label>
                        </div>
                    </div>
                </div>
                 <div class="pl-4 border-l-2">
                    <p class="mb-3">d. Monitoring/kunjungan guru selama Prakerin/PKL</p>
                    <div class="flex flex-wrap gap-x-6 gap-y-3">
                        <div v-for="option in qualityRatingOptions" :key="option.key" class="flex items-center">
                            <RadioButton v-model="formData.pklQuality.monitoring" :inputId="`mon-${option.key}`" name="pklMonitoring" :value="option.key" />
                            <label :for="`mon-${option.key}`" class="ml-2">{{ option.text }}</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <p>Apakah Anda memiliki sertifikat kompetensi yang dikeluarkan oleh industri/Lembaga Sertifikasi Profesi (LSP) saat menempuh pendidikan di SMK?</p>
                <div class="flex items-center gap-6">
                    <div v-for="option in certificateOwnershipOptions" :key="option.key" class="flex items-center">
                        <RadioButton v-model="formData.hasCertificate" :inputId="`cert-${option.key}`" name="hasCert" :value="option.key" />
                        <label :for="`cert-${option.key}`" class="ml-2">{{ option.text }}</label>
                    </div>
                </div>
            </div>

            <template v-if="formData.hasCertificate === 'Ya' && formData.certificates">
                <div class="flex flex-col gap-3">
                    <label>Tuliskan nama sertifikat kompetensi yang dimiliki!</label>
                    <small class="text-gray-500 -mt-2">(Misal: Sertifikat Keahlian Teknik Komputer Jaringan)</small>
                    <div v-for="(cert, index) in formData.certificates" :key="cert.id" class="flex items-center gap-2">
                        <InputText v-model="cert.name" class="flex-grow" placeholder="Nama sertifikat..." />
                        <Button
                            icon="pi pi-plus"
                            severity="success"
                            @click="addCertificate"
                            v-if="index === formData.certificates.length - 1"
                            aria-label="Tambah Sertifikat"
                        />
                         <Button
                            icon="pi pi-trash"
                            severity="danger"
                            @click="removeCertificate(cert.id)"
                            v-if="formData.certificates.length > 1"
                            aria-label="Hapus Sertifikat"
                        />
                    </div>
                </div>
            </template>
        </div>
    </Fieldset>
</template>
