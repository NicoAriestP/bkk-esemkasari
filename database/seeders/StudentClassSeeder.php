<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $majors = ['TKR', 'RPL', 'DKV', 'TL', 'AK', 'TP'];
        $grades = ['X', 'XI', 'XII'];
        $currentYear = Carbon::now()->year;
        $firstYear = 2020; // Tahun pertama di DatabaseSeeder

        $classes = [];

        foreach ($grades as $grade) {
            foreach ($majors as $major) {
                $yearOffset = match ($grade) {
                    'X' => 0,
                    'XI' => -1,
                    'XII' => -2,
                };

                $targetYear = $currentYear + $yearOffset;
                $yearId = ($targetYear - $firstYear) + 1;

                $classes[] = [
                    'year_id' => $yearId,
                    'name' => "{$grade} {$major}",
                ];
            }
        }

        DB::table('student_classes')->insert($classes);
    }
}
