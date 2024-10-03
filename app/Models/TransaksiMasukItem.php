<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiMasukItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaksi_masuk_id', 
        'barang_id', 
        'qty', 
        'satuan',
        'harga_beli',
        'total'
    ];

    public function transaksi_masuk(): BelongsTo
    {
        return $this->belongsTo(TransaksiMasuk::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
