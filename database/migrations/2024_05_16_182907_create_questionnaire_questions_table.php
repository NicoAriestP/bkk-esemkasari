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
        Schema::create('questionnaire_questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->foreignId('questionnaire_id')
                ->nullable()
                ->constrained('questionnaires')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->string('question_title');
            $table->string('type', 20);
            $table->text('notes')->nullable();
            // $table->tinyInteger('is_multiple_answers')->default(0); // Dicomment dulu karena tidak efektif secara code convention untuk handle logic pilihan pertanyaan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_questions');
    }
};
