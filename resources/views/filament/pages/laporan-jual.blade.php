<x-filament-panels::page>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
    <div class="p-4">
        <div class="text-center mb-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Laporan Penjualan Barang UD Usaha Jaya</h1>
            <p class="text-gray-600 dark:text-gray-400">Dibuat pada: {{ now()->format('d-m-Y H:i:s') }}</p>
            <p class="text-gray-600 dark:text-gray-400">Rentang Tanggal: <span id="date-range">{{ $dateRange ?? 'Semua Tanggal' }}</span></p>
        </div>
        <div class="overflow-x-auto">
            <form id="filter-form" class="mb-4 flex justify-center gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai</label>
                <input type="text" name="start_date" id="start_date"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-800"
                    placeholder="Pilih Tanggal Mulai" readonly>
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Akhir</label>
                <input type="text" name="end_date" id="end_date"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-800"
                    placeholder="Pilih Tanggal Akhir" readonly>
            </div>
            <div class="self-end">
                <button type="button" id="filter-button" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Filter</button>
            </div>
            </form>

<table class="w-full border border-gray-200 bg-white dark:bg-gray-800" id="data-table">
    <thead>
        <tr class="bg-gray-100 dark:bg-gray-700 text-gray-800">
            <th class="px-4 py-2 border">Tanggal</th>
            <th class="px-4 py-2 border">Nama Pelanggan</th>
            <th class="px-4 py-2 border">Nama Barang</th>
            <th class="px-4 py-2 border">Jumlah</th>
            <th class="px-4 py-2 border">Satuan</th>
            <th class="px-4 py-2 border">Jenis Pembayaran</th>
            <th class="px-4 py-2 border">Harga</th>
            <th class="px-4 py-2 border">Total Penjualan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
        <tr class="border dark:border-gray-700">
            <td class="px-4 py-2 border dark:border-gray-700">{{ \Carbon\Carbon::parse($item['Tanggal'])->format('d/m/Y') }}</td>
            <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Nama Pembeli'] }}</td>
            <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Nama Barang'] }}</td>
            <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Stok'] }}</td>
            <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Satuan'] }}</td>
            <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Transaksi'] }}</td>
            <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Harga'] }}</td>
            <td class="px-4 py-2 border dark:border-gray-700">Rp. {{ number_format($item['Total Pendapatan']) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
    <tr class="border dark:border-gray-700 bg-gray-100 dark:bg-gray-700">
        <td colspan="7" class="px-4 py-2 border dark:border-gray-700 text-right font-bold">Total:</td>
        <td class="px-4 py-2 border dark:border-gray-700">
            Rp. {{ number_format($totalSum ?? 0, 0, ',', '.') }}
        </td>
    </tr>
</tfoot>

</table>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('filter-button').addEventListener('click', function() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            fetch(`{{ route('laporan-jual') }}?start_date=${startDate}&end_date=${endDate}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                const tableBody = document.querySelector('#data-table tbody');
                tableBody.innerHTML = ''; // Clear existing rows

                data.data.forEach(item => {
                    const row = `
                        <tr class="border dark:border-gray-700">
                            <td class="px-4 py-2 border dark:border-gray-700">${new Date(item.Tanggal).toLocaleDateString()}</td>
                            <td class="px-4 py-2 border dark:border-gray-700">${item['Nama Pembeli']}</td>
                            <td class="px-4 py-2 border dark:border-gray-700">${item['Nama Barang']}</td>
                            <td class="px-4 py-2 border dark:border-gray-700">${item.Stok}</td>
                            <td class="px-4 py-2 border dark:border-gray-700">${item.Satuan}</td>
                            <td class="px-4 py-2 border dark:border-gray-700">${item.Transaksi}</td>
                            <td class="px-4 py-2 border dark:border-gray-700">${item.Harga}</td>
                            <td class="px-4 py-2 border dark:border-gray-700">Rp. ${parseFloat(item['Total Pendapatan']).toLocaleString()}</td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });

                document.getElementById('date-range').innerText = startDate && endDate ? `${startDate} - ${endDate}` : 'Semua Tanggal';

                const footerTotalCell = document.querySelector('#data-table tfoot td:last-child');
                if (footerTotalCell) {
                    footerTotalCell.innerText = 'Rp. ' + parseFloat(data.totalSum || 0).toLocaleString();
                } else {
                    const tfoot = document.createElement('tfoot');
                    tfoot.innerHTML = `
                        <tr class="border dark:border-gray-700 bg-gray-100 dark:bg-gray-700">
                            <td colspan="5" class="px-4 py-2 border dark:border-gray-700 text-right font-bold">Total:</td>
                            <td class="px-4 py-2 border dark:border-gray-700">
                                Rp. ${parseFloat(data.totalSum || 0).toLocaleString()}
                            </td>
                        </tr>
                    `;
                    document.querySelector('#data-table').appendChild(tfoot);
                }

            })
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#start_date", {
                dateFormat: "Y-m-d",
                allowInput: true,
                onClose: function(selectedDates, dateStr, instance) {
                    // Optional: You can add custom behavior on date selection
                }
            });

            flatpickr("#end_date", {
                dateFormat: "Y-m-d",
                allowInput: true,
                onClose: function(selectedDates, dateStr, instance) {
                    // Optional: You can add custom behavior on date selection
                }
            });
        });
    </script>
    @endpush
</x-filament-panels::page>