<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import Fieldset from 'primevue/fieldset';
import RadioButton from 'primevue/radiobutton';
import StudentWorking from './StudentWorking.vue';
import StudentUniversity from './StudentUniversity.vue';
import StudentEntrepreneur from './StudentEntrepreneur.vue';

// --- Props & Emits ---
const props = defineProps<{
    modelValue: string | null
}>();
const emit = defineEmits(['update:modelValue']);

// --- State untuk semua form ---

// Definisikan state awal untuk setiap form
const initialWorkingState = () => ({
  workingHours: '',
  salaryRange: null,
  jobChangeFrequency: null,
  howFoundFirstJob: [],
  otherJobSourceText: '',
  jobRelevance: null
});

const initialUniversityState = () => ({
  universityName: '',
  studyProgram: '',
  educationLevel: null,
  fundingSource: null,
  majorRelevance: null
});

const initialEntrepreneurState = () => ({
  businessName: '',
  businessField: '',
  businessScale: null,
  businessIncome: null
});

// Buat state reaktif untuk setiap form
const workingData = ref(initialWorkingState());
const universityData = ref(initialUniversityState());
const entrepreneurData = ref(initialEntrepreneurState());

// --- Logika Reset ---
watch(() => props.modelValue, (newValue) => {
    // Setiap kali pilihan utama berubah, reset data form yang TIDAK relevan.
    if (newValue !== 'bekerja') {
        workingData.value = initialWorkingState();
    }
    if (newValue !== 'kuliah') {
        universityData.value = initialUniversityState();
    }
    if (newValue !== 'wirausaha') {
        entrepreneurData.value = initialEntrepreneurState();
    }
});


// Opsi untuk pertanyaan utama
const activityOptions = [
    { value: 'kuliah', label: 'Melanjutkan Studi / Kuliah' },
    { value: 'bekerja', label: 'Bekerja' },
    { value: 'wirausaha', label: 'Membuka Usaha / Wirausaha' },
    { value: 'belum', label: 'Belum Bekerja / Belum Melanjutkan Studi' },
];

const selectedActivity = computed({
    get: () => props.modelValue,
    set: (value) => {
        emit('update:modelValue', value)
    }
});
</script>

<template>
    <div>
        <Fieldset legend="Detail Aktivitas Saat Ini" class="!mb-8">
            <div class="p-6">
                <div class="flex flex-col gap-3">
                    <p class="font-semibold text-lg">Apa aktivitas utama Anda saat ini?</p>
                    <div class="mt-2 flex flex-col gap-4">
                        <div v-for="option in activityOptions" :key="option.value" class="flex items-center">
                            <RadioButton v-model="selectedActivity" :inputId="option.value" name="mainActivity" :value="option.value" />
                            <label :for="option.value" class="ml-2">{{ option.label }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </Fieldset>

        <!-- Tampilkan komponen yang sesuai dan hubungkan dengan v-model -->
        <template v-if="selectedActivity === 'bekerja'">
            <StudentWorking v-model="workingData" />
        </template>

        <template v-if="selectedActivity === 'kuliah'">
            <StudentUniversity v-model="universityData" />
        </template>

        <template v-if="selectedActivity === 'wirausaha'">
            <StudentEntrepreneur v-model="entrepreneurData" />
        </template>

        <div v-if="selectedActivity === 'belum'" class="p-4 text-center bg-blue-50 dark:bg-blue-900 border-l-4 dark:border-blue-400 text-blue-700 dark:text-white rounded-r-lg">
            <p>Anda telah memilih untuk belum bekerja/kuliah. Silakan klik "Lanjut" untuk melanjutkan ke langkah berikutnya.</p>
        </div>
    </div>
</template>
