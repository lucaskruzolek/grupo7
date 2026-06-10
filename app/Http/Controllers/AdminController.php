<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consulta;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. Obtener productos reales con stock bajo o igual al mínimo
        $productosBajoStock = \App\Models\Producto::whereColumn('stock', '<=', 'stock_minimo')
            ->with('imagenPortada')
            ->select('sku_base', 'nombre', 'stock', 'sku', 'stock_minimo')
            ->take(3)
            ->get();

        // Contar el total de alertas de stock bajo en el sistema
        $countBajoStock = \App\Models\Producto::whereColumn('stock', '<=', 'stock_minimo')->count();

        // 2. Simulación de datos operacionales (Pedidos y Ventas)
        $ventasDelDia = 48500.00;
        $pedidosPendientesCount = 5;

        $ultimosPedidos = [
            ['usuario' => 'Juan Pérez', 'n_pedido' => '1024', 'estado' => 'Pendiente', 'monto' => 12500.00, 'fecha' => '29/05/2026'],
            ['usuario' => 'Ana Gómez', 'n_pedido' => '1023', 'estado' => 'Completado', 'monto' => 18200.00, 'fecha' => '28/05/2026'],
            ['usuario' => 'Carlos M.', 'n_pedido' => '1022', 'estado' => 'Pendiente', 'monto' => 9500.00, 'fecha' => '28/05/2026'],
            ['usuario' => 'María Luz', 'n_pedido' => '1021', 'estado' => 'Completado', 'monto' => 24500.00, 'fecha' => '27/05/2026'],
            ['usuario' => 'Lucas K.', 'n_pedido' => '1020', 'estado' => 'Cancelado', 'monto' => 15000.00, 'fecha' => '26/05/2026'],
        ];

        // 3. Consultas reales no leídas
        $consultasRecientes = Consulta::where('leido', false)
            ->latest()
            ->take(5)
            ->get();

        return view('backend.admin.dashboard', compact(
            'productosBajoStock',
            'countBajoStock',
            'ventasDelDia',
            'pedidosPendientesCount',
            'ultimosPedidos',
            'consultasRecientes'
        ));
    }



    public function clientes()
    {
        return view('backend.admin.clientes');
    }
}
