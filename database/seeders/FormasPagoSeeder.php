<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormasPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formasPago = [
            ['id' => 1, 'descripcion' => 'Efectivo / Contraentrega'],
            ['id' => 2, 'descripcion' => 'Tarjeta de Crédito / Débito'],
            ['id' => 3, 'descripcion' => 'Transferencia Bancaria'],
        ];

        foreach ($formasPago as $forma) {
            DB::table('formas_pago')->updateOrInsert(
                ['id' => $forma['id']],
                ['descripcion' => $forma['descripcion'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
