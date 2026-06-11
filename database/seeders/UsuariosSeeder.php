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
                'rol_id' => $adminRol->id,
                'telefono' => '1145678901',
                'direccion' => 'Av. Cabildo 1500, Piso 2 B',
                'localidad' => 'Belgrano',
                'provincia' => 'CABA',
                'codigo_postal' => 'C1426'
            ]
        );

        // Usuarios Clientes con datos de Ecommerce simulados
        $clientes = [
            [
                'nombre' => 'Casandra',
                'apellido' => 'Villalobos',
                'email' => 'casandra.v@outlook.com',
                'password' => Hash::make('password'),
                'rol_id' => $clienteRol->id,
                'telefono' => '1154823901',
                'direccion' => 'Av. Santa Fe 3421, Piso 4 A',
                'localidad' => 'Palermo',
                'provincia' => 'CABA',
                'codigo_postal' => 'C1425'
            ],
            [
                'nombre' => 'Lucas',
                'apellido' => 'Kowalk',
                'email' => 'lucasjk23@gmail.com',
                'password' => Hash::make('lucas123'),
                'rol_id' => $adminRol->id,
                'telefono' => '1163332549',
                'direccion' => 'Av. Santa Fe 3421, Piso 4 A',
                'localidad' => 'Capital',
                'provincia' => 'Mendoza',
                'codigo_postal' => 'M5500'
            ],
            [
                'nombre' => 'Lisandro',
                'apellido' => 'Altamirano',
                'email' => 'lisandro_alta@gmail.com',
                'password' => Hash::make('password'),
                'rol_id' => $clienteRol->id,
                'telefono' => '1130987654',
                'direccion' => 'Chacabuco 892',
                'localidad' => 'San Telmo',
                'provincia' => 'CABA',
                'codigo_postal' => 'C1069'
            ],
            [
                'nombre' => 'Eloy',
                'apellido' => 'Palacios',
                'email' => 'eloy.palacios@hotmail.com',
                'password' => Hash::make('password'),
                'rol_id' => $clienteRol->id,
                'telefono' => '1165432109',
                'direccion' => 'Av. Rivadavia 5210, Piso 10 B',
                'localidad' => 'Caballito',
                'provincia' => 'CABA',
                'codigo_postal' => 'C1424'
            ],
            [
                'nombre' => 'Nicanor',
                'apellido' => 'Benavídez',
                'email' => 'nicanor.b@gmail.com',
                'password' => Hash::make('password'),
                'rol_id' => $clienteRol->id,
                'telefono' => '3415987654',
                'direccion' => 'Bv. Oroño 1230',
                'localidad' => 'Rosario',
                'provincia' => 'Santa Fe',
                'codigo_postal' => 'S2000'
            ],
            [
                'nombre' => 'Aureliano',
                'apellido' => 'Buendía',
                'email' => 'aureliano.buendia@outlook.com',
                'password' => Hash::make('password'),
                'rol_id' => $clienteRol->id,
                'telefono' => '3514765432',
                'direccion' => 'Av. Colón 450, Depto 2',
                'localidad' => 'Córdoba',
                'provincia' => 'Córdoba',
                'codigo_postal' => 'X5000'
            ],
            [
                'nombre' => 'Macarena',
                'apellido' => 'Santillán',
                'email' => 'maca.santillan@hotmail.com',
                'password' => Hash::make('password'),
                'rol_id' => $clienteRol->id,
                'telefono' => '2613987654',
                'direccion' => 'San Martín 1500',
                'localidad' => 'Mendoza',
                'provincia' => 'Mendoza',
                'codigo_postal' => 'M5500'
            ],
        ];

        foreach ($clientes as $cliente) {
            // Buscamos por email y actualizamos o creamos con los datos completos
            Usuario::updateOrCreate(
                ['email' => $cliente['email']],
                $cliente
            );
        }
    }
}
