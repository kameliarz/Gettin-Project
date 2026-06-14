<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('canteen_id')
                ->constrained('canteens')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->unique(
                ['customer_id', 'canteen_id'],
                'uq_customer_canteen_cart'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
