<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransaksiKeluarController;
use App\Models\TransaksiKeluar;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;

Route::get('/', function () {
    return view('welcome');
});

Route::get('pdf/{transaksi}', TransaksiKeluarController::class)->name('pdf'); 
// Route::get('/transaksi-keluar/{transaksi}', [TransaksiKeluarController::class, 'view'])->name('filament.resources.transaksi-keluars.view');

// Route::get('/transaksi-keluars/{id}/pdf', function($id) {
//     $transaksi = TransaksiKeluar::with('transaksi_keluar_item')->findOrFail($id);
//     $pdf = FacadePdf::loadView('transaksi-keluars.view', compact('transaksi'));
//     return $pdf->download('nota_transaksi_'.$transaksi->id.'.pdf');
// })->name('transaksi-keluars.view');
