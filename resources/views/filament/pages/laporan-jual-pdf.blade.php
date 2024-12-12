<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">UD USAHA JAYA</h1>
    <h2 style="text-align: center;">LAPORAN PENJUALAN</h2>
    <p style="text-align: center;">Rentang Tanggal: {{ request('start_date') }} - {{ request('end_date') }}</p>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Pembeli</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Jenis Pembayaran</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->Tanggal)->format('d/m/Y') }}</td>
                <td>{{ $item->Nama_Pembeli }}</td>
                <td>{{ $item->Nama_Barang }}</td>
                <td>{{ $item->Satuan }}</td>
                <td>{{ $item->Transaksi }}</td>
                <td>Rp. {{ number_format($item->Harga, 0, ',', '.') }}</td>
                <td>{{ $item->Stok }}</td>
                <td>Rp. {{ number_format($item->Total_Pendapatan, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
