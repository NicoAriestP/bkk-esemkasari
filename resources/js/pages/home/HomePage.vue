<template>
    <Head title="Home" />
    <div class="home-page">
        <!-- Navigation -->
        <nav class="fixed top-0 right-0 left-0 z-50 bg-white shadow-md">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center">
                        <img src="/img/logo-smk.png" alt="SMK Negeri Purwosari" class="mr-3 h-10 w-10" />
                        <div>
                            <h1 class="text-lg font-bold text-blue-900">BKK EduSurya</h1>
                            <p class="text-sm text-gray-600">SMK Negeri Purwosari Bojonegoro</p>
                        </div>
                    </div>
                    <div class="hidden space-x-6 md:flex">
                        <a href="#home" class="font-medium text-blue-900 hover:text-blue-700">Beranda</a>
                        <a href="#about" class="font-medium text-blue-900 hover:text-blue-700">Tentang</a>
                        <a href="#programs" class="font-medium text-blue-900 hover:text-blue-700">Program Keahlian</a>
                        <a href="#facilities" class="font-medium text-blue-900 hover:text-blue-700">Fasilitas</a>
                        <a href="#news" class="font-medium text-blue-900 hover:text-blue-700">Berita</a>
                        <a href="#contact" class="font-medium text-blue-900 hover:text-blue-700">Kontak</a>
                    </div>
                    <div class="flex space-x-2">
                        <Button v-if="!$page.props.auth.user" @click="$inertia.visit('/login')" size="small" outlined> Masuk </Button>
                        <Button v-if="$page.props.auth.user" @click="$inertia.visit('/dashboard')" size="small"> Dashboard </Button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section id="home" class="bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 pt-20 text-white">
            <div class="container mx-auto px-4 py-16">
                <div class="grid items-center gap-12 md:grid-cols-2">
                    <div>
                        <h1 class="mb-6 text-4xl font-bold md:text-6xl">
                            Selamat Datang di
                            <span class="text-yellow-400">BKK EduSurya</span>
                        </h1>
                        <p class="mb-8 text-xl text-blue-100">
                            Sistem Informasi Bursa Kerja Khusus SMK Negeri Purwosari Bojonegoro - Menghubungkan lulusan terbaik dengan peluang karir
                            yang cemerlang.
                        </p>
                        <div class="flex flex-col gap-4 sm:flex-row">
                            <Button
                                @click="$inertia.visit('/announcements')"
                                size="large"
                                class="bg-yellow-500 font-semibold text-blue-900 hover:bg-yellow-600"
                            >
                                <i class="pi pi-megaphone mr-2"></i>
                                Lihat Pengumuman
                            </Button>
                            <Button
                                @click="$inertia.visit('/students/vacancies')"
                                size="large"
                                outlined
                                class="border-white text-white hover:bg-white hover:text-blue-900 dark:hover:bg-gray-800 dark:hover:text-white"
                            >
                                <i class="pi pi-briefcase mr-2"></i>
                                Cari Lowongan Kerja
                            </Button>
                        </div>
                    </div>
                    <div class="relative">
                        <Carousel
                            :value="heroImages"
                            :showThumbnails="false"
                            :showIndicators="true"
                            :autoplayInterval="4000"
                            class="overflow-hidden rounded-lg shadow-2xl"
                        >
                            <template #item="{ data }">
                                <img :src="data.image" :alt="data.title" class="h-80 w-full object-cover" />
                            </template>
                        </Carousel>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="bg-white py-16">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                    <div class="text-center">
                        <div class="mb-2 text-3xl font-bold text-blue-900 md:text-4xl">{{ stats.students }}+</div>
                        <div class="text-gray-600">Siswa Aktif</div>
                    </div>
                    <div class="text-center">
                        <div class="mb-2 text-3xl font-bold text-blue-900 md:text-4xl">{{ stats.graduates }}+</div>
                        <div class="text-gray-600">Alumni</div>
                    </div>
                    <div class="text-center">
                        <div class="mb-2 text-3xl font-bold text-blue-900 md:text-4xl">{{ stats.partners }}+</div>
                        <div class="text-gray-600">Mitra Industri</div>
                    </div>
                    <div class="text-center">
                        <div class="mb-2 text-3xl font-bold text-blue-900 md:text-4xl">{{ stats.placements }}%</div>
                        <div class="text-gray-600">Tingkat Penempatan</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="bg-gray-50 py-16">
            <div class="container mx-auto px-4">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-3xl font-bold text-blue-900 md:text-4xl">Tentang SMK Negeri Purwosari</h2>
                    <p class="mx-auto max-w-3xl text-xl text-gray-600">
                        SMK Negeri Purwosari Bojonegoro merupakan sekolah menengah kejuruan yang telah Terakreditasi A dan merupakan Sekolah Pusat
                        Keunggulan, serta menjadi "SMK Negeri Favorit" di wilayah Bojonegoro bagian barat.
                    </p>
                </div>

                <div class="grid items-center gap-12 md:grid-cols-2">
                    <div>
                        <img src="/img/about_image.jpeg" alt="SMK Negeri Purwosari" class="w-full rounded-lg shadow-lg" />
                    </div>
                    <div>
                        <div class="mb-8">
                            <h3 class="mb-4 text-2xl font-bold text-blue-900">Visi</h3>
                            <p class="text-lg text-gray-700">Menghasilkan Sumber Daya Manusia Yang Unggul Berkarakter</p>
                        </div>

                        <div>
                            <h3 class="mb-4 text-2xl font-bold text-blue-900">Misi</h3>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <i class="pi pi-check mt-1 mr-3 text-green-600"></i>
                                    Meningkatkan keimanan dan ketaqwaan terhadap Tuhan Yang Maha Esa
                                </li>
                                <li class="flex items-start">
                                    <i class="pi pi-check mt-1 mr-3 text-green-600"></i>
                                    Membudayakan warga sekolah yang jujur, bertanggung jawab, disiplin, dan peduli lingkungan
                                </li>
                                <li class="flex items-start">
                                    <i class="pi pi-check mt-1 mr-3 text-green-600"></i>
                                    Meningkatkan kompetensi guru dan tenaga kependidikan
                                </li>
                                <li class="flex items-start">
                                    <i class="pi pi-check mt-1 mr-3 text-green-600"></i>
                                    Meningkatkan kompetensi peserta didik, di bidang Akademik dan Nonakademik
                                </li>
                                <li class="flex items-start">
                                    <i class="pi pi-check mt-1 mr-3 text-green-600"></i>
                                    Menjalin kerjasama dengan Dunia Usaha dan Dunia Industri yang relevan
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Programs Section -->
        <section id="programs" class="bg-white py-16">
            <div class="container mx-auto px-4">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-3xl font-bold text-blue-900 md:text-4xl">Program Keahlian</h2>
                    <p class="text-xl text-gray-600">6 Jurusan Unggulan dengan Fasilitas Modern</p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <Card v-for="program in programs" :key="program.id" class="transition-shadow hover:shadow-lg">
                        <template #header>
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
                                <i :class="program.icon" class="mb-4 block text-4xl"></i>
                                <h3 class="text-xl font-bold">{{ program.name }}</h3>
                                <p class="text-blue-100">{{ program.abbreviation }}</p>
                            </div>
                        </template>
                        <template #content>
                            <p class="mb-4 text-gray-600 dark:text-gray-400">{{ program.description }}</p>
                            <div class="mb-4">
                                <h4 class="mb-2 font-semibold text-blue-900 dark:text-blue-400">Prospek Karir:</h4>
                                <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    <li v-for="career in program.careers" :key="career" class="flex items-center">
                                        <i class="pi pi-circle-fill mr-2 text-xs text-blue-500"></i>
                                        {{ career }}
                                    </li>
                                </ul>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </section>

        <!-- Facilities Section -->
        <section id="facilities" class="bg-gray-50 py-16">
            <div class="container mx-auto px-4">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-3xl font-bold text-blue-900 md:text-4xl">Fasilitas Unggulan</h2>
                    <p class="text-xl text-gray-600">Menunjang Pembelajaran dan Pengembangan Kompetensi</p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="facility in facilities"
                        :key="facility.id"
                        class="rounded-lg bg-white p-6 text-center shadow transition-shadow hover:shadow-lg"
                    >
                        <i :class="facility.icon" class="mb-4 text-4xl text-blue-600"></i>
                        <h3 class="mb-2 text-lg font-semibold text-blue-900">{{ facility.name }}</h3>
                        <p class="text-sm text-gray-600">{{ facility.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- News Section -->
        <!-- <section id="news" class="bg-white py-16">
            <div class="container mx-auto px-4">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-3xl font-bold text-blue-900 md:text-4xl">Berita Terbaru</h2>
                    <p class="text-xl text-gray-600">Kabar Terkini dari SMK Negeri Purwosari</p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <Card v-for="news in latestNews" :key="news.id" class="transition-shadow hover:shadow-lg">
                        <template #header>
                            <img :src="news.image" :alt="news.title" class="h-48 w-full object-cover" />
                        </template>
                        <template #content>
                            <div class="p-6">
                                <div class="mb-3 flex items-center text-sm text-gray-500">
                                    <i class="pi pi-calendar mr-2"></i>
                                    {{ news.date }}
                                </div>
                                <h3 class="mb-3 line-clamp-2 text-lg font-semibold text-blue-900">{{ news.title }}</h3>
                                <p class="mb-4 line-clamp-3 text-sm text-gray-600">{{ news.excerpt }}</p>
                                <Button @click="openNews(news.link)" size="small" outlined class="w-full"> Baca Selengkapnya </Button>
                            </div>
                        </template>
                    </Card>
                </div>

                <div class="mt-12 text-center">
                    <Button @click="$inertia.visit('/announcements')" size="large">
                        <i class="pi pi-list mr-2"></i>
                        Lihat Semua Berita
                    </Button>
                </div>
            </div>
        </section> -->

        <!-- Contact Section -->
        <section id="contact" class="bg-blue-900 py-16 text-white">
            <div class="container mx-auto px-4">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-3xl font-bold md:text-4xl">Hubungi Kami</h2>
                    <p class="text-xl text-blue-100">Dapatkan Informasi Lebih Lanjut</p>
                </div>

                <div class="grid gap-12 lg:grid-cols-2">
                    <!-- Google Maps -->
                    <div class="overflow-hidden rounded-lg bg-white shadow-2xl">
                        <div class="bg-gray-100 px-6 py-4">
                            <h3 class="flex items-center text-lg font-semibold text-gray-800">
                                <i class="pi pi-map mr-2 text-blue-600"></i>
                                Lokasi SMK Negeri Purwosari
                            </h3>
                        </div>
                        <div class="relative">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3958.4463192060407!2d111.67422287581617!3d-7.189805870570574!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e77818b50248895%3A0xc21b8bedece79edb!2sSMK%20Negeri%20Purwosari!5e0!3m2!1sid!2sid!4v1759824568087!5m2!1sid!2sid"
                                width="100%"
                                height="400"
                                style="border: 0"
                                allowfullscreen
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                class="w-full"
                            ></iframe>
                            <div class="absolute right-4 bottom-4">
                                <a
                                    href="https://maps.google.com/?q=SMK+Negeri+Purwosari+Bojonegoro+Jl+Ngambon+Pojok+Purwosari"
                                    target="_blank"
                                    class="maps-button flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-lg transition-colors hover:bg-blue-700"
                                >
                                    <i class="pi pi-external-link mr-2"></i>
                                    Buka di Google Maps
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Contact Information -->
                    <div class="grid gap-8 md:grid-cols-2">
                        <div class="text-center">
                            <i class="pi pi-map-marker mb-4 text-3xl text-yellow-400"></i>
                            <h3 class="mb-2 text-lg font-semibold">Alamat</h3>
                            <p class="text-sm text-blue-100">
                                Jl. Ngambon No.Km. 1.5<br />
                                Pojok, Purwosari<br />
                                Kabupaten Bojonegoro<br />
                                Jawa Timur 62161
                            </p>
                        </div>
                        <div class="text-center">
                            <i class="pi pi-phone mb-4 text-3xl text-yellow-400"></i>
                            <h3 class="mb-2 text-lg font-semibold">Telepon</h3>
                            <p class="text-blue-100">
                                <a href="tel:03535215446" class="transition-colors hover:text-yellow-400"> (0353) 5215446 </a>
                            </p>
                        </div>
                        <div class="text-center">
                            <i class="pi pi-envelope mb-4 text-3xl text-yellow-400"></i>
                            <h3 class="mb-2 text-lg font-semibold">Email</h3>
                            <p class="text-blue-100">
                                <a href="mailto:admin@smknegeripurwosaribjn.sch.id" class="transition-colors hover:text-yellow-400">
                                    admin@smknegeripurwosaribjn.sch.id
                                </a>
                            </p>
                        </div>
                        <div class="text-center">
                            <i class="pi pi-globe mb-4 text-3xl text-yellow-400"></i>
                            <h3 class="mb-2 text-lg font-semibold">Media Sosial</h3>
                            <div class="flex justify-center space-x-4">
                                <a
                                    href="https://www.facebook.com/SMK-Negeri-Purwosari-Bojonegoro-105372411942239"
                                    target="_blank"
                                    class="text-blue-100 transition-colors hover:text-yellow-400"
                                >
                                    <i class="pi pi-facebook text-xl"></i>
                                </a>
                                <a
                                    href="https://www.instagram.com/smkn_purwosari/"
                                    target="_blank"
                                    class="text-blue-100 transition-colors hover:text-yellow-400"
                                >
                                    <i class="pi pi-instagram text-xl"></i>
                                </a>
                                <a
                                    href="https://www.youtube.com/channel/UCc0mMPRw6sMiKy_gpDSP20g"
                                    target="_blank"
                                    class="text-blue-100 transition-colors hover:text-yellow-400"
                                >
                                    <i class="pi pi-youtube text-xl"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Footer -->
        <footer class="bg-gray-900 py-8 text-gray-300">
            <div class="container mx-auto px-4">
                <div class="grid gap-8 md:grid-cols-3">
                    <div>
                        <div class="mb-4 flex items-center">
                            <img src="/img/logo-smk.png" alt="SMK Negeri Purwosari" class="mr-3 h-8 w-8" />
                            <div>
                                <h3 class="font-bold text-white">BKK EduSurya</h3>
                                <p class="text-sm">SMK Negeri Purwosari Bojonegoro</p>
                            </div>
                        </div>
                        <p class="text-sm">
                            Sistem Informasi Bursa Kerja Khusus yang menghubungkan lulusan SMK Negeri Purwosari dengan peluang karir terbaik di dunia
                            industri.
                        </p>
                    </div>
                    <div>
                        <h3 class="mb-4 font-bold text-white">Link Cepat</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#about" class="transition-colors hover:text-white">Tentang Sekolah</a></li>
                            <li><a href="#programs" class="transition-colors hover:text-white">Program Keahlian</a></li>
                            <li><a href="/announcements" class="transition-colors hover:text-white">Pengumuman</a></li>
                            <li><a href="/vacancies" class="transition-colors hover:text-white">Lowongan Kerja</a></li>
                            <li><a href="/tracer-study" class="transition-colors hover:text-white">Tracer Study</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="mb-4 font-bold text-white">Kontak</h3>
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-center">
                                <i class="pi pi-map-marker mr-2 text-yellow-400"></i>
                                Jl. Ngambon No.Km. 1.5, Pojok, Purwosari
                            </li>
                            <li class="flex items-center">
                                <i class="pi pi-phone mr-2 text-yellow-400"></i>
                                (0353) 5215446
                            </li>
                            <li class="flex items-center">
                                <i class="pi pi-envelope mr-2 text-yellow-400"></i>
                                admin@smknegeripurwosaribjn.sch.id
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 border-t border-gray-700 pt-8 text-center text-sm">
                    <p>&copy; 2025 BKK EduSurya - SMK Negeri Purwosari Bojonegoro. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Carousel from 'primevue/carousel';
import { onMounted, onUnmounted, ref } from 'vue';

// Define page title
defineOptions({
    layout: null,
});

// Hero images carousel
const heroImages = ref([
    {
        image: '/img/img1.jpg',
        title: 'SMK Negeri Purwosari',
    },
    {
        image: '/img/img2.jpg',
        title: 'Fasilitas Modern',
    },
    {
        image: '/img/img3.jpg',
        title: 'Prestasi Siswa',
    },
]);

// Statistics data
const stats = ref({
    students: 1200,
    graduates: 2500,
    partners: 85,
    placements: 92,
});

// Programs data
const programs = ref([
    {
        id: 1,
        name: 'Rekayasa Perangkat Lunak',
        abbreviation: 'RPL',
        icon: 'pi pi-code',
        description: 'Mempelajari pengembangan aplikasi, web development, dan mobile programming untuk memenuhi kebutuhan teknologi informasi.',
        careers: ['Web Developer', 'Mobile App Developer', 'Software Engineer', 'System Analyst', 'Database Administrator'],
    },
    {
        id: 2,
        name: 'Teknik Kendaraan Ringan',
        abbreviation: 'TKR',
        icon: 'pi pi-car',
        description: 'Fokus pada perawatan, perbaikan, dan modifikasi kendaraan bermotor roda empat dengan teknologi terkini.',
        careers: ['Mekanik Otomotif', 'Service Advisor', 'Quality Control', 'Supervisor Bengkel', 'Teknisi Spare Part'],
    },
    {
        id: 3,
        name: 'Teknik Pemesinan',
        abbreviation: 'TPm',
        icon: 'pi pi-cog',
        description: 'Menguasai teknik manufaktur, pengoperasian mesin perkakas, dan pembuatan komponen presisi.',
        careers: ['Operator Mesin CNC', 'Quality Inspector', 'Machinist', 'Tool Maker', 'Production Supervisor'],
    },
    {
        id: 4,
        name: 'Akuntansi',
        abbreviation: 'AK',
        icon: 'pi pi-calculator',
        description: 'Mempelajari pencatatan keuangan, analisis laporan keuangan, dan manajemen administrasi bisnis.',
        careers: ['Staff Accounting', 'Admin Keuangan', 'Kasir', 'Tax Consultant', 'Finance Analyst'],
    },
    {
        id: 5,
        name: 'Desain Komunikasi Visual',
        abbreviation: 'DKV',
        icon: 'pi pi-palette',
        description: 'Mengembangkan kreativitas dalam desain grafis, multimedia, dan komunikasi visual untuk berbagai media.',
        careers: ['Graphic Designer', 'UI/UX Designer', 'Content Creator', 'Brand Designer', 'Digital Marketing'],
    },
    {
        id: 6,
        name: 'Teknik Las',
        abbreviation: 'TL',
        icon: 'pi pi-wrench',
        description: 'Menguasai berbagai teknik pengelasan dan fabrikasi logam untuk industri konstruksi dan manufaktur.',
        careers: ['Welder Professional', 'Welding Inspector', 'Fabricator', 'Supervisor Las', 'Quality Control Welding'],
    },
]);

// Facilities data
const facilities = ref([
    {
        id: 1,
        name: 'Lab Komputer',
        icon: 'pi pi-desktop',
        description: 'Laboratorium komputer modern dengan perangkat terkini untuk pembelajaran programming dan desain',
    },
    {
        id: 2,
        name: 'Workshop Otomotif',
        icon: 'pi pi-car',
        description: 'Bengkel otomotif lengkap dengan peralatan diagnosa dan perbaikan kendaraan modern',
    },
    {
        id: 3,
        name: 'Ruang Praktik Akuntansi',
        icon: 'pi pi-chart-line',
        description: 'Ruang praktik dengan software akuntansi professional dan simulasi bisnis',
    },
    {
        id: 4,
        name: 'Studio Desain',
        icon: 'pi pi-image',
        description: 'Studio desain grafis dan multimedia dengan perangkat editing professional',
    },
    {
        id: 5,
        name: 'Workshop Las',
        icon: 'pi pi-bolt',
        description: 'Workshop pengelasan dengan berbagai jenis mesin las dan peralatan safety standard industri',
    },
    {
        id: 6,
        name: 'Perpustakaan Digital',
        icon: 'pi pi-book',
        description: 'Perpustakaan modern dengan koleksi digital dan akses internet untuk research',
    },
    {
        id: 7,
        name: 'Aula Serbaguna',
        icon: 'pi pi-building',
        description: 'Aula untuk kegiatan seminar, workshop, dan acara sekolah dengan kapasitas besar',
    },
    {
        id: 8,
        name: 'Masjid',
        icon: 'pi pi-home',
        description: 'Masjid untuk kegiatan ibadah dan pembinaan spiritual siswa',
    },
]);

// Latest news data
const latestNews = ref([
    {
        id: 1,
        title: 'PENGUMUMAN KELULUSAN SISWA KELAS XII SMK NEGERI PURWOSARI BOJONEGORO TAHUN 2024/2025',
        excerpt: 'Pengumuman Kelulusan Siswa Kelas XII SMKN Purwosari Bojonegoro tahun Pelajaran 2024/2025',
        date: '5 Mei 2025',
        image: '/img/news-1.jpg',
        link: 'https://www.smknegeripurwosaribjn.sch.id/pengumuman-kelulusan-siswa-kelas-xii-smk-negeri-purwosari-bojonegoro-tahun-2024-2025/',
    },
    {
        id: 2,
        title: 'LKS (Lomba Kompetensi Siswa) Tahun 2025 – Prestasi Tidak Datang dengan Sendirinya',
        excerpt: 'SMK Negeri Purwosari - Bojonegoro kembali menorehkan kebanggaan dalam kompetisi LKS tingkat regional',
        date: '17 Feb 2025',
        image: '/img/news-2.jpg',
        link: 'https://www.smknegeripurwosaribjn.sch.id/lomba-komptensi-siswa-lks-tahun-2025-smkn-purwosari-bojonegoro/',
    },
    {
        id: 3,
        title: "SMK Negeri Purwosari Gelar Peringatan Isra Mi'raj dengan Khidmat dan Penuh Makna",
        excerpt: "Suasana penuh khidmat menyelimuti Masjid Utama SMKN Purwosari dalam peringatan Isra Mi'raj",
        date: '31 Jan 2025',
        image: '/img/news-3.jpg',
        link: 'https://www.smknegeripurwosaribjn.sch.id/smk-negeri-purwosari-bojonegoro-gelar-peringatan-isra-miraj-dengan-khidmat-dan-penuh-makna/',
    },
]);

// Methods
const openNews = (link: string) => {
    window.open(link, '_blank');
};

// Smooth scrolling for anchor links
const handleAnchorClick = (event: Event) => {
    const target = event.target as HTMLAnchorElement;
    if (target.hash) {
        event.preventDefault();
        const element = document.querySelector(target.hash);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }
};

onMounted(() => {
    // Add smooth scrolling to all anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach((link) => {
        link.addEventListener('click', handleAnchorClick);
    });
});

onUnmounted(() => {
    // Clean up event listeners
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach((link) => {
        link.removeEventListener('click', handleAnchorClick);
    });
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Navigation scroll effect */
nav {
    transition: all 0.3s ease;
}

/* Google Maps responsive styling */
.google-maps-container {
    position: relative;
    width: 100%;
    height: 400px;
    overflow: hidden;
    border-radius: 0.5rem;
}

.google-maps-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

/* Hover effects */
.hover-scale:hover {
    transform: scale(1.05);
    transition: transform 0.3s ease;
}

/* Maps button hover effect */
.maps-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}
</style>
