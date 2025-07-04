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
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('partners')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('partners')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('title');
            $table->text('description');
            $table->date('due_at')->nullable();
            $table->string('location', 100);
            $table->string('file', 4096)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
