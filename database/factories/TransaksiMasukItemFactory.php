<?php

namespace Database\Factories;

use App\Models\TransaksiMasukItem;
use App\Models\TransaksiMasuk;
use App\Models\Barang;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransaksiMasukItemFactory extends Factory
{
    protected $model = TransaksiMasukItem::class;

    public function definition()
    {
        return [
            'transaksi_masuk_id' => TransaksiMasuk::factory(),
            'barang_id' => Barang::factory(),
            'qty' => $this->faker->numberBetween(1, 100),
            'satuan' => $this->faker->randomElement(['Pcs', 'Box', 'Kg']),
            'harga_beli' => $this->faker->numberBetween(1000, 20000),
            'total' => function (array $attributes) {
                return $attributes['qty'] * $attributes['harga_beli'];
            },
        ];
    }
}
