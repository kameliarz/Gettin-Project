<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('canteen_id')
                ->constrained('canteens')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('canteen_pickup_slot_id')
                ->constrained('canteen_pickup_slots')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('pickup_date');

            $table->enum('status', [
                'diproses',
                'siap_ambil',
                'selesai',
            ])->default('diproses');

            $table->text('note')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->index(
                ['customer_id', 'created_at'],
                'idx_orders_customer_created_at'
            );

            $table->index(
                ['canteen_id', 'pickup_date'],
                'idx_orders_canteen_pickup_date'
            );

            $table->index(
                'status',
                'idx_orders_status'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
