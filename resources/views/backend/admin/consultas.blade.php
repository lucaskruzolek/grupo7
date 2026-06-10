@extends('backend.admin.layout')

@section('styles')
    <!-- Estilos específicos de la gestión de consultas -->
    <link rel="stylesheet" href="{{ asset('css/backend/consultas.css') }}">
@endsection

@section('contenido')
<div class="container-fluid px-0 consultas-container">
    
    <!-- Encabezado Principal -->
    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-3">
        <div>
            <h1 class="section-title">Consultas Recibidas</h1>
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
                <span class="kpi-icon kpi-icon-total"></span>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $totalConsultas }}</span>
                    <span class="kpi-title">Total Recibidas</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <span class="kpi-icon kpi-icon-new"></span>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $nuevasConsultas }}</span>
                    <span class="kpi-title">Consultas Nuevas</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <span class="kpi-icon kpi-icon-pending"></span>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $leidasConsultas }}</span>
                    <span class="kpi-title">Pendientes</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <span class="kpi-icon kpi-icon-answered"></span>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $respondidasConsultas }}</span>
                    <span class="kpi-title">Respondidas</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Master Panel Layout -->
    <div class="row master-detail-row">
        <div class="col-12">
            <div class="consultas-master-panel">
                
                <!-- Buscador y Filtros -->
                <div class="d-flex align-items-center justify-content-start gap-2 mb-3 flex-wrap">
                    <div class="input-group search-bar-wrapper" style="max-width: 320px; width: 100%;">
                        <input type="text" id="search-consulta" class="form-control search-input" value="{{ request('search') }}" placeholder="Buscar por ID, remitente o mensaje..." aria-label="Buscar consultas">
                        <button class="btn search-btn" id="btn-search-consulta" type="button">
                            <img src="{{ asset('img/icons/search.svg') }}" alt="Buscar" style="width: 18px; height: 18px;">
                        </button>
                    </div>
                    <select id="filter-asunto" class="filter-select" style="min-width: 150px;">
                        <option value="all" {{ request('asunto') === 'all' || !request('asunto') ? 'selected' : '' }}>Todos los asuntos</option>
                        <option value="consulta" {{ request('asunto') === 'consulta' ? 'selected' : '' }}>Consulta General</option>
                        <option value="reclamo" {{ request('asunto') === 'reclamo' ? 'selected' : '' }}>Reclamo</option>
                        <option value="devolucion" {{ request('asunto') === 'devolucion' ? 'selected' : '' }}>Devoluciones</option>
                        <option value="otro" {{ request('asunto') === 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    <select id="filter-estado" class="filter-select" style="min-width: 180px;">
                        <option value="all" {{ request('estado') === 'all' || !request('estado') ? 'selected' : '' }}>Todos los estados</option>
                        <option value="nuevo" {{ request('estado') === 'nuevo' ? 'selected' : '' }}>Nuevo</option>
                        <option value="leido" {{ request('estado') === 'leido' ? 'selected' : '' }}>Leído</option>
                        <option value="respondido" {{ request('estado') === 'respondido' ? 'selected' : '' }}>Respondido</option>
                    </select>
                </div>

                <!-- Tabla de Listado -->
                <div class="table-responsive">
                    <table class="table admin-table consultas-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Consulta</th>
                                <th>Remitente</th>
                                <th style="width: 180px;">Asunto</th>
                                <th>Mensaje</th>
                                <th style="width: 140px;">Fecha</th>
                                <th style="text-align: center; width: 130px;">Estado</th>
                                <th style="text-align: center; width: 160px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($consultas as $consulta)
                                <tr data-consulta-id="#{{ str_pad($consulta->id, 6, '0', STR_PAD_LEFT) }}"
                                    data-leido="{{ $consulta->leido ? '1' : '0' }}"
                                    data-respondido="{{ $consulta->respondido ? '1' : '0' }}"
                                    data-asunto="{{ $consulta->asunto }}"
                                    data-pedido="{{ $consulta->pedido ?? '' }}"
                                    style="{{ !$consulta->leido ? 'background-color: rgba(125, 140, 120, 0.04);' : '' }}">
                                    
                                    <!-- ID Consulta -->
                                    <td>
                                        <span class="order-badge-id">#{{ str_pad($consulta->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    
                                    <!-- Remitente -->
                                    <td>
                                        <div class="client-name">{{ $consulta->nombre }}</div>
                                        <div class="client-email">
                                            <a href="mailto:{{ $consulta->email }}" class="text-decoration-none text-muted">{{ $consulta->email }}</a>
                                        </div>
                                        @if ($consulta->telefono)
                                            <div class="text-muted" style="font-size: 0.72rem;">{{ $consulta->telefono }}</div>
                                        @endif
                                    </td>
                                    
                                    <!-- Asunto -->
                                    <td>
                                        @if ($consulta->asunto === 'consulta')
                                            <span class="tipo-asunto">Consulta General</span>
                                        @elseif ($consulta->asunto === 'reclamo')
                                            <span class="tipo-asunto">Reclamo</span>
                                        @elseif ($consulta->asunto === 'devolucion')
                                            <span class="tipo-asunto">Devoluciones</span>
                                        @else
                                            <span class="tipo-asunto">Otro</span>
                                        @endif

                                        @if ($consulta->pedido)
                                            <div class="mt-1 small fw-bold" style="color: var(--neutral-600); font-size: 0.75rem;">
                                                Pedido: <span class="font-monospace">{{ $consulta->pedido }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <!-- Mensaje truncado con enlace -->
                                    <td>
                                        <div class="mensaje-text text-secondary text-hyphenated" style="font-size: 0.85rem; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0 auto;">
                                            {{ $consulta->mensaje }}
                                        </div>
                                        <button type="button" class="btn btn-link p-0 text-decoration-none small fw-bold mt-1" style="font-size: 0.75rem; color: var(--green-600);" data-bs-toggle="modal" data-bs-target="#modalConsulta{{ $consulta->id }}">
                                            Leer mensaje completo &rarr;
                                        </button>
                                    </td>
                                    
                                    <!-- Fecha -->
                                    <td>
                                        <div class="date-main">{{ $consulta->created_at->format('d M Y') }}</div>
                                        <div class="date-sub">{{ $consulta->created_at->format('H:i \h\s') }}</div>
                                    </td>
                                    
                                    <!-- Estado -->
                                    <td>
                                        <div class="d-flex flex-column gap-1 align-items-center justify-content-center">
                                            @if ($consulta->respondido)
                                                <span class="badge badge-completed" style="font-size: 0.65rem; border-radius: 4px; font-weight: 600;">RESPONDIDO</span>
                                            @elseif ($consulta->leido)
                                                <span class="badge badge-pending" style="font-size: 0.65rem; border-radius: 4px;">Leído</span>
                                            @else
                                                <span class="badge bg-danger text-white px-2 py-1" style="font-size: 0.65rem; border-radius: 4px; font-weight: 600;">NUEVO</span>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <!-- Acciones -->
                                    <td style="white-space: nowrap;">
                                        <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                            <!-- Marcar Leído / No Leído -->
                                            <form action="{{ route('admin.consultas.toggle-leido', $consulta->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn-action-flat" title="{{ $consulta->leido ? 'Marcar como no leído' : 'Marcar como leído' }}">
                                                    @if ($consulta->leido)
                                                        <!-- eye-off -->
                                                        <span class="action-icon action-icon-eye-off"></span>
                                                    @else
                                                        <!-- eye -->
                                                        <span class="action-icon action-icon-eye"></span>
                                                    @endif
                                                </button>
                                            </form>

                                            <!-- Marcar Respondido / Pendiente -->
                                            <form action="{{ route('admin.consultas.toggle-respondido', $consulta->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn-action-flat" title="{{ $consulta->respondido ? 'Marcar como pendiente' : 'Marcar como respondido' }}">
                                                    @if ($consulta->respondido)
                                                        <!-- hourglass -->
                                                        <span class="action-icon action-icon-hourglass"></span>
                                                    @else
                                                        <!-- check -->
                                                        <span class="action-icon action-icon-check"></span>
                                                    @endif
                                                </button>
                                            </form>

                                            <!-- Eliminar -->
                                            <form action="{{ route('admin.consultas.destroy', $consulta->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta consulta?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action-flat" title="Eliminar Consulta">
                                                    <!-- delete (trash) -->
                                                    <span class="action-icon action-icon-delete"></span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    
                                </tr>

                                <!-- Modal de Lectura Completa para esta Consulta (mejorado con Poppins y colores consistentes) -->
                                <div class="modal fade" id="modalConsulta{{ $consulta->id }}" tabindex="-1" aria-labelledby="modalConsultaLabel{{ $consulta->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
                                            <div class="modal-header py-3 px-4" style="background-color: #ffffff; border-bottom: 1px solid var(--neutral-150);">
                                                <h5 class="modal-title fw-bold text-dark poppins-bold" id="modalConsultaLabel{{ $consulta->id }}">Consulta #{{ str_pad($consulta->id, 6, '0', STR_PAD_LEFT) }}</h5>
                                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4 text-start" style="background-color: #fafaf5;">
                                                <!-- Info Remitente -->
                                                <div class="p-3 rounded-4 mb-3 border" style="background-color: #ffffff; font-size: 0.85rem; border-color: var(--neutral-200) !important;">
                                                    <div class="mb-2 text-secondary"><strong>Remitente:</strong> {{ $consulta->nombre }}</div>
                                                    <div class="mb-2 text-secondary"><strong>Email:</strong> <a href="mailto:{{ $consulta->email }}">{{ $consulta->email }}</a></div>
                                                    @if ($consulta->telefono)
                                                        <div class="mb-2 text-secondary"><strong>Teléfono:</strong> {{ $consulta->telefono }}</div>
                                                    @endif
                                                    <div class="mb-2 text-secondary"><strong>Fecha de envío:</strong> {{ $consulta->created_at->format('d/m/Y H:i') }} hs</div>
                                                    @if ($consulta->pedido)
                                                        <div class="mb-0 text-secondary"><strong>Pedido Relacionado:</strong> <span class="font-monospace fw-bold">{{ $consulta->pedido }}</span></div>
                                                    @endif
                                                </div>

                                                <div class="mb-2">
                                                    <strong class="d-block mb-2 text-secondary uppercase poppins-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Mensaje recibido:</strong>
                                                    <div class="text-dark p-3 border rounded-4 text-hyphenated" style="background-color: #ffffff; font-size: 0.88rem; max-height: 250px; overflow-y: auto; white-space: pre-wrap; line-height: 1.5; border-color: var(--neutral-200) !important;">{{ $consulta->mensaje }}</div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0 pt-0 px-4 pb-4" style="background-color: #fafaf5;">
                                                <!-- Formulario rápido para responder vía email -->
                                                <a href="mailto:{{ $consulta->email }}?subject=Re: {{ ucfirst($consulta->asunto) }} - Pet Threads" class="btn-admin btn-admin-primary px-3 text-white text-decoration-none d-flex align-items-center gap-1" style="border-radius: 8px;">
                                                    Responder por Correo
                                                </a>
                                                <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        
                                        <p class="mb-0 fw-semibold">No se encontraron consultas registradas en este período.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-3 custom-pagination">
                    {{ $consultas->appends(request()->all())->links('backend.admin.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Script de interactividad y filtros en cliente -->
    <script src="{{ asset('js/backend/consultas.js') }}"></script>
@endsection
