<script setup lang="ts">
import { computed } from 'vue';

// Import komponen PrimeVue
import Fieldset from 'primevue/fieldset';
import RadioButton from 'primevue/radiobutton';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';

// --- Props & Emits untuk v-model ---
const props = defineProps<{
    modelValue: Record<string, any>
    universityOptions?: Record<string, Array<{ key: string; text: string }>>
}>();

const emit = defineEmits(['update:modelValue']);

// Data dikelola melalui computed property yang terhubung ke props
const formData = computed({
    get: () => props.modelValue,
    set: (value) => {
        emit('update:modelValue', value);
    }
});

const educationLevelOptions = computed(() => props.universityOptions?.educationLevelOptions || []);
const fundingSourceOptions = computed(() => props.universityOptions?.fundingSourceOptions || []);
const majorRelevanceOptions = computed(() => props.universityOptions?.majorRelevanceOptions || []);
</script>

<template>
    <Fieldset toggleable legend="Form Lulusan Kuliah" class="!mb-8">
        <div class="p-6 space-y-8">
            <!-- Pertanyaan Nama Perguruan Tinggi -->
            <div class="flex flex-col gap-2">
                <label for="university-name">Di Perguruan Tinggi mana Anda melanjutkan studi?</label>
                <InputText id="university-name" v-model="formData.universityName" type="text" placeholder="Contoh: Universitas Indonesia" />
            </div>

            <!-- Pertanyaan Program Studi -->
            <div class="flex flex-col gap-2">
                <label for="study-program">Apa Program Studi yang Anda ambil?</label>
                <InputText id="study-program" v-model="formData.studyProgram" type="text" placeholder="Contoh: Teknik Informatika" />
            </div>

            <!-- Pertanyaan Jenjang Pendidikan -->
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

            <!-- Pertanyaan Sumber Pembiayaan -->
            <div class="flex flex-col gap-3">
                <p>Apa sumber pembiayaan utama kuliah Anda?</p>
                <div class="flex flex-col gap-3 mt-1">
                    <div v-for="option in fundingSourceOptions" :key="option.key" class="flex items-center">
                        <RadioButton v-model="formData.fundingSource" :inputId="option.key" name="funding" :value="option.key" />
                        <label :for="option.key" class="ml-2">{{ option.text }}</label>
                    </div>
                </div>
            </div>

            <!-- Pertanyaan Kesesuaian Jurusan -->
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
