<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransaksiKeluarController;
use App\Http\Controllers\LaporanBeliController;
use App\Http\Controllers\LaporanJualController;
use App\Http\Controllers\LaporanOpnameController;
use App\Models\TransaksiKeluar;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;

Route::get('/', function () {
    return view('welcome');
});

Route::get('pdf/{transaksi}', TransaksiKeluarController::class)->name('pdf'); 
Route::get('/laporan-beli', [LaporanBeliController::class, 'index'])->name('laporan-beli');
Route::get('/laporan-jual', [LaporanJualController::class, 'index'])->name('laporan-jual');
Route::get('/laporan-opname', [LaporanOpnameController::class, 'index'])->name('laporan-opname');
Route::get('laporan-beli-pdf', [LaporanBeliController::class, 'exportPdf'])->name('laporan-beli.export-pdf'); 
Route::get('/laporan-jual/cetak', [LaporanJualController::class, 'cetakPdf'])->name('laporan-jual.cetak');
// Route::get('/transaksi-keluar/{transaksi}', [TransaksiKeluarController::class, 'view'])->name('filament.resources.transaksi-keluars.view');

// Route::get('/transaksi-keluars/{id}/pdf', function($id) {
//     $transaksi = TransaksiKeluar::with('transaksi_keluar_item')->findOrFail($id);
//     $pdf = FacadePdf::loadView('transaksi-keluars.view', compact('transaksi'));
//     return $pdf->download('nota_transaksi_'.$transaksi->id.'.pdf');
// })->name('transaksi-keluars.view');
