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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('student_id')->unique();
            $table->string('grade');
            $table->foreignId('parent_id')->nullable()->constrained('guardians')->onDelete('set null');
            $table->enum('status', ['enrolled', 'graduated', 'withdrawn', 'suspended'])->default('enrolled');
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medical_notes')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->timestamps();
            
            $table->index(['school_id', 'status']);
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
