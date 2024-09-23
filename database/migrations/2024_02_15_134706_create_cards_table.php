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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('nmID');
            $table->string('vendorCode');
            $table->unsignedBigInteger('supplier')->default(0);
            $table->unsignedBigInteger('supplierSku')->default(0);
            $table->boolean('removeByStock')->default(0);
            $table->unsignedBigInteger('imtID');
            $table->unsignedBigInteger('subjectID');
            $table->string('subjectName');
            $table->string('brand');
            $table->string('title');
            $table->boolean('syncStatus')->default(1)->nullable();
            $table->string('createdAt');
            $table->string('updatedAt');
            $table->timestamps();
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
