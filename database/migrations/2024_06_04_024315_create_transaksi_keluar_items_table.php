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
        Schema::create('transaksi_keluar_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_keluar_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('barang_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('qty');
            $table->integer('harga');
            $table->integer('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_keluar_items');
    }
};
