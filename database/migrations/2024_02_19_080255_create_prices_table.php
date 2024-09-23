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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('nmId');
            $table->unsignedBigInteger('price');
            $table->double('s_price')->default(0);
            $table->unsignedBigInteger('discount');
            $table->unsignedInteger('percent')->default(0);
            $table->boolean('toUpload')->default(0);
            $table->boolean('holdPrice')->default(0);
            $table->dateTime('holdedAt')->nullable();
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
