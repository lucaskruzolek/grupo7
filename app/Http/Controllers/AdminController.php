<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consulta;
use App\Models\Venta;
use Carbon\Carbon;

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

        // 2. Datos operacionales reales (Pedidos y Ventas)
        $ventasDelDia = Venta::ventas()
            ->whereDate('fecha_venta', Carbon::today())
            ->sum('total');

        $ventasDeAyer = Venta::ventas()
            ->whereDate('fecha_venta', Carbon::yesterday())
            ->sum('total');

        if ($ventasDeAyer > 0) {
            $variacionVentas = (($ventasDelDia - $ventasDeAyer) / $ventasDeAyer) * 100;
        } else {
            $variacionVentas = $ventasDelDia > 0 ? 100.0 : 0.0;
        }

        $pedidosPendientesCount = Venta::where('estado', 'CONFIRMADO')->count();

        // Clientes registrados hoy (rol cliente)
        $nuevosRegistros = \App\Models\Usuario::whereHas('rol', function($q) {
            $q->where('nombre', 'cliente');
        })->whereDate('created_at', Carbon::today())->count();

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
            'variacionVentas',
            'pedidosPendientesCount',
            'nuevosRegistros',
            'ultimosPedidos',
            'consultasRecientes'
        ));
    }



    public function clientes()
    {
        return view('backend.admin.clientes');
    }
}
