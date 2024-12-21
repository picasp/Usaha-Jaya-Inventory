<?php

namespace Database\Factories;

use App\Models\TransaksiKeluarItem;
use App\Models\TransaksiKeluar;
use App\Models\Barang;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransaksiKeluarItemFactory extends Factory
{
    protected $model = TransaksiKeluarItem::class;

    public function definition()
    {
        return [
            'transaksi_keluar_id' => TransaksiKeluar::factory(),
            'barang_id' => Barang::factory(),
            'qty' => $this->faker->numberBetween(1, 50),
            'harga' => $this->faker->numberBetween(1000, 50000),
            'total' => function (array $attributes) {
                return $attributes['qty'] * $attributes['harga'];
            },
        ];
    }
}
