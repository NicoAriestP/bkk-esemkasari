<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { ref, watch } from 'vue';
import { BreadcrumbItem } from '@/types';

import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import DatePicker from 'primevue/datepicker';
import Dialog from 'primevue/dialog';
import Drawer from 'primevue/drawer';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Select from 'primevue/select';
import SplitButton from 'primevue/splitbutton';
import Tag from 'primevue/tag';
import Textarea from 'primevue/textarea';
import Toast from 'primevue/toast';
import dayjs from 'dayjs';
import 'dayjs/locale/id';

// Set locale ke Indonesia
dayjs.locale('id');

const toast = useToast();

const props = defineProps({
    year: {
        type: Object,
        required: true,
    },
    studentClass: {
        type: Object,
        required: true,
    },
    models: {
        type: Array,
        required: true,
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Angkatan',
        href: '/years',
    },
    {
        title: props.year.year,
        href: '/years',
    },
    {
        title: 'Kelas',
        href: route('years.student-classes.index', props.year.id),
    },
    {
        title: props.studentClass.name,
        href: route('years.student-classes.index', props.year.id),
    },
    {
        title: 'Siswa',
        href: '#',
    },
];

const splitButtonItems = [
    {
        label: 'Import',
        icon: 'pi pi-upload',
        command: () => {
            importStudent();
        },
    },
    {
        label: 'Export',
        icon: 'pi pi-download',
        command: () => {
            // exportStudent();
        },
    },
];

const filters = ref();
const selectedStudent = ref<any>(null);
const displayDrawer = ref(false);
const displayDeleteDialog = ref(false);
const dialogVisible = ref(false);
const dialogMode = ref(''); // add | edit
const displayImportDialog = ref(false);
const importForm = useForm({
    file: null as File | null,
});
const importLoading = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    id: null,
    student_class_id: props.studentClass.id,
    name: '',
    nisn: '',
    phone: '',
    email: '',
    password: '',
    gender: 'laki-laki',
    born_date: '',
    height: '',
    weight: '',
    province: '',
    city: '',
    address: '',
    is_graduated: false,
    is_married: false,
});

const formatGender = (gender: string) => {
    return gender === 'laki-laki' ? 'Laki-laki' : 'Perempuan';
};

const formatTanggal = (dateString: string) => {
    if (!dateString) return '-';
    return dayjs(dateString).format('dddd, D MMMM YYYY');
};

const detailStudent = (student: any) => {
    selectedStudent.value = student;
    displayDrawer.value = true;
};

const confirmDelete = () => {
    displayDeleteDialog.value = true;
};

const importStudent = () => {
    displayImportDialog.value = true;
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        importForm.file = target.files[0];
    }
};

const submitImport = () => {
    if (!importForm.file) {
        toast.add({
            severity: 'warn',
            summary: 'Peringatan',
            detail: 'Silakan pilih file Excel yang akan diimport',
            life: 5000,
        });
        return;
    }

    importLoading.value = true;

    const formData = new FormData();
    formData.append('file', importForm.file);

    router.post(
        route('students.import', {
            year: props.year.id,
            studentClass: props.studentClass.id,
        }),
        formData,
        {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Data siswa berhasil diimport',
                    life: 5000,
                });
                displayImportDialog.value = false;
                importForm.reset();
                if (fileInput.value) {
                    fileInput.value.value = '';
                }
            },
            onError: (errors) => {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: errors.message || 'Terjadi kesalahan saat mengimport data',
                    life: 5000,
                });
            },
            onFinish: () => {
                importLoading.value = false;
            },
        },
    );
};

const deleteStudent = () => {
    if (!selectedStudent.value) {
        return;
    }

    form.delete(
        route('years.student-classes.students.destroy', {
            year: props.year.id,
            studentClass: props.studentClass.id,
            model: selectedStudent.value.id,
        }),
        {
            errorBag: 'Siswa',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Siswa berhasil dihapus!',
                    life: 5000,
                });
                displayDeleteDialog.value = false;
                form.reset();
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Terjadi kesalahan saat menghapus siswa',
                    life: 5000,
                });
            },
        },
    );
};

const createStudent = () => {
    form.post(
        route('years.student-classes.students.store', {
            year: props.year.id,
            studentClass: props.studentClass.id,
        }),
        {
            errorBag: 'student',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Siswa berhasil ditambahkan!',
                    life: 5000,
                });
                dialogVisible.value = false;
                form.reset();
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Terjadi kesalahan saat menambahkan siswa',
                    life: 5000,
                });
            },
        },
    );
};

