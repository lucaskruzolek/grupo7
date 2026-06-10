<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use App\Models\Consulta;
use App\Models\Usuario;
use App\Models\Venta;
use Carbon\Carbon;
 
class ConsultasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Simulando consultas de clientes...');
 
        // Obtener algunos usuarios registrados para asociar consultas
        $usuarios = Usuario::whereHas('rol', function($q) {
            $q->where('nombre', 'cliente');
        })->get();
 
        // Obtener algunas ventas reales para asociar números de pedidos
        $ventasIds = Venta::ventas()->pluck('id')->toArray();
 
        $consultasMock = [
            [
                'nombre' => 'María Luján',
                'email' => 'marialujan@example.com',
                'telefono' => '1198765432',
                'pedido' => null,
                'asunto' => 'consulta',
                'mensaje' => 'Hola! Quería consultar si tienen stock del buzo polar en color rojo pero talle XL. En la web solo veo hasta L. Gracias!',
                'leido' => false,
                'respondido' => false,
                'anonimo' => true,
                'horas_atras' => 1,
            ],
            [
                'nombre' => 'Carlos M.',
                'email' => 'carlos.m@yahoo.com.ar',
                'telefono' => '3412345678',
                'pedido' => null,
                'asunto' => 'consulta',
                'mensaje' => 'Buenas tardes, hacen envíos a Rosario? Si compro hoy, llega antes del viernes que es el cumpleaños de mi perro? Saludos.',
                'leido' => false,
                'respondido' => false,
                'anonimo' => true,
                'horas_atras' => 3,
            ],
            [
                'nombre' => 'Jorge Ramírez',
                'email' => 'jorge.ramirez@gmail.com',
                'telefono' => null,
                'pedido' => null,
                'asunto' => 'consulta',
                'mensaje' => 'Hola, quería saber si las pecheras son regulables en el cuello o solo en el pecho. Tengo un bulldog francés que es muy cabezón.',
                'leido' => true,
                'respondido' => true,
                'anonimo' => true,
                'horas_atras' => 24,
            ],
            [
                'nombre' => 'Casandra Villalobos',
                'email' => 'casandra.v@outlook.com',
                'telefono' => '1155443322',
                'pedido' => null,
                'asunto' => 'devolucion',
                'mensaje' => 'Hola equipo de Pet Threads, le compré el impermeable amarillo a mi caniche pero le queda muy chico de largo. Quisiera saber cómo gestionar la devolución o el cambio por un talle más grande.',
                'leido' => false,
                'respondido' => false,
                'anonimo' => false,
                'horas_atras' => 5,
            ],
            [
                'nombre' => 'Eloy Palacios',
                'email' => 'eloy.palacios@hotmail.com',
                'telefono' => null,
                'pedido' => null,
                'asunto' => 'reclamo',
                'mensaje' => 'Hola, hice un pago ayer por transferencia pero el estado del pedido sigue figurando como pendiente de pago. Les adjunto los datos por aquí por si acaso.',
                'leido' => false,
                'respondido' => false,
                'anonimo' => false,
                'horas_atras' => 8,
            ],
            [
                'nombre' => 'Lisandro Altamirano',
                'email' => 'lisandro_alta@gmail.com',
                'telefono' => '3794120987',
                'pedido' => null,
                'asunto' => 'reclamo',
                'mensaje' => 'Hola, el correo me dice que ya entregaron el paquete pero a mi domicilio no llegó nada. Podrían verificar a qué dirección lo mandaron?',
                'leido' => false,
                'respondido' => false,
                'anonimo' => false,
                'horas_atras' => 12,
            ],
            [
                'nombre' => 'Diana Peralta',
                'email' => 'dianaperalta@gmail.com',
                'telefono' => '1133221100',
                'pedido' => null,
                'asunto' => 'otro',
                'mensaje' => 'Hola! Quería saber si venden al por mayor para veterinarias. Tenemos locales en Zona Norte. Muchas gracias.',
                'leido' => true,
                'respondido' => true,
                'anonimo' => true,
                'horas_atras' => 48,
            ],
            [
                'nombre' => 'Nicanor Benavídez',
                'email' => 'nicanor.b@gmail.com',
                'telefono' => null,
                'pedido' => null,
                'asunto' => 'consulta',
                'mensaje' => 'Tienen local físico para ir a probarle la ropa a mi perro? Es una raza mestiza y se me complica tomarle las medidas.',
                'leido' => true,
                'respondido' => false,
                'anonimo' => false,
                'horas_atras' => 36,
            ],
            [
                'nombre' => 'Roberto Gómez',
                'email' => 'robertog@live.com',
                'telefono' => '1166778899',
                'pedido' => null,
                'asunto' => 'devolucion',
                'mensaje' => 'Quiero cancelar mi compra porque me confundí de color. Me enviaron confirmación hace 10 minutos.',
                'leido' => false,
                'respondido' => false,
                'anonimo' => true,
                'horas_atras' => 2,
            ],
            [
                'nombre' => 'Macarena Santillán',
                'email' => 'maca.santillan@hotmail.com',
                'telefono' => '3419998877',
                'pedido' => null,
                'asunto' => 'consulta',
                'mensaje' => 'Hola, se le puede agregar nombre bordado a los buzos? Vi fotos en instagram y quedaban hermosos!',
                'leido' => true,
                'respondido' => true,
                'anonimo' => false,
                'horas_atras' => 72,
            ],
        ];
 
        $periodos = [
            ['dias' => 0],   // Recientes (hoy)
            ['dias' => 1],   // Ayer (un día atrás)
            ['dias' => 7],   // Hace una semana atrás
            ['dias' => 30],  // Hace un mes atrás
            ['dias' => 90],  // Hace varios meses atrás
        ];
 
        foreach ($periodos as $periodo) {
            $diasOffset = $periodo['dias'];
 
            foreach ($consultasMock as $mock) {
                $usuarioId = null;
                if (!$mock['anonimo'] && $usuarios->isNotEmpty()) {
                    // Buscar el usuario correspondiente por email
                    $usuario = $usuarios->where('email', $mock['email'])->first();
                    if ($usuario) {
                        $usuarioId = $usuario->id;
                    }
                }
 
                // Asignar un número de pedido real aleatorio en el 40% de los casos
                $pedidoStr = null;
                if (rand(1, 100) <= 40 && !empty($ventasIds)) {
                    $pedidoRandomId = $ventasIds[array_rand($ventasIds)];
                    $pedidoStr = '#' . str_pad($pedidoRandomId, 5, '0', STR_PAD_LEFT);
                }
 
                // Determinar leido y respondido según la antigüedad
                if ($diasOffset === 0) {
                    $leido = $mock['leido'];
                    $respondido = $mock['respondido'];
                } else {
                    // Para consultas pasadas (dias > 0)
                    // La inmensa mayoría ya fueron leídas
                    $leido = (rand(1, 100) <= 95); 
                    if ($leido) {
                        // Si se leyó, hay alta probabilidad de haberse respondido (entre 75% y 95% según antigüedad)
                        $probRespondido = 70 + min(25, $diasOffset / 3);
                        $respondido = (rand(1, 100) <= $probRespondido);
                    } else {
                        $respondido = false;
                    }
                }
 
                $createdAt = Carbon::now()
                    ->subDays($diasOffset)
                    ->subHours($mock['horas_atras'])
                    ->subMinutes(rand(1, 59));
 
                Consulta::create([
                    'nombre' => $mock['nombre'],
                    'email' => $mock['email'],
                    'telefono' => $mock['telefono'],
                    'pedido' => $pedidoStr,
                    'asunto' => $mock['asunto'],
                    'mensaje' => $mock['mensaje'],
                    'leido' => $leido,
                    'respondido' => $respondido,
                    'usuario_id' => $usuarioId,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt->copy()->addMinutes(rand(5, 120)),
                ]);
            }
        }
 
        $this->command->info('Simulación de consultas finalizada con éxito.');
    }
}
