<script setup lang="ts">
import { computed } from 'vue';
import Fieldset from 'primevue/fieldset';
import RadioButton from 'primevue/radiobutton';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';

// --- Props & Emits ---
const props = defineProps<{
    modelValue: Record<string, any>
}>();
const emit = defineEmits(['update:modelValue']);

// Data dikelola melalui computed property
const formData = computed({
    get: () => props.modelValue,
    set: (value) => {
        emit('update:modelValue', value);
    }
});

// --- Opsi untuk Form ---
const educationLevelOptions = [
    { key: 'd1', text: 'D1 (Diploma 1)' },
    { key: 'd2', text: 'D2 (Diploma 2)' },
    { key: 'd3', text: 'D3 (Diploma 3)' },
    { key: 's1d4', text: 'S1 Terapan / D4 (Sarjana Terapan)' },
    { key: 's1', text: 'S1 (Sarjana)' },
];

const fundingSourceOptions = [
    { key: 'fs1', text: 'Biaya Sendiri / Keluarga' },
    { key: 'fs2', text: 'Beasiswa KIP-Kuliah' },
    { key: 'fs3', text: 'Beasiswa Penuh (selain KIP-K)' },
    { key: 'fs4', text: 'Beasiswa Sebagian (selain KIP-K)' },
    { key: 'fs5', text: 'Ikatan Dinas' },
];

const majorRelevanceOptions = [
    { key: 'mr1', text: 'Sangat Sesuai' },
    { key: 'mr2', text: 'Sesuai' },
    { key: 'mr3', text: 'Kurang Sesuai' },
    { key: 'mr4', text: 'Tidak Sesuai Sama Sekali' },
];
</script>

<template>
    <Fieldset legend="Form Lulusan Kuliah" class="!mb-8">
        <div class="p-6 space-y-8">
            <div class="flex flex-col gap-2">
                <label for="university-name">Di Perguruan Tinggi mana Anda melanjutkan studi?</label>
                <InputText id="university-name" v-model="formData.universityName" type="text" placeholder="Contoh: Universitas Indonesia" />
            </div>

            <div class="flex flex-col gap-2">
                <label for="study-program">Apa Program Studi yang Anda ambil?</label>
                <InputText id="study-program" v-model="formData.studyProgram" type="text" placeholder="Contoh: Teknik Informatika" />
            </div>

            <div class="flex flex-col gap-3">
                <p>Apa jenjang pendidikan yang Anda ambil?</p>
                <Select
                    v-model="formData.educationLevel"
                    :options="educationLevelOptions"
                    optionLabel="text"
                    optionValue="key"
                    placeholder="Pilih jenjang pendidikan"
                    class="w-full md:w-1/2"
                />
            </div>

            <div class="flex flex-col gap-3">
                <p>Apa sumber pembiayaan utama kuliah Anda?</p>
                <div class="flex flex-col gap-3 mt-1">
                    <div v-for="option in fundingSourceOptions" :key="option.key" class="flex items-center">
                        <RadioButton v-model="formData.fundingSource" :inputId="option.key" name="funding" :value="option.key" />
                        <label :for="option.key" class="ml-2">{{ option.text }}</label>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <p>Bagaimana tingkat kesesuaian jurusan yang Anda ambil di Perguruan Tinggi dengan jurusan Anda di SMK?</p>
                <div class="flex flex-col gap-3 mt-1">
                    <div v-for="option in majorRelevanceOptions" :key="option.key" class="flex items-center">
                        <RadioButton v-model="formData.majorRelevance" :inputId="option.key" name="relevance" :value="option.key" />
                        <label :for="option.key" class="ml-2">{{ option.text }}</label>
                    </div>
                </div>
            </div>
        </div>
    </Fieldset>
</template>
