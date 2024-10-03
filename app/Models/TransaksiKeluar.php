<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiKeluar extends Model
{
    use HasFactory;

    protected $table = 'transaksi_keluars';

    protected $casts = [
        'total_harga',
        'nama_pembeli',
        'tgl_penjualan' => 'datetime',
        'keterangan'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($transaksikeluar) {
            $transaksikeluar->transaksi_keluar_item()->each(function ($transaksiKeluarItem) {
                $transaksiKeluarItem->delete();
            });
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function transaksi_keluar_item(): HasMany
    {
        return $this->hasMany(TransaksiKeluarItem::class);
    }
}
