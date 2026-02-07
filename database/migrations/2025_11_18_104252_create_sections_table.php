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
            Schema::create('sections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('test_id')->constrained('tests')->onDelete('cascade');
                $table->string('name'); // Masalan: Frontend, Mantiqiy
                $table->integer('total_questions')->default(0);
                $table->integer('questions_to_ask')->default(10); // Nechta savol tushishi kerak
                $table->timestamps();
            });
        }        
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
