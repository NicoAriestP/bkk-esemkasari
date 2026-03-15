<script setup lang="ts">
import { computed, watch } from 'vue';

// Import komponen PrimeVue
import Fieldset from 'primevue/fieldset';
import RadioButton from 'primevue/radiobutton';

// Import komponen form anak
import StudentWorking from './StudentWorking.vue';
import StudentUniversity from './StudentUniversity.vue';
import StudentEntrepreneur from './StudentEntrepreneur.vue';

// --- Props & Emits ---
const props = defineProps<{
    modelValue: Record<string, any>
    activityOptions?: Array<{ key: string; text: string }>
    workingOptions?: Record<string, Array<{ key: string; text: string }>>
    universityOptions?: Record<string, Array<{ key: string; text: string }>>
    entrepreneurOptions?: Record<string, Array<{ key: string; text: string }>>
}>();
const emit = defineEmits(['update:modelValue']);

// Data dikelola melalui computed property
const formData = computed({
    get: () => props.modelValue,
    set: (value) => {
        emit('update:modelValue', value);
    }
});

// --- Logika Reset Data Anak ---
watch(() => formData.value.mainActivity, () => {
    // Setiap kali pilihan utama berubah, reset data untuk form yang tidak relevan.
    formData.value.workingData = {};
    formData.value.universityData = {};
    formData.value.entrepreneurData = {};
});

// --- Opsi untuk Form ---
const activityOptions = computed(() => props.activityOptions || []);
const workingOptions = computed(() => props.workingOptions || {});
const universityOptions = computed(() => props.universityOptions || {});
const entrepreneurOptions = computed(() => props.entrepreneurOptions || {});

</script>

<template>
    <div>
        <Fieldset toggleable legend="Detail Aktivitas Saat Ini" class="!mb-8">
            <div class="p-6">
                <div class="flex flex-col gap-3">
                    <p class="font-semibold text-lg">Apa aktivitas utama Anda saat ini?</p>
                    <div class="mt-2 flex flex-col gap-4">
                        <div v-for="option in activityOptions" :key="option.key" class="flex items-center">
                            <RadioButton v-model="formData.mainActivity" :inputId="option.key" name="mainActivity" :value="option.key" />
                            <label :for="option.key" class="ml-2">{{ option.text }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </Fieldset>

        <!-- Tampilkan komponen yang sesuai dan hubungkan dengan v-model -->
        <template v-if="formData.mainActivity === 'bekerja'">
            <StudentWorking v-model="formData.workingData" :working-options="workingOptions" />
        </template>

        <template v-if="formData.mainActivity === 'kuliah'">
            <StudentUniversity v-model="formData.universityData" :university-options="universityOptions" />
        </template>

        <template v-if="formData.mainActivity === 'wirausaha'">
            <StudentEntrepreneur v-model="formData.entrepreneurData" :entrepreneur-options="entrepreneurOptions" />
        </template>

        <div v-if="formData.mainActivity === 'belum'" class="p-4 text-center bg-blue-50 border-l-4 border-blue-400 text-blue-700 rounded-r-lg">
            <p>Anda telah memilih untuk belum bekerja/kuliah. Silakan klik "Lanjut" untuk melanjutkan ke langkah berikutnya.</p>
        </div>
    </div>
</template>
