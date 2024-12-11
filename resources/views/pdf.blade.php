<!-- resources/views/pdf.blade.php -->

@extends('layouts.app')

@section('content')
    <!-- <div style="text-align:center">
    <h1>UD Usaha Jaya</h1>
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
    </table> -->

    <div class="nota">
        <div class="header">
            UD. USAHA JAYA
        </div>
        <div class="sub-header">
            Jl. Swadaya 2 No.100b, Dero, Condongcatur, Kec. Depok, Kabupaten Sleman, Daerah Istimewa Yogyakarta<br>
            Telp. +62 858-0274-7974
        </div>

        <div class="content">
            <p>Pamulang, {{ $transaksi->tgl_penjualan->format('d F Y') }}</p>
            <p>Kepada Yth,</p>
            <p>{{ $transaksi->nama_pembeli }}</p>

            <table class="tabel">
                <thead>

                </thead>
                <tbody>
                <tr>
                        <th>Banyaknya</th>
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
                <!-- <tfoot>
                    <tr class="ttd">
                        <th colspan="2">Penerima</th>
                        <th colspan="2">Hormat Kami</th>
                    </tr>
                    <tr>
                        <td colspan="2">{{ $transaksi->nama_pembeli }}</td>
                        <td colspan="2">UD Usaha Jaya</td>
                    </tr>
                </tfoot> -->
            </table>

            <table font-size:7pt; cellspacing='2' class="tabel2">
                <tr>
                    <td align='center'>Diterima Oleh,</br></br><u>( {{ $transaksi->nama_pembeli }} )</u></td>
                    <td style='padding:5px; text-align:left; width:30%'></td>
                    <td align='center'>TTD,</br></br><u>( UD. Usaha Jaya )</u></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <div class="note">
                Barang yang sudah dibeli tidak dapat ditukar/dikembalikan
            </div>
        </div>
    </div>

@endsection
