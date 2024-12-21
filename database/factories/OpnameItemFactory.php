<?php

namespace Database\Factories;

use App\Models\OpnameItem;
use App\Models\Opname;
use App\Models\Barang;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpnameItemFactory extends Factory
{
    protected $model = OpnameItem::class;

    public function definition()
    {
        return [
            'opname_id' => Opname::factory(),
            'barang_id' => Barang::factory(),
            'qty_sistem' => $this->faker->numberBetween(1, 500),
            'qty_fisik' => $this->faker->numberBetween(1, 500),
            'selisih' => function (array $attributes) {
                return $attributes['qty_fisik'] - $attributes['qty_sistem'];
            },
            'keterangan' => $this->faker->sentence(),
        ];
    }
}
