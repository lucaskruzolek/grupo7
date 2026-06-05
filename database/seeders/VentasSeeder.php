<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Producto;
use App\Models\FormaPago;
use Carbon\Carbon;

class VentasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Obtener el rol de cliente para filtrar los usuarios
        $clienteRol = Rol::where('nombre', 'cliente')->first();
        if (!$clienteRol) {
            $this->command->error('El rol "cliente" no existe. Ejecuta primero RolesSeeder.');
            return;
        }

        $clientes = Usuario::where('rol_id', $clienteRol->id)->get();
        if ($clientes->isEmpty()) {
            $this->command->error('No hay usuarios clientes cargados. Ejecuta primero UsuariosSeeder.');
            return;
        }

        // 2. Obtener los productos disponibles
        $productos = Producto::all();
        if ($productos->isEmpty()) {
            $this->command->error('No hay productos cargados. Ejecuta primero ProductosSeeder.');
            return;
        }

        // 3. Obtener formas de pago
        $formasPagoIds = FormaPago::pluck('id')->toArray();
        if (empty($formasPagoIds)) {
            $this->command->error('No hay formas de pago cargadas. Ejecuta primero FormasPagoSeeder.');
            return;
        }

        $this->command->info('Simulando ventas y detalles...');

        // 4. Configurar cantidad de ventas a simular
        $cantidadVentas = 120; // Cantidad de transacciones a generar

        for ($i = 0; $i < $cantidadVentas; $i++) {
            // Seleccionar un cliente aleatorio
            $cliente = $clientes->random();

            // Determinar estado de la venta con distribución probabilística
            // 15% Carrito, 35% Confirmado (pagado pero no despachado), 50% Despachado
            $randEstado = rand(1, 100);
            if ($randEstado <= 15) {
                $estado = 'CARRITO';
            } elseif ($randEstado <= 50) {
                $estado = 'CONFIRMADO';
            } else {
                $estado = 'DESPACHADO';
            }

            // Fechas y formas de pago según el estado
            $fechaCreacion = null;
            $fechaVenta = null;
            $fechaDespacho = null;
            $formaPagoId = null;

            if ($estado === 'CARRITO') {
                // Carritos activos son recientes (últimos 3 días)
                $fechaCreacion = Carbon::now()->subHours(rand(1, 72));
            } else {
                // Ventas confirmadas o despachadas (últimos 90 días)
                $diasAtras = rand(1, 90);
                $fechaVenta = Carbon::now()->subDays($diasAtras)->subHours(rand(1, 23))->subMinutes(rand(1, 59));
                $fechaCreacion = $fechaVenta->copy()->subMinutes(rand(5, 45)); // Creada poco antes de pagarse
                $formaPagoId = $formasPagoIds[array_rand($formasPagoIds)];

                if ($estado === 'DESPACHADO') {
                    // Despachada entre 6 y 48 horas después del pago
                    $fechaDespacho = $fechaVenta->copy()->addHours(rand(6, 48));
                }
            }

            // Crear la cabecera de la venta
            $venta = Venta::create([
                'usuario_id' => $cliente->id,
                'estado' => $estado,
                'fecha_venta' => $fechaVenta,
                'fecha_despacho' => $fechaDespacho,
                'forma_pago_id' => $formaPagoId,
                'total' => 0.00, // Se calculará sumando los detalles
                'created_at' => $fechaCreacion,
                'updated_at' => $fechaCreacion ? $fechaCreacion->copy()->addMinutes(rand(1, 120)) : Carbon::now(),
            ]);

            // Determinar cuántos productos diferentes tendrá esta venta (1 a 4 items)
            $cantItems = rand(1, 4);
            $productosVenta = $productos->random(min($cantItems, $productos->count()));

            $totalVenta = 0.00;

            foreach ($productosVenta as $producto) {
                $cantidad = rand(1, 3); // De 1 a 3 unidades de este producto
                $precioUnitario = $producto->precio;
                $subtotal = $precioUnitario * $cantidad;

                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotal,
                    'created_at' => $fechaCreacion ?: Carbon::now(),
                    'updated_at' => $fechaCreacion ?: Carbon::now(),
                ]);

                $totalVenta += $subtotal;
            }

            // Actualizar el total de la venta con la suma de los detalles
            $venta->update([
                'total' => $totalVenta
            ]);
        }

        $this->command->info("Se han simulado con éxito {$cantidadVentas} ventas.");
    }
}
