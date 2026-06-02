<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categorías Padre
        $ropa = Categoria::firstOrCreate(
            ['nombre' => 'ropa'],
            ['parent_id' => null]
        );

        $accesorios = Categoria::firstOrCreate(
            ['nombre' => 'accesorios'],
            ['parent_id' => null]
        );

        // Subcategorías de Ropa
        $subcategoriasRopa = [
            'buzos',
            'sueteres',
            'pecheras',
            'impermeables',
            'camisetas',
            'pijamas'
        ];

        foreach ($subcategoriasRopa as $nombre) {
            Categoria::firstOrCreate([
                'nombre' => $nombre,
                'parent_id' => $ropa->id
            ]);
        }

        // Subcategorías de Accesorios
        $subcategoriasAccesorios = [
            'juguetes',
            'correas',
            'arneces',
            'camas'
        ];

        foreach ($subcategoriasAccesorios as $nombre) {
            Categoria::firstOrCreate([
                'nombre' => $nombre,
                'parent_id' => $accesorios->id
            ]);
        }
    }
}
