<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'nama_barang', 
        'stok', 
        'harga_jual', 
        'harga_beli',
        'keterangan'
    ];

    public function transaksi_keluar_item(): HasMany
    {
        return $this->hasMany(TransaksiKeluarItem::class);
    }

    public function transaksi_masuk_item(): HasMany
    {
        return $this->hasMany(TransaksiMasukItem::class);
    }
}
