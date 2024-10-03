<?php

namespace App\Observers;

use App\Models\TransaksiMasukItem;
use App\Models\TransaksiMasuk;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class TransaksiMasukItemObserver
{
    /**
     * Handle the TransaksiMasukItem "created" event.
     */
    public function created(TransaksiMasukItem $transaksiMasukItem): void
    {
        $product = Barang::find($transaksiMasukItem->barang_id);
        if ($product) {
            $product->stok += $transaksiMasukItem->qty;
            $product->save();
        }
    }

    /**
     * Handle the TransaksiMasukItem "updated" event.
     */
    public function updated(TransaksiMasukItem $transaksiMasukItem): void
    {
        $originalQuantity = $transaksiMasukItem->getOriginal('qty');
        $newQuantity = $transaksiMasukItem->qty;
        $quantityDifference = $newQuantity - $originalQuantity;

        $product = Barang::find($transaksiMasukItem->barang_id);
        if ($product) {
            $product->stok -= $quantityDifference;
            $product->save();
        }
    }

    /**
     * Handle the TransaksiMasukItem "deleted" event.
     */
    public function deleted(TransaksiMasukItem $transaksiMasukItem): void
    {
        $product = Barang::find($transaksiMasukItem->barang_id);
        if ($product) {
            $product->stok += $transaksiMasukItem->qty;
            $product->save();
        }
    }

    /**
     * Handle the TransaksiMasukItem "restored" event.
     */
    public function restored(TransaksiMasukItem $transaksiMasukItem): void
    {
        //
    }

    /**
     * Handle the TransaksiMasukItem "force deleted" event.
     */
    public function forceDeleted(TransaksiMasukItem $transaksiMasukItem): void
    {
        //
    }
}
