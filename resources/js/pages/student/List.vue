<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { ref, watch } from 'vue';
import dayjs from 'dayjs';
import 'dayjs/locale/id';

import { BreadcrumbItem } from '@/types';

import Button from 'primevue/button';
import Card from 'primevue/card';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import Drawer from 'primevue/drawer';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Toast from 'primevue/toast';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';
import Password from 'primevue/password';

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
        href: route('student-classes.index', props.year.id),
    },
    {
        title: props.studentClass.name,
        href: route('student-classes.index', props.year.id),
    },
    {
        title: 'Siswa',
        href: '#',
    },
];

const filters = ref();
const selectedStudent = ref<any>(null);
const displayDrawer = ref(false);
const displayDeleteDialog = ref(false);
const dialogVisible = ref(false);
const dialogMode = ref(''); // add | edit

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

const deleteStudent = () => {
    if (!selectedStudent.value) {
        return;
    }

    form.delete(route('students.destroy', {
        year: props.year.id,
        studentClass: props.studentClass.id,
        model: selectedStudent.value.id,
    }), {
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
    });
};

const createStudent = () => {
    form.post(route('students.store', {
        year: props.year.id,
        studentClass: props.studentClass.id,
    }), {
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
    });
};

const updateStudent = () => {
    form.put(route('students.update', {
        year: props.year.id,
        studentClass: props.studentClass.id,
        model: form.id,
    }), {
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
    });
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
const datePickerValue = ref<Date | null>(
    form.born_date ? dayjs(form.born_date).toDate() : null
);

// Watch for changes from the DatePicker (datePickerValue)
watch(datePickerValue, (newValue) => {
    if (newValue instanceof Date) { // Ensure it's a Date object before formatting
        form.born_date = dayjs(newValue).format('YYYY-MM-DD');
    } else {
        // If the DatePicker value becomes null (e.g., user clears the date)
        form.born_date = '';
    }
}, { immediate: true }); // Use immediate to run the watch on initial load if born_date is present

watch(filters, (newValue) => {
    router.get(
        route('students.index', props.studentClass.id),
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
        <DataTable
            :value="props.models"
            paginator
            removableSort
            row-hover
            :rows="10"
            :rows-per-page-options="[10, 20, 50, 100]"
            tableStyle="min-width: 50rem"
        >
            <template #header>
                <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                    <InputText class="w-full lg:w-72" v-model="filters" placeholder="Search ..." />
                    <Button label="Tambah" @click="openCreateDialog" variant="primary" icon="pi pi-plus" />
                </div>
            </template>
            <template #empty>
                <span class="text-center">No data found.</span>
            </template>

            <Column field="name" sortable header="Nama"></Column>
            <Column field="nisn" sortable header="NISN"></Column>
            <Column field="phone" sortable header="No. Telepon"></Column>
            <Column field="gender" sortable header="Jenis Kelamin">
                <template #body="{ data }">
                    {{ formatGender(data.gender) }}
                </template>
            </Column>
            <Column style="width: 10%">
                <template #body="slotProps">
                    <div class="flex justify-center">
                        <Button style="color: #eab308 !important" icon="pi pi-pencil" variant="link" severity="warn" @click="openEditDialog(slotProps.data)" />
                        <Button icon="pi pi-angle-right" variant="link" severity="secondary" @click="detailStudent(slotProps.data)" />
                    </div>
                </template>
            </Column>
        </DataTable>

        <Drawer v-model:visible="displayDrawer" header="Detail Siswa" position="right" class="!w-full lg:!w-sm">
            <Card class="my-2 !shadow-xl dark:!bg-gray-800 dark:!shadow-none">
                <template #title> Informasi Pribadi </template>
                <template #content>
                    <table class="w-full">
                        <tbody>
                            <tr>
                                <td>Nama</td>
                                <td class="py-2 text-right font-bold">{{ selectedStudent?.name }}</td>
                            </tr>
                            <tr>
                                <td>NISN</td>
                                <td class="py-2 text-right font-bold">{{ selectedStudent?.nisn }}</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td class="py-2 text-right font-bold">{{ selectedStudent?.email }}</td>
                            </tr>
                            <tr>
                                <td>No. Telepon</td>
                                <td class="py-2 text-right font-bold">{{ selectedStudent?.phone }}</td>
                            </tr>
                            <tr>
                                <td>Jenis Kelamin</td>
                                <td class="py-2 text-right font-bold">{{ formatGender(selectedStudent?.gender) }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Lahir</td>
                                <td class="py-2 text-right font-bold">{{ formatTanggal(selectedStudent?.born_date) }}</td>
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
                                <td class="py-2 text-right font-bold">{{ selectedStudent?.height }} cm</td>
                            </tr>
                            <tr>
                                <td>Berat Badan</td>
                                <td class="py-2 text-right font-bold">{{ selectedStudent?.weight }} kg</td>
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
                                <td class="py-2 text-right font-bold">{{ selectedStudent?.province }}</td>
                            </tr>
                            <tr>
                                <td>Kota</td>
                                <td class="py-2 text-right font-bold">{{ selectedStudent?.city }}</td>
                            </tr>
                            <tr>
                                <td>Alamat Lengkap</td>
                                <td class="py-2 text-right font-bold">{{ selectedStudent?.address }}</td>
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
                                        :value="selectedStudent?.is_graduated_label"
                                        :severity="selectedStudent?.is_graduated === true ? 'success' : 'danger'"
                                    />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="pb-2">Status Menikah</td>
                                <td class="py-2 text-right">
                                    <Tag
                                        :value="selectedStudent?.is_married_label"
                                        :severity="selectedStudent?.is_married === true ? 'success' : 'danger'"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </template>
            </Card>

            <template #footer>
                <Button label="Download CV" icon="pi pi-download" class="my-2 w-full px-12 text-center" size="small" severity="info" />
                <Button
                    label="Hapus"
                    icon="pi pi-trash"
                    class="my-2 w-full px-12 text-center"
                    size="small"
                    severity="danger"
                    @click="confirmDelete"
                />
            </template>
        </Drawer>

        <Dialog v-model:visible="displayDeleteDialog" modal header="Konfirmasi Hapus" :style="{ width: '450px' }">
            <div class="text-center">
                <i class="pi pi-exclamation-triangle text-red-500 mb-4" style="font-size: 3rem"></i>
                <p>Apakah Anda yakin ingin menghapus siswa ini?</p>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" text @click="displayDeleteDialog = false" />
                <Button label="Hapus" icon="pi pi-check" severity="danger" @click="deleteStudent" />
            </template>
        </Dialog>

        <Dialog
            v-model:visible="dialogVisible"
            modal
            :header="dialogMode === 'add' ? 'Tambah Siswa' : 'Ubah Siswa'"
            class="w-full max-w-lg sm:max-w-xl md:max-w-2xl"
        >
            <div class="flex flex-col gap-y-4 p-2 lg:p-4">
                <!-- Informasi Pribadi -->
                <div>
                    <h5 class="mb-2 text-lg font-bold text-foreground">Informasi Pribadi</h5>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-foreground">Nama</label>
                            <InputText id="name" v-model="form.name" class="mt-1 w-full" :class="{ 'p-invalid': form.errors.name }" />
                            <small v-if="form.errors.name" class="p-error text-xs">{{ form.errors.name }}</small>
                        </div>
                        <div>
                            <label for="nisn" class="block text-sm font-medium text-foreground">NISN</label>
                            <InputText id="nisn" v-model="form.nisn" class="mt-1 w-full" :class="{ 'p-invalid': form.errors.nisn }" />
                            <small v-if="form.errors.nisn" class="p-error text-xs">{{ form.errors.nisn }}</small>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-foreground">Email</label>
                            <InputText id="email" v-model="form.email" class="mt-1 w-full" :class="{ 'p-invalid': form.errors.email }" />
                            <small v-if="form.errors.email" class="p-error text-xs">{{ form.errors.email }}</small>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-foreground">No. Telepon</label>
                            <InputText id="phone" v-model="form.phone" class="mt-1 w-full" :class="{ 'p-invalid': form.errors.phone }" />
                            <small v-if="form.errors.phone" class="p-error text-xs">{{ form.errors.phone }}</small>
                        </div>
                        <div>
                            <label for="gender" class="block text-sm font-medium text-foreground">Jenis Kelamin</label>
                            <Select
                                id="gender"
                                v-model="form.gender"
                                :options="[
                                    { label: 'Laki-laki', value: 'laki-laki' },
                                    { label: 'Perempuan', value: 'perempuan' },
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                class="mt-1 w-full"
                                :class="{ 'p-invalid': form.errors.gender }"
                            />
                            <small v-if="form.errors.gender" class="p-error text-xs">{{ form.errors.gender }}</small>
                        </div>
                        <div>
                            <label for="born_date" class="block text-sm font-medium text-foreground">Tanggal Lahir</label>
                            <DatePicker
                                id="born_date"
                                v-model="datePickerValue"
                                class="mt-1 w-full"
                                :class="{ 'p-invalid': form.errors.born_date }"
                                dateFormat="yy-mm-dd"
                            />
                            <small v-if="form.errors.born_date" class="p-error text-xs">{{ form.errors.born_date }}</small>
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-foreground">Password</label>
                            <Password
                                id="password"
                                v-model="form.password"
                                class="mt-1"
                                :class="{ 'p-invalid': form.errors.password }"
                                :feedback="true"
                                min-length="8"
                                toggleMask
                            />
                            <small v-if="form.errors.password" class="p-error text-xs">{{ form.errors.password }}</small>
                        </div>
                    </div>
                </div>

                <!-- Informasi Fisik -->
                <div>
                    <h5 class="mb-2 text-lg font-bold text-foreground">Informasi Fisik</h5>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="height" class="block text-sm font-medium text-foreground">Tinggi Badan (cm)</label>
                            <InputText id="height" v-model="form.height" class="mt-1 w-full" :class="{ 'p-invalid': form.errors.height }" />
                            <small v-if="form.errors.height" class="p-error text-xs">{{ form.errors.height }}</small>
                        </div>
                        <div>
                            <label for="weight" class="block text-sm font-medium text-foreground">Berat Badan (kg)</label>
                            <InputText id="weight" v-model="form.weight" class="mt-1 w-full" :class="{ 'p-invalid': form.errors.weight }" />
                            <small v-if="form.errors.weight" class="p-error text-xs">{{ form.errors.weight }}</small>
                        </div>
                    </div>
                </div>

                <!-- Alamat -->
                <div>
                    <h5 class="mb-2 text-lg font-bold text-foreground">Alamat</h5>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="province" class="block text-sm font-medium text-foreground">Provinsi</label>
                            <InputText id="province" v-model="form.province" class="mt-1 w-full" :class="{ 'p-invalid': form.errors.province }" />
                            <small v-if="form.errors.province" class="p-error text-xs">{{ form.errors.province }}</small>
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium text-foreground">Kota</label>
                            <InputText id="city" v-model="form.city" class="mt-1 w-full" :class="{ 'p-invalid': form.errors.city }" />
                            <small v-if="form.errors.city" class="p-error text-xs">{{ form.errors.city }}</small>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-foreground">Alamat Lengkap</label>
                            <Textarea
                                id="address"
                                v-model="form.address"
                                class="mt-1 w-full"
                                rows="3"
                                :class="{ 'p-invalid': form.errors.address }"
                            />
                            <small v-if="form.errors.address" class="p-error text-xs">{{ form.errors.address }}</small>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <h5 class="mb-2 text-lg font-bold text-foreground">Status</h5>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="is_graduated" class="block text-sm font-medium text-foreground">Status Lulus</label>
                            <Select
                                id="is_graduated"
                                v-model="form.is_graduated"
                                :options="[
                                    { label: 'Lulus', value: true },
                                    { label: 'Belum Lulus', value: false },
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                class="mt-1 w-full"
                                :class="{ 'p-invalid': form.errors.is_graduated }"
                            />
                            <small v-if="form.errors.is_graduated" class="p-error text-xs">{{ form.errors.is_graduated }}</small>
                        </div>
                        <div>
                            <label for="is_married" class="block text-sm font-medium text-foreground">Status Menikah</label>
                            <Select
                                id="is_married"
                                v-model="form.is_married"
                                :options="[
                                    { label: 'Menikah', value: true },
                                    { label: 'Belum Menikah', value: false },
                                ]"
                                optionLabel="label"
                                optionValue="value"
                                class="mt-1 w-full"
                                :class="{ 'p-invalid': form.errors.is_married }"
                            />
                            <small v-if="form.errors.is_married" class="p-error text-xs">{{ form.errors.is_married }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <template #footer>
                <Button label="Batal" icon="pi pi-times" class="p-button-text p-button-secondary" @click="dialogVisible = false" />
                <Button
                    :label="dialogMode === 'add' ? 'Simpan' : 'Update'"
                    icon="pi pi-check"
                    class="p-button-success"
                    @click="dialogMode === 'add' ? createStudent() : updateStudent()"
                />
            </template>
        </Dialog>
    </AppLayout>
</template>
