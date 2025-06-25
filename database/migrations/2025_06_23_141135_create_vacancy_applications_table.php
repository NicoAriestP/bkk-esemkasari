<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vacancy_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->nullable()
                ->constrained('students')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreignId('vacancy_id')
                ->nullable()
                ->constrained('vacancies')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->enum('status', ['applied', 'qualified', 'rejected']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancy_applications');
    }
};
