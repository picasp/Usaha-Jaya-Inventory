<!DOCTYPE html>
<html>
<head>
    <title>Laporan Opname</title>
    <style>
        @page {
            margin: 50px 50px; /* Margin untuk semua halaman */
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
        }
        header {
            position: fixed;
            top: -65px; /* Header di atas konten */
            left: -70px;
            right: -70px;
            height: 40px;
            text-align: left;
            padding-left: 20px;
            padding-right: 20px;
            font-size: 8pt;
            line-height: 1.5;
            font-style: italic;
        }

        .header-left {
            float: left;
        }

        .header-right {
            float: right;
        }

        .content {
            margin-top: 20px; /* Konten tetap memiliki jarak dari header */
        }
        .kop-surat {
            text-align: center;
            font-size: small;
            margin-bottom: 20px;
            border-bottom: 2px solid #000; /* Menambahkan garis bawah pada kop surat */
            padding-bottom: 10px; /* Memberi jarak antara teks dan garis */
        }
        .kop-surat h2, .kop-surat p {
            margin: 0;
            padding: 0;
        }
        .kop-surat img {
            max-height: 80px;
            margin-bottom: 10px;
        }
        h2 {
            text-align: center;
        }
        span {
            font-size: 9pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tfoot #total {
            text-align: right;
        }
        .signature-section {
            margin-top: 40px;
            text-align: center;
            margin-left: 500px;
            position: relative;
        }
        .signature-section img.stamp {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%) rotate(-30deg);
            max-width: 200px;
            opacity: 0.8;
        }
        .signature-section img.signature {
            max-width: 120px;
            display: block;
            margin: 0 auto;
        }
        .signature-section p {
            margin: 1px 0;
        }
        .tab {
            display: inline-block;
            margin-left: 50px;  /* for e.g: value = 40px*/
        }
        .tab2 {
            display: inline-block;
            margin-left: 52px;  /* for e.g: value = 40px*/
        }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
            <p>Laporan Penjualan Barang UD. Usaha Jaya</p>
        </div>
        <div class="header-right">
            <p>{{ now()->locale('id')->translatedFormat('d/m/Y H:i') }}</p>
        </div>
    </header>
    <div class="content">
    <div class="kop-surat">
        <h2>UD. Usaha Jaya</h2>
        <p>Jl. Swadaya 2 No.100b, Dero, Condongcatur, Depok, Sleman</p>
        <p>Telepon: +62 858-0274-7974</p>
    </div>
    <b><span>Laporan Pembelian Barang</span> <br></b>
    <span>Tanggal<span class="tab"></span>: {{ now()->locale('id')->translatedFormat('d/m/Y H:i:s') }}</span> <br>
    <span>Periode<span class="tab2"></span>: {{ $dateRange }}</span> <br>
    <table>
    <thead>
            <tr>
                <th class="px-4 py-2 border">Tanggal</th>
                <th class="px-4 py-2 border">Kode Barang</th>
                <th class="px-4 py-2 border">Nama Barang</th>
                <th class="px-4 py-2 border">Pemasok</th>
                <th class="px-4 py-2 border">Stok Diterima</th>
                <th class="px-4 py-2 border">Satuan</th>
                <th class="px-4 py-2 border">Total Pembelian</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td class="px-4 py-2 border dark:border-gray-700">{{ \Carbon\Carbon::parse($item['Tanggal'])->format('d/m/Y') }}</td>
                <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Kode Barang'] }}</td>
                <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Nama Barang'] }}</td>
                <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Supplier'] }}</td>
                <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Stok'] }}</td>
                <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Satuan'] }}</td>
                <td class="px-4 py-2 border dark:border-gray-700">Rp. {{ number_format($item['Total Pengeluaran'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="border dark:border-gray-700 bg-gray-100 dark:bg-gray-700">
                <td colspan="6" class="px-4 py-2 border dark:border-gray-700 text-right font-bold" id="total">Total:</td>
                <td class="px-4 py-2 border dark:border-gray-700">
                    Rp. {{ number_format($totalSum ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
    <div class="signature-section">
        <p>Yogyakarta, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
        <p>UD. Usaha Jaya</p>
        <img src="{{ $stampBase64 }}" alt="Stamp" class="stamp">
        <img src="{{ $ttdBase64 }}" alt="Signature" class="signature">
        <p>(Suryono)</p>
    </div>
    </div>
</body>
</html>
