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

        // Previous month sales amount and transaction count
        $totalPenjualanBulanLalu = TransaksiKeluar::whereMonth('tgl_penjualan', $previousMonth)
            ->whereYear('tgl_penjualan', $previousMonthYear)
            ->sum('total_harga');

        $totalTransaksiBulanLalu = TransaksiKeluar::whereMonth('tgl_penjualan', $previousMonth)
            ->whereYear('tgl_penjualan', $previousMonthYear)
            ->count();

        // Calculate percentage changes
        $penjualanChange = $totalPenjualanBulanLalu > 0
            ? (($totalPenjualanBulanIni - $totalPenjualanBulanLalu) / $totalPenjualanBulanLalu) * 100
            : 0;

        $transaksiChange = $totalTransaksiBulanLalu > 0
            ? (($totalTransaksiBulanIni - $totalTransaksiBulanLalu) / $totalTransaksiBulanLalu) * 100
            : 0;

        return [
            Stat::make('Total Barang', Barang::query()->count('id')),
            Stat::make('Supplier', Supplier::query()->count('id')),
            Stat::make('Total Penjualan Bulan Ini', 'Rp. ' . number_format($totalPenjualanBulanIni, 0, ',', '.'))
                ->description($penjualanChange >= 0 ? 'Naik ' . round($penjualanChange, 2) . '%' : 'Turun ' . round($penjualanChange, 2) . '%')
                ->color($penjualanChange >= 0 ? 'success' : 'danger'),
            Stat::make('Jumlah Transaksi Bulan Ini', $totalTransaksiBulanIni . ' transaksi')
                ->description($transaksiChange >= 0 ? 'Naik ' . round($transaksiChange, 2) . '%' : 'Turun ' . round($transaksiChange, 2) . '%')
                ->color($transaksiChange >= 0 ? 'success' : 'danger'),
        ];
    }
}
