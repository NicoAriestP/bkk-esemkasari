<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Announcement;
use Carbon\Carbon;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $announcements = [
            [
                'title' => 'Pembukaan Pendaftaran BKK Angkatan 2024',
                'content' => '<p>Dibuka pendaftaran untuk siswa angkatan 2024 yang ingin bergabung dengan <strong>BKK Esemkasari</strong>.</p><p>Daftarkan diri Anda segera! Jangan lewatkan kesempatan ini.</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Pelatihan Wawancara Kerja',
                'content' => '<p>BKK Esemkasari akan mengadakan pelatihan wawancara kerja pada tanggal <em>15 Juni 2024</em>.</p><p>Daftarkan diri Anda di <u>ruang BKK</u> untuk meningkatkan keterampilan wawancara Anda.</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Lowongan Kerja PT. Maju Jaya Abadi',
                'content' => '<p>Dibutuhkan <strong>10 tenaga kerja</strong> untuk posisi <span>Operator Produksi</span> di PT. Maju Jaya Abadi.</p><p>Segera daftarkan diri Anda dan raih karir impian!</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Seminar Kiat Sukses Berkarir di Dunia Kerja',
                'content' => '<p>Ikuti seminar inspiratif bersama praktisi HRD ternama pada tanggal <strong>20 Juni 2024</strong>.</p><ul><li>Gratis untuk anggota BKK!</li><li>Dapatkan wawasan berharga.</li></ul>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Pendaftaran Magang Tahun 2024',
                'content' => '<p>Buka pendaftaran program magang untuk siswa kelas XI. Dapatkan <em>pengalaman kerja nyata</em> di perusahaan mitra BKK Esemkasari.</p><p>Jangan tunda lagi, kuota terbatas!</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Info Beasiswa Pendidikan Lanjut',
                'content' => '<p>Tersedia beasiswa untuk lulusan yang ingin melanjutkan pendidikan ke jenjang yang lebih tinggi.</p><p>Segera <u>hubungi petugas BKK</u> untuk informasi lebih lanjut dan persyaratan.</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Pelatihan Pembuatan CV dan Surat Lamaran',
                'content' => '<p>Pelatihan akan diadakan pada tanggal <strong>25 Juni 2024</strong>.</p><p>Dapatkan tips membuat CV dan surat lamaran yang <em>menarik dan efektif</em>!</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Lowongan Kerja di PT. Sejahtera Mandiri',
                'content' => '<p>Dibutuhkan <strong>15 tenaga kerja</strong> untuk posisi <span>Admin</span> dan <span>Staff Gudang</span>.</p><p>Segera daftarkan diri Anda di BKK sebelum batas waktu pendaftaran!</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Info Rekrutmen Bersama Perusahaan Mitra',
                'content' => '<p>Akan diadakan rekrutmen bersama oleh <strong>5 perusahaan mitra BKK</strong> pada tanggal <em>30 Juni 2024</em>.</p><p>Persiapkan diri Anda sebaik mungkin untuk kesempatan ini!</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'title' => 'Pembekalan Prakerin 2024',
                'content' => '<p>Bagi siswa kelas XI yang akan melaksanakan Prakerin, diwajibkan mengikuti <u>pembekalan</u> pada tanggal <strong>5 Juli 2024</strong>.</p><p>Kehadiran Anda sangat penting.</p>',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }
    }
}
