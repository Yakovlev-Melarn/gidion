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
        Schema::create('marketplace_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orderId');
            $table->unsignedBigInteger('warehouseId');
            $table->unsignedBigInteger('nmId');
            $table->unsignedBigInteger('chrtId');
            $table->unsignedInteger('price');
            $table->unsignedInteger('convertedPrice');
            $table->unsignedInteger('currencyCode')->default(643);
            $table->unsignedInteger('convertedCurrencyCode')->default(643);
            $table->unsignedInteger('cargoType')->default(1);
            $table->boolean('status')->default(0);
            $table->string('address')->nullable();
            $table->string('requiredMeta')->nullable();
            $table->string('deliveryType')->nullable();
            $table->string('user')->nullable();
            $table->string('orderUid')->nullable();
            $table->string('article')->nullable();
            $table->string('rid')->nullable();
            $table->string('createdAt')->nullable();
            $table->string('offices')->nullable();
            $table->string('skus')->nullable();
            $table->string('qrcode')->default(0);
            $table->string('partA')->nullable();
            $table->string('partB')->nullable();
            $table->string('supplierStatus')->nullable();
            $table->string('wbStatus')->nullable();
            $table->string('shipmentId')->nullable();
            $table->integer('seller_id');
            $table->boolean('printabled')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_orders');
    }
};
