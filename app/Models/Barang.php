<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'kode_barang',
        'nama_barang', 
        'stok', 
        'stok_minimal',
        'satuan',
        'harga_jual', 
        'harga_beli',
        'keterangan',
        'status'

    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($barang) {
            if (empty($barang->kode_barang)) {
                $barang->kode_barang = strtoupper(Str::random(6));
            }
        });

        static::saving(function ($barang) {
            // Set status berdasarkan stok dan stok_minimal
            $barang->status = $barang->stok <= $barang->stok_minimal ? 'Kritis' : 'Aman';
        });
    }

    public function transaksi_keluar_item(): HasMany
    {
        return $this->hasMany(TransaksiKeluarItem::class);
    }

    public function transaksi_masuk_item(): HasMany
    {
        return $this->hasMany(TransaksiMasukItem::class);
    }

    public function opname_item(): HasMany
    {
        return $this->hasMany(OpnameItem::class);
    }
}
