<?php

namespace App\Filament\Resources\TransaksiKeluarResource\Pages;

use App\Filament\Resources\TransaksiKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\TransaksiKeluar;
use App\Models\Barang;
use App\Models\TransaksiKeluarItem;
use Illuminate\Support\Facades\DB;

class CreateTransaksiKeluar extends CreateRecord
{
    protected static string $resource = TransaksiKeluarResource::class;
    protected static ?string $title = 'Penjualan Baru';
    
    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // foreach ($data['transaksi_keluar_item'] as $item) {
    //     //     $product = Barang::find($item['barang_id']);
    //     //     $product->stok -= $item['qty'];
    //     //     $product->save();
    //     // }

    //     foreach ($data['transaksi_keluar_item'] as $item) {
    //         $product = Barang::find($item['barang_id']);
    //         if ($product) {
    //             if ($product->stok >= $item['qty']) {
    //                 $product->stok -= $item['qty'];
    //                 $product->save();
    //             } else {
    //                 // Handle insufficient stock scenario
    //                 throw new \Exception("Not enough stock for product: " . $product->nama_barang);
    //             }
    //         }
    //     }
        

    //     return $data;
    // }

    public function afterSave(): void
    {

    }
    }
