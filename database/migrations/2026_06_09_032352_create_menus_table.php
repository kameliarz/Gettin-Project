<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();

            $table->foreignId('canteen_id')
                ->constrained('canteens')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedSmallInteger('category_id');

            $table->string('name', 120);
            $table->decimal('price', 12, 2);

            // Sesuai koreksi: gambar menu disimpan sebagai MEDIUMBLOB
            $table->mediumText('image')
                ->charset('binary')
                ->nullable();

            $table->integer('stock_qty')->default(0);
            $table->boolean('is_popular')->default(true);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('category_id')
                ->references('id')
                ->on('menu_categories')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
