<?php

namespace App\Observers;

use App\Models\Barang;
use App\Models\Transaksikeluar;
use App\Models\TransaksiKeluarItem;
use Illuminate\Support\Facades\DB;

class TransaksiKeluarObserver
{
    /**
     * Handle the Transaksikeluar "created" event.
     */
    public function created(Transaksikeluar $transaksikeluar): void
    {
        // $product = Barang::find($transaksikeluar['transaksi_keluar_item']);
        // if ($product) {
        //     $product->stok -= $transaksikeluar->qty;
        //     $product->save();
        // }
        // dd($product);
    }


    /**
     * Handle the Transaksikeluar "updated" event.
     */
    public function updated(Transaksikeluar $transaksikeluar): void
    {
        //
    }

    /**
     * Handle the Transaksikeluar "deleted" event.
     */
    public function deleted(Transaksikeluar $transaksikeluar): void
    {
        //
    }

    /**
     * Handle the Transaksikeluar "restored" event.
     */
    public function restored(Transaksikeluar $transaksikeluar): void
    {
        //
    }

    /**
     * Handle the Transaksikeluar "force deleted" event.
     */
    public function forceDeleted(Transaksikeluar $transaksikeluar): void
    {
        //
    }
}
