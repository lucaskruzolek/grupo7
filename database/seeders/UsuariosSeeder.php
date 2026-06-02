<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener los roles
        $adminRol = Rol::where('nombre', 'admin')->first();
        $clienteRol = Rol::where('nombre', 'cliente')->first();

        if (!$adminRol || !$clienteRol) {
            $this->command->error('Los roles admin o cliente no existen. Asegúrate de ejecutar RolesSeeder primero.');
            return;
        }

        // Usuario Administrador
        Usuario::firstOrCreate(
            ['email' => 'amadeo.bermudez@gmail.com'],
            [
                'nombre' => 'Amadeo',
                'apellido' => 'Bermúdez',
                'password' => Hash::make('password'),
                'rol_id' => $adminRol->id
            ]
        );

        // Usuarios Clientes
        $clientes = [
            [
                'nombre' => 'Casandra',
                'apellido' => 'Villalobos',
                'email' => 'casandra.v@outlook.com',
                'password' => Hash::make('password'),
                'rol_id' => $clienteRol->id
            ],
            [
                'nombre' => 'Lisandro',
                'apellido' => 'Altamirano',
                'email' => 'lisandro_alta@gmail.com',
                'password' => Hash::make('password'),
                'rol_id' => $clienteRol->id
            ],
            [
                'nombre' => 'Eloy',
                'apellido' => 'Palacios',
                'email' => 'eloy.palacios@hotmail.com',
                'password' => Hash::make('password'),
                'rol_id' => $clienteRol->id
            ],
            [
                'nombre' => 'Nicanor',
                'apellido' => 'Benavídez',
                'email' => 'nicanor.b@gmail.com',
                'password' => Hash::make('password'),
                'rol_id' => $clienteRol->id
            ],
            [
                'nombre' => 'Aureliano',
                'apellido' => 'Buendía',
                'email' => 'aureliano.buendia@outlook.com',
                'password' => Hash::make('password'),
                'rol_id' => $clienteRol->id
            ],
            [
                'nombre' => 'Macarena',
                'apellido' => 'Santillán',
                'email' => 'maca.santillan@hotmail.com',
                'password' => Hash::make('password'),
                'rol_id' => $clienteRol->id
            ],
        ];

        foreach ($clientes as $cliente) {
            Usuario::firstOrCreate(
                ['email' => $cliente['email']],
                $cliente
            );
        }
    }
}
