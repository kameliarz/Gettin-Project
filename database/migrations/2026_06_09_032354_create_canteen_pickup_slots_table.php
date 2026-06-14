<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('canteen_pickup_slots', function (Blueprint $table) {
            $table->id();

            $table->foreignId('canteen_id')
                ->constrained('canteens')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedSmallInteger('pickup_slot_option_id');

            $table->integer('quota');
            $table->boolean('is_active')->default(true);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->unique(
                ['canteen_id', 'pickup_slot_option_id'],
                'uq_canteen_pickup_slot'
            );

            $table->foreign('pickup_slot_option_id')
                ->references('id')
                ->on('pickup_slot_options')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('canteen_pickup_slots');
    }
};
