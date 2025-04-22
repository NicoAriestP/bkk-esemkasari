<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Year;
use App\Models\StudentClass;
use App\Models\Partner;
use App\Models\Student;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear cache
        Artisan::call('cache:clear');

        User::create([
            'name' => 'Pak Sururi',
            'phone' => '081234567890',
            'email' => 'paksururi@gmail.com',
            'password' => Hash::make('password'),
            'is_leader' => true,
            'remember_token' => Str::random(10),
        ]);

        // Seed years from 2020 to the current year
        for ($initialYear = 2020; $initialYear <= date('Y'); $initialYear++) {
            Year::create([
                'year' => $initialYear,
            ]);
        }

        // Call the seeders
        $this->call([
            UserSeeder::class,
            PartnerSeeder::class,
            StudentClassSeeder::class,
            StudentSeeder::class,
            VacancySeeder::class,
            QuestionnaireSeeder::class,
            QuestionnaireQuestionSeeder::class,
            QuestionOptionSeeder::class,
            QuestionAnswerSeeder::class,
        ]);
    }
}
