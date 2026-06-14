<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('menu_id')
                ->constrained('menus')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->integer('quantity');

            $table->unique(
                ['order_id', 'menu_id'],
                'uq_order_item'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
