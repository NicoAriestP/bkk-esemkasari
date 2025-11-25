<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { computed, ref, watch } from 'vue';

// Import komponen PrimeVue
import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Drawer from 'primevue/drawer';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Toast from 'primevue/toast';

// dayjs untuk format tanggal
import dayjs from 'dayjs';
import 'dayjs/locale/id';
dayjs.locale('id');

// Inisialisasi service dari PrimeVue
const toast = useToast();

// Props untuk menerima data dari controller
const props = defineProps({
    vacancy: {
        type: Object as () => any,
        required: true,
    },
    applicants: {
        type: Array as () => any[],
        required: true,
    },
});

// --- State Management ---

// State untuk menampung pelamar yang dipilih.
const selectedApplicants = ref(
    props.applicants.filter(applicant => applicant.status === 'qualified')
);
const selectedDetailStudent = ref<any>(null);
const filters = ref();
const displayDrawer = ref(false);

// Computed property untuk menonaktifkan tombol jika tidak ada yang dipilih
const areAnyApplicantsSelected = computed(() => selectedApplicants.value.length > 0);

// Breadcrumbs dinamis
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Lowongan Kerja',
        href: route('partners.vacancies.index'), // Sesuaikan dengan route Anda
    },
    {
        title: props.vacancy.title,
        href: route('partners.vacancies.edit', props.vacancy.id), // Link kembali ke halaman edit
    },
    {
        title: 'Seleksi Pelamar',
        href: '#',
    },
];

// --- Fungsi ---

const detailStudent = (student: any) => {
    selectedDetailStudent.value = student;
    displayDrawer.value = true;
};

const formatGender = (gender: string) => {
    return gender === 'laki-laki' ? 'Laki-laki' : 'Perempuan';
};

const formatTanggal = (dateString: string) => {
    if (!dateString) return '-';
    return dayjs(dateString).format('dddd, D MMMM YYYY');
};

const formatCreatedAt = (dateString: string) => {
    if (!dateString) return '-';
    return dayjs(dateString).format('DD MMMM YYYY, HH:mm');
};

// Fungsi untuk mengirim data seleksi ke backend
const save = () => {
    const applicantIds = selectedApplicants.value.map((applicant: any) => applicant.id);

    // Gunakan router Inertia untuk mengirim data
    router.post(route('partners.vacancies.applications.store', props.vacancy.id), {
        applicant_ids: applicantIds,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Sukses',
                detail: `Berhasil menyimpan seleksi pelamar.`,
                life: 3000
            });
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Gagal',
                detail: 'Terjadi kesalahan saat memproses data.',
                life: 3000
            });
        }
    });
};

const exportQualifiedApplicants = () => {
    const targetWindow = window.top ?? window;
    targetWindow.location.href = route('partners.vacancies.applications.export', props.vacancy.id);

    toast.add({
        severity: 'info',
        summary: 'Sedang memproses',
        detail: 'Ekspor seleksi pelamar sedang diproses. Silakan tunggu.',
        life: 3000
    });
};

