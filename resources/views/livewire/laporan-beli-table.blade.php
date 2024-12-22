<div>
    <table class="w-full border border-gray-200 bg-white dark:bg-gray-800">
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
            @forelse ($data as $item)
                <tr class="border dark:border-gray-700">
                    <td class="px-4 py-2 border dark:border-gray-700">{{ \Carbon\Carbon::parse($item->Tanggal)->format('d/m/Y') }}</td>
                    <td class="px-4 py-2 border dark:border-gray-700">{{ $item->Nama Barang }}</td>
                    <td class="px-4 py-2 border dark:border-gray-700">{{ $item->Supplier }}</td>
                    <td class="px-4 py-2 border dark:border-gray-700">{{ $item->Stok }}</td>
                    <td class="px-4 py-2 border dark:border-gray-700">{{ $item->Satuan }}</td>
                    <td class="px-4 py-2 border dark:border-gray-700">Rp. {{ number_format($item->Total Pengeluaran, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr class="border dark:border-gray-700">
                    <td colspan="6" class="px-4 py-2 text-center">Tidak ada data</td>
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

    <div class="mt-4">
        {{ $data->links() }}
    </div>
</div>
