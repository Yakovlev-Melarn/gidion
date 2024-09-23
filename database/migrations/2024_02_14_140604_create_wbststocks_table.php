<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wbststocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id');
            $table->string('lastChangeDate')->nullable();
            $table->string('warehouseName')->nullable();
            $table->string('supplierArticle')->nullable();
            $table->unsignedBigInteger('nmId')->nullable();
            $table->string('barcode')->nullable();
            $table->unsignedBigInteger('quantity')->default(0);
            $table->unsignedBigInteger('inWayToClient')->default(0);
            $table->unsignedBigInteger('inWayFromClient')->default(0);
            $table->unsignedBigInteger('quantityFull')->default(0);
            $table->string('category')->nullable();
            $table->string('subject')->nullable();
            $table->string('brand')->nullable();
            $table->string('techSize')->nullable();
            $table->double('Price')->default(0);
            $table->unsignedBigInteger('Discount')->default(0);
            $table->boolean('isSupply')->default(0);
            $table->boolean('isRealization')->default(0);
            $table->string('SCCode')->nullable();
            $table->timestamps();
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wbststocks');
    }
};
