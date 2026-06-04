@extends('backend.admin.layout')

@section('styles')
    <!-- Estilos específicos de la gestión de ventas -->
    <link rel="stylesheet" href="{{ asset('css/backend/ventas.css') }}">
@endsection

@section('contenido')
<div class="container-fluid px-0 ventas-container">
    
    <!-- Encabezado Principal -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="section-title">Gestión de Ventas</h1>
        </div>
        <!-- Filtro Rango de Fechas (Mockup de interfaz) -->
        <div class="d-flex align-items-center gap-2">
            <span class="text-secondary" style="font-size: 0.85rem; font-weight: 500;">Período:</span>
            <select class="filter-select" style="min-width: 160px; height: 38px;">
                <option value="today">Hoy</option>
                <option value="7days">Últimos 7 días</option>
                <option value="month" selected>Este mes</option>
                <option value="custom">Rango personalizado</option>
            </select>
        </div>
    </div>

    <!-- 1. Fila de Tarjetas Informativas (KPIs) -->
    <div class="row kpi-row g-3">
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon-wrapper income">💵</div>
                <div class="kpi-info">
                    <span class="kpi-value">${{ number_format($totalIngresos, 2, ',', '.') }}</span>
                    <span class="kpi-title">Ingresos Totales</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon-wrapper orders">📦</div>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $cantidadVentas }}</span>
                    <span class="kpi-title">Cant. Ventas</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon-wrapper units">👕</div>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $productosVendidos }}</span>
                    <span class="kpi-title">Productos Vendidos</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <div class="kpi-icon-wrapper pending">⏳</div>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $pedidosPendientes }}</span>
                    <span class="kpi-title">Pedidos Pendientes</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Split Layout (Listado Master & Detalle) -->
    <div class="row master-detail-row">
        
        <!-- COLUMNA IZQUIERDA: Listado de Pedidos (Master) -->
        <div class="col-12 col-lg-7">
            <div class="orders-master-panel">
                
                <!-- Buscador y Filtros -->
                <div class="row mb-3 g-2 align-items-center">
                    <div class="col-12 col-sm-6">
                        <div class="input-group search-bar-wrapper mb-2">
                            <input type="text" id="search-pedido" class="form-control search-input" placeholder="Buscar por ID, cliente o email..." aria-label="Buscar ventas">
                            <button class="btn search-btn" type="button">
                                <img src="{{ asset('img/icons/search.svg') }}" alt="Buscar" style="width: 18px; height: 18px;">
                            </button>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <select id="filter-estado" class="filter-select">
                            <option value="all" selected>Todos</option>
                            <option value="CONFIRMADO">Confirmado</option>
                            <option value="DESPACHADO">Despachado</option>
                        </select>
                    </div>
                    <div class="col-6 col-sm-3">
                        <select id="filter-pago" class="filter-select">
                            <option value="all" selected>Cualquier pago</option>
                            @foreach ($formasPago as $fp)
                                <option value="{{ $fp->id }}">{{ $fp->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tabla de Listado -->
                <div class="table-responsive">
                    <table class="table admin-table orders-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Pedido</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th style="text-align: right;">Total</th>
                                <th style="text-align: center; width: 130px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ventas as $venta)
                                <tr data-order-id="{{ $venta->id }}" 
                                    data-estado="{{ $venta->estado }}" 
                                    data-pago-id="{{ $venta->forma_pago_id }}">
                                    <td>
                                        <span class="order-badge-id">#{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td>
                                        <div class="client-name">{{ $venta->usuario ? $venta->usuario->nombre . ' ' . $venta->usuario->apellido : 'Invitado' }}</div>
                                        <div class="client-email">{{ $venta->usuario ? $venta->usuario->email : 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <div class="date-main">{{ $venta->fecha_venta ? $venta->fecha_venta->format('d M Y') : $venta->created_at->format('d M Y') }}</div>
                                        <div class="date-sub">{{ $venta->fecha_venta ? $venta->fecha_venta->format('H:i \h\s') : $venta->created_at->format('H:i \h\s') }}</div>
                                    </td>
                                    <td style="text-align: right;">
                                        <span class="total-value">${{ number_format($venta->total, 2, ',', '.') }}</span>
                                    </td>
                                    <td style="text-align: center;">
                                        @if ($venta->estado === 'DESPACHADO')
                                            <span class="status-badge status-badge-active" style="background-color: rgba(42, 157, 143, 0.15); color: var(--green-600); font-weight: 600;">DESPACHADO</span>
                                        @else
                                            <span class="status-badge status-badge-pending" style="background-color: rgba(69, 123, 157, 0.15); color: #457B9D; font-weight: 600;">CONFIRMADO</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No se encontraron pedidos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $ventas->links() }}
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Detalle del Pedido Seleccionado -->
        <div class="col-12 col-lg-5">
            <div class="orders-detail-panel">
                
                <!-- Estado Vacío -->
                <div id="detail-empty" class="text-center py-5 text-muted">
                    <span style="font-size: 2.5rem; display: block; margin-bottom: 1rem;">📦</span>
                    <p class="mb-0 fw-medium">Selecciona un pedido del listado para ver su desglose detallado.</p>
                </div>

                <!-- Contenedor con Información Dinámica -->
                <div id="detail-content" class="d-none">
                    
                    <!-- ID de Pedido y botón factura -->
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                        <h2 class="h5 fw-bold mb-0 text-dark" id="det-order-id">#000000</h2>
                        <!-- Botón Descargar Factura -->
                        <a href="#" id="det-btn-factura" target="_blank" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 py-1.5 px-3 rounded-3 fw-semibold poppins-medium" style="font-size: 0.75rem;">
                            Factura 📄
                        </a>
                    </div>

                    <!-- Datos del Cliente -->
                    <div class="mb-4">
                        <h3 class="text-secondary uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">Cliente</h3>
                        <p class="mb-0 fw-bold text-dark" id="det-cliente-nombre" style="font-size: 0.9rem;">Juan Perez</p>
                        <p class="mb-0 text-muted" id="det-cliente-email" style="font-size: 0.8rem;">juan@test.com</p>
                    </div>

                    <!-- Dirección de Envío (Simulado / Mockup) -->
                    <div class="mb-4">
                        <h3 class="text-secondary uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">Dirección de Envío</h3>
                        <p class="mb-0 text-dark fw-medium" style="font-size: 0.85rem;">Av. Cabildo 123, Piso 3 Depto B</p>
                        <p class="mb-0 text-muted" style="font-size: 0.75rem;">Buenos Aires, Argentina (C1426)</p>
                    </div>

                    <!-- Método de Pago -->
                    <div class="mb-4">
                        <h3 class="text-secondary uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">Método de Pago</h3>
                        <p class="mb-0 text-dark fw-medium" id="det-forma-pago" style="font-size: 0.85rem;">Tarjeta de Crédito</p>
                    </div>

                    <!-- Detalles del Carrito / Ropa Comprada -->
                    <div class="mb-4">
                        <h3 class="text-secondary uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">Artículos del Pedido</h3>
                        <div id="det-items-container" style="max-height: 240px; overflow-y: auto;">
                            <!-- Dinámico -->
                        </div>
                    </div>

                    <!-- Total de Venta -->
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-4 mb-4" style="background-color: var(--neutral-100);">
                        <span class="fw-semibold text-secondary" style="font-size: 0.85rem;">Total Facturado</span>
                        <span class="fw-bold text-dark" id="det-total" style="font-size: 1.15rem;">$0,00</span>
                    </div>

                    <!-- Línea de Tiempo del Pedido -->
                    <div class="mb-4">
                        <h3 class="text-secondary uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">Línea de Tiempo del Pedido</h3>
                        <div class="order-timeline">
                            <!-- Carrito Creado -->
                            <div class="timeline-item completed">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <span class="timeline-title">🛒 Carrito creado</span>
                                    <span class="timeline-date" id="tl-carrito-date">-</span>
                                </div>
                            </div>
                            <!-- Pago Confirmado -->
                            <div id="tl-pago-item" class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <span class="timeline-title">💳 Pago confirmado</span>
                                    <span class="timeline-date" id="tl-pago-date">-</span>
                                </div>
                            </div>
                            <!-- Pedido Despachado -->
                            <div id="tl-despacho-item" class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <span class="timeline-title">📦 Pedido despachado</span>
                                    <span class="timeline-date" id="tl-despacho-date">Pendiente</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acción Rápida: Despachar / Revertir -->
                    <form id="det-form-estado" method="POST" action="">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="estado" id="det-input-estado" value="DESPACHADO">
                        <button type="submit" id="det-btn-submit-estado" class="btn-admin btn-admin-primary w-100 mt-3">
                            📦 Marcar como Despachado
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
    <!-- Script de interactividad AJAX y filtros -->
    <script src="{{ asset('js/backend/ventas.js') }}"></script>
@endsection
