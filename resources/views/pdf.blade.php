<!-- resources/views/pdf.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="nota">
        <table class="tabel-header" style='font-size:8pt; font-family:calibri; border-collapse: collapse;'>
            <td width='65%' style='padding-right:80px; vertical-align:top'>
                <span style='font-size:12pt'><b>UD. USAHA JAYA</b></span></br>
                Jl. Swadaya 2 No.100b, Dero, Condongcatur, Kec. Depok, Kabupaten Sleman, Daerah Istimewa Yogyakarta </br>
                Telp : +62 858-0274-7974
            </td>
            <td style='vertical-align:top' width='35%'>
                <b><span style='font-size:12pt'>INVOICE PENJUALAN</span></b></br>
                Nama Pembeli : {{ $transaksi->nama_pembeli }}</br>
                Tanggal : {{ $transaksi->tgl_penjualan->format('d F Y') }}</br>
            </td>
        </table>

        <div class="content">
            <table class="tabel">
                <thead>

                </thead>
                <tbody>
                <tr>
                    <th width='10%'>Banyaknya</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                </tr>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->barang->nama_barang }}</td>
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
                <div class="signature-section">
                <p>Yogyakarta, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
                <p>UD. Usaha Jaya</p>
                <img src="{{ $stampBase64 }}" alt="Stamp" class="stamp">
                <img src="{{ $ttdBase64 }}" alt="Signature" class="signature">
                <p>(Suryono)</p>
            </div>
        </div>

        <div class="footer">
            <div class="note">
                Barang yang sudah dibeli tidak dapat ditukar/dikembalikan
            </div>
        </div>
    </div>

@endsection
