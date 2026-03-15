<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type GenericObject = Record<string, any>;

const props = defineProps({
	student: { type: Object, required: true },
	studentClass: { type: Object, required: true },
	studentYear: { type: Object, required: true },
	studentActivityAnswer: { type: Object, default: () => ({}) },
	detailActivityAnswer: { type: Object, default: () => ({}) },
	studentUniversityAnswer: { type: Object, default: () => ({}) },
	studentWorkingAnswer: { type: Object, default: () => ({}) },
	studentEntrepreneurAnswer: { type: Object, default: () => ({}) },
	feedbackAnswer: { type: Object, default: () => ({}) },
	tracerStudyOptions: { type: Object, default: () => ({}) },
});

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Detail Tracer Study', href: '#' }];

const parseAnswers = (prop: any) => {
	if (prop && prop.answers && typeof prop.answers === 'string') {
		try {
			return JSON.parse(prop.answers);
		} catch {
			return {};
		}
	}

	return prop?.answers || {};
};

const studentActivityData = computed(() => parseAnswers(props.studentActivityAnswer));
const detailActivityData = computed(() => parseAnswers(props.detailActivityAnswer));
const workingData = computed(() => parseAnswers(props.studentWorkingAnswer));
const universityData = computed(() => parseAnswers(props.studentUniversityAnswer));
const entrepreneurData = computed(() => parseAnswers(props.studentEntrepreneurAnswer));
const feedbackData = computed(() => parseAnswers(props.feedbackAnswer));

const activityMainLabels = computed<Record<string, string>>(
	() => (props.tracerStudyOptions as GenericObject)?.activityMain?.labels || {},
);
const pklDurationLabels = computed<Record<string, string>>(
	() => (props.tracerStudyOptions as GenericObject)?.pklDuration?.labels || {},
);
const qualityRatingLabels = computed<Record<string, string>>(
	() => (props.tracerStudyOptions as GenericObject)?.pklQuality?.labels || {},
);
const salaryLabels = computed<Record<string, string>>(
	() => (props.tracerStudyOptions as GenericObject)?.salary?.labels || {},
);
const jobChangeLabels = computed<Record<string, string>>(
	() => (props.tracerStudyOptions as GenericObject)?.jobChange?.labels || {},
);
const howFoundJobLabels = computed<Record<string, string>>(
	() => (props.tracerStudyOptions as GenericObject)?.howFoundJob?.labels || {},
);
const jobRelevanceLabels = computed<Record<string, string>>(
	() => (props.tracerStudyOptions as GenericObject)?.jobRelevance?.labels || {},
);
const educationLevelLabels = computed<Record<string, string>>(
	() => (props.tracerStudyOptions as GenericObject)?.educationLevel?.labels || {},
);
const fundingSourceLabels = computed<Record<string, string>>(
	() => (props.tracerStudyOptions as GenericObject)?.fundingSource?.labels || {},
);
const majorRelevanceLabels = computed<Record<string, string>>(
	() => (props.tracerStudyOptions as GenericObject)?.majorRelevance?.labels || {},
);
const businessScaleLabels = computed<Record<string, string>>(
	() => (props.tracerStudyOptions as GenericObject)?.businessScale?.labels || {},
);
const businessIncomeLabels = computed<Record<string, string>>(
	() => (props.tracerStudyOptions as GenericObject)?.businessIncome?.labels || {},
);
const smkReasonLabels = computed<Record<string, string>>(
	() => (props.tracerStudyOptions as GenericObject)?.smkReason?.labels || {},
);

const mainActivityLabel = computed(() => {
	const value = detailActivityData.value.mainActivity;
	return mapSingle(value, activityMainLabels.value);
});

const displayValue = (value: any) => {
	if (value === null || value === undefined || value === '') return '-';
	if (typeof value === 'boolean') return value ? 'Ya' : 'Tidak';
	return String(value);
};

const mapSingle = (value: any, dictionary: Record<string, string>) => {
	if (!value) return '-';
	return dictionary[String(value)] || String(value);
};

const mapList = (values: any, dictionary: Record<string, string>) => {
	if (!Array.isArray(values) || values.length === 0) return [];
	return values.map((value) => dictionary[String(value)] || String(value));
};

const mappedHowFoundJob = computed(() => {
	const list = mapList(workingData.value.howFoundFirstJob, howFoundJobLabels.value);

	if (workingData.value.otherJobSourceText) {
		return list.map((item) =>
			item === 'Lainnya' ? `Lainnya: ${workingData.value.otherJobSourceText}` : item,
		);
	}

	return list;
});

const mappedSmkReasons = computed(() => mapList(feedbackData.value.smkReasons, smkReasonLabels.value));
const mappedCertificates = computed(() => {
	if (!Array.isArray(feedbackData.value.certificates)) return [];

	return feedbackData.value.certificates
		.map((certificate: GenericObject) => certificate?.name)
		.filter((name: string) => !!name && String(name).trim().length > 0);
});
</script>

