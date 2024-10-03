<?php
namespace App\Http\Controllers;

use App\Models\TransaksiKeluar;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TransaksiKeluarController extends Controller
{
    // public function view(TransaksiKeluar $transaksi)
    // {
    //     // Dapatkan detail transaksi beserta item barang
    //     $items = $transaksi->transaksi_keluar_item()->with('barang')->get(); // Mengambil item terkait
    //     return view('filament.resources.transaksi-keluars.view', compact('transaksi', 'items'));
    // }

    public function __invoke(TransaksiKeluar $transaksi)
    {
        $items = $transaksi->transaksi_keluar_item()->with('barang')->get();
        return Pdf::loadView('pdf', ['transaksi' => $transaksi, 'items' => $items])
            ->stream($transaksi->id . '.pdf');
    }
    
}
