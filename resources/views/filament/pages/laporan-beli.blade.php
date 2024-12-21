<x-filament-panels::page>
<head>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
</head>
    <div class="p-4">
        <div class="text-center mb-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Laporan Pembelian Barang UD Usaha Jaya</h1>
            <p class="text-gray-600 dark:text-gray-400">Dibuat pada: {{ now()->format('d-m-Y H:i:s') }}</p>
            <p class="text-gray-600 dark:text-gray-400">Rentang Tanggal: <span id="date-range">{{ $dateRange ?? 'Semua Tanggal' }}</span></p>
        </div>
        <div class="overflow-x-auto">
            <form id="filter-form" class="mb-4 flex justify-center gap-4">
            <div>
                <input type="text" name="date_range" id="date_range"
                    class="block w-full rounded-md border-gray-300 dark:bg-gray-800"
                    placeholder="Pilih Rentang Tanggal" readonly>
            </div>
            <div class="self-end">
                <x-filament::button id="filter-button" icon="heroicon-m-bars-arrow-down">
                    Filter
                </x-filament::button>
            </div>
            <div class="flex justify-end mb-4">
                <x-filament::button 
                href="#"
                tag="a"
                target="_blank"
                id="print-button"
                color="info"
                icon="heroicon-m-printer">
                    Print
                </x-filament::button>
            </div>
            </form>

<table class="w-full border border-gray-200 bg-white dark:bg-gray-800" id="data-table">
    <thead>
        <tr class="bg-gray-100 dark:bg-gray-700 text-gray-800">
            <th class="px-4 py-2 border">Tanggal</th>
            <th class="px-4 py-2 border">Nama Barang</th>
            <th class="px-4 py-2 border">Pemasok</th>
            <th class="px-4 py-2 border">Stok Diterima</th>
            <th class="px-4 py-2 border">Satuan</th>
            <th class="px-4 py-2 border">Total Pembelian</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $item)
        <tr class="border dark:border-gray-700">
            <td class="px-4 py-2 border dark:border-gray-700">{{ \Carbon\Carbon::parse($item['Tanggal'])->format('d/m/Y') }}</td>
            <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Nama Barang'] }}</td>
            <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Supplier'] }}</td>
            <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Stok'] }}</td>
            <td class="px-4 py-2 border dark:border-gray-700">{{ $item['Satuan'] }}</td>
            <td class="px-4 py-2 border dark:border-gray-700">Rp. {{ number_format($item['Total Pengeluaran']) }}</td>
        </tr>
        @empty
        <tr class="border dark:border-gray-700">
            <td colspan="6" class="px-4 py-2 border dark:border-gray-700">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
    <tr class="border dark:border-gray-700 bg-gray-100 dark:bg-gray-700">
        <td colspan="5" class="px-4 py-2 border dark:border-gray-700 text-right font-bold">Total:</td>
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
        $(document).ready(function() {
            // Inisialisasi Date Range Picker
            $('#date_range').daterangepicker({
                locale: {
                    format: 'DD-MM-YYYY',
                    separator: ' - ',
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Batal',
                    fromLabel: 'Dari',
                    toLabel: 'Hingga',
                    customRangeLabel: 'Sesuaikan',
                    daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    monthNames: [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ],
                    firstDay: 1
                },
                opens: 'center',
                autoUpdateInput: false,
                ranges: {
                    'Hari ini': [moment(), moment()],
                    'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7  Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                    '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                    'Bulan ini': [moment().startOf('month'), moment().endOf('month')],
                    'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Tahun ini': [moment().startOf('year'), moment().endOf('year')],
                },
                "alwaysShowCalendars": true,
            });

            $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });

            // Update tombol Print dengan URL dinamis
            function updatePrintButton(startDate, endDate) {
                const printButton = document.getElementById('print-button');
                const baseUrl = `{{ route('laporan-beli.export-pdf') }}`;
                const params = new URLSearchParams();

                if (startDate && endDate) {
                    params.append('start_date', startDate);
                    params.append('end_date', endDate);
                }

                printButton.href = `${baseUrl}?${params.toString()}`;
            }

            // Filter Data Saat Tombol Diklik
            $('#filter-button').on('click', function() {
                const dateRange = $('#date_range').val();
                const [startDate, endDate] = dateRange.split(' - ');

                fetch(`{{ route('laporan-beli') }}?start_date=${startDate}&end_date=${endDate}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.querySelector('#data-table tbody');
                    tableBody.innerHTML = ''; // Clear existing rows

                    if (data.data.length === 0) {
                        const emptyRow = `
                            <tr class="border dark:border-gray-700">
                                <td colspan="6" class="px-4 py-2 border dark:border-gray-700 text-center">Tidak ada data</td>
                            </tr>
                        `;
                        tableBody.insertAdjacentHTML('beforeend', emptyRow);
                    } else {
                        data.data.forEach(item => {
                            const row = `
                                <tr class="border dark:border-gray-700">
                                    <td class="px-4 py-2 border dark:border-gray-700">${new Date(item.Tanggal).toLocaleDateString('id-ID')}</td>
                                    <td class="px-4 py-2 border dark:border-gray-700">${item['Nama Barang']}</td>
                                    <td class="px-4 py-2 border dark:border-gray-700">${item.Supplier}</td>
                                    <td class="px-4 py-2 border dark:border-gray-700">${item.Stok}</td>
                                    <td class="px-4 py-2 border dark:border-gray-700">${item.Satuan}</td>
                                    <td class="px-4 py-2 border dark:border-gray-700">Rp. ${parseFloat(item['Total Pengeluaran']).toLocaleString('id-ID')}</td>
                                </tr>
                            `;
                            tableBody.insertAdjacentHTML('beforeend', row);
                        });
                    }

                    document.getElementById('date-range').innerText = dateRange || 'Semua Tanggal';

                    const footerTotalCell = document.querySelector('#data-table tfoot td:last-child');
                    if (footerTotalCell) {
                        footerTotalCell.innerText = 'Rp. ' + parseFloat(data.totalSum || 0).toLocaleString('id-ID');
                    }

                    // Perbarui tombol print
                    updatePrintButton(startDate, endDate);
                });
            });
        });
    </script>
    @endpush
</x-filament-panels::page>
