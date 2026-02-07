<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('results', function (Blueprint $table) {
        $table->id();
        $table->foreignId('test_id')->constrained()->onDelete('cascade');

        // --- O'ZGARISH BOSHLANDI ---
        // Eski: $table->foreignId('student_id')... o'rniga:
        $table->string('student_name'); // O'quvchi ismi
        $table->string('phone');        // Telefon raqami
        // --- O'ZGARISH TUGADI ---

        $table->integer('correct_answers')->default(0);
        $table->integer('total_questions')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
