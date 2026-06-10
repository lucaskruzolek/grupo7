<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            FormasPagoSeeder::class,
            CategoriasSeeder::class,
            ColoresSeeder::class,
            ColeccionesSeeder::class,
            ProductosSeeder::class,
            UsuariosSeeder::class,
            VentasSeeder::class,
            ConsultasSeeder::class,
        ]);
    }
}
