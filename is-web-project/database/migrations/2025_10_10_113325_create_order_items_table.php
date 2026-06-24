<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('order_item_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->string('product_name', 255);
            $table->json('variant_attributes')->nullable();
            $table->decimal('unit_price', 15, 2);
            $table->integer('quantity');
            $table->decimal('total_price', 15, 2);

            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('set null');
            $table->foreign('variant_id')->references('variant_id')->on('product_variants')->onDelete('set null');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
