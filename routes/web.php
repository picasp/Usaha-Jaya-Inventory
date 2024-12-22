<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransaksiKeluarController;
use App\Http\Controllers\LaporanBeliController;
use App\Http\Controllers\LaporanJualController;
use App\Http\Controllers\LaporanOpnameController;
use App\Filament\Pages\LaporanBeli;
use App\Filament\Pages\LaporanJual;
use App\Filament\Pages\LaporanOpname;
use App\Models\TransaksiKeluar;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('pdf/{transaksi}', TransaksiKeluarController::class)->name('pdf'); 
// Route::get('/laporan-beli', [LaporanBeliController::class, 'index'])->name('laporan-beli');
// Route::get('/laporan-jual', [LaporanJualController::class, 'index'])->name('laporan-jual');
// Route::get('/laporan-opname', [LaporanOpnameController::class, 'index'])->name('laporan-opname');
Route::get('laporan-beli-pdf', [LaporanBeli::class, 'exportPdf'])->name('laporan-beli.export-pdf'); 
Route::get('laporan-jual-pdf', [LaporanJual::class, 'exportPdf'])->name('laporan-jual.export-pdf');
Route::get('laporan-opname-pdf', [LaporanOpname::class, 'exportPdf'])->name('laporan-opname.export-pdf');