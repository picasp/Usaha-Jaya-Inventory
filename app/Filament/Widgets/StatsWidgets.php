<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use App\Models\Supplier;
use App\Models\TransaksiKeluar;
use App\Models\TransaksiMasuk;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsWidgets extends BaseWidget
{
    protected function getStats(): array
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $previousMonth = Carbon::now()->subMonth()->month;
        $previousMonthYear = Carbon::now()->subMonth()->year;

        $totalPenjualanBulanIni = TransaksiKeluar::whereMonth('tgl_penjualan', $currentMonth)
            ->whereYear('tgl_penjualan', $currentYear)
            ->sum('total_harga');

        $totalTransaksiBulanIni = TransaksiKeluar::whereMonth('tgl_penjualan', $currentMonth)
            ->whereYear('tgl_penjualan', $currentYear)
            ->count();

        $totalPengeluaranBulanIni = TransaksiMasuk::whereMonth('tgl_pembelian', $currentMonth)
            ->whereYear('tgl_pembelian', $currentYear)
            ->sum('total_harga_masuk');

        // Previous month sales amount and transaction count
        $totalPenjualanBulanLalu = TransaksiKeluar::whereMonth('tgl_penjualan', $previousMonth)
            ->whereYear('tgl_penjualan', $previousMonthYear)
            ->sum('total_harga');

        $totalTransaksiBulanLalu = TransaksiKeluar::whereMonth('tgl_penjualan', $previousMonth)
            ->whereYear('tgl_penjualan', $previousMonthYear)
            ->count();
        
        $totalPengeluaranBulanLalu = TransaksiMasuk::whereMonth('tgl_pembelian', $previousMonth)
            ->whereYear('tgl_pembelian', $previousMonthYear)
            ->sum('total_harga_masuk');

        // Calculate percentage changes
        $penjualanChange = $totalPenjualanBulanIni - $totalPenjualanBulanLalu;
        $pengeluaranChange = $totalPengeluaranBulanIni - $totalPengeluaranBulanLalu;

        $transaksiChange = $totalTransaksiBulanLalu > 0
            ? (($totalTransaksiBulanIni - $totalTransaksiBulanLalu) / $totalTransaksiBulanLalu) * 100
            : 0;

        return [
            Stat::make('Total Barang', Barang::query()->count('id')),
            Stat::make('Supplier', Supplier::query()->count('id')),
            Stat::make('Pendapatan Bulan Ini', 'Rp. ' . number_format($totalPenjualanBulanIni, 0, ',', '.'))
                ->description($penjualanChange >= 0 
                ? 'Naik Rp. ' . number_format($penjualanChange, 0, ',', '.') 
                : 'Turun Rp. ' . number_format(abs($penjualanChange), 0, ',', '.'))
                ->descriptionIcon($penjualanChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($penjualanChange >= 0 ? 'success' : 'danger'),

            Stat::make('Pengeluaran Bulan Ini', 'Rp. ' . number_format($totalPengeluaranBulanIni, 0, ',', '.'))
                ->description($pengeluaranChange >= 0 
                    ? 'Naik Rp. ' . number_format($pengeluaranChange, 0, ',', '.') 
                    : 'Turun Rp. ' . number_format(abs($pengeluaranChange), 0, ',', '.'))
                ->color($pengeluaranChange >= 0 ? 'danger' : 'success')
                ->descriptionIcon($pengeluaranChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down'),

            Stat::make('Penjualan Bulan Ini', $totalTransaksiBulanIni)
                ->description($transaksiChange >= 0 
                ? 'Naik ' . round($transaksiChange, 2) . '%' 
                : 'Turun ' . round($transaksiChange, 2) . '%')
                ->descriptionIcon($transaksiChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($transaksiChange >= 0 ? 'success' : 'danger'),
        ];
    }
}
