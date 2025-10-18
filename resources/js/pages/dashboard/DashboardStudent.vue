<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

// PrimeVue Components
import Card from 'primevue/card';
import Button from 'primevue/button';
import Badge from 'primevue/badge';
import Chart from 'primevue/chart';
import Tag from 'primevue/tag';
import ProgressBar from 'primevue/progressbar';
import Avatar from 'primevue/avatar';
import Divider from 'primevue/divider';

// dayjs for date formatting
import dayjs from 'dayjs';
import 'dayjs/locale/id';
dayjs.locale('id');

const props = defineProps({
    dashboardData: {
        type: Object,
        required: true
    }
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Computed properties for dashboard data
const studentProfile = computed(() => props.dashboardData.studentProfile || {});
const totalApplications = computed(() => props.dashboardData.totalApplications || 0);
const pendingApplications = computed(() => props.dashboardData.pendingApplications || 0);
const qualifiedApplications = computed(() => props.dashboardData.qualifiedApplications || 0);
const rejectedApplications = computed(() => props.dashboardData.rejectedApplications || 0);
const recentVacancies = computed(() => props.dashboardData.recentVacancies || []);
const myApplications = computed(() => props.dashboardData.myApplications || []);
const announcements = computed(() => props.dashboardData.announcements || []);
const tracerStudyStatus = computed(() => props.dashboardData.tracerStudyStatus || {});
const applicationStats = computed(() => props.dashboardData.applicationStats || []);
const graduationStatus = computed(() => props.dashboardData.graduationStatus || {});

// Chart configuration for application timeline
const applicationChartData = computed(() => ({
    labels: applicationStats.value.map((stat: any) => dayjs(stat.month).format('MMM YYYY')),
    datasets: [{
        label: 'Lamaran Diajukan',
        data: applicationStats.value.map((stat: any) => stat.count),
        backgroundColor: 'rgba(59, 130, 246, 0.5)',
        borderColor: 'rgb(59, 130, 246)',
        borderWidth: 2,
        fill: true
    }]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top' as const,
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1
            }
        }
    }
};

// Helper functions
const getStatusSeverity = (status: string) => {
    switch (status) {
        case 'qualified': return 'success';
        case 'applied': return 'info';
        default: return 'secondary';
    }
};

const getStatusLabel = (status: string) => {
    switch (status) {
        case 'qualified': return 'Lolos Seleksi';
        case 'applied': return 'Menunggu Seleksi';
        default: return 'Tidak Diketahui';
    }
};

const formatDate = (date: string) => {
    return dayjs(date).format('DD MMM YYYY');
};

const formatDateTime = (date: string) => {
    return dayjs(date).format('DD MMM YYYY, HH:mm');
};

const getApplicationProgress = () => {
    const total = totalApplications.value;
    if (total === 0) return 0;
    return Math.round((qualifiedApplications.value / total) * 100);
};

const getGraduationStatusSeverity = (isGraduated: boolean) => {
    return isGraduated ? 'success' : 'warning';
};

const getGraduationStatusLabel = (isGraduated: boolean) => {
    return isGraduated ? 'Sudah Lulus' : 'Belum Lulus';
};
</script>

<template>
    <Head title="Dashboard Siswa" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-green-600 to-green-800 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">Selamat Datang, {{ studentProfile.name }}!</h1>
                        <p class="text-green-100">{{ studentProfile.student_class?.name }} - {{ studentProfile.student_class?.year?.year }}</p>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="text-sm">NISN: {{ studentProfile.nisn }}</span>
                            <Tag
                                :value="getGraduationStatusLabel(graduationStatus.is_graduated)"
                                :severity="getGraduationStatusSeverity(graduationStatus.is_graduated)"
                                size="small"
                            />
                        </div>
                    </div>
                    <Avatar
                        :label="studentProfile.name?.charAt(0) || 'S'"
                        class="bg-white text-green-600 w-16 h-16 text-2xl"
                    />
                </div>
            </div>

            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Applications -->
                <Card class="border-l-4 border-blue-500">
                    <template #content>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total Lamaran</p>
                                <p class="text-2xl font-bold text-blue-600">{{ totalApplications }}</p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="pi pi-file text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Pending Applications -->
                <Card class="border-l-4 border-yellow-500">
                    <template #content>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Menunggu</p>
                                <p class="text-2xl font-bold text-yellow-600">{{ pendingApplications }}</p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <i class="pi pi-clock text-yellow-600 text-xl"></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Qualified Applications -->
                <Card class="border-l-4 border-green-500">
                    <template #content>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Diterima</p>
                                <p class="text-2xl font-bold text-green-600">{{ qualifiedApplications }}</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="pi pi-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Rejected Applications -->
                <Card class="border-l-4 border-red-500">
                    <template #content>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Ditolak</p>
                                <p class="text-2xl font-bold text-red-600">{{ rejectedApplications }}</p>
                            </div>
                            <div class="bg-red-100 p-3 rounded-full">
                                <i class="pi pi-times-circle text-red-600 text-xl"></i>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Charts and Quick Actions Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Application Timeline Chart -->
                <Card class="lg:col-span-2">
                    <template #title>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Riwayat Lamaran</h3>
                            <Button icon="pi pi-refresh" text rounded size="small" />
                        </div>
                    </template>
                    <template #content>
                        <div class="h-80">
                            <Chart type="line" :data="applicationChartData" :options="chartOptions" class="h-full" />
                        </div>
                    </template>
                </Card>

                <!-- Quick Actions & Progress -->
                <Card>
                    <template #title>
                        <h3 class="text-lg font-semibold">Aksi Cepat</h3>
                    </template>
                    <template #content>
                        <div class="space-y-3">
                            <Button
                                label="Cari Lowongan"
                                icon="pi pi-search"
                                class="w-full"
                                @click="router.visit('/students/vacancies')"
                            />
                            <Button
                                label="Riwayat Lamaran"
                                icon="pi pi-history"
                                severity="secondary"
                                class="w-full"
                                @click="router.visit('/students/vacancies')"
                            />
                            <Button
                                label="Pengumuman"
                                icon="pi pi-bell"
                                severity="info"
                                class="w-full"
                                @click="router.visit('students/announcements')"
                            />

                            <Divider />

                            <div class="text-center">
                                <p class="text-sm text-gray-600 mb-2">Tingkat Keberhasilan</p>
                                <div class="mb-2">
                                    <ProgressBar
                                        :value="getApplicationProgress()"
                                        :show-value="false"
                                        class="h-2"
                                    />
                                </div>
                                <p class="text-xs text-gray-500">
                                    {{ getApplicationProgress() }}% dari {{ totalApplications }} lamaran
                                </p>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Content Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Vacancies -->
                <Card>
                    <template #title>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Lowongan Terbaru</h3>
                            <Button
                                label="Lihat Semua"
                                text
                                size="small"
                                @click="router.visit('/students/vacancies')"
                            />
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-4">
                            <div
                                v-for="vacancy in recentVacancies"
                                :key="vacancy.id"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
                                @click="router.visit(`/students/vacancies/${vacancy.id}`)"
                            >
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ vacancy.title }}</h4>
                                    <p class="text-sm text-gray-600">{{ vacancy.created_by?.name }}</p>
                                    <p class="text-sm text-gray-500">{{ vacancy.location }}</p>
                                    <p class="text-xs text-gray-400">Deadline: {{ formatDate(vacancy.due_at) }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Badge
                                        :value="vacancy.applicants_count"
                                        severity="info"
                                        size="small"
                                    />
                                    <i class="pi pi-chevron-right text-gray-400"></i>
                                </div>
                            </div>
                            <div v-if="recentVacancies.length === 0" class="text-center py-8">
                                <i class="pi pi-briefcase text-gray-400 text-3xl mb-2"></i>
                                <p class="text-gray-500">Belum ada lowongan tersedia</p>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- My Applications -->
                <Card>
                    <template #title>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Lamaran Saya</h3>
                            <Button
                                label="Lihat Semua"
                                text
                                size="small"
                                @click="router.visit('/students/vacancies')"
                            />
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-4">
                            <div
                                v-for="application in myApplications"
                                :key="application.id"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
                                @click="router.visit(`/students/vacancies/${application.vacancy.id}`)"
                            >
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ application.vacancy.title }}</h4>
                                    <p class="text-sm text-gray-600">{{ application.vacancy.created_by?.name }}</p>
                                    <p class="text-xs text-gray-500">{{ formatDateTime(application.created_at) }}</p>
                                </div>
                                <Tag
                                    :value="getStatusLabel(application.status)"
                                    :severity="getStatusSeverity(application.status)"
                                    size="small"
                                />
                            </div>
                            <div v-if="myApplications.length === 0" class="text-center py-8">
                                <i class="pi pi-file text-gray-400 text-3xl mb-2"></i>
                                <p class="text-gray-500">Belum ada lamaran yang diajukan</p>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Announcements & Tracer Study -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Announcements -->
                <Card>
                    <template #title>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Pengumuman Terbaru</h3>
                            <Button
                                label="Lihat Semua"
                                text
                                size="small"
                                @click="router.visit('students/announcements')"
                            />
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-4">
                            <div
                                v-for="announcement in announcements"
                                :key="announcement.id"
                                class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
                                @click="router.visit(`students/announcements/${announcement.id}`)"
                            >
                                <h4 class="font-medium text-gray-900 mb-1">{{ announcement.title }}</h4>
                                <p class="text-sm text-gray-600 mb-2">{{ announcement.excerpt }}</p>
                                <p class="text-xs text-gray-500">{{ formatDate(announcement.created_at) }}</p>
                            </div>
                            <div v-if="announcements.length === 0" class="text-center py-8">
                                <i class="pi pi-bell text-gray-400 text-3xl mb-2"></i>
                                <p class="text-gray-500">Belum ada pengumuman</p>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Tracer Study Status -->
                <Card>
                    <template #title>
                        <h3 class="text-lg font-semibold">Tracer Study</h3>
                    </template>
                    <template #content>
                        <div class="space-y-4">
                            <div v-if="tracerStudyStatus.completed" class="text-center p-6">
                                <i class="pi pi-check-circle text-green-500 text-4xl mb-3"></i>
                                <h4 class="font-medium text-green-700 mb-2">Tracer Study Selesai</h4>
                                <p class="text-sm text-gray-600">Terima kasih telah mengisi tracer study</p>
                                <p class="text-xs text-gray-500 mt-2">
                                    Diselesaikan: {{ formatDate(tracerStudyStatus.completed_at) }}
                                </p>
                            </div>
                            <div v-else class="text-center p-6">
                                <i class="pi pi-info-circle text-blue-500 text-4xl mb-3"></i>
                                <h4 class="font-medium text-blue-700 mb-2">Tracer Study Belum Selesai</h4>
                                <p class="text-sm text-gray-600 mb-4">
                                    Bantu kami melacak perkembangan alumni dengan mengisi tracer study
                                </p>
                                <Button
                                    label="Isi Sekarang"
                                    icon="pi pi-arrow-right"
                                    size="small"
                                    @click="router.visit('/tracer-study')"
                                />
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Profile Completion -->
            <Card>
                <template #title>
                    <h3 class="text-lg font-semibold">Profil Saya</h3>
                </template>
                <template #content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-lg font-bold text-blue-600">{{ studentProfile.student_class?.name }}</div>
                            <div class="text-sm text-blue-800">Kelas</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-lg font-bold text-green-600">{{ studentProfile.student_class?.year?.year }}</div>
                            <div class="text-sm text-green-800">Tahun Angkatan</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-lg font-bold text-purple-600">{{ studentProfile.age }} tahun</div>
                            <div class="text-sm text-purple-800">Umur</div>
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </AppLayout>
</template>
