<?php

namespace Database\Factories;

use App\Models\TransaksiKeluar;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransaksiKeluarFactory extends Factory
{
    protected $model = TransaksiKeluar::class;

    public function definition()
    {
        return [
            'total_harga' => $this->faker->numberBetween(5000, 500000),
            'nama_pembeli' => $this->faker->name(),
            'jenis_pembayaran' => $this->faker->randomElement(['Cash', 'Transfer', 'E-Wallet']),
            'tgl_penjualan' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'keterangan' => $this->faker->sentence(),
        ];
    }
}
