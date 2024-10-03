<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiKeluarItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaksi_keluar_id', 
        'barang_id', 
        'qty', 
        'harga',
        'total'
    ];

    public function transaksi_keluar(): BelongsTo
    {
        return $this->belongsTo(TransaksiKeluar::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
