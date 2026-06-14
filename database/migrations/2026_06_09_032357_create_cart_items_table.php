<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')
                ->constrained('carts')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('menu_id')
                ->constrained('menus')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->integer('quantity');

            $table->unique(
                ['cart_id', 'menu_id'],
                'uq_cart_item'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
