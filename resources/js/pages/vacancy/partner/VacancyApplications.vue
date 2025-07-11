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
    window.top.location = route('partners.vacancies.applications.export', props.vacancy.id);

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
        <DataTable
            :value="applicants"
            dataKey="id"
            paginator
            :rows="10"
            v-model:selection="selectedApplicants"
            removableSort
            row-hover
            :rows-per-page-options="[5, 10, 20, 50, 100]"
            tableStyle="min-width: 50rem"
        >
            <template #header>
                <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                    <div class="flex-grow">
                        <InputText class="w-full lg:w-80" v-model="filters" placeholder="Cari nama, angkatan, kelas pelamar..." />
                    </div>
                    <div class="flex gap-2">
                        <Button severity="info" icon="pi pi-external-link" label="Export" @click="exportQualifiedApplicants" />
                        <Button label="Simpan" icon="pi pi-check" severity="success" :disabled="!areAnyApplicantsSelected" @click="save()" />
                    </div>
                </div>
            </template>
            <template #empty>
                <span class="text-center">Belum ada pelamar untuk lowongan ini.</span>
            </template>

            <!-- Kolom Checkbox Seleksi -->
            <Column selectionMode="multiple"></Column>

            <!-- Kolom Nama Pelamar -->
            <Column field="student.name" header="Nama" sortable></Column>
            <Column field="student.student_class.year.year" header="Tahun Angkatan" sortable></Column>
            <Column field="student.student_class.name" header="Kelas" sortable></Column>
            <Column field="student.nisn" header="NISN" sortable></Column>
            <Column field="student.phone" header="No. Telepon" sortable></Column>
            <Column field="student.gender" header="Jenis Kelamin">
                <template #body="{ data }">
                    {{ formatGender(data.student.gender) }}
                </template>
            </Column>
            <Column field="student.age" header="Umur" sortable>
                <template #body="{ data }"> {{ data.student.age }} tahun </template>
            </Column>

            <!-- Kolom Tanggal Melamar -->
            <Column field="created_at" header="Tanggal Melamar" sortable :export-function="formatCreatedAt">
                <template #body="slotProps">
                    {{ formatCreatedAt(slotProps.data.created_at) }}
                </template>
            </Column>

            <!-- Kolom Aksi Individual -->
            <Column style="width: 10% !important" bodyStyle="text-align: center">
                <template #body="slotProps">
                    <div class="flex justify-center">
                        <Button
                            class="hover:!opacity-50"
                            icon="pi pi-angle-right"
                            variant="link"
                            severity="secondary"
                            v-tooltip.top="'Detail Siswa'"
                            @click="detailStudent(slotProps.data.student)"
                        />
                    </div>
                </template>
            </Column>
        </DataTable>

        <Drawer v-model:visible="displayDrawer" header="Detail Siswa" position="right" class="!w-full lg:!w-lg">
            <Card class="my-2 !shadow-xl dark:!bg-gray-800 dark:!shadow-none">
                <template #title> Informasi Pribadi </template>
                <template #content>
                    <table class="w-full">
                        <tbody>
                            <tr>
                                <td>Nama</td>
                                <td class="py-2 text-right font-bold">{{ selectedDetailStudent?.name }}</td>
                            </tr>
                            <tr>
                                <td>NISN</td>
                                <td class="py-2 text-right font-bold">{{ selectedDetailStudent?.nisn }}</td>
                            </tr>
                            <tr>
                                <td>Tahun Angkatan</td>
                                <td class="py-2 text-right font-bold">{{ selectedDetailStudent?.student_class?.year.year }}</td>
                            </tr>
                            <tr>
                                <td>Kelas</td>
                                <td class="py-2 text-right font-bold">{{ selectedDetailStudent?.student_class?.name }}</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td class="py-2 text-right font-bold">{{ selectedDetailStudent?.email }}</td>
                            </tr>
                            <tr>
                                <td>No. Telepon</td>
                                <td class="py-2 text-right font-bold">{{ selectedDetailStudent?.phone }}</td>
                            </tr>
                            <tr>
                                <td>Jenis Kelamin</td>
                                <td class="py-2 text-right font-bold">{{ formatGender(selectedDetailStudent?.gender) }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Lahir</td>
                                <td class="py-2 text-right font-bold">{{ formatTanggal(selectedDetailStudent?.born_date) }}</td>
                            </tr>
                            <tr>
                                <td>Umur</td>
                                <td class="py-2 text-right font-bold">{{ selectedDetailStudent?.age }} tahun</td>
                            </tr>
                        </tbody>
                    </table>
                </template>
            </Card>

            <Card class="my-2 !shadow-xl dark:!bg-gray-800 dark:!shadow-none">
                <template #title> Informasi Fisik </template>
                <template #content>
                    <table class="w-full">
                        <tbody>
                            <tr>
                                <td>Tinggi Badan</td>
                                <td class="py-2 text-right font-bold">{{ selectedDetailStudent?.height }} cm</td>
                            </tr>
                            <tr>
                                <td>Berat Badan</td>
                                <td class="py-2 text-right font-bold">{{ selectedDetailStudent?.weight }} kg</td>
                            </tr>
                        </tbody>
                    </table>
                </template>
            </Card>

            <Card class="my-2 !shadow-xl dark:!bg-gray-800 dark:!shadow-none">
                <template #title> Alamat </template>
                <template #content>
                    <table class="w-full">
                        <tbody>
                            <tr>
                                <td>Provinsi</td>
                                <td class="py-2 text-right font-bold">{{ selectedDetailStudent?.province }}</td>
                            </tr>
                            <tr>
                                <td>Kota</td>
                                <td class="py-2 text-right font-bold">{{ selectedDetailStudent?.city }}</td>
                            </tr>
                            <tr>
                                <td>Alamat Lengkap</td>
                                <td class="py-2 text-right font-bold">{{ selectedDetailStudent?.address }}</td>
                            </tr>
                        </tbody>
                    </table>
                </template>
            </Card>

            <Card class="my-2 !shadow-xl dark:!bg-gray-800 dark:!shadow-none">
                <template #title> Status </template>
                <template #content>
                    <table class="w-full">
                        <tbody>
                            <tr>
                                <td colspan="2" class="pb-2">Status Lulus</td>
                                <td class="py-2 text-right">
                                    <Tag
                                        :value="selectedDetailStudent?.is_graduated_label"
                                        :severity="selectedDetailStudent?.is_graduated === true ? 'success' : 'danger'"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="pb-2">Status Menikah</td>
                                <td class="py-2 text-right">
                                    <Tag
                                        :value="selectedDetailStudent?.is_married_label"
                                        :severity="selectedDetailStudent?.is_married === true ? 'success' : 'danger'"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </template>
            </Card>

            <template #footer>
                <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-center">
                    <a :href="selectedDetailStudent?.cv_file_url" download target="_blank">
                        <Button
                            label="Download CV"
                            icon="pi pi-download"
                            class="my-2 w-full px-12 text-center lg:w-64"
                            size="small"
                            severity="info"
                        />
                    </a>
                </div>
            </template>
        </Drawer>
    </AppLayout>
</template>
