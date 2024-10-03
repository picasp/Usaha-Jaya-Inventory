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
        Schema::create('transaksi_masuk_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_masuk_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('barang_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('qty');
            $table->string('satuan')->nullable();
            $table->integer('harga_beli')->nullable();
            $table->integer('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_masuk_items');
    }
};
