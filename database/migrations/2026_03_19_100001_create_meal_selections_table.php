<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('weekly_meal_schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('guardians')->onDelete('set null');
            $table->date('meal_date');
            $table->decimal('price', 8, 2);
            $table->enum('status', ['selected', 'paid', 'cancelled'])->default('selected');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'weekly_meal_schedule_id'], 'student_weekly_meal_unique');
            $table->index(['student_id', 'meal_date']);
            $table->index(['parent_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_selections');
    }
};
