<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity', 12, 3);
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->string('supplier')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('inventory_items')->onDelete('cascade');
            $table->index(['item_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ins');
    }
};
