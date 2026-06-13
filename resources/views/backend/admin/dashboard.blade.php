@extends('backend.admin.layout')

@section('styles')
    <!-- Script de Chart.js para renderizar el gráfico de líneas -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/backend/dashboard.css') }}">
@endsection

@section('contenido')
<div class="container-fluid px-0 dashboard-container">
    
    <!-- Fila 1: Título -->
    <div class="dashboard-header mb-2">
        <div>
            <h1 class="section-title">Dashboard</h1>
            <p class="text-muted mb-0">Bienvenido/a, {{ auth()->user()->nombre ?? 'Administrador' }}</p>
        </div>
    </div>

    <!-- Fila 2: Resumen Rápido (KPIs vectorizados de referencia) -->
    <div class="row 3 mb-3">
        <!-- Ventas del Día -->
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <span class="kpi-icon kpi-icon-sales"></span>
                <div class="kpi-info">
                    <div class="d-flex align-items-center gap-2">
                        <span class="kpi-value">${{ number_format($ventasDelDia, 2) }}</span>
                        @if($variacionVentas >= 0)
                            <span class="d-inline-flex align-items-center gap-1 fw-bold" style="font-size: 0.82rem; color: var(--green-600);">
                                <span class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/arrow-up.svg') }}'); mask-image: url('{{ asset('img/icons/arrow-up.svg') }}'); width: 12px; height: 12px; display: inline-block; background-color: var(--green-600);"></span>
                                {{ number_format($variacionVentas, 1) }}%
                            </span>
                        @else
                            <span class="d-inline-flex align-items-center gap-1 fw-bold" style="font-size: 0.82rem; color: var(--coral-500);">
                                <span class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/arrow-down.svg') }}'); mask-image: url('{{ asset('img/icons/arrow-down.svg') }}'); width: 12px; height: 12px; display: inline-block; background-color: var(--coral-500);"></span>
                                {{ number_format(abs($variacionVentas), 1) }}%
                            </span>
                        @endif
                    </div>
                    <span class="kpi-title">Ventas del día</span>
                </div>
            </div>
        </div>

        <!-- Nuevos Registros -->
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <span class="kpi-icon kpi-icon-new-users"></span>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $nuevosRegistros }}</span>
                    <span class="kpi-title">Nuevos Registros</span>
                </div>
            </div>
        </div>

        <!-- Pedidos Pendientes -->
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <span class="kpi-icon kpi-icon-pending-orders"></span>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $pedidosPendientesCount }} </span>
                    <span class="kpi-title">Envíos pendientes</span>
                </div>
            </div>
        </div>

        <!-- Alertas de Stock Bajo -->
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <span class="kpi-icon kpi-icon-low-stock"></span>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $countBajoStock }} </span>
                    <span class="kpi-title">Alertas de Stock Bajo</span>                    
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 3: Gráfico de Ventas y Últimos Pedidos -->
    <div class="row g-3 mb-3">
        <!-- Gráfico de Ventas -->
        <div class="col-12 col-lg-8">
            <div class="surface-card p-4 align-items-stretch">
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                    <h3 class="h4 text-dark mb-0 fw-bold">Reporte de Ventas</h3>
                    <select class="form-select form-select-sm shadow-none" style="width: auto; border-radius: 8px; border-color: var(--neutral-300);">
                        <option value="semana">Esta Semana</option>
                        <option value="mes" selected>Este Mes</option>
                        <option value="año">Este Año</option>
                    </select>
                </div>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Últimos Pedidos -->
        <div class="col-12 col-lg-4">
            <div class="surface-card p-4 align-items-start">
                <h3 class="h4 text-dark mb-3 fw-bold">Últimos Pedidos</h3>
                <div class="table-responsive w-100">
                    <table class="table table-hover align-middle recent-orders-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ultimosPedidos as $pedido)
                                <tr>
                                    <td>
                                        <span class="fw-bold">#{{ $pedido['n_pedido'] }}</span>
                                        <small class="d-block text-muted">{{ $pedido['fecha'] }}</small>
                                    </td>
                                    <td>{{ $pedido['usuario'] }}</td>
                                    <td>${{ number_format($pedido['monto'], 2) }}</td>
                                    <td>
                                        <span class="badge py-1 px-2 {{ $pedido['estado'] == 'Completado' ? 'badge-completed' : ($pedido['estado'] == 'Pendiente' ? 'badge-pending' : 'badge-cancelled') }}">
                                            {{ $pedido['estado'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Fila 4: Productos con Stock Bajo y Consultas Recientes -->
    <div class="row g-3">
        <!-- Productos con Stock Bajo -->
        <div class="col-12 col-lg-6">
            <div class="surface-card p-4 align-items-start">
                <h3 class="h4 text-dark mb-3 fw-bold">Alertas de Inventario</h3>
                <div class="w-100">
                    @forelse($productosBajoStock as $prod)
                        <div class="low-stock-item">
                            <div class="d-flex align-items-center gap-3">
                                @if($prod->imagenPortada)
                                    <img src="{{ $prod->imagenPortada->url }}" alt="{{ $prod->nombre }}" class="low-stock-img">
                                @else
                                    <div class="low-stock-img d-flex align-items-center justify-content-center text-muted small fw-bold">PT</div>
                                @endif
                                <div class="text-start">
                                    <span class="fw-bold d-block text-dark small" style="line-height: 1.2;">{{ $prod->nombre }}</span>
                                    <small class="text-muted" style="font-size: 0.75rem;">SKU: {{ $prod->sku }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-danger rounded-pill fw-bold">Stock: {{ $prod->stock }}</span>
                                <small class="d-block text-muted mt-1" style="font-size: 0.7rem;">Mín: {{ $prod->stock_minimo }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 w-100">
                            <p class="text-muted mb-0">No hay alertas de stock bajo.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Consultas Recientes -->
        <div class="col-12 col-lg-6">
            <div class="surface-card p-4 align-items-start">
                <h3 class="h4 text-dark mb-3 fw-bold">Consultas de Clientes</h3>
                <div class="w-100 d-flex flex-column gap-3">
                    @foreach($consultasRecientes as $consulta)
                        <div class="p-3 rounded text-start" style="background-color: var(--neutral-100); border: 1px solid var(--neutral-200); width: 100%;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-dark small">{{ $consulta['nombre'] }}</span>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ $consulta['fecha'] }}</small>
                            </div>
                            <p class="mb-1 text-secondary small text-hyphenated" style="line-height: 1.3;">
                                "{{ $consulta['mensaje'] }}"
                            </p>
                            <small class="text-muted d-block" style="font-size: 0.72rem;">{{ $consulta['email'] }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Script de interactividad del dashboard -->
    <script src="{{ asset('js/backend/dashboard.js') }}"></script>
@endsection
