<script setup lang="ts">
import { computed, watch } from 'vue';
import Fieldset from 'primevue/fieldset';
import RadioButton from 'primevue/radiobutton';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';

// --- Props & Emits untuk v-model ---
const props = defineProps<{
    modelValue: Record<string, any>
    workingOptions?: Record<string, Array<{ key: string; text: string }>>
}>();
const emit = defineEmits(['update:modelValue']);

// Data dikelola melalui computed property
const formData = computed({
    get: () => props.modelValue,
    set: (value) => {
        emit('update:modelValue', value);
    }
});

// Watcher untuk mereset input teks "Lainnya"
watch(() => formData.value.howFoundFirstJob, (newValue) => {
    if (newValue && !newValue.includes('hfj-other')) {
        formData.value.otherJobSourceText = '';
    }
}, { deep: true });


const salaryOptions = computed(() => props.workingOptions?.salaryOptions || []);
const jobChangeOptions = computed(() => props.workingOptions?.jobChangeOptions || []);
const howFoundJobOptions = computed(() => props.workingOptions?.howFoundJobOptions || []);
const jobRelevanceOptions = computed(() => props.workingOptions?.jobRelevanceOptions || []);
const howFoundJobMainOptions = computed(() => howFoundJobOptions.value.filter((option) => option.key !== 'hfj-other'));
const howFoundJobOtherOption = computed(() => howFoundJobOptions.value.find((option) => option.key === 'hfj-other'));
</script>

<template>
    <Fieldset toggleable legend="Form Lulusan Bekerja" class="!mb-8">
        <div class="p-6 space-y-8">
            <div class="flex flex-col gap-2">
                <label for="working-hours">Berapa rata-rata jam kerja Anda per minggu?</label>
                <InputText id="working-hours" v-model="formData.workingHours" type="number" class="max-w-xs" />
            </div>

            <div class="flex flex-col gap-3">
                <p>Berapa gaji/upah Anda dalam sebulan (dalam rupiah)?<br><small class="text-gray-500">(gaji/upah juga termasuk fasilitas/barang yang dibayarkan oleh tempat kerja)</small></p>
                <div class="flex flex-col gap-3 mt-1">
                    <div v-for="option in salaryOptions" :key="option.key" class="flex items-center">
                        <RadioButton v-model="formData.salaryRange" :inputId="option.key" name="salary" :value="option.key" />
                        <label :for="option.key" class="ml-2">{{ option.text }}</label>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <p>Berapa kali Anda ganti pekerjaan sejak lulus dari SMK?</p>
                <div class="flex flex-col gap-3 mt-1">
                    <div v-for="option in jobChangeOptions" :key="option.key" class="flex items-center">
                        <RadioButton v-model="formData.jobChangeFrequency" :inputId="option.key" name="jobChange" :value="option.key" />
                        <label :for="option.key" class="ml-2">{{ option.text }}</label>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <p>Bagaimana cara Anda mendapatkan pekerjaan yang pertama kali?<br><small class="text-gray-500">(boleh pilih lebih dari satu)</small></p>
                <div class="flex flex-col gap-3 mt-1">
                    <div v-for="option in howFoundJobMainOptions" :key="option.key" class="flex items-center">
                        <Checkbox v-model="formData.howFoundFirstJob" :inputId="option.key" name="howFoundJob" :value="option.key" />
                        <label :for="option.key" class="ml-2">{{ option.text }}</label>
                    </div>
                    <div class="flex items-center flex-wrap gap-2 mt-2">
                        <div class="flex items-center">
                            <Checkbox v-model="formData.howFoundFirstJob" inputId="hfj-other" name="howFoundJob" value="hfj-other" />
                            <label for="hfj-other" class="ml-2">{{ howFoundJobOtherOption?.text || 'Lainnya' }}, tuliskan</label>
                        </div>
                        <InputText
                            v-model="formData.otherJobSourceText"
                            class="flex-grow w-full sm:w-auto"
                            :disabled="!formData.howFoundFirstJob?.includes('hfj-other')"
                            placeholder="Tuliskan cara lainnya di sini..."
                        />
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <p>Bagaimana keselarasan pekerjaan Anda sekarang dengan program/kompetensi keahlian Anda di SMK?</p>
                <div class="flex flex-col gap-3 mt-1">
                    <div v-for="option in jobRelevanceOptions" :key="option.key" class="flex items-center">
                        <RadioButton v-model="formData.jobRelevance" :inputId="option.key" name="jobRelevance" :value="option.key" />
                        <label :for="option.key" class="ml-2">{{ option.text }}</label>
                    </div>
                </div>
            </div>
        </div>
    </Fieldset>
</template>
