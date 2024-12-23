<?php

namespace Database\Factories;

use App\Models\Opname;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpnameFactory extends Factory
{
    protected $model = Opname::class;

    public function definition()
    {
        return [
            'tgl' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
