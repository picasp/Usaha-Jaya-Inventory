<?php

namespace App\Observers;

use App\Models\TransaksiKeluarItem;
use App\Models\Barang;
use App\Models\Transaksikeluar;
use Illuminate\Support\Facades\DB;

class TransaksiKeluarItemObserver
{
    /**
     * Handle the TransaksiKeluarItem "created" event.
     */
    public function created(TransaksiKeluarItem $transaksiKeluarItem): void
    {
        $product = Barang::find($transaksiKeluarItem->barang_id);
        if ($product) {
            $product->stok -= $transaksiKeluarItem->qty;
            $product->save();
        }
        // dd($product);
    }

    /**
     * Handle the TransaksiKeluarItem "updated" event.
     */
    public function updated(TransaksiKeluarItem $transaksiKeluarItem): void
    {
        $originalQuantity = $transaksiKeluarItem->getOriginal('qty');
        $newQuantity = $transaksiKeluarItem->qty;
        $quantityDifference = $newQuantity - $originalQuantity;

        $product = Barang::find($transaksiKeluarItem->barang_id);
        if ($product) {
            $product->stok -= $quantityDifference;
            $product->save();
        }
    }

    /**
     * Handle the TransaksiKeluarItem "deleted" event.
     */
    public function deleted(TransaksiKeluarItem $transaksiKeluarItem): void
    {
        $product = Barang::find($transaksiKeluarItem->barang_id);
        if ($product) {
            $product->stok += $transaksiKeluarItem->qty;
            $product->save();
        }
    }

    /**
     * Handle the TransaksiKeluarItem "restored" event.
     */
    public function restored(TransaksiKeluarItem $transaksiKeluarItem): void
    {
        //
    }

    /**
     * Handle the TransaksiKeluarItem "force deleted" event.
     */
    public function forceDeleted(TransaksiKeluarItem $transaksiKeluarItem): void
    {
        //
    }
}
