<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup_slot_options', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_slot_options');
    }
};
