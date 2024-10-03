<!-- resources/views/filament/resources/transaksi-keluars/view.blade.php -->

@extends('layouts.app')

@section('content')
    <div style="text-align:center">
    <h1>UD Usaha Jaya</h1>
        <!-- <h1>Nota Transaksi #{{ $transaksi->id }}</h1> -->
        <p>Tanggal: {{ $transaksi->tgl_penjualan }}</p>
        <p>Pembeli: {{ $transaksi->nama_pembeli }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->barang->nama_barang }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3"><strong>Total Harga</strong></td>
                <td><strong>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <a href="{{ route('transaksi-keluars.view', $transaksi->id) }}" class="btn btn-secondary">
    Cetak PDF
</a>

@endsection
