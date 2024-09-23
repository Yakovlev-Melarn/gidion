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
        Schema::create('wbstorders', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date')->nullable();
            $table->dateTime('lastChangeDate')->nullable();
            $table->string('warehouseName')->nullable();
            $table->string('countryName')->nullable();
            $table->string('oblastOkrugName')->nullable();
            $table->string('regionName')->nullable();
            $table->string('supplierArticle')->nullable();
            $table->unsignedBigInteger('nmId')->nullable();
            $table->string('barcode')->nullable();
            $table->string('category')->nullable();
            $table->string('subject')->nullable();
            $table->string('brand')->nullable();
            $table->string('techSize')->nullable();
            $table->unsignedBigInteger('incomeID')->nullable();
            $table->boolean('isSupply')->nullable();
            $table->boolean('isRealization')->nullable();
            $table->double('totalPrice')->nullable();
            $table->integer('discountPercent')->nullable();
            $table->double('spp')->nullable();
            $table->double('finishedPrice')->nullable();
            $table->double('priceWithDisc')->nullable();
            $table->boolean('isCancel')->nullable();
            $table->dateTime('cancelDate')->nullable();
            $table->string('orderType')->nullable();
            $table->string('sticker')->nullable();
            $table->string('gNumber')->nullable();
            $table->string('srid')->nullable();
            $table->integer('seller_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wbstorders');
    }
};
