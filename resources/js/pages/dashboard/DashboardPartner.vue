<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

// PrimeVue Components
import Avatar from 'primevue/avatar';
import Badge from 'primevue/badge';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Chart from 'primevue/chart';
import Tag from 'primevue/tag';

// dayjs for date formatting
import dayjs from 'dayjs';
import 'dayjs/locale/id';
dayjs.locale('id');

const props = defineProps({
    dashboardData: {
        type: Object,
        required: true,
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/partners/dashboard/',
    },
];

// Computed properties for dashboard metrics
const totalVacancies = computed(() => props.dashboardData.totalVacancies || 0);
const totalApplicants = computed(() => props.dashboardData.totalApplicants || 0);
const totalQualifiedApplicants = computed(() => props.dashboardData.totalQualifiedApplicants || 0);
const activeVacancies = computed(() => props.dashboardData.activeVacancies || 0);
const recentVacancies = computed(() => props.dashboardData.recentVacancies || []);
const recentApplications = computed(() => props.dashboardData.recentApplications || []);
const applicationStats = computed(() => props.dashboardData.applicationStats || {});
const monthlyStats = computed(() => props.dashboardData.monthlyStats || []);

// Chart configuration
const chartData = computed(() => ({
    labels: monthlyStats.value.map((stat: any) => dayjs(stat.month).format('MMM YYYY')),
    datasets: [
        {
            label: 'Lowongan Dibuat',
            data: monthlyStats.value.map((stat: any) => stat.vacancies),
            backgroundColor: 'rgba(59, 130, 246, 0.5)',
            borderColor: 'rgb(59, 130, 246)',
            borderWidth: 2,
            fill: true,
        },
        {
            label: 'Pelamar',
            data: monthlyStats.value.map((stat: any) => stat.applications),
            backgroundColor: 'rgba(34, 197, 94, 0.5)',
            borderColor: 'rgb(34, 197, 94)',
            borderWidth: 2,
            fill: true,
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top' as const,
        },
    },
    scales: {
        y: {
            beginAtZero: true,
        },
    },
};

// Helper functions
const getStatusSeverity = (status: string) => {
    switch (status) {
        case 'qualified':
            return 'success';
        case 'applied':
            return 'info';
        default:
            return 'secondary';
    }
};

const getStatusLabel = (status: string) => {
    switch (status) {
        case 'qualified':
            return 'Diterima';
        case 'applied':
            return 'Menunggu';
        default:
            return 'Tidak Diketahui';
    }
};

const formatDate = (date: string) => {
    return dayjs(date).format('DD MMM YYYY');
};

const formatDateTime = (date: string) => {
    return dayjs(date).format('DD MMM YYYY, HH:mm');
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Welcome Section -->
            <div class="rounded-lg bg-gradient-to-r from-blue-600 to-blue-800 p-6 text-white">
                <h1 class="mb-2 text-2xl font-bold">Selamat Datang, Mitra DU/DI!</h1>
                <p class="text-blue-100">Kelola lowongan kerja dan pantau pelamar dari satu dashboard</p>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Vacancies -->
                <Card class="border-l-4 border-blue-500">
                    <template #content>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="mb-1 text-sm text-gray-600">Total Lowongan</p>
                                <p class="text-2xl font-bold text-blue-600">{{ totalVacancies }}</p>
                            </div>
                            <div class="rounded-full bg-blue-100 p-3">
                                <i class="pi pi-briefcase text-xl text-blue-600"></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Active Vacancies -->
                <Card class="border-l-4 border-green-500">
                    <template #content>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="mb-1 text-sm text-gray-600">Lowongan Aktif</p>
                                <p class="text-2xl font-bold text-green-600">{{ activeVacancies }}</p>
                            </div>
                            <div class="rounded-full bg-green-100 p-3">
                                <i class="pi pi-check-circle text-xl text-green-600"></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Total Applicants -->
                <Card class="border-l-4 border-yellow-500">
                    <template #content>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="mb-1 text-sm text-gray-600">Total Pelamar</p>
                                <p class="text-2xl font-bold text-yellow-600">{{ totalApplicants }}</p>
                            </div>
                            <div class="rounded-full bg-yellow-100 p-3">
                                <i class="pi pi-users text-xl text-yellow-600"></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Qualified Applicants -->
                <Card class="border-l-4 border-purple-500">
                    <template #content>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="mb-1 text-sm text-gray-600">Pelamar Terseleksi</p>
                                <p class="text-2xl font-bold text-purple-600">{{ totalQualifiedApplicants }}</p>
                            </div>
                            <div class="rounded-full bg-purple-100 p-3">
                                <i class="pi pi-star text-xl text-purple-600"></i>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Charts and Quick Actions Row -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Application Statistics Chart -->
                <Card class="lg:col-span-2">
                    <template #title>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Statistik Bulanan</h3>
                            <Button icon="pi pi-refresh" text rounded size="small" />
                        </div>
                    </template>
                    <template #content>
                        <div class="h-80">
                            <Chart type="line" :data="chartData" :options="chartOptions" class="h-full" />
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Recent Activity Row -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Recent Vacancies -->
                <Card>
                    <template #title>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Lowongan Terbaru</h3>
                            <Button label="Lihat Semua" text size="small" @click="router.visit('/partners/vacancies')" />
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-4">
                            <div
                                v-for="vacancy in recentVacancies"
                                :key="vacancy.id"
                                class="flex cursor-pointer items-center justify-between rounded-lg bg-gray-50 p-3 transition-colors hover:bg-gray-100"
                                @click="router.visit(`/partners/vacancies/${vacancy.id}/edit`)"
                            >
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ vacancy.title }}</h4>
                                    <p class="text-sm text-gray-600">{{ vacancy.location }}</p>
                                    <p class="text-xs text-gray-500">{{ formatDate(vacancy.created_at) }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <Badge :value="`${vacancy.applicants_count} pelamar`" severity="info" />
                                    <i class="pi pi-chevron-right text-gray-400"></i>
                                </div>
                            </div>
                            <div v-if="recentVacancies.length === 0" class="py-8 text-center">
                                <i class="pi pi-briefcase mb-2 text-3xl text-gray-400"></i>
                                <p class="text-gray-500">Belum ada lowongan dibuat</p>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Recent Applications -->
                <Card>
                    <template #title>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Pelamar Terbaru</h3>
                            <Button label="Lihat Semua" text size="small" @click="router.visit('/partners/vacancies')" />
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-4">
                            <div
                                v-for="application in recentApplications"
                                :key="application.id"
                                class="flex cursor-pointer items-center justify-between rounded-lg bg-gray-50 p-3 transition-colors hover:bg-gray-100"
                                @click="router.visit(`/partners/vacancies/${application.vacancy.id}/applications`)"
                            >
                                <div class="flex items-center space-x-3">
                                    <Avatar :label="application.student.name.charAt(0)" class="bg-blue-500 text-white" size="small" />
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ application.student.name }}</h4>
                                        <p class="text-sm text-gray-600">{{ application.vacancy.title }}</p>
                                        <p class="text-xs text-gray-500">{{ formatDateTime(application.created_at) }}</p>
                                    </div>
                                </div>
                                <Tag :value="getStatusLabel(application.status)" :severity="getStatusSeverity(application.status)" size="small" />
                            </div>
                            <div v-if="recentApplications.length === 0" class="py-8 text-center">
                                <i class="pi pi-users mb-2 text-3xl text-gray-400"></i>
                                <p class="text-gray-500">Belum ada pelamar</p>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>

            <!-- Application Status Overview -->
            <Card>
                <template #title>
                    <h3 class="text-lg font-semibold">Status Aplikasi</h3>
                </template>
                <template #content>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="rounded-lg bg-yellow-50 p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ applicationStats.applied || 0 }}</div>
                            <div class="text-sm text-yellow-800">Menunggu Review</div>
                        </div>
                        <div class="rounded-lg bg-green-50 p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">{{ applicationStats.qualified || 0 }}</div>
                            <div class="text-sm text-green-800">Diterima</div>
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </AppLayout>
</template>
