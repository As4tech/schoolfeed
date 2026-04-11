<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kitchen_usage_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usage_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity', 12, 3);
            $table->timestamps();

            $table->foreign('usage_id')->references('id')->on('kitchen_usages')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('inventory_items')->onDelete('cascade');
            $table->unique(['usage_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kitchen_usage_items');
    }
};
