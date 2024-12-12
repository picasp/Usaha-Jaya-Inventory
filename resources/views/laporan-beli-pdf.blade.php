<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Laporan Penjualan</h1>
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
                <td class="px-4 py-2 border dark:border-gray-700">Rp. {{ number_format($item['Total Pengeluaran']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
