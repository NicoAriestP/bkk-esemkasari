<script setup lang="ts">
import { computed } from 'vue';

// Import komponen PrimeVue
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
const businessScaleOptions = [
    { key: 'bs1', text: 'Usaha Mikro (omzet < 300 juta/tahun)' },
    { key: 'bs2', text: 'Usaha Kecil (omzet 300 juta - 2.5 miliar/tahun)' },
    { key: 'bs3', text: 'Usaha Menengah (omzet > 2.5 miliar/tahun)' },
];

const businessIncomeOptions = [
    { key: 'bi1', text: 'Kurang dari Rp. 2.500.000,-' },
    { key: 'bi2', text: 'Rp. 2.500.000 s.d Rp. 5.000.000,-' },
    { key: 'bi3', text: 'Rp. 5.000.001 s.d Rp. 10.000.000,-' },
    { key: 'bi4', text: 'Lebih dari Rp. 10.000.000,-' },
];
</script>

<template>
    <Fieldset legend="Form Lulusan Wirausaha" class="!mb-8">
        <div class="p-6 space-y-8">
            <!-- Pertanyaan Nama Usaha -->
            <div class="flex flex-col gap-2">
                <label for="business-name">Apa nama usaha yang Anda rintis?</label>
                <InputText id="business-name" v-model="formData.businessName" type="text" placeholder="Contoh: Maju Jaya Digital Printing" />
            </div>

            <!-- Pertanyaan Bidang Usaha -->
            <div class="flex flex-col gap-2">
                <label for="business-field">Apa bidang usaha Anda?</label>
                <InputText id="business-field" v-model="formData.businessField" type="text" placeholder="Contoh: Percetakan, Kuliner, Jasa Digital" />
            </div>

            <!-- Pertanyaan Skala Usaha -->
            <div class="flex flex-col gap-3">
                <p>Termasuk dalam skala apakah usaha Anda saat ini?</p>
                <Select
                    v-model="formData.businessScale"
                    :options="businessScaleOptions"
                    optionLabel="text"
                    optionValue="key"
                    placeholder="Pilih skala usaha"
                    class="w-full md:w-1/2"
                />
            </div>

            <!-- Pertanyaan Pendapatan Rata-rata -->
            <div class="flex flex-col gap-3">
                <p>Berapa rata-rata pendapatan bersih (keuntungan) usaha Anda per bulan?</p>
                <div class="flex flex-col gap-3 mt-1">
                    <div v-for="option in businessIncomeOptions" :key="option.key" class="flex items-center">
                        <RadioButton v-model="formData.businessIncome" :inputId="option.key" name="income" :value="option.key" />
                        <label :for="option.key" class="ml-2">{{ option.text }}</label>
                    </div>
                </div>
            </div>
        </div>
    </Fieldset>
</template>
