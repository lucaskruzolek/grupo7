@extends('backend.admin.layout')

@section('styles')
    <!-- Estilos específicos de la gestión de ventas -->
    <link rel="stylesheet" href="{{ asset('css/backend/ventas.css') }}">
@endsection

@section('contenido')
<div class="container-fluid px-0 ventas-container">
    
    <!-- Encabezado Principal -->
    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-3">
        <div>
            <h1 class="section-title">Gestión de Ventas</h1>
        </div>
        <!-- Filtro Rango de Fechas -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-secondary" style="font-size: 0.85rem; font-weight: 500;">Período:</span>
            <select id="filter-period" class="filter-select" style="min-width: 160px; height: 38px;">
                <option value="all" {{ request('period') === 'all' ? 'selected' : '' }}>Todo</option>
                <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Hoy</option>
                <option value="7days" {{ request('period') === '7days' ? 'selected' : '' }}>Últimos 7 días</option>
                <option value="month" {{ request('period', 'month') === 'month' ? 'selected' : '' }}>Este mes</option>
                <option value="custom" {{ request('period') === 'custom' ? 'selected' : '' }}>Rango personalizado</option>
            </select>
            
            <!-- Contenedor de Rango Personalizado -->
            <div id="custom-date-container" class="{{ request('period') === 'custom' ? 'd-flex' : 'd-none' }} align-items-center gap-2 flex-wrap">
                <input type="date" id="start-date" class="form-control form-control-sm shadow-none" style="height: 38px; width: 130px; border-radius: 8px; border-color: var(--neutral-300);" value="{{ request('start_date') }}">
                <span class="text-muted small">a</span>
                <input type="date" id="end-date" class="form-control form-control-sm shadow-none" style="height: 38px; width: 130px; border-radius: 8px; border-color: var(--neutral-300);" value="{{ request('end_date') }}">
                <button id="btn-apply-custom-date" class="btn btn-sm btn-outline-secondary" style="height: 38px; border-radius: 8px; font-weight: 600; font-size: 0.75rem;">Filtrar</button>
            </div>
        </div>
    </div>

    <!-- 1. Fila de Tarjetas Informativas (KPIs) -->
    <div class="row mb-2">
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <span class="kpi-icon kpi-icon-income"></span>
                <div class="kpi-info">
                    <span class="kpi-value">${{ number_format($totalIngresos, 0, ',', '.') }}</span>
                    <span class="kpi-title">Ingresos Totales</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <span class="kpi-icon kpi-icon-orders"></span>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $cantidadVentas }}</span>
                    <span class="kpi-title">Cant. Ventas</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <span class="kpi-icon kpi-icon-units"></span>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $productosVendidos }}</span>
                    <span class="kpi-title">Productos Vendidos</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <span class="kpi-icon kpi-icon-pending"></span>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $pedidosPendientes }}</span>
                    <span class="kpi-title">Pedidos Pendientes</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Split Layout (Listado Master & Detalle) -->
    <div class="row master-detail-row">
        
        <!-- Listado de Pedidos -->
        <div class="col-12">
            <div class="orders-master-panel">
                
                <!-- Buscador y Filtros -->
                <div class="d-flex align-items-center justify-content-start gap-2 mb-3 flex-wrap">
                    <div class="input-group search-bar-wrapper" style="max-width: 320px; width: 100%;">
                        <input type="text" id="search-pedido" class="form-control search-input" value="{{ request('search') }}" placeholder="Buscar por ID, cliente o email..." aria-label="Buscar ventas">
                        <button class="btn search-btn" id="btn-search-pedido" type="button">
                            <img src="{{ asset('img/icons/search.svg') }}" alt="Buscar" style="width: 18px; height: 18px;">
                        </button>
                    </div>
                    <select id="filter-estado" class="filter-select" style="min-width: 150px;">
                        <option value="all" {{ request('estado') === 'all' || !request('estado') ? 'selected' : '' }}>Todos</option>
                        <option value="CONFIRMADO" {{ request('estado') === 'CONFIRMADO' ? 'selected' : '' }}>Confirmado</option>
                        <option value="DESPACHADO" {{ request('estado') === 'DESPACHADO' ? 'selected' : '' }}>Despachado</option>
                    </select>
                    <select id="filter-pago" class="filter-select" style="min-width: 180px;">
                        <option value="all" {{ request('pago') === 'all' || !request('pago') ? 'selected' : '' }}>Cualquier pago</option>
                        @foreach ($formasPago as $fp)
                            <option value="{{ $fp->id }}" {{ request('pago') == $fp->id ? 'selected' : '' }}>{{ $fp->descripcion }}</option>
                        @endforeach
                    </select>
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
                                    data-pago-id="{{ $venta->forma_pago_id }}"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#orderDetailOffcanvas"
                                    onclick="selectOrder(this)">
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
                                        <span class="total-value">${{ number_format($venta->total, 0, ',', '.') }}</span>
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
                    {{ $ventas->appends(request()->all())->links('backend.admin.pagination') }}
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Offcanvas Detalle de Pedido -->
<div class="offcanvas offcanvas-end poppins-regular" tabindex="-1" id="orderDetailOffcanvas" aria-labelledby="orderDetailOffcanvasLabel">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 class="offcanvas-title fw-bold text-dark poppins-bold" id="orderDetailOffcanvasLabel">Detalle del Pedido</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar" style="box-shadow: none;"></button>
    </div>
    <div class="offcanvas-body p-4">
        <!-- Estado Vacío / Cargando -->
        <div id="detail-empty" class="text-center py-5 text-muted">
            <div class="spinner-border text-secondary mb-3" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mb-0 fw-medium poppins-medium">Cargando detalles...</p>
        </div>

        <!-- Contenedor con Información Dinámica -->
        <div id="detail-content" class="d-none">
            
            <!-- ID de Pedido y botón factura -->
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <h2 class="h5 fw-bold mb-0 text-dark poppins-bold">Pedido N° <span id="det-order-id">#000000</span></h2>
                <!-- Botón Descargar Factura -->
                <a href="#" id="det-btn-factura" target="_blank" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 py-1.5 px-3 rounded-3 fw-semibold poppins-medium" style="font-size: 0.75rem;">
                    Factura 📄
                </a>
            </div>

            <!-- Datos del Cliente, Dirección de Envío y Método de Pago -->
            <div class="row mb-4 border-bottom pb-4 align-items-stretch">
                <!-- Columna 1: Cliente (Centrado Verticalmente) -->
                <div class="col-6 border-end" style="border-color: var(--neutral-200) !important;">
                    <div class="d-flex flex-column justify-content-center h-100 pe-3">
                        <h3 class="text-secondary uppercase mb-2 poppins-bold d-flex align-items-center gap-2" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">
                            <span class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/user.svg') }}'); mask-image: url('{{ asset('img/icons/user.svg') }}'); width: 14px; height: 14px; display: inline-block;"></span>
                            Cliente
                        </h3>
                        <p class="mb-0 fw-bold text-dark poppins-bold" id="det-cliente-nombre" style="font-size: 0.9rem;">Juan Perez</p>
                        <p class="mb-0 text-muted poppins-regular" id="det-cliente-email" style="font-size: 0.8rem;">juan@test.com</p>
                    </div>
                </div>
                <!-- Columna 2: Dirección (Fila 1) y Método de Pago (Fila 2) -->
                <div class="col-6 ps-3">
                    <!-- Fila 1: Dirección de Envío -->
                    <div class="mb-3">
                        <h3 class="text-secondary uppercase mb-2 poppins-bold d-flex align-items-center gap-2" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">
                            <span class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/domicilio.svg') }}'); mask-image: url('{{ asset('img/icons/domicilio.svg') }}'); width: 14px; height: 14px; display: inline-block;"></span>
                            Dirección de Envío
                        </h3>
                        <p class="mb-0 text-dark fw-medium poppins-medium" style="font-size: 0.85rem;">Av. Cabildo 123, Piso 3 Depto B</p>
                        <p class="mb-0 text-muted poppins-regular" style="font-size: 0.75rem;">Buenos Aires, Argentina (C1426)</p>
                    </div>
                    <!-- Fila 2: Método de Pago -->
                    <div>
                        <h3 class="text-secondary uppercase mb-2 poppins-bold d-flex align-items-center gap-2" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">
                            <span class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/credit-card.svg') }}'); mask-image: url('{{ asset('img/icons/credit-card.svg') }}'); width: 14px; height: 14px; display: inline-block;"></span>
                            Método de Pago
                        </h3>
                        <p class="mb-0 text-dark fw-medium poppins-medium" id="det-forma-pago" style="font-size: 0.85rem;">Tarjeta de Crédito</p>
                    </div>
                </div>
            </div>

            <!-- Detalles del Carrito / Ropa Comprada -->
            <div class="mb-4">
                <h3 class="text-secondary uppercase mb-2 poppins-bold d-flex align-items-center gap-2" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">
                    <span class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/shopping-bag.svg') }}'); mask-image: url('{{ asset('img/icons/shopping-bag.svg') }}'); width: 14px; height: 14px; display: inline-block;"></span>
                    Artículos del Pedido
                </h3>
                <div id="det-items-container" style="max-height: 240px; overflow-y: auto;">
                    <!-- Dinámico -->
                </div>
            </div>

            <!-- Total de Venta -->
            <div class="d-flex align-items-center justify-content-between p-3 rounded-4 mb-4" style="background-color: var(--neutral-100);">
                <span class="fw-semibold text-secondary poppins-semibold" style="font-size: 0.85rem;">Total Facturado</span>
                <span class="fw-bold text-dark poppins-bold" id="det-total" style="font-size: 1.15rem;">$0</span>
            </div>

            <hr class="my-4" style="border-top: 1px solid var(--neutral-200); opacity: 1;">

            <!-- Línea de Tiempo del Pedido -->
            <div class="mb-4">
                <h3 class="text-secondary uppercase mb-3 poppins-bold d-flex align-items-center justify-content-center gap-2" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">
                    <span class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/clock.svg') }}'); mask-image: url('{{ asset('img/icons/clock.svg') }}'); width: 14px; height: 14px; display: inline-block;"></span>
                    Línea de Tiempo del Pedido
                </h3>
                <div class="order-timeline-centered">
                    <!-- Carrito Creado -->
                    <div class="timeline-item completed">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <span class="timeline-title poppins-semibold">🛒 Carrito creado</span>
                            <span class="timeline-date poppins-regular" id="tl-carrito-date">-</span>
                        </div>
                    </div>
                    <!-- Pago Confirmado -->
                    <div id="tl-pago-item" class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <span class="timeline-title poppins-semibold">💳 Pago confirmado</span>
                            <span class="timeline-date poppins-regular" id="tl-pago-date">-</span>
                        </div>
                    </div>
                    <!-- Pedido Despachado -->
                    <div id="tl-despacho-item" class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <span class="timeline-title poppins-semibold">📦 Pedido despachado</span>
                            <span class="timeline-date poppins-regular" id="tl-despacho-date">Pendiente</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acción Rápida: Despachar / Revertir -->
            <form id="det-form-estado" method="POST" action="">
                @csrf
                @method('PATCH')
                <input type="hidden" name="estado" id="det-input-estado" value="DESPACHADO">
                <button type="submit" id="det-btn-submit-estado" class="btn-admin btn-admin-primary w-100 mt-3 poppins-bold">
                    📦 Marcar como Despachado
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Script de interactividad AJAX y filtros -->
    <script src="{{ asset('js/backend/ventas.js') }}"></script>
@endsection
