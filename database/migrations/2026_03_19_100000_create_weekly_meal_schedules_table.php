<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_meal_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('meal_id')->constrained()->onDelete('cascade');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
            $table->decimal('price', 8, 2);
            $table->date('week_start_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['school_id', 'meal_id', 'day_of_week', 'week_start_date'], 'weekly_schedule_unique');
            $table->index(['school_id', 'week_start_date']);
            $table->index(['day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_meal_schedules');
    }
};
