<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Avatar from 'primevue/avatar';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Chart from 'primevue/chart';
import Select from 'primevue/select';
import Tag from 'primevue/tag';

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
		href: '/dashboard',
	},
];

const page = usePage() as any;
const currentUserName = computed(() => page.props.auth.user?.name || page.props.auth.partner?.name || 'Staff BKK');

const totalAnnouncements = computed(() => props.dashboardData.totalAnnouncements || 0);
const announcementsThisMonth = computed(() => props.dashboardData.announcementsThisMonth || 0);
const totalQuestionnaires = computed(() => props.dashboardData.totalQuestionnaires || 0);
const activeQuestionnaires = computed(() => props.dashboardData.activeQuestionnaires || 0);
const expiredQuestionnaires = computed(() => props.dashboardData.expiredQuestionnaires || 0);
const questionnaireResponses = computed(() => props.dashboardData.questionnaireResponses || 0);
const totalStudents = computed(() => props.dashboardData.totalStudents || 0);
const graduatedStudents = computed(() => props.dashboardData.graduatedStudents || 0);
const tracerStudyCompletedStudents = computed(() => props.dashboardData.tracerStudyCompletedStudents || 0);
const pendingTracerStudents = computed(() => props.dashboardData.pendingTracerStudents || 0);
const totalPartners = computed(() => props.dashboardData.totalPartners || 0);
const recentAnnouncements = computed(() => props.dashboardData.recentAnnouncements || []);
const recentQuestionnaires = computed(() => props.dashboardData.recentQuestionnaires || []);
const recentTracerStudyStudents = computed(() => props.dashboardData.recentTracerStudyStudents || []);
const recentPartners = computed(() => props.dashboardData.recentPartners || []);
const monthlyStats = computed(() => props.dashboardData.monthlyStats || []);
const studentStatusStats = computed(() => props.dashboardData.studentStatusStats || {});
const selectedMonthlyRange = computed(() => props.dashboardData.monthlyRange || 7);

const monthlyRangeOptions = [
	{ label: '1 Bulan', value: 1 },
	{ label: '3 Bulan', value: 3 },
	{ label: '6 Bulan', value: 6 },
	{ label: '12 Bulan', value: 12 },
	{ label: '24 Bulan', value: 24 },
];

const monthlyActivityChartData = computed(() => ({
	labels: monthlyStats.value.map((stat: any) => dayjs(stat.month).format('MMM YYYY')),
	datasets: [
		{
			label: 'Pengumuman',
			data: monthlyStats.value.map((stat: any) => stat.announcements),
			borderColor: 'rgb(14, 165, 233)',
			backgroundColor: 'rgba(14, 165, 233, 0.18)',
			tension: 0.35,
			fill: true,
		},
		{
			label: 'Kuesioner',
			data: monthlyStats.value.map((stat: any) => stat.questionnaires),
			borderColor: 'rgb(16, 185, 129)',
			backgroundColor: 'rgba(16, 185, 129, 0.18)',
			tension: 0.35,
			fill: true,
		},
		{
			label: 'Respons',
			data: monthlyStats.value.map((stat: any) => stat.responses),
			borderColor: 'rgb(245, 158, 11)',
			backgroundColor: 'rgba(245, 158, 11, 0.18)',
			tension: 0.35,
			fill: true,
		},
		{
			label: 'Siswa Baru',
			data: monthlyStats.value.map((stat: any) => stat.students),
			borderColor: 'rgb(168, 85, 247)',
			backgroundColor: 'rgba(168, 85, 247, 0.18)',
			tension: 0.35,
			fill: true,
		},
		{
			label: 'Mitra Baru',
			data: monthlyStats.value.map((stat: any) => stat.partners),
			borderColor: 'rgb(239, 68, 68)',
			backgroundColor: 'rgba(239, 68, 68, 0.18)',
			tension: 0.35,
			fill: true,
		},
	],
}));

