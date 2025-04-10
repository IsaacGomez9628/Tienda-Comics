<?php

namespace Database\Factories;

use App\Models\Estatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class EstatusFactory extends Factory
{
    protected $model = EstatusFactory::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->randomElement([
                'Activo', 'Inactivo', 'Pendiente', 'Cancelado', 
                'Agotado', 'Disponible', 'En reserva', 'Descontinuado'
            ]),
            'descripcion' => $this->faker->optional(0.7)->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
}