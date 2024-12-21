<?php

namespace Database\Factories;

use App\Models\Barang;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangFactory extends Factory
{
    protected $model = Barang::class;

    public function definition()
    {
        return [
            'nama_barang' => $this->faker->word(),
            'stok' => $this->faker->numberBetween(10, 500),
            'satuan' => $this->faker->randomElement(['Pcs', 'Kg', 'Ltr']),
            'harga_jual' => $this->faker->numberBetween(1000, 100000),
            'harga_beli' => $this->faker->numberBetween(1000, 80000),
            'keterangan' => $this->faker->sentence(),
        ];
    }
}