const updateStudent = () => {
    form.put(
        route('years.student-classes.students.update', {
            year: props.year.id,
            studentClass: props.studentClass.id,
            model: form.id,
        }),
        {
            errorBag: 'student',
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Sukses',
                    detail: 'Siswa berhasil diperbarui!',
                    life: 5000,
                });
                dialogVisible.value = false;
                form.reset();
            },
            onError: () => {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Terjadi kesalahan saat memperbarui siswa',
                    life: 5000,
                });
            },
        },
    );
};

const openCreateDialog = () => {
    form.reset();
    datePickerValue.value = null; // Reset date picker ke null saat buka dialog tambah baru
    dialogMode.value = 'add';
    dialogVisible.value = true;
};

const openEditDialog = (student: any) => {
    form.id = student.id;
    form.student_class_id = student.student_class_id;
    form.name = student.name;
    form.nisn = student.nisn;
    form.phone = student.phone;
    form.email = student.email;
    form.gender = student.gender;
    form.born_date = student.born_date;
    // Update datePickerValue dengan nilai dari student.born_date
    datePickerValue.value = student.born_date ? dayjs(student.born_date).toDate() : null;
    form.height = student.height;
    form.weight = student.weight;
    form.province = student.province;
    form.city = student.city;
    form.address = student.address;
    form.is_graduated = student.is_graduated;
    form.is_married = student.is_married;
    dialogMode.value = 'edit';
    dialogVisible.value = true;
};

// Expose functions to template
defineExpose({
    formatGender,
    detailStudent,
    confirmDelete,
    deleteStudent,
    createStudent,
    updateStudent,
    openCreateDialog,
    openEditDialog,
});

// Create a reactive reference specifically for the DatePicker's modelValue.
// It should be initialized with a Date object if born_date exists, otherwise null.
const datePickerValue = ref<Date | null>(form.born_date ? dayjs(form.born_date).toDate() : null);

// Watch for changes from the DatePicker (datePickerValue)
watch(
    datePickerValue,
    (newValue) => {
        if (newValue instanceof Date) {
            // Ensure it's a Date object before formatting
            form.born_date = dayjs(newValue).format('YYYY-MM-DD');
        } else {
            // If the DatePicker value becomes null (e.g., user clears the date)
            form.born_date = '';
        }
    },
    { immediate: true },
); // Use immediate to run the watch on initial load if born_date is present

