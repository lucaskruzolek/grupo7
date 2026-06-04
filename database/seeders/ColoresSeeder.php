<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Color;

class ColoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colores = [
            ['nombre' => 'Rojo', 'hex_code' => '#E63946'],
            ['nombre' => 'Azul', 'hex_code' => '#457B9D'],
            ['nombre' => 'Verde', 'hex_code' => '#2A9D8F'],
            ['nombre' => 'Amarillo', 'hex_code' => '#FFD166'],
            ['nombre' => 'Rosa', 'hex_code' => '#FFB3C6'],
            ['nombre' => 'Negro', 'hex_code' => '#1D3557'],
            ['nombre' => 'Blanco', 'hex_code' => '#FFFFFF'],
            ['nombre' => 'Gris', 'hex_code' => '#A8DADC'],
            ['nombre' => 'Naranja', 'hex_code' => '#F4A261'],
            ['nombre' => 'Violeta', 'hex_code' => '#9D4EDD'],
            ['nombre' => 'Único', 'hex_code' => '#CCCCCC'],
        ];

        foreach ($colores as $color) {
            Color::firstOrCreate(
                ['nombre' => $color['nombre']],
                ['hex_code' => $color['hex_code']]
            );
        }
    }
}
