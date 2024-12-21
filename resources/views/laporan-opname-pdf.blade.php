<!DOCTYPE html>
<html>
<head>
    <title>Laporan Opname</title>
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

    <h1>Laporan Opname</h1>
    <p>Rentang Tanggal: {{ $dateRange }}</p>
    <table>
        <thead>
            <tr>
                <th class="px-4 py-2 border">Tanggal</th>
                <th class="px-4 py-2 border">Nama Barang</th>
                <th class="px-4 py-2 border">Stok Sistem</th>
                <th class="px-4 py-2 border">Stok Fisik</th>
                <th class="px-4 py-2 border">Selisih</th>
                <th class="px-4 py-2 border">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td class="px-4 py-2 border dark:border-gray-700">{{ \Carbon\Carbon::parse($item['Tanggal'])->format('d/m/Y') }}</td>
                <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Nama Barang'] }}</td>
                <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Stok Sistem'] }}</td>
                <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Stok Fisik'] }}</td>
                <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Selisih'] }}</td>
                <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Keterangan'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
