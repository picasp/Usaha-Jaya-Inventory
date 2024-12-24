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
        $stampPath = public_path('img/stempel.png');
        $ttdPath = public_path('img/ttd.png');
        $stampBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($stampPath));
        $ttdBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($ttdPath));
        $items = $transaksi->transaksi_keluar_item()->with('barang')->get();
        return Pdf::loadView('pdf', [
            'transaksi' => $transaksi, 
            'items' => $items,
            'stampBase64' => $stampBase64,
            'ttdBase64' => $ttdBase64,
            ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'debugPng' => false,
            ])
            ->stream($transaksi->id . '.pdf');
    }
    
}
