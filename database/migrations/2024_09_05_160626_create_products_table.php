<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('regular_price', 10, 2);
            $table->decimal('sales_price', 10, 2);
            $table->text('description')->nullable();
            $table->text('UPC');
            $table->string('image')->nullable();
            $table->text('images')->nullable();
            $table->integer('sales_number')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_new_arrival')->default(true);
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->bigInteger('brand_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
            $table->foreign('brand_id')->references('id')->on('brands')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