watch(filters, (newValue) => {
    router.get(
        route('partners.vacancies.applicants', props.vacancy.id),
        { search: newValue },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
});
</script>

<template>

    <Head :title="`Seleksi Pelamar - ${vacancy.title}`" />
    <Toast />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-4">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg">
                        <i class="pi pi-users text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                            Seleksi Pelamar
                        </h1>
                        <p class="mt-2 text-lg font-medium text-gray-700">
                            {{ vacancy.title }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Pilih dan kelola pelamar yang memenuhi kualifikasi lowongan untuk lanjut ke proses rekrutmen
                            selanjutnya.
                        </p>
                    </div>
                </div>
                <!-- <div class="flex items-center gap-2 rounded-lg bg-blue-50 px-4 py-2 border border-blue-200">
                    <i class="pi pi-info-circle text-blue-600"></i>
                    <span class="text-sm font-medium text-blue-800">
                        {{ applicants.length }} Total Pelamar
                    </span>
                </div> -->
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="rounded-xl bg-white shadow-lg border border-gray-200 overflow-hidden">
            <!-- Card Header with Statistics -->
            <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900">{{ applicants.length }}</div>
                            <div class="text-xs text-gray-500 uppercase tracking-wide">Total Pelamar</div>
                        </div>
                        <div class="h-8 w-px bg-gray-300"></div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-emerald-600">{{ selectedApplicants.length }}</div>
                            <div class="text-xs text-gray-500 uppercase tracking-wide">Terpilih</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="rounded-full bg-green-100 px-3 py-1">
                            <span class="text-xs font-medium text-green-800">
                                {{ selectedApplicants.length }} dipilih
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DataTable -->
            <div class="p-6">
                <DataTable :value="applicants" dataKey="id" paginator :rows="10" v-model:selection="selectedApplicants"
                    removableSort row-hover :rows-per-page-options="[5, 10, 20, 50, 100]" tableStyle="min-width: 50rem"
                    class="custom-datatable"
                    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                    currentPageReportTemplate="Menampilkan {first} sampai {last} dari {totalRecords} pelamar">
                    <template #header>
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="relative flex-1 max-w-md">
                                <!-- <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="pi pi-search text-gray-400"></i>
                                </div> -->
                                <InputText
                                    class="w-full pl-10 pr-4 py-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    v-model="filters" placeholder="Cari nama, angkatan, kelas pelamar..." />
                            </div>
                            <div class="flex gap-3">
                                <Button severity="info" icon="pi pi-download" label="Export Data"
                                    @click="exportQualifiedApplicants" outlined class="shadow-sm" />
                                <Button label="Simpan Seleksi" icon="pi pi-check" severity="success"
                                    :disabled="!areAnyApplicantsSelected" @click="save()" class="shadow-sm"
                                    :class="{ 'opacity-50 cursor-not-allowed': !areAnyApplicantsSelected }" />
                            </div>
                        </div>
                    </template>

                    <template #empty>
                        <div class="text-center py-12">
                            <div class="mb-4">
                                <i class="pi pi-users text-6xl text-gray-300"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Pelamar</h3>
                            <p class="text-gray-500">Belum ada siswa yang melamar untuk lowongan kerja ini.</p>
                        </div>
                    </template>

                    <!-- Kolom Checkbox Seleksi -->
                    <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>

                    <!-- Kolom Nama Pelamar -->
                    <Column field="student.name" header="Nama Siswa" sortable>
                        <template #body="{ data }">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-700 font-semibold">
                                    {{ data.student.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ data.student.name }}</div>
                                    <div class="text-sm text-gray-500">NISN: {{ data.student.nisn }}</div>
                                </div>
                            </div>
                        </template>
                    </Column>

                    <Column field="student.student_class.year.year" header="Angkatan" sortable>
                        <template #body="{ data }">
                            <div class="rounded-full bg-indigo-100 px-3 py-1 text-center">
                                <span class="text-sm font-medium text-indigo-800">{{
                                    data.student.student_class.year.year }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column field="student.student_class.name" header="Kelas" sortable>
                        <template #body="{ data }">
                            <div class="rounded-lg bg-emerald-100 px-3 py-1 text-center">
                                <span class="text-sm font-medium text-emerald-800">{{ data.student.student_class.name
                                }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column field="student.phone" header="Kontak" sortable>
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-phone text-gray-400"></i>
                                <span class="text-sm text-gray-900">{{ data.student.phone }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column field="student.gender" header="Gender">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <i
                                    :class="data.student.gender === 'laki-laki' ? 'pi pi-mars text-blue-500' : 'pi pi-venus text-pink-500'"></i>
                                <span class="text-sm">{{ formatGender(data.student.gender) }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column field="student.age" header="Usia" sortable>
                        <template #body="{ data }">
                            <div class="text-center">
                                <span
                                    class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-sm font-medium text-gray-800">
                                    {{ data.student.age }} th
                                </span>
                            </div>
                        </template>
                    </Column>

                    <!-- Kolom Tanggal Melamar -->
                    <Column field="created_at" header="Tanggal Melamar" sortable :export-function="formatCreatedAt">
                        <template #body="slotProps">
                            <div class="flex items-center gap-2">
                                <i class="pi pi-calendar text-gray-400"></i>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ dayjs(slotProps.data.created_at).format('DD MMM YYYY') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ dayjs(slotProps.data.created_at).format('HH:mm') }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Column>

                    <!-- Kolom Aksi Individual -->
                    <Column header="Aksi" style="width: 8rem" bodyStyle="text-align: center">
                        <template #body="slotProps">
                            <div class="flex justify-center">
                                <Button icon="pi pi-eye" severity="info" variant="text" size="small"
                                    v-tooltip.top="'Lihat Detail'" @click="detailStudent(slotProps.data.student)"
                                    class="hover:!bg-blue-50" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <Drawer v-model:visible="displayDrawer" header="Detail Siswa" position="right" class="!w-full lg:!w-lg">
            <!-- Student Header Card -->
            <div class="mb-6 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl p-6 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold">
                            {{ selectedDetailStudent?.name?.charAt(0).toUpperCase() }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">{{ selectedDetailStudent?.name }}</h3>
                        <p class="text-blue-100">{{ selectedDetailStudent?.nisn }}</p>
                        <p class="text-blue-100 text-sm">{{ selectedDetailStudent?.student_class?.name }} - {{
                            selectedDetailStudent?.student_class?.year.year }}</p>
                    </div>
                </div>
            </div>

            <!-- Information Cards -->
            <div class="space-y-4">
                <!-- Informasi Pribadi -->
                <Card class="!shadow-sm border border-gray-200">
                    <template #title>
                        <div class="flex items-center gap-2 text-gray-800">
                            <i class="pi pi-user text-blue-600"></i>
                            <span>Informasi Pribadi</span>
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-3">
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-gray-600 text-sm">Nama</span>
                                <span class="font-semibold text-gray-900">{{ selectedDetailStudent?.name }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-gray-600 text-sm">NISN</span>
                                <span class="font-mono font-semibold text-gray-900">{{ selectedDetailStudent?.nisn
                                    }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-gray-600 text-sm">Email</span>
                                <span class="font-semibold text-gray-900">{{ selectedDetailStudent?.email }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-gray-600 text-sm">No. Telepon</span>
                                <span class="font-semibold text-gray-900">{{ selectedDetailStudent?.phone }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-gray-600 text-sm">Jenis Kelamin</span>
                                <div class="flex items-center gap-2">
                                    <i :class="[
                                        'text-sm',
                                        selectedDetailStudent?.gender === 'laki-laki' ? 'pi pi-mars text-blue-600' : 'pi pi-venus text-pink-600'
                                    ]"></i>
                                    <span class="font-semibold text-gray-900">{{
                                        formatGender(selectedDetailStudent?.gender)
                                        }}</span>
                                </div>
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-gray-600 text-sm">Usia</span>
                                <span class="font-semibold text-gray-900">{{ selectedDetailStudent?.age }} tahun</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600 text-sm">Tanggal Lahir</span>
                                <span class="font-semibold text-gray-900">{{
                                    formatTanggal(selectedDetailStudent?.born_date)
                                    }}</span>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Informasi Fisik -->
                <Card class="!shadow-sm border border-gray-200">
                    <template #title>
                        <div class="flex items-center gap-2 text-gray-800">
                            <i class="pi pi-heart text-red-500"></i>
                            <span>Informasi Fisik</span>
                        </div>
                    </template>
                    <template #content>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4 text-center">
                                <i class="pi pi-arrow-up text-blue-600 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-600">Tinggi Badan</p>
                                <p class="text-xl font-bold text-gray-900">{{ selectedDetailStudent?.height }}<span
                                        class="text-sm font-normal"> cm</span></p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4 text-center">
                                <i class="pi pi-circle text-green-600 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-600">Berat Badan</p>
                                <p class="text-xl font-bold text-gray-900">{{ selectedDetailStudent?.weight }}<span
                                        class="text-sm font-normal"> kg</span></p>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Alamat -->
                <Card class="!shadow-sm border border-gray-200">
                    <template #title>
                        <div class="flex items-center gap-2 text-gray-800">
                            <i class="pi pi-map-marker text-orange-500"></i>
                            <span>Alamat</span>
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-3">
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-gray-600 text-sm">Provinsi</span>
                                <span class="font-semibold text-gray-900">{{ selectedDetailStudent?.province }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-gray-600 text-sm">Kota</span>
                                <span class="font-semibold text-gray-900">{{ selectedDetailStudent?.city }}</span>
                            </div>
                            <div class="py-2">
                                <span class="text-gray-600 text-sm block mb-2">Alamat Lengkap</span>
                                <p class="font-semibold text-gray-900 bg-gray-50 rounded-lg p-3">{{
                                    selectedDetailStudent?.address }}</p>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Status -->
                <Card class="!shadow-sm border border-gray-200">
                    <template #title>
                        <div class="flex items-center gap-2 text-gray-800">
                            <i class="pi pi-check-circle text-green-500"></i>
                            <span>Status</span>
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Status Lulus</span>
                                <Tag :value="selectedDetailStudent?.is_graduated_label"
                                    :severity="selectedDetailStudent?.is_graduated === true ? 'success' : 'warn'"
                                    class="font-medium" />
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Status Menikah</span>
                                <Tag :value="selectedDetailStudent?.is_married_label"
                                    :severity="selectedDetailStudent?.is_married === true ? 'info' : 'secondary'"
                                    class="font-medium" />
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <template #footer>
                <div class="flex flex-col gap-3 pt-4">
                    <a v-if="selectedDetailStudent?.cv_file_url" :href="selectedDetailStudent?.cv_file_url"
                        target="_blank" download class="w-full">
                        <Button label="Download CV" icon="pi pi-download"
                            class="w-full bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-blue-700 transition-colors duration-200" />
                    </a>
                    <Button v-else label="CV Tidak Tersedia" icon="pi pi-file"
                        class="w-full bg-gray-200 text-gray-500 border-gray-200 cursor-not-allowed" severity="secondary"
                        outlined disabled />
                </div>
            </template>
        </Drawer>
    </AppLayout>
</template>
