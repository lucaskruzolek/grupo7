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
            ->take(3)
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

    public function chartData(Request $request)
    {
        $type = $request->query('type', 'sales');
        $period = $request->query('period', 'mes');

        // Determinar rango de fecha
        switch ($period) {
            case 'semana':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                break;
            case 'año':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                break;
            case 'mes':
            default:
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
        }

        $labels = [];
        $data = [];
        $datasets = [];

        if ($type === 'sales') {
            if ($period === 'semana') {
                $ventas = Venta::ventas()
                    ->whereBetween('fecha_venta', [$start, $end])
                    ->selectRaw('DAYOFWEEK(fecha_venta) as dia, SUM(total) as total')
                    ->groupBy('dia')
                    ->pluck('total', 'dia');

                $dias = [
                    2 => 'Lunes',
                    3 => 'Martes',
                    4 => 'Miércoles',
                    5 => 'Jueves',
                    6 => 'Viernes',
                    7 => 'Sábado',
                    1 => 'Domingo'
                ];
                foreach ($dias as $num => $nombre) {
                    $labels[] = $nombre;
                    $data[] = floatval($ventas->get($num, 0));
                }
            } elseif ($period === 'año') {
                $ventas = Venta::ventas()
                    ->whereBetween('fecha_venta', [$start, $end])
                    ->selectRaw('MONTH(fecha_venta) as mes_num, SUM(total) as total')
                    ->groupBy('mes_num')
                    ->pluck('total', 'mes_num');

                $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                foreach ($meses as $index => $nombre) {
                    $mesNum = $index + 1;
                    $labels[] = $nombre;
                    $data[] = floatval($ventas->get($mesNum, 0));
                }
            } else { // mes
                $ventas = Venta::ventas()
                    ->whereBetween('fecha_venta', [$start, $end])
                    ->selectRaw('FLOOR((DAY(fecha_venta) - 1) / 7) + 1 as semana_num, SUM(total) as total')
                    ->groupBy('semana_num')
                    ->pluck('total', 'semana_num');

                $labels = ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'];
                for ($i = 1; $i <= 4; $i++) {
                    $data[] = floatval($ventas->get($i, 0));
                }
                if ($ventas->has(5)) {
                    $labels[] = 'Semana 5';
                    $data[] = floatval($ventas->get(5));
                }
            }

            $datasets[] = [
                'label' => 'Ventas ($)',
                'data' => $data,
                'borderColor' => '#7d8c78',
                'backgroundColor' => 'rgba(125, 140, 120, 0.1)',
                'borderWidth' => 3,
                'fill' => true,
                'tension' => 0.3,
                'pointBackgroundColor' => '#7d8c78',
                'pointBorderColor' => '#fff',
                'pointHoverRadius' => 6
            ];

        } elseif ($type === 'categories') {
            $categoriasVentas = \App\Models\VentaDetalle::whereHas('venta', function($q) use ($start, $end) {
                    $q->ventas()->whereBetween('fecha_venta', [$start, $end]);
                })
                ->join('productos', 'venta_detalles.producto_id', '=', 'productos.id')
                ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
                ->selectRaw('categorias.nombre as categoria, SUM(venta_detalles.subtotal) as total')
                ->groupBy('categorias.id', 'categorias.nombre')
                ->orderByDesc('total')
                ->get();

            $labels = $categoriasVentas->pluck('categoria')->toArray();
            $data = $categoriasVentas->pluck('total')->map(fn($val) => floatval($val))->toArray();

            $datasets[] = [
                'data' => $data,
                'backgroundColor' => ['#7d8c78', '#c89d7c', '#f2d6b5', '#4a5347', '#a9b8a6', '#dcdcdc'],
                'borderWidth' => 2,
                'borderColor' => '#fff'
            ];

        } elseif ($type === 'products') {
            $productosMasVendidos = \App\Models\VentaDetalle::whereHas('venta', function($q) use ($start, $end) {
                    $q->ventas()->whereBetween('fecha_venta', [$start, $end]);
                })
                ->join('productos', 'venta_detalles.producto_id', '=', 'productos.id')
                ->selectRaw("SUBSTRING_INDEX(productos.nombre, ' - ', 1) as nombre_base, SUM(venta_detalles.cantidad) as total_unidades")
                ->groupBy('productos.sku_base', 'nombre_base')
                ->orderByDesc('total_unidades')
                ->take(5)
                ->get();

            $labels = $productosMasVendidos->pluck('nombre_base')->toArray();
            $data = $productosMasVendidos->pluck('total_unidades')->map(fn($val) => intval($val))->toArray();

            $datasets[] = [
                'label' => 'Unidades Vendidas',
                'data' => $data,
                'backgroundColor' => '#c89d7c',
                'borderRadius' => 6,
                'borderWidth' => 0
            ];
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => $datasets
        ]);
    }
}
