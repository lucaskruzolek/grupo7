<?php

namespace App\Http\Controllers;

use App\Models\FormaPago;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    // ── GESTIÓN DEL CARRITO (Frontend / Público) ──────────────────────────

    /**
     * Muestra el carrito activo del usuario autenticado.
     */
    public function verCarrito()
    {
        $userId = auth()->id();
        $carrito = Venta::where('usuario_id', $userId)
            ->carrito()
            ->with(['detalles.producto.color', 'detalles.producto.imagenes'])
            ->first();

        $formasPago = FormaPago::all();

        return view('frontend.carrito', compact('carrito', 'formasPago'));
    }

    /**
     * Agrega una variante de producto al carrito.
     */
    public function agregarAlCarrito(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad'    => 'required|integer|min:1',
        ]);

        $userId = auth()->id();
        $producto = Producto::findOrFail($request->producto_id);
        $cantidadAñadir = intval($request->cantidad);

        // Verificar stock físico inicial
        if ($producto->stock < $cantidadAñadir) {
            return $this->responseWithError($request, 'No hay suficiente stock disponible para este producto.');
        }

        // Buscar o crear carrito activo del usuario
        $carrito = Venta::firstOrCreate(
            ['usuario_id' => $userId, 'estado' => 'CARRITO'],
            ['total' => 0.00]
        );

        // Buscar si la variante ya existe en el carrito
        $detalle = VentaDetalle::where('venta_id', $carrito->id)
            ->where('producto_id', $producto->id)
            ->first();

        if ($detalle) {
            $nuevaCantidad = $detalle->cantidad + $cantidadAñadir;
            if ($producto->stock < $nuevaCantidad) {
                return $this->responseWithError($request, 'No puedes agregar esa cantidad. Excede el stock disponible.');
            }
            $detalle->update([
                'cantidad' => $nuevaCantidad,
                'subtotal' => $nuevaCantidad * $producto->precio,
            ]);
        } else {
            VentaDetalle::create([
                'venta_id'        => $carrito->id,
                'producto_id'     => $producto->id,
                'cantidad'        => $cantidadAñadir,
                'precio_unitario' => $producto->precio,
                'subtotal'        => $cantidadAñadir * $producto->precio,
            ]);
        }

        // Recalcular total de la cabecera
        $this->recalcularTotal($carrito);

        return $this->responseWithSuccess($request, 'Producto agregado al carrito con éxito.', 'carrito.ver');
    }

    /**
     * Actualiza la cantidad de un item del carrito.
     */
    public function actualizarCantidad(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $detalle = VentaDetalle::findOrFail($id);
        $carrito = $detalle->venta;

        // Verificar propiedad del carrito
        if ($carrito->usuario_id !== auth()->id() || $carrito->estado !== 'CARRITO') {
            return $this->responseWithError($request, 'Operación no autorizada.');
        }

        $producto = $detalle->producto;
        $nuevaCantidad = intval($request->cantidad);

        // Verificar stock disponible
        if ($producto->stock < $nuevaCantidad) {
            return $this->responseWithError($request, 'No hay suficiente stock disponible.');
        }

        $detalle->update([
            'cantidad' => $nuevaCantidad,
            'subtotal' => $nuevaCantidad * $detalle->precio_unitario,
        ]);

        $this->recalcularTotal($carrito);

        return $this->responseWithSuccess($request, 'Carrito actualizado con éxito.', 'carrito.ver');
    }

    /**
     * Elimina un item del carrito.
     */
    public function eliminarDelCarrito(Request $request, $id)
    {
        $detalle = VentaDetalle::findOrFail($id);
        $carrito = $detalle->venta;

        if ($carrito->usuario_id !== auth()->id() || $carrito->estado !== 'CARRITO') {
            return $this->responseWithError($request, 'Operación no autorizada.');
        }

        $detalle->delete();

        $this->recalcularTotal($carrito);

        return $this->responseWithSuccess($request, 'Producto eliminado del carrito.', 'carrito.ver');
    }

    /**
     * Procesa la compra (Checkout). Transición de CARRITO a CONFIRMADO.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'forma_pago_id' => 'required|exists:formas_pago,id',
        ]);

        $userId = auth()->id();

        try {
            $resultado = DB::transaction(function () use ($userId, $request) {
                // Obtener el carrito bloqueando los detalles y productos para prevenir condiciones de carrera
                $carrito = Venta::where('usuario_id', $userId)
                    ->carrito()
                    ->first();

                if (!$carrito || $carrito->detalles->isEmpty()) {
                    throw new \Exception('El carrito está vacío.');
                }

                foreach ($carrito->detalles as $detalle) {
                    // Cargar el producto bloqueándolo para escritura temporal
                    $producto = Producto::where('id', $detalle->producto_id)
                        ->lockForUpdate()
                        ->first();

                    if ($producto->stock < $detalle->cantidad) {
                        throw new \Exception("Stock insuficiente para el producto: {$producto->nombre} ({$producto->talle})");
                    }

                    // Decrementar stock
                    $producto->decrement('stock', $detalle->cantidad);
                }

                // Confirmar cabecera de venta
                $carrito->update([
                    'estado'        => 'CONFIRMADO',
                    'fecha_venta'   => now(),
                    'forma_pago_id' => $request->forma_pago_id,
                ]);

                return $carrito;
            });

            return $this->responseWithSuccess($request, '¡Muchas gracias por tu compra! El pedido ha sido confirmado.', 'inicio');

        } catch (\Exception $e) {
            return $this->responseWithError($request, $e->getMessage());
        }
    }

    // ── ADMINISTRACIÓN DE VENTAS (Backend) ───────────────────────────────

    /**
     * Listado administrativo de ventas.
     */
    public function adminIndex(Request $request)
    {
        $period = $request->input('period', 'all');
        $startDate = null;
        $endDate = null;

        if ($period !== 'all') {
            switch ($period) {
                case 'today':
                    $startDate = \Carbon\Carbon::today()->startOfDay();
                    $endDate = \Carbon\Carbon::today()->endOfDay();
                    break;
                case '7days':
                    $startDate = \Carbon\Carbon::today()->subDays(6)->startOfDay();
                    $endDate = \Carbon\Carbon::today()->endOfDay();
                    break;
                case 'month':
                    $startDate = \Carbon\Carbon::today()->startOfMonth()->startOfDay();
                    $endDate = \Carbon\Carbon::today()->endOfMonth()->endOfDay();
                    break;
                case 'custom':
                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        $startDate = \Carbon\Carbon::parse($request->input('start_date'))->startOfDay();
                        $endDate = \Carbon\Carbon::parse($request->input('end_date'))->endOfDay();
                    } else {
                        // Si es custom pero no hay fechas, caemos por defecto en el mes actual
                        $startDate = \Carbon\Carbon::today()->startOfMonth()->startOfDay();
                        $endDate = \Carbon\Carbon::today()->endOfMonth()->endOfDay();
                    }
                    break;
            }
        }

        // Construir queries base
        $queryIngresos = Venta::ventas();
        $queryVentas = Venta::ventas();
        $queryPendientes = Venta::where('estado', 'CONFIRMADO');
        $queryProductos = VentaDetalle::whereHas('venta', function ($q) use ($startDate, $endDate) {
            $q->ventas();
            if ($startDate && $endDate) {
                $q->whereBetween('fecha_venta', [$startDate, $endDate]);
            }
        });
        
        $queryVentasList = Venta::ventas()
            ->with(['usuario', 'formaPago'])
            ->orderBy('fecha_venta', 'desc');

        // Aplicar filtros de fecha si aplican
        if ($startDate && $endDate) {
            $queryIngresos->whereBetween('fecha_venta', [$startDate, $endDate]);
            $queryVentas->whereBetween('fecha_venta', [$startDate, $endDate]);
            $queryPendientes->whereBetween('created_at', [$startDate, $endDate]);
            $queryVentasList->whereBetween('fecha_venta', [$startDate, $endDate]);
        }

        // Aplicar filtro por estado si aplica
        if ($request->filled('estado') && $request->input('estado') !== 'all') {
            $queryVentasList->where('estado', $request->input('estado'));
        }

        // Aplicar filtro por forma de pago si aplica
        if ($request->filled('pago') && $request->input('pago') !== 'all') {
            $queryVentasList->where('forma_pago_id', $request->input('pago'));
        }

        // Aplicar filtro por búsqueda de texto
        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $searchClean = ltrim($request->input('search'), '#');
            $searchCleanLike = '%' . $searchClean . '%';

            $queryVentasList->where(function ($q) use ($search, $searchCleanLike) {
                $q->where('id', 'like', $searchCleanLike)
                  ->orWhereHas('usuario', function ($uQuery) use ($search) {
                      $uQuery->where('nombre', 'like', $search)
                             ->orWhere('apellido', 'like', $search)
                             ->orWhere('email', 'like', $search);
                      
                      $driver = DB::connection()->getDriverName();
                      if ($driver === 'sqlite') {
                          $uQuery->orWhereRaw("nombre || ' ' || apellido LIKE ?", [$search]);
                      } else {
                          $uQuery->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", [$search]);
                      }
                  });
            });
        }

        // Obtener KPIs agregados
        $totalIngresos = $queryIngresos->sum('total');
        $cantidadVentas = $queryVentas->count();
        $pedidosPendientes = $queryPendientes->count();
        $productosVendidos = $queryProductos->sum('cantidad');

        $ventas = $queryVentasList->paginate(10);

        $formasPago = FormaPago::all();

        return view('backend.admin.ventas', compact(
            'ventas',
            'formasPago',
            'totalIngresos',
            'cantidadVentas',
            'productosVendidos',
            'pedidosPendientes'
        ));
    }

    /**
     * Detalle administrativo de una venta (Soporta vistas de Blade tradicionales y respuestas JSON AJAX).
     */
    public function adminShow(Request $request, $id)
    {
        $venta = Venta::ventas()
            ->with(['usuario', 'formaPago', 'detalles.producto.color', 'detalles.producto.imagenes'])
            ->findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'pedido' => [
                    'id' => $venta->id,
                    'fecha_venta' => $venta->fecha_venta ? $venta->fecha_venta->format('d M Y H:i \h\s') : null,
                    'fecha_despacho' => $venta->fecha_despacho ? $venta->fecha_despacho->format('d M Y H:i \h\s') : null,
                    'created_at' => $venta->created_at ? $venta->created_at->format('d M Y H:i \h\s') : null,
                    'estado' => $venta->estado,
                    'total' => $venta->total,
                    'forma_pago' => $venta->formaPago ? $venta->formaPago->descripcion : 'No especificada',
                    'cliente' => $venta->usuario ? [
                        'nombre' => $venta->usuario->nombre . ' ' . $venta->usuario->apellido,
                        'email' => $venta->usuario->email,
                    ] : [
                        'nombre' => 'Invitado',
                        'email' => 'N/A',
                    ],
                    'detalles' => $venta->detalles->map(function ($det) {
                        $img = $det->producto->imagenes->sortBy('orden')->first();
                        return [
                            'producto' => $det->producto->nombre,
                            'talle' => $det->producto->talle,
                            'color' => $det->producto->color ? $det->producto->color->nombre : 'N/A',
                            'cantidad' => $det->cantidad,
                            'precio_unitario' => $det->precio_unitario,
                            'subtotal' => $det->subtotal,
                            'imagen' => $img ? $img->url : null,
                        ];
                    }),
                ]
            ]);
        }

        return view('backend.admin.venta_detalle', compact('venta'));
    }

    /**
     * Actualiza el estado de un venta (por parte del administrador) y registra marcas de tiempo.
     */
    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:CONFIRMADO,DESPACHADO',
        ]);

        $venta = Venta::ventas()->findOrFail($id);
        $venta->update([
            'estado' => $request->estado,
            'fecha_despacho' => $request->estado === 'DESPACHADO' ? now() : null
        ]);

        return redirect()->back()->with('exito', 'El estado del pedido fue actualizado.');
    }

    /**
     * Muestra la factura comercial del pedido lista para imprimir.
     */
    public function descargarFactura($id)
    {
        $venta = Venta::ventas()
            ->with(['usuario', 'formaPago', 'detalles.producto.color'])
            ->findOrFail($id);

        return view('backend.admin.factura', compact('venta'));
    }

    // ── MÉTODOS AUXILIARES ────────────────────────────────────────────────

    /**
     * Recalcula el total de la venta sumando los subtotales de sus detalles.
     */
    private function recalcularTotal(Venta $venta)
    {
        $total = $venta->detalles()->sum('subtotal');
        $venta->update(['total' => $total]);
    }

    /**
     * Retorna respuesta unificada de éxito según el tipo de solicitud.
     */
    private function responseWithSuccess(Request $request, string $mensaje, string $routeRedirect)
    {
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $mensaje]);
        }
        return redirect()->route($routeRedirect)->with('exito', $mensaje);
    }

    /**
     * Retorna respuesta unificada de error según el tipo de solicitud.
     */
    private function responseWithError(Request $request, string $mensaje)
    {
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $mensaje], 400);
        }
        return redirect()->back()->withErrors([$mensaje])->withInput();
    }
}