watch(filters, (newValue) => {
    router.get(
        route('years.student-classes.students.index', {
            year: props.year.id,
            studentClass: props.studentClass.id,
        }),
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

    <Head title="Siswa" />
    <Toast />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                        Data Siswa - {{ props.studentClass.name }}
                    </h1>
                    <p class="mt-1.5 text-sm text-gray-500">
                        Kelola data siswa kelas {{ props.studentClass.name }} angkatan {{ props.year.year }}
                    </p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <SplitButton label="Tambah Siswa" @click="openCreateDialog" icon="pi pi-plus"
                        :model="splitButtonItems"
                        class="bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-blue-700 transition-colors duration-200" />
                </div>
            </div>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Search Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative">
                        <!-- Dicomment terlebih dahulu karena icon pi-search menabrak placeholder input -->
                        <!-- <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="pi pi-search text-gray-400 text-sm"></i>
                        </div> -->
                        <InputText v-model="filters" placeholder="Cari siswa berdasarkan nama, NISN, atau telepon..."
                            class="pl-10 w-full sm:w-96 bg-white border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-sm" />
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="pi pi-users"></i>
                        <span>{{ props.models.length }} siswa terdaftar</span>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <DataTable :value="props.models" paginator removableSort row-hover :rows="10"
                :rows-per-page-options="[10, 20, 50, 100]" tableStyle="min-width: 100%" class="custom-datatable"
                paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                currentPageReportTemplate="Menampilkan {first} - {last} dari {totalRecords} siswa">
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                        <i class="pi pi-users text-4xl mb-4 text-gray-300"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada data siswa</h3>
                        <p class="text-sm text-gray-500">Belum ada siswa yang terdaftar di kelas ini</p>
                    </div>
                </template>

                <Column field="name" sortable header="Nama Siswa"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6" bodyClass="py-4 px-6">
                    <template #body="slotProps">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">
                                    {{ slotProps.data.name.charAt(0).toUpperCase() }}
                                </span>
                            </div>
                            <div>
                                <p class="text-base font-semibold text-gray-900">
                                    {{ slotProps.data.name }}
                                </p>
                                <p class="text-xs text-gray-500">{{ slotProps.data.email }}</p>
                            </div>
                        </div>
                    </template>
                </Column>

                <Column field="nisn" sortable header="NISN"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6" bodyClass="py-4 px-6"
                    class="w-40">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <div
                                class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="pi pi-id-card text-purple-600 text-sm"></i>
                            </div>
                            <span class="font-mono text-sm font-medium text-gray-900">
                                {{ slotProps.data.nisn }}
                            </span>
                        </div>
                    </template>
                </Column>

                <Column field="phone" sortable header="No. Telepon"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6" bodyClass="py-4 px-6"
                    class="w-44">
                    <template #body="slotProps">
                        <div class="flex items-center gap-2">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="pi pi-phone text-green-600 text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-900">
                                {{ slotProps.data.phone }}
                            </span>
                        </div>
                    </template>
                </Column>

                <Column field="gender" sortable header="Jenis Kelamin"
                    headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6" bodyClass="py-4 px-6"
                    class="w-40">
                    <template #body="{ data }">
                        <div class="flex items-center gap-2">
                            <div :class="[
                                'flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center',
                                data.gender === 'laki-laki' ? 'bg-blue-100' : 'bg-pink-100'
                            ]">
                                <i :class="[
                                    'text-sm',
                                    data.gender === 'laki-laki' ? 'pi pi-mars text-blue-600' : 'pi pi-venus text-pink-600'
                                ]"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">
                                {{ formatGender(data.gender) }}
                            </span>
                        </div>
                    </template>
                </Column>

                <Column header="Aksi" headerClass="bg-gray-50 text-gray-700 font-semibold text-sm py-4 px-6"
                    bodyClass="py-4 px-6" class="w-32">
                    <template #body="slotProps">
                        <div class="flex items-center justify-center gap-1">
                            <Button icon="pi pi-pencil" severity="warn" @click="openEditDialog(slotProps.data)"
                                class="p-2 text-amber-600 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition-colors duration-200"
                                text size="small" v-tooltip.top="'Edit siswa'" />
                            <Button icon="pi pi-eye" severity="info" @click="detailStudent(slotProps.data)"
                                class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                text size="small" v-tooltip.top="'Detail siswa'" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Enhanced Student Detail Drawer -->
        <Drawer v-model:visible="displayDrawer" header="Detail Siswa" position="right" class="!w-full lg:!w-lg">
            <!-- Student Header Card -->
            <div class="mb-6 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl p-6 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold">
                            {{ selectedStudent?.name.charAt(0).toUpperCase() }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">{{ selectedStudent?.name }}</h3>
                        <p class="text-blue-100">{{ selectedStudent?.nisn }}</p>
                        <p class="text-blue-100 text-sm">{{ props.studentClass.name }} - {{ props.year.year }}</p>
                    </div>
                </div>
            </div>

            <!-- Information Cards -->
            <div class="space-y-4">
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
                                <span class="font-semibold text-gray-900">{{ selectedStudent?.name }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-gray-600 text-sm">NISN</span>
                                <span class="font-mono font-semibold text-gray-900">{{ selectedStudent?.nisn }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-gray-600 text-sm">Email</span>
                                <span class="font-semibold text-gray-900">{{ selectedStudent?.email }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-gray-600 text-sm">No. Telepon</span>
                                <span class="font-semibold text-gray-900">{{ selectedStudent?.phone }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-gray-600 text-sm">Jenis Kelamin</span>
                                <div class="flex items-center gap-2">
                                    <i :class="[
                                        'text-sm',
                                        selectedStudent?.gender === 'laki-laki' ? 'pi pi-mars text-blue-600' : 'pi pi-venus text-pink-600'
                                    ]"></i>
                                    <span class="font-semibold text-gray-900">{{ formatGender(selectedStudent?.gender)
                                    }}</span>
                                </div>
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-gray-600 text-sm">Usia</span>
                                <span class="font-semibold text-gray-900">{{ selectedStudent?.age }} tahun</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600 text-sm">Tanggal Lahir</span>
                                <span class="font-semibold text-gray-900">{{ formatTanggal(selectedStudent?.born_date)
                                }}</span>
                            </div>
                        </div>
                    </template>
                </Card>

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
                                <p class="text-xl font-bold text-gray-900">{{ selectedStudent?.height }}<span
                                        class="text-sm font-normal"> cm</span></p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4 text-center">
                                <i class="pi pi-circle text-green-600 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-600">Berat Badan</p>
                                <p class="text-xl font-bold text-gray-900">{{ selectedStudent?.weight }}<span
                                        class="text-sm font-normal"> kg</span></p>
                            </div>
                        </div>
                    </template>
                </Card>

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
                                <span class="font-semibold text-gray-900">{{ selectedStudent?.province }}</span>
                            </div>
                            <div
                                class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-gray-600 text-sm">Kota</span>
                                <span class="font-semibold text-gray-900">{{ selectedStudent?.city }}</span>
                            </div>
                            <div class="py-2">
                                <span class="text-gray-600 text-sm block mb-2">Alamat Lengkap</span>
                                <p class="font-semibold text-gray-900 bg-gray-50 rounded-lg p-3">{{
                                    selectedStudent?.address }}</p>
                            </div>
                        </div>
                    </template>
                </Card>

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
                                <Tag :value="selectedStudent?.is_graduated_label"
                                    :severity="selectedStudent?.is_graduated === true ? 'success' : 'warn'"
                                    class="font-medium" />
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Status Menikah</span>
                                <Tag :value="selectedStudent?.is_married_label"
                                    :severity="selectedStudent?.is_married === true ? 'info' : 'secondary'"
                                    class="font-medium" />
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <template #footer>
                <div class="flex flex-col gap-3 pt-4">
                    <Button v-if="selectedStudent?.cv_file_url" label="Download CV" icon="pi pi-download"
                        class="w-full bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-blue-700 transition-colors duration-200"
                        @click="window.open(selectedStudent?.cv_file_url, '_blank')" />
                    <Button label="Hapus Siswa" icon="pi pi-trash" severity="danger" class="w-full" outlined
                        @click="confirmDelete" />
                </div>
            </template>
        </Drawer>

        <!-- Enhanced Delete Confirmation Dialog -->
        <Dialog v-model:visible="displayDeleteDialog" modal class="w-full max-w-md mx-4">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="pi pi-exclamation-triangle text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
            </template>

            <div class="py-4">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <i class="pi pi-exclamation-triangle text-red-500 text-xl mt-0.5"></i>
                        <div>
                            <p class="text-sm text-red-800 font-medium mb-1">
                                Hapus data siswa {{ selectedStudent?.name }}?
                            </p>
                            <p class="text-sm text-red-700">
                                Semua data yang terkait dengan siswa ini akan ikut terhapus.
                                Pastikan Anda telah membackup data yang diperlukan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end gap-3">
                    <Button label="Batal" @click="displayDeleteDialog = false"
                        class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                        text />
                    <Button label="Hapus Siswa" icon="pi pi-trash" @click="deleteStudent"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 border-red-600 hover:border-red-700 transition-colors duration-200"
                        :loading="form.processing" />
                </div>
            </template>
        </Dialog>

        <!-- Enhanced Student Form Dialog -->
        <Dialog v-model:visible="dialogVisible" modal class="w-full max-w-4xl mx-4">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="pi pi-user text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ dialogMode === 'add' ? 'Tambah Siswa Baru' : 'Edit Data Siswa' }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            {{ dialogMode === 'add' ? 'Masukkan data siswa baru untuk kelas ' + props.studentClass.name
                                : 'Ubah data siswa' }}
                        </p>
                    </div>
                </div>
            </template>
            <div class="p-6 space-y-8 max-h-[70vh] overflow-y-auto">
                <!-- Informasi Pribadi -->
                <div class="bg-blue-50/50 rounded-lg p-6 border border-blue-100">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="pi pi-user text-blue-600"></i>
                        </div>
                        <h5 class="text-lg font-semibold text-gray-900">Informasi Pribadi</h5>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <InputText id="name" v-model="form.name" class="w-full"
                                :class="{ 'border-red-300': form.errors.name }" placeholder="Masukkan nama lengkap" />
                            <small v-if="form.errors.name" class="text-red-600 text-xs">{{ form.errors.name }}</small>
                        </div>
                        <div class="space-y-2">
                            <label for="nisn" class="block text-sm font-medium text-gray-700">
                                NISN <span class="text-red-500">*</span>
                            </label>
                            <InputText id="nisn" v-model="form.nisn" class="w-full"
                                :class="{ 'border-red-300': form.errors.nisn }"
                                placeholder="Nomor Induk Siswa Nasional" />
                            <small v-if="form.errors.nisn" class="text-red-600 text-xs">{{ form.errors.nisn }}</small>
                        </div>
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <InputText id="email" v-model="form.email" class="w-full"
                                :class="{ 'border-red-300': form.errors.email }" placeholder="contoh@email.com" />
                            <small v-if="form.errors.email" class="text-red-600 text-xs">{{ form.errors.email }}</small>
                        </div>
                        <div class="space-y-2">
                            <label for="phone" class="block text-sm font-medium text-gray-700">
                                No. Telepon <span class="text-red-500">*</span>
                            </label>
                            <InputText id="phone" v-model="form.phone" class="w-full"
                                :class="{ 'border-red-300': form.errors.phone }" placeholder="08123456789" />
                            <small v-if="form.errors.phone" class="text-red-600 text-xs">{{ form.errors.phone }}</small>
                        </div>
                        <div class="space-y-2">
                            <label for="gender" class="block text-sm font-medium text-gray-700">
                                Jenis Kelamin <span class="text-red-500">*</span>
                            </label>
                            <Select id="gender" v-model="form.gender" :options="[
                                { label: 'Laki-laki', value: 'laki-laki' },
                                { label: 'Perempuan', value: 'perempuan' },
                            ]" optionLabel="label" optionValue="value" class="w-full"
                                :class="{ 'border-red-300': form.errors.gender }" placeholder="Pilih jenis kelamin" />
                            <small v-if="form.errors.gender" class="text-red-600 text-xs">{{ form.errors.gender
                            }}</small>
                        </div>
                        <div class="space-y-2">
                            <label for="born_date" class="block text-sm font-medium text-gray-700">
                                Tanggal Lahir <span class="text-red-500">*</span>
                            </label>
                            <DatePicker id="born_date" v-model="datePickerValue" class="w-full"
                                :class="{ 'border-red-300': form.errors.born_date }" dateFormat="yy-mm-dd"
                                placeholder="Pilih tanggal lahir" />
                            <small v-if="form.errors.born_date" class="text-red-600 text-xs">{{ form.errors.born_date
                            }}</small>
                        </div>
                        <div class="space-y-2 sm:col-span-2">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <Password id="password" v-model="form.password"
                                :class="{ 'border-red-300': form.errors.password }" :feedback="true" toggleMask
                                placeholder="Masukkan password" />
                            <small v-if="form.errors.password" class="text-red-600 text-xs">{{ form.errors.password
                            }}</small>
                        </div>
                    </div>
                </div>

                <!-- Informasi Fisik -->
                <div class="bg-green-50/50 rounded-lg p-6 border border-green-100">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="pi pi-heart text-green-600"></i>
                        </div>
                        <h5 class="text-lg font-semibold text-gray-900">Informasi Fisik</h5>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label for="height" class="block text-sm font-medium text-gray-700">Tinggi Badan
                                (cm)</label>
                            <InputText id="height" v-model="form.height" class="w-full"
                                :class="{ 'border-red-300': form.errors.height }" placeholder="Contoh: 170" />
                            <small v-if="form.errors.height" class="text-red-600 text-xs">{{ form.errors.height
                            }}</small>
                        </div>
                        <div class="space-y-2">
                            <label for="weight" class="block text-sm font-medium text-gray-700">Berat Badan (kg)</label>
                            <InputText id="weight" v-model="form.weight" class="w-full"
                                :class="{ 'border-red-300': form.errors.weight }" placeholder="Contoh: 65" />
                            <small v-if="form.errors.weight" class="text-red-600 text-xs">{{ form.errors.weight
                            }}</small>
                        </div>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="bg-orange-50/50 rounded-lg p-6 border border-orange-100">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="pi pi-map-marker text-orange-600"></i>
                        </div>
                        <h5 class="text-lg font-semibold text-gray-900">Alamat</h5>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label for="province" class="block text-sm font-medium text-gray-700">Provinsi</label>
                            <InputText id="province" v-model="form.province" class="w-full"
                                :class="{ 'border-red-300': form.errors.province }" placeholder="Contoh: Jawa Timur" />
                            <small v-if="form.errors.province" class="text-red-600 text-xs">{{ form.errors.province
                            }}</small>
                        </div>
                        <div class="space-y-2">
                            <label for="city" class="block text-sm font-medium text-gray-700">Kota</label>
                            <InputText id="city" v-model="form.city" class="w-full"
                                :class="{ 'border-red-300': form.errors.city }" placeholder="Contoh: Bojonegoro" />
                            <small v-if="form.errors.city" class="text-red-600 text-xs">{{ form.errors.city }}</small>
                        </div>
                        <div class="space-y-2 sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                            <Textarea id="address" v-model="form.address" class="w-full" rows="3"
                                :class="{ 'border-red-300': form.errors.address }"
                                placeholder="Masukkan alamat lengkap" />
                            <small v-if="form.errors.address" class="text-red-600 text-xs">{{ form.errors.address
                            }}</small>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-purple-50/50 rounded-lg p-6 border border-purple-100">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="pi pi-check-circle text-purple-600"></i>
                        </div>
                        <h5 class="text-lg font-semibold text-gray-900">Status</h5>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label for="is_graduated" class="block text-sm font-medium text-gray-700">Status
                                Lulus</label>
                            <Select id="is_graduated" v-model="form.is_graduated" :options="[
                                { label: 'Lulus', value: true },
                                { label: 'Belum Lulus', value: false },
                            ]" optionLabel="label" optionValue="value" class="w-full"
                                :class="{ 'border-red-300': form.errors.is_graduated }"
                                placeholder="Pilih status kelulusan" />
                            <small v-if="form.errors.is_graduated" class="text-red-600 text-xs">{{
                                form.errors.is_graduated
                            }}</small>
                        </div>
                        <div class="space-y-2">
                            <label for="is_married" class="block text-sm font-medium text-gray-700">Status
                                Menikah</label>
                            <Select id="is_married" v-model="form.is_married" :options="[
                                { label: 'Menikah', value: true },
                                { label: 'Belum Menikah', value: false },
                            ]" optionLabel="label" optionValue="value" class="w-full"
                                :class="{ 'border-red-300': form.errors.is_married }"
                                placeholder="Pilih status pernikahan" />
                            <small v-if="form.errors.is_married" class="text-red-600 text-xs">{{ form.errors.is_married
                            }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    <Button label="Batal" @click="dialogVisible = false"
                        class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                        text />
                    <Button :label="dialogMode === 'add' ? 'Tambah Siswa' : 'Simpan Perubahan'" icon="pi pi-check"
                        @click="dialogMode === 'add' ? createStudent() : updateStudent()"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 border-blue-600 hover:border-blue-700 transition-colors duration-200"
                        :loading="form.processing" />
                </div>
            </template>
        </Dialog>

        <!-- Enhanced Import Dialog -->
        <Dialog v-model:visible="displayImportDialog" modal class="w-full max-w-lg mx-4">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="pi pi-file-excel text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Import Data Siswa</h3>
                        <p class="text-sm text-gray-500">Unggah file Excel untuk menambah data siswa secara massal</p>
                    </div>
                </div>
            </template>

            <div class="p-6 space-y-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <i class="pi pi-info-circle text-blue-600 text-lg mt-0.5"></i>
                        <div>
                            <p class="text-sm text-blue-800 font-medium mb-1">Panduan Import</p>
                            <p class="text-sm text-blue-700 mb-2">
                                Pastikan format file Excel sesuai dengan template yang disediakan untuk menghindari
                                error saat
                                import.
                            </p>
                            <a href="/templates/template_import_siswa.xlsx"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium underline">
                                📥 Unduh Template Excel
                            </a>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">File Excel</label>
                    <label for="dropzone-file"
                        class="flex h-32 w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 hover:bg-gray-100 transition-colors duration-200"
                        :class="{ 'border-blue-400 bg-blue-50': importForm.file }">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="pi pi-upload mb-2 text-2xl text-gray-400"></i>
                            <p class="mb-2 text-sm text-gray-500">
                                <span class="font-semibold">Klik untuk mengunggah</span> atau drag & drop
                            </p>
                            <p class="text-xs text-gray-500">CSV, XLS, XLSX (MAX. 10MB)</p>
                        </div>
                        <input id="dropzone-file" ref="fileInput" type="file" class="hidden" accept=".csv, .xls, .xlsx"
                            @change="handleFileChange" />
                    </label>
                    <p v-if="importForm.file" class="text-sm text-green-600 flex items-center gap-2">
                        <i class="pi pi-check-circle"></i>
                        <span>File: {{ importForm.file?.name }}</span>
                    </p>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    <Button label="Batal" @click="displayImportDialog = false"
                        class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                        :disabled="importLoading" text />
                    <Button label="Import Data" icon="pi pi-upload" @click="submitImport"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 border-green-600 hover:border-green-700 transition-colors duration-200"
                        :loading="importLoading" />
                </div>
            </template>
        </Dialog>
    </AppLayout>
</template>
