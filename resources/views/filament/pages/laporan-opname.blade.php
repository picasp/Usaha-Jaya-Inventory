<x-filament-panels::page>
<head>
</head>
    <div class="p-4">
        <div class="text-center mb-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Laporan Opname Barang UD Usaha Jaya</h1>
            <p class="text-gray-600 dark:text-gray-400">Dibuat pada: {{ now()->format('d-m-Y H:i:s') }}</p>
            <p class="text-gray-600 dark:text-gray-400">Rentang Tanggal: <span id="date-range">{{ $dateRange ?? 'Semua Tanggal' }}</span></p>
        </div>
        <div class="overflow-x-auto">
            {{ $this->table}}
        </div>
    </div>
</x-filament-panels::page>
