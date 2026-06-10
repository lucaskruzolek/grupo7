@extends('backend.admin.layout')

@section('styles')
    <!-- Script de Chart.js para renderizar el gráfico de líneas -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .metric-icon {
            font-size: 2rem;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: var(--neutral-100);
        }
        .recent-orders-table th,
        .recent-orders-table td {
            font-size: 0.85rem;
            padding: 0.75rem 0.5rem;
        }
        .low-stock-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--neutral-200);
            width: 100%;
        }
        .low-stock-item:last-child {
            border-bottom: none;
        }
        .low-stock-img {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 8px;
            background-color: var(--neutral-200);
        }
        .badge-pending {
            background-color: var(--coral-100);
            color: var(--coral-800);
        }
        .badge-completed {
            background-color: var(--green-100);
            color: var(--green-800);
        }
        .badge-cancelled {
            background-color: var(--neutral-200);
            color: var(--neutral-700);
        }
        /* Enable vertical scrolling specifically for the dashboard view */
        .admin-content {
            overflow-y: auto !important;
        }
    </style>
@endsection

@section('contenido')
<div class="container-fluid px-0">
    
    <!-- Fila 1: Título y Selector de Fecha -->
    <div class="dashboard-header mb-4">
        <div>
            <h1 class="section-title">Dashboard</h1>
            <p class="text-muted mb-0">Bienvenido/a, {{ auth()->user()->nombre ?? 'Administrador' }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <label for="dashboard-date" class="form-label mb-0 fw-bold small text-muted text-uppercase">Fecha:</label>
            <input type="date" id="dashboard-date" class="form-control form-control-sm shadow-none" value="{{ date('Y-m-d') }}" style="border-radius: 8px; border-color: var(--neutral-300);">
        </div>
    </div>

    <!-- Fila 2: Resumen Rápido -->
    <div class="row g-4 mb-4">
        <!-- Ventas del Día -->
        <div class="col-12 col-md-4">
            <div class="surface-card p-4 justify-content-between flex-row align-items-center">
                <div class="text-start">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Ventas del día</small>
                    <h3 class="h2 mb-0 mt-1 fw-bold text-dark">${{ number_format($ventasDelDia, 2) }}</h3>
                    <span class="badge bg-success mt-2 d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                        <span>↑</span> 12.5% hoy
                    </span>
                </div>
                <div class="metric-icon">💰</div>
            </div>
        </div>

        <!-- Pedidos Pendientes -->
        <div class="col-12 col-md-4">
            <div class="surface-card p-4 justify-content-between flex-row align-items-center">
                <div class="text-start">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Pedidos pendientes</small>
                    <h3 class="h2 mb-0 mt-1 fw-bold text-dark">{{ $pedidosPendientesCount }} pedidos</h3>
                    <span class="badge bg-warning text-dark mt-2" style="font-size: 0.75rem;">Por procesar</span>
                </div>
                <div class="metric-icon">📦</div>
            </div>
        </div>

        <!-- Alertas de Stock Bajo -->
        <div class="col-12 col-md-4">
            <div class="surface-card p-4 justify-content-between flex-row align-items-center">
                <div class="text-start">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Alertas de Stock Bajo</small>
                    <h3 class="h2 mb-0 mt-1 fw-bold text-dark">{{ $countBajoStock }} productos</h3>
                    <span class="badge bg-danger mt-2" style="font-size: 0.75rem;">Atención requerida</span>
                </div>
                <div class="metric-icon">⚠️</div>
            </div>
        </div>
    </div>

    <!-- Fila 3: Gráfico de Ventas y Últimos Pedidos -->
    <div class="row g-4 mb-4">
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
    <div class="row g-4">
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
                            <p class="text-muted mb-0">No hay alertas de stock bajo. ¡Buen trabajo!</p>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
                    datasets: [{
                        label: 'Ventas Semanales ($)',
                        data: [15000, 22000, 18500, 24500],
                        borderColor: '#7d8c78', // Verde tierra del tema
                        backgroundColor: 'rgba(125, 140, 120, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#7d8c78',
                        pointBorderColor: '#fff',
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    family: 'Poppins'
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'Poppins'
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
