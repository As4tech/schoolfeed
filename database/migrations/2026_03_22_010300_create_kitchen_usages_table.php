<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kitchen_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->unsignedInteger('students_fed')->default(0);
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unique(['school_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kitchen_usages');
    }
};
