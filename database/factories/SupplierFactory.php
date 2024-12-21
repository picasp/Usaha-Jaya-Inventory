<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition()
    {
        return [
            'nama_supplier' => $this->faker->company(),
            'no_telp' => $this->faker->phoneNumber(),
            'alamat' => $this->faker->address(),
            'keterangan' => $this->faker->sentence(),
        ];
    }
}
