<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
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
        h1, p {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
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
    </style>
</head>
<body>
    <div class="kop-surat">
        <h2>UD. Usaha Jaya</h2>
        <p>Jl. Swadaya 2 No.100b, Dero, Condongcatur, Depok, Sleman</p>
        <p>Telepon: +62 858-0274-7974</p>
    </div>

    <h1>Laporan Pembelian</h1>
    <p>Rentang Tanggal: {{ $dateRange }}</p>
    <table>
        <thead>
            <tr>
                <th class="px-4 py-2 border">Tanggal</th>
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
                <td colspan="5" class="px-4 py-2 border dark:border-gray-700 text-right font-bold" id="total">Total:</td>
                <td class="px-4 py-2 border dark:border-gray-700">
                    Rp. {{ number_format($totalSum ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
