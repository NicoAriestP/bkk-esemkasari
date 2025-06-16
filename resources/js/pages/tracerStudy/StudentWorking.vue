<script setup lang="ts">
import { computed, watch } from 'vue';
import Fieldset from 'primevue/fieldset';
import RadioButton from 'primevue/radiobutton';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';

// --- Props & Emits untuk v-model ---
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

// Watcher untuk mereset input teks "Lainnya"
watch(() => formData.value.howFoundFirstJob, (newValue) => {
    if (newValue && !newValue.includes('hfj-other')) {
        formData.value.otherJobSourceText = '';
    }
}, { deep: true });


// --- Opsi untuk Form ---
const salaryOptions = [
    { key: 's1', text: 'Kurang dari Rp. 1.530.183,-' },
    { key: 's2', text: 'Rp. 1.530.183 s.d Rp. 2.040.243,-' },
    { key: 's3', text: 'Rp. 2.040.244 s.d Rp. 2.448.292,-' },
    { key: 's4', text: 'Lebih dari Rp. 2.448.292,-' },
];

const jobChangeOptions = [
    { key: 'jc1', text: 'Belum pernah' },
    { key: 'jc2', text: 'Satu kali' },
    { key: 'jc3', text: 'Dua kali' },
    { key: 'jc4', text: 'Tiga kali atau lebih' },
];

const howFoundJobOptions = [
    { key: 'hfj1', text: 'Melalui industri yang menjadi mitra SMK' },
    { key: 'hfj2', text: 'Melalui layanan pusat karir/bursa kerja di SMK' },
    { key: 'hfj3', text: 'Melalui tempat magang (Prakerin/PKL) selama di SMK' },
    { key: 'hfj4', text: 'Melalui ikatan alumni' },
    { key: 'hfj5', text: 'Melalui iklan di media cetak/online' },
    { key: 'hfj6', text: 'Melalui bursa kerja/pameran kerja/job fair' },
    { key: 'hfj7', text: 'Melalui dinas ketenagakerjaan setempat' },
    { key: 'hfj8', text: 'Melalui bantuan orang tua/famili/teman' },
];

const jobRelevanceOptions = [
    { key: 'jr1', text: 'Sangat tidak selaras' },
    { key: 'jr2', text: 'Tidak Selaras' },
    { key: 'jr3', text: 'Selaras' },
    { key: 'jr4', text: 'Sangat selaras' },
];
</script>

<template>
    <Fieldset legend="Form Lulusan Bekerja" class="!mb-8">
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
                    <div v-for="option in howFoundJobOptions" :key="option.key" class="flex items-center">
                        <Checkbox v-model="formData.howFoundFirstJob" :inputId="option.key" name="howFoundJob" :value="option.key" />
                        <label :for="option.key" class="ml-2">{{ option.text }}</label>
                    </div>
                    <div class="flex items-center flex-wrap gap-2 mt-2">
                        <div class="flex items-center">
                            <Checkbox v-model="formData.howFoundFirstJob" inputId="hfj-other" name="howFoundJob" value="hfj-other" />
                            <label for="hfj-other" class="ml-2">Lainnya, tuliskan</label>
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