const studentStatusChartData = computed(() => ({
	labels: ['Lulus', 'Belum Lulus', 'Tracer Study Selesai', 'Tracer Study Belum'],
	datasets: [
		{
			label: 'Jumlah Siswa',
			data: [
				studentStatusStats.value.graduatedStudents || graduatedStudents.value,
				studentStatusStats.value.pendingGraduationStudents || Math.max(totalStudents.value - graduatedStudents.value, 0),
				studentStatusStats.value.tracerStudyCompletedStudents || tracerStudyCompletedStudents.value,
				studentStatusStats.value.pendingTracerStudents || pendingTracerStudents.value,
			],
			backgroundColor: ['rgba(16, 185, 129, 0.8)', 'rgba(245, 158, 11, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(239, 68, 68, 0.8)'],
			borderRadius: 12,
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
			ticks: {
				precision: 0,
			},
		},
	},
};

const formatDate = (date: string) => dayjs(date).format('DD MMM YYYY');

const formatDateTime = (date: string) => dayjs(date).format('DD MMM YYYY, HH:mm');

const quickNavigate = (path: string) => router.visit(path);

const updateMonthlyRange = (months: number) => {
	router.get('/dashboard', { months }, { preserveState: true, preserveScroll: true, replace: true });
};
</script>

<template>
	<Head title="Dashboard Staff" />

	<AppLayout :breadcrumbs="breadcrumbs">
		<div class="space-y-6">
			<div class="rounded-lg bg-gradient-to-r from-blue-600 to-blue-800 p-6 text-white">
				<h1 class="mb-2 text-2xl font-bold">Selamat Datang, {{ currentUserName }}!</h1>
				<p class="text-blue-100">
					Kelola pengumuman, kuisioner, siswa tracer study, dan mitra DU/DI dari satu dashboard.
				</p>
			</div>

			<div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
				<Card class="border-l-4 border-sky-500">
					<template #content>
						<div class="flex items-center justify-between">
							<div>
								<p class="text-sm text-slate-500">Total Pengumuman</p>
								<p class="text-2xl font-semibold text-sky-600">{{ totalAnnouncements }}</p>
								<p class="mt-1 text-xs text-slate-400">{{ announcementsThisMonth }} bulan ini</p>
							</div>
							<div class="rounded-full bg-sky-100 p-3 text-sky-600">
								<i class="pi pi-megaphone text-xl"></i>
							</div>
						</div>
					</template>
				</Card>

				<Card class="border-l-4 border-emerald-500">
					<template #content>
						<div class="flex items-center justify-between">
							<div>
								<p class="text-sm text-slate-500">Kuesioner Aktif</p>
								<p class="text-2xl font-semibold text-emerald-600">{{ activeQuestionnaires }}</p>
								<p class="mt-1 text-xs text-slate-400">{{ totalQuestionnaires }} total, {{ expiredQuestionnaires }} sudah kedaluwarsa</p>
							</div>
							<div class="rounded-full bg-emerald-100 p-3 text-emerald-600">
								<i class="pi pi-list-check text-xl"></i>
							</div>
						</div>
					</template>
				</Card>

				<Card class="border-l-4 border-violet-500">
					<template #content>
						<div class="flex items-center justify-between">
							<div>
								<p class="text-sm text-slate-500">Respons Kuesioner</p>
								<p class="text-2xl font-semibold text-violet-600">{{ questionnaireResponses }}</p>
								<p class="mt-1 text-xs text-slate-400">Total respons terkumpul</p>
							</div>
							<div class="rounded-full bg-violet-100 p-3 text-violet-600">
								<i class="pi pi-inbox text-xl"></i>
							</div>
						</div>
					</template>
				</Card>

				<Card class="border-l-4 border-amber-500">
					<template #content>
						<div class="flex items-center justify-between">
							<div>
								<p class="text-sm text-slate-500">Total Siswa</p>
								<p class="text-2xl font-semibold text-amber-600">{{ totalStudents }}</p>
								<p class="mt-1 text-xs text-slate-400">{{ graduatedStudents }} sudah lulus</p>
							</div>
							<div class="rounded-full bg-amber-100 p-3 text-amber-600">
								<i class="pi pi-users text-xl"></i>
							</div>
						</div>
					</template>
				</Card>

				<Card class="border-l-4 border-blue-500">
					<template #content>
						<div class="flex items-center justify-between">
							<div>
								<p class="text-sm text-slate-500">Tracer Study Selesai</p>
								<p class="text-2xl font-semibold text-blue-600">{{ tracerStudyCompletedStudents }}</p>
								<p class="mt-1 text-xs text-slate-400">{{ pendingTracerStudents }} belum mengisi</p>
							</div>
							<div class="rounded-full bg-blue-100 p-3 text-blue-600">
								<i class="pi pi-chart-bar text-xl"></i>
							</div>
						</div>
					</template>
				</Card>

				<Card class="border-l-4 border-rose-500">
					<template #content>
						<div class="flex items-center justify-between">
							<div>
								<p class="text-sm text-slate-500">Total Mitra</p>
								<p class="text-2xl font-semibold text-rose-600">{{ totalPartners }}</p>
								<p class="mt-1 text-xs text-slate-400">Data mitra aktif di sistem</p>
							</div>
							<div class="rounded-full bg-rose-100 p-3 text-rose-600">
								<i class="pi pi-briefcase text-xl"></i>
							</div>
						</div>
					</template>
				</Card>
			</div>

			<div class="grid gap-6 xl:grid-cols-5">
				<Card class="xl:col-span-3">
					<template #title>
						<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
							<h3 class="text-lg font-semibold">Aktivitas Bulanan</h3>
							<Select
								:modelValue="selectedMonthlyRange"
								:options="monthlyRangeOptions"
								optionLabel="label"
								optionValue="value"
								class="w-40"
								placeholder="Pilih rentang"
								@update:modelValue="updateMonthlyRange"
							/>
						</div>
					</template>
					<template #content>
						<div class="h-96">
							<Chart type="line" :data="monthlyActivityChartData" :options="chartOptions" class="h-full" />
						</div>
					</template>
				</Card>

				<Card class="xl:col-span-2">
					<template #title>
						<h3 class="text-lg font-semibold">Status Siswa & Tracer Study</h3>
					</template>
					<template #content>
						<div class="h-96">
							<Chart type="bar" :data="studentStatusChartData" :options="chartOptions" class="h-full" />
						</div>
					</template>
				</Card>
			</div>

			<div class="grid gap-6 xl:grid-cols-2">
				<Card>
					<template #title>
						<div class="flex items-center justify-between">
							<h3 class="text-lg font-semibold">Pengumuman Terbaru</h3>
							<Button label="Lihat Semua" text size="small" @click="quickNavigate('/announcements')" />
						</div>
					</template>
					<template #content>
						<div class="space-y-3">
							<div v-for="announcement in recentAnnouncements" :key="announcement.id" class="flex items-start justify-between rounded-xl bg-slate-50 p-3">
								<div class="flex gap-3 flex-1">
									<Avatar icon="pi pi-megaphone" class="bg-sky-100 text-sky-600 flex-shrink-0" shape="circle" />
									<div class="min-w-0 flex-1">
										<p class="font-medium text-slate-900 truncate">{{ announcement.title }}</p>
										<p class="text-sm text-slate-500">{{ announcement.created_by?.name }}</p>
										<p class="text-xs text-slate-400">{{ formatDateTime(announcement.created_at) }}</p>
									</div>
								</div>
								<Button
									icon="pi pi-pencil"
									rounded
									text
									severity="warn"
									size="small"
									class="flex-shrink-0"
									@click="quickNavigate(`/announcements/${announcement.id}/edit`)"
									v-tooltip.top="'Edit pengumuman'"
								/>
							</div>
							<div v-if="recentAnnouncements.length === 0" class="py-8 text-center text-slate-500">
								Belum ada pengumuman
							</div>
						</div>
					</template>
				</Card>

				<Card>
					<template #title>
						<div class="flex items-center justify-between">
							<h3 class="text-lg font-semibold">Kuesioner Terbaru</h3>
							<Button label="Lihat Semua" text size="small" @click="quickNavigate('/questionnaires')" />
						</div>
					</template>
					<template #content>
						<div class="space-y-3">
							<div v-for="questionnaire in recentQuestionnaires" :key="questionnaire.id" class="flex items-start justify-between rounded-xl bg-slate-50 p-3">
								<div class="flex min-w-0 flex-1 gap-3">
									<Avatar icon="pi pi-list-check" class="bg-emerald-100 text-emerald-600 flex-shrink-0" shape="circle" />
									<div class="min-w-0 flex-1">
									<p class="font-medium text-slate-900 truncate">{{ questionnaire.title }}</p>
									<p class="text-sm text-slate-500">{{ questionnaire.created_by?.name }}</p>
									<p class="text-xs text-slate-400">{{ questionnaire.responses_count }} respons</p>
									<p v-if="questionnaire.due_at" class="text-xs text-slate-400">Tenggat: {{ formatDate(questionnaire.due_at) }}</p>
									</div>
								</div>
								<div class="flex items-center gap-2 flex-shrink-0">
									<Button
										icon="pi pi-list"
										rounded
										text
										severity="info"
										size="small"
										@click="quickNavigate(`/questionnaires/${questionnaire.id}/responses`)"
										v-tooltip.top="'Lihat respons'"
									/>
									<Button
										icon="pi pi-pencil"
										rounded
										text
										severity="warn"
										size="small"
										@click="quickNavigate(`/questionnaires/${questionnaire.id}/edit`)"
										v-tooltip.top="'Edit kuisioner'"
									/>
								</div>
							</div>
							<div v-if="recentQuestionnaires.length === 0" class="py-8 text-center text-slate-500">
								Belum ada kuesioner
							</div>
						</div>
					</template>
				</Card>

				<Card>
					<template #title>
						<div class="flex items-center justify-between">
							<h3 class="text-lg font-semibold">Siswa Tracer Study Terbaru</h3>
							<Button label="Lihat Data" text size="small" @click="quickNavigate('/years')" />
						</div>
					</template>
					<template #content>
						<div class="space-y-3">
									<div v-for="student in recentTracerStudyStudents" :key="student.id" class="flex items-start justify-between rounded-xl bg-slate-50 p-3">
										<div class="flex min-w-0 flex-1 gap-3">
											<Avatar icon="pi pi-users" class="bg-blue-100 text-blue-600 flex-shrink-0" shape="circle" />
											<div class="min-w-0 flex-1">
									<p class="font-medium text-slate-900">{{ student.name }}</p>
									<p class="text-sm text-slate-500">{{ student.student_class?.name }} - {{ student.student_class?.year?.year }}</p>
									<p class="text-xs text-slate-400">NISN: {{ student.nisn }}</p>
											</div>
								</div>
								<div class="flex items-center gap-2 flex-shrink-0">
									<Tag :value="student.is_graduated ? 'Lulus' : 'Belum Lulus'" :severity="student.is_graduated ? 'success' : 'warning'" />
									<Button
										icon="pi pi-eye"
										rounded
										text
										severity="info"
										size="small"
										@click="quickNavigate(`/years/${student.student_class?.year_id}/student-classes/${student.student_class?.id}/students/${student.id}/tracer-study`)"
										v-tooltip.top="'Detail siswa'"
									/>
								</div>
							</div>
							<div v-if="recentTracerStudyStudents.length === 0" class="py-8 text-center text-slate-500">
								Belum ada siswa mengisi tracer study
							</div>
						</div>
					</template>
				</Card>

				<Card>
					<template #title>
						<div class="flex items-center justify-between">
							<h3 class="text-lg font-semibold">Mitra Terbaru</h3>
							<Button label="Lihat Semua" text size="small" @click="quickNavigate('/partners')" />
						</div>
					</template>
					<template #content>
						<div class="space-y-3">
									<div v-for="partner in recentPartners" :key="partner.id" class="flex items-start justify-between rounded-xl bg-slate-50 p-3">
										<div class="flex min-w-0 flex-1 gap-3">
											<Avatar icon="pi pi-briefcase" class="bg-rose-100 text-rose-600 flex-shrink-0" shape="circle" />
											<div class="min-w-0 flex-1">
									<p class="font-medium text-slate-900">{{ partner.name }}</p>
									<p class="text-sm text-slate-500">{{ partner.email }}</p>
									<p class="text-xs text-slate-400">{{ partner.phone }}</p>
									<p class="text-xs text-slate-400 mt-1 line-clamp-2">{{ partner.address }}</p>
											</div>
								</div>
							</div>
							<div v-if="recentPartners.length === 0" class="py-8 text-center text-slate-500">
								Belum ada data mitra
							</div>
						</div>
					</template>
				</Card>
			</div>
		</div>
	</AppLayout>
</template>
