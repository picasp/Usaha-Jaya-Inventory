<?php

namespace Database\Factories;

use App\Models\TransaksiMasuk;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransaksiMasukFactory extends Factory
{
    protected $model = TransaksiMasuk::class;

    public function definition()
    {
        return [
            'supplier_id' => Supplier::factory(),
            'total_harga_masuk' => $this->faker->numberBetween(10000, 1000000),
            'tgl_pembelian' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'keterangan' => $this->faker->sentence(),
        ];
    }
}