<template>
	<Head title="Detail Tracer Study" />

	<AppLayout :breadcrumbs="breadcrumbs">
		<div class="space-y-6">
			<section class="rounded-2xl border border-sky-100 bg-gradient-to-br from-sky-50 via-blue-50 to-indigo-100 p-6 shadow-sm sm:p-8">
				<div class="mb-4 inline-flex items-center gap-2 rounded-full bg-sky-600 px-4 py-2 text-sm font-medium text-white">
					<i class="pi pi-graduation-cap"></i>
					<span>Detail Respons Tracer Study</span>
				</div>

				<h1 class="mb-2 text-2xl font-bold text-slate-900 sm:text-3xl">{{ props.student?.name }}</h1>
				<p class="text-sm text-slate-600 sm:text-base">
					{{ props.studentClass?.name }} - Tahun Ajaran {{ props.studentYear?.year }}
				</p>

				<div class="mt-5 flex flex-wrap gap-2">
					<span class="rounded-full bg-white/80 px-3 py-1 text-xs font-medium text-slate-700 ring-1 ring-slate-200">NISN: {{ displayValue(props.student?.nisn) }}</span>
					<span class="rounded-full bg-white/80 px-3 py-1 text-xs font-medium text-slate-700 ring-1 ring-slate-200">Status Menikah: {{ props.student?.is_married ? 'Menikah' : 'Belum Menikah' }}</span>
					<span class="rounded-full bg-white/80 px-3 py-1 text-xs font-medium text-slate-700 ring-1 ring-slate-200">Aktivitas Utama: {{ mainActivityLabel }}</span>
				</div>
			</section>

			<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
				<h2 class="mb-4 text-lg font-semibold text-slate-900">Data Pribadi</h2>

				<div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2 lg:grid-cols-3">
					<div><p class="text-slate-500">Email</p><p class="font-medium text-slate-800">{{ displayValue(props.student?.email) }}</p></div>
					<div><p class="text-slate-500">No. HP</p><p class="font-medium text-slate-800">{{ displayValue(props.student?.phone) }}</p></div>
					<div><p class="text-slate-500">Kota/Kabupaten</p><p class="font-medium text-slate-800">{{ displayValue(props.student?.city) }}</p></div>
					<div><p class="text-slate-500">Provinsi</p><p class="font-medium text-slate-800">{{ displayValue(props.student?.province) }}</p></div>
					<div><p class="text-slate-500">Tinggi Badan</p><p class="font-medium text-slate-800">{{ displayValue(props.student?.height) }} cm</p></div>
					<div><p class="text-slate-500">Berat Badan</p><p class="font-medium text-slate-800">{{ displayValue(props.student?.weight) }} kg</p></div>
				</div>

				<div class="mt-4 border-t border-slate-100 pt-4 text-sm">
					<p class="mb-1 text-slate-500">Alamat</p>
					<p class="font-medium text-slate-800">{{ displayValue(props.student?.address) }}</p>
				</div>

				<div class="mt-4 border-t border-slate-100 pt-4 text-sm">
					<p class="mb-2 text-slate-500">CV</p>
					<a
						v-if="props.student?.cv_file_url"
						:href="props.student.cv_file_url"
						target="_blank"
						class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 font-medium text-blue-700 hover:bg-blue-100"
					>
						<i class="pi pi-file"></i>
						<span>Lihat CV</span>
					</a>
					<p v-else class="font-medium text-slate-700">Belum ada CV</p>
				</div>
			</section>

			<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
				<h2 class="mb-4 text-lg font-semibold text-slate-900">Aktivitas Lulusan</h2>

				<div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
					<div><p class="text-slate-500">Sedang Kuliah</p><p class="font-medium text-slate-800">{{ displayValue(studentActivityData.isStudying) }}</p></div>
					<div><p class="text-slate-500">Sedang Bekerja</p><p class="font-medium text-slate-800">{{ displayValue(studentActivityData.isWorking) }}</p></div>
				</div>

				<div class="mt-4 border-t border-slate-100 pt-4 text-sm">
					<p class="text-slate-500">Jenis Pekerjaan</p>
					<p class="font-medium text-slate-800">{{ displayValue(studentActivityData.workType) }}</p>
				</div>
			</section>

			<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
				<h2 class="mb-4 text-lg font-semibold text-slate-900">Detail Aktivitas Utama</h2>
				<p class="mb-4 text-sm text-slate-600">Aktivitas utama: <span class="font-semibold text-slate-800">{{ mainActivityLabel }}</span></p>

				<div v-if="detailActivityData.mainActivity === 'bekerja'" class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
					<div><p class="text-slate-500">Jam kerja per minggu</p><p class="font-medium text-slate-800">{{ displayValue(workingData.workingHours) }}</p></div>
					<div><p class="text-slate-500">Range gaji</p><p class="font-medium text-slate-800">{{ mapSingle(workingData.salaryRange, salaryLabels) }}</p></div>
					<div><p class="text-slate-500">Frekuensi ganti pekerjaan</p><p class="font-medium text-slate-800">{{ mapSingle(workingData.jobChangeFrequency, jobChangeLabels) }}</p></div>
					<div><p class="text-slate-500">Keselarasan pekerjaan</p><p class="font-medium text-slate-800">{{ mapSingle(workingData.jobRelevance, jobRelevanceLabels) }}</p></div>
					<div class="sm:col-span-2">
						<p class="mb-2 text-slate-500">Cara mendapat pekerjaan pertama</p>
						<div v-if="mappedHowFoundJob.length" class="flex flex-wrap gap-2">
							<span v-for="item in mappedHowFoundJob" :key="item" class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-200">{{ item }}</span>
						</div>
						<p v-else class="font-medium text-slate-700">-</p>
					</div>
				</div>

				<div v-else-if="detailActivityData.mainActivity === 'kuliah'" class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
					<div><p class="text-slate-500">Perguruan Tinggi</p><p class="font-medium text-slate-800">{{ displayValue(universityData.universityName) }}</p></div>
					<div><p class="text-slate-500">Program Studi</p><p class="font-medium text-slate-800">{{ displayValue(universityData.studyProgram) }}</p></div>
					<div><p class="text-slate-500">Jenjang Pendidikan</p><p class="font-medium text-slate-800">{{ mapSingle(universityData.educationLevel, educationLevelLabels) }}</p></div>
					<div><p class="text-slate-500">Sumber Pembiayaan</p><p class="font-medium text-slate-800">{{ mapSingle(universityData.fundingSource, fundingSourceLabels) }}</p></div>
					<div class="sm:col-span-2"><p class="text-slate-500">Kesesuaian Jurusan</p><p class="font-medium text-slate-800">{{ mapSingle(universityData.majorRelevance, majorRelevanceLabels) }}</p></div>
				</div>

				<div v-else-if="detailActivityData.mainActivity === 'wirausaha'" class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
					<div><p class="text-slate-500">Nama Usaha</p><p class="font-medium text-slate-800">{{ displayValue(entrepreneurData.businessName) }}</p></div>
					<div><p class="text-slate-500">Bidang Usaha</p><p class="font-medium text-slate-800">{{ displayValue(entrepreneurData.businessField) }}</p></div>
					<div><p class="text-slate-500">Skala Usaha</p><p class="font-medium text-slate-800">{{ mapSingle(entrepreneurData.businessScale, businessScaleLabels) }}</p></div>
					<div><p class="text-slate-500">Pendapatan Usaha</p><p class="font-medium text-slate-800">{{ mapSingle(entrepreneurData.businessIncome, businessIncomeLabels) }}</p></div>
				</div>

				<div v-else class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-600">
					Tidak ada detail aktivitas tambahan yang diisi.
				</div>
			</section>

			<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
				<h2 class="mb-4 text-lg font-semibold text-slate-900">Umpan Balik Pendidikan</h2>

				<div class="mb-4">
					<p class="mb-2 text-sm text-slate-500">Alasan memilih pendidikan di SMK</p>
					<div v-if="mappedSmkReasons.length" class="flex flex-wrap gap-2">
						<span v-for="reason in mappedSmkReasons" :key="reason" class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 ring-1 ring-blue-200">{{ reason }}</span>
					</div>
					<p v-else class="text-sm font-medium text-slate-700">-</p>
				</div>

				<div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
					<div><p class="text-slate-500">Durasi PKL</p><p class="font-medium text-slate-800">{{ mapSingle(feedbackData.pklDuration, pklDurationLabels) }}</p></div>
					<div><p class="text-slate-500">Sertifikat Kompetensi</p><p class="font-medium text-slate-800">{{ displayValue(feedbackData.hasCertificate) }}</p></div>
					<div><p class="text-slate-500">Kualitas Tempat PKL</p><p class="font-medium text-slate-800">{{ mapSingle(feedbackData.pklQuality?.location, qualityRatingLabels) }}</p></div>
					<div><p class="text-slate-500">Kesesuaian Tugas PKL</p><p class="font-medium text-slate-800">{{ mapSingle(feedbackData.pklQuality?.taskRelevance, qualityRatingLabels) }}</p></div>
					<div><p class="text-slate-500">Bimbingan Selama PKL</p><p class="font-medium text-slate-800">{{ mapSingle(feedbackData.pklQuality?.guidance, qualityRatingLabels) }}</p></div>
					<div><p class="text-slate-500">Monitoring Guru</p><p class="font-medium text-slate-800">{{ mapSingle(feedbackData.pklQuality?.monitoring, qualityRatingLabels) }}</p></div>
				</div>

				<div class="mt-4 border-t border-slate-100 pt-4">
					<p class="mb-2 text-sm text-slate-500">Daftar Sertifikat</p>
					<div v-if="mappedCertificates.length" class="flex flex-wrap gap-2">
						<span v-for="certificate in mappedCertificates" :key="certificate" class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 ring-1 ring-amber-200">{{ certificate }}</span>
					</div>
					<p v-else class="text-sm font-medium text-slate-700">-</p>
				</div>
			</section>
		</div>
	</AppLayout>
</template>
