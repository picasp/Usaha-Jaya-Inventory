<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiMasuk extends Model
{
    use HasFactory;

    protected $table = 'transaksi_masuks';

    protected $fillable = [
        'total_harga_masuk',
        'keterangan'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($transaksimasuk) {
            $transaksimasuk->transaksi_masuk_item()->each(function ($transaksiMasukItem) {
                $transaksiMasukItem->delete();
            });
        });
    }
    

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
    public function transaksi_masuk_item(): HasMany
    {
        return $this->hasMany(TransaksiMasukItem::class);
    }
}
