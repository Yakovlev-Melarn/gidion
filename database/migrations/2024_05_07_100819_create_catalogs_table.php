<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('catalogs', function (Blueprint $table) {
            $table->id();
            $table->integer('sku');
            $table->string('name');
            $table->string('manufacturer')->nullable();
            $table->string('brand')->nullable();
            $table->string('barcode')->nullable();
            $table->longText('description')->nullable();
            $table->integer('ban_not_multiple')->nullable();
            $table->integer('out_of_stock')->nullable();
            $table->longText('characteristic_list')->nullable();
            $table->longText('facet_list')->nullable();
            $table->longText('photo_list')->nullable();
            $table->string('package_size')->nullable();
            $table->string('price_list')->nullable();
            $table->string('stock_list')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogs');
    }
};
