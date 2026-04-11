<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meal_recipes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('meal_name');
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity_per_student', 12, 4); // e.g., kg per student
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('inventory_items')->onDelete('cascade');
            $table->unique(['school_id', 'meal_name', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_recipes');
    }
};
