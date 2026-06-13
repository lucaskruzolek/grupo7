@extends('backend.admin.layout')

@section('styles')
    <!-- Estilos específicos de la gestión de clientes/usuarios -->
    <link rel="stylesheet" href="{{ asset('css/backend/clientes.css') }}">
@endsection

@section('contenido')
<div class="container-fluid px-0 clientes-container">
    
    <!-- Alertas de éxito y de error -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-3" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if (session('exito'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-3" role="alert">
            {{ session('exito') }}
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <!-- Encabezado Principal -->
    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-3">
        <div>
            <h1 class="section-title">Gestión de Clientes</h1>
        </div>
        <!-- Filtro Rango de Fechas y Botón Crear -->
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="text-secondary" style="font-size: 0.85rem; font-weight: 500;">Registro:</span>
                <select id="filter-period" class="filter-select" style="min-width: 160px; height: 38px;">
                    <option value="all" {{ request('period', 'all') === 'all' ? 'selected' : '' }}>Todo</option>
                    <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Hoy</option>
                    <option value="7days" {{ request('period') === '7days' ? 'selected' : '' }}>Últimos 7 días</option>
                    <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Este mes</option>
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

            <!-- Botón Crear Nuevo Usuario -->
            <button type="button" class="btn-admin btn-admin-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <span>+</span> Nuevo Usuario
            </button>
        </div>
    </div>

    <!-- 1. Fila de Tarjetas Informativas (KPIs) -->
    <div class="row mb-2">
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <span class="kpi-icon kpi-icon-users"></span>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $totalUsuarios }}</span>
                    <span class="kpi-title">Total Usuarios</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <span class="kpi-icon kpi-icon-new-users"></span>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $nuevosRegistros }}</span>
                    <span class="kpi-title">Nuevos Registros</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <span class="kpi-icon kpi-icon-buyers"></span>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $compradoresActivos }}</span>
                    <span class="kpi-title">Compradores Activos</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="kpi-card">
                <span class="kpi-icon kpi-icon-admins"></span>
                <div class="kpi-info">
                    <span class="kpi-value">{{ $totalAdmins }}</span>
                    <span class="kpi-title">Administradores</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Split Layout (Listado Master & Detalle Offcanvas) -->
    <div class="row master-detail-row">
        
        <!-- Listado de Clientes -->
        <div class="col-12">
            <div class="clients-master-panel">
                
                <!-- Buscador y Filtros -->
                <div class="d-flex align-items-center justify-content-start gap-2 mb-3 flex-wrap">
                    <div class="input-group search-bar-wrapper" style="max-width: 320px; width: 100%;">
                        <input type="text" id="search-cliente" class="form-control search-input" value="{{ request('search') }}" placeholder="Buscar por ID, nombre o email..." aria-label="Buscar clientes">
                        <button class="btn search-btn" id="btn-search-cliente" type="button">
                            <img src="{{ asset('img/icons/search.svg') }}" alt="Buscar" style="width: 18px; height: 18px;">
                        </button>
                    </div>
                    <select id="filter-rol" class="filter-select" style="min-width: 150px;">
                        <option value="all" {{ request('rol') === 'all' || !request('rol') ? 'selected' : '' }}>Todos los Roles</option>
                        <option value="cliente" {{ request('rol') === 'cliente' ? 'selected' : '' }}>Cliente</option>
                        <option value="admin" {{ request('rol') === 'admin' ? 'selected' : '' }}>Administrador</option>
                    </select>
                </div>

                <!-- Tabla de Listado -->
                <div class="table-responsive">
                    <table class="table admin-table clients-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Usuario</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th style="width: 130px; text-align: center;">Rol</th>
                                <th style="width: 180px;">Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($usuarios as $usuario)
                                <tr data-client-id="{{ $usuario->id }}" 
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#clientDetailOffcanvas"
                                    onclick="selectClient(this)">
                                    <td>
                                        <span class="client-badge-id">#{{ str_pad($usuario->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td>
                                        <div class="client-name-main">{{ $usuario->nombre }} {{ $usuario->apellido }}</div>
                                    </td>
                                    <td>
                                        <div class="client-email-sub">{{ $usuario->email }}</div>
                                    </td>
                                    <td style="text-align: center;">
                                        @if ($usuario->rol->nombre === 'admin')
                                            <span class="role-badge role-badge-admin">Admin</span>
                                        @else
                                            <span class="role-badge role-badge-cliente">Cliente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="date-main">{{ $usuario->created_at ? $usuario->created_at->format('d M Y') : '-' }}</div>
                                        <div class="date-sub">{{ $usuario->created_at ? $usuario->created_at->format('H:i \h\s') : '-' }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No se encontraron clientes o usuarios registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-3 custom-pagination-container">
                    {{ $usuarios->appends(request()->all())->links('backend.admin.pagination') }}
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Offcanvas Detalle de Cliente -->
<div class="offcanvas offcanvas-end poppins-regular" tabindex="-1" id="clientDetailOffcanvas" aria-labelledby="clientDetailOffcanvasLabel">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <h5 class="offcanvas-title fw-bold text-dark poppins-bold" id="clientDetailOffcanvasLabel">Detalle del Usuario</h5>
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
            
            <!-- ID de Usuario -->
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <h2 class="h5 fw-bold mb-0 text-dark poppins-bold">Usuario N° <span id="det-client-id">#00000</span></h2>
            </div>

            <!-- Datos del Usuario y Dirección -->
            <div class="row mb-4 border-bottom pb-4 align-items-stretch">
                <!-- Columna 1: Perfil -->
                <div class="col-6 border-end" style="border-color: var(--neutral-200) !important;">
                    <div class="d-flex flex-column justify-content-center h-100 pe-3">
                        <h3 class="text-secondary uppercase mb-2 poppins-bold d-flex align-items-center gap-2" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">
                            <span class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/user.svg') }}'); mask-image: url('{{ asset('img/icons/user.svg') }}'); width: 14px; height: 14px; display: inline-block; background-color: var(--neutral-500);"></span>
                            Perfil
                        </h3>
                        <p class="mb-0 fw-bold text-dark poppins-bold" id="det-cliente-nombre" style="font-size: 0.9rem;">-</p>
                        <a href="mailto:-" class="mb-1 text-primary text-decoration-none poppins-regular" id="det-cliente-email" style="font-size: 0.8rem;">-</a>
                        <div>
                            <span id="det-cliente-rol" class="role-badge">-</span>
                        </div>
                    </div>
                </div>
                <!-- Columna 2: Datos de Contacto y Dirección -->
                <div class="col-6 ps-3">
                    <div class="mb-3">
                        <h3 class="text-secondary uppercase mb-1 poppins-bold d-flex align-items-center gap-2" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">
                            Contacto
                        </h3>
                        <p class="mb-0 text-dark fw-medium poppins-medium" style="font-size: 0.8rem;">Tel: <span id="det-cliente-telefono" class="text-muted poppins-regular">-</span></p>
                        <p class="mb-0 text-dark fw-medium poppins-medium" style="font-size: 0.8rem;">Registro: <span id="det-cliente-registro" class="text-muted poppins-regular" style="font-size: 0.75rem;">-</span></p>
                    </div>
                    <div>
                        <h3 class="text-secondary uppercase mb-1 poppins-bold d-flex align-items-center gap-2" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">
                            Dirección de Envío
                        </h3>
                        <p class="mb-0 text-muted poppins-regular" id="det-cliente-direccion" style="font-size: 0.8rem; line-height: 1.3;">-</p>
                    </div>
                </div>
            </div>

            <!-- Historial de compras -->
            <div class="mb-4" id="det-history-section">
                <h3 class="text-secondary uppercase mb-3 poppins-bold d-flex align-items-center gap-2" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">
                    <span class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/pedidos.svg') }}'); mask-image: url('{{ asset('img/icons/pedidos.svg') }}'); width: 14px; height: 14px; display: inline-block; background-color: var(--neutral-500);"></span>
                    Historial de Compras
                </h3>
                <div id="det-history-container" style="max-height: 240px; overflow-y: auto; padding-right: 2px;">
                    <!-- Dinámico -->
                </div>
            </div>

            <!-- Acción de Eliminar (Solo para no-admins) -->
            <div id="det-delete-section" class="d-none border-top pt-3 mt-4">
                <form id="det-form-delete" method="POST" action="" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción realizará una baja lógica en el sistema.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100 py-2 rounded-3 fw-bold poppins-bold" style="font-size: 0.85rem;">
                        Eliminar Usuario
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear Nuevo Usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <form action="{{ route('admin.usuarios.store') }}" method="POST" class="m-0">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h6 class="modal-title fw-bold text-dark" style="font-family: var(--font-main);">Crear Nuevo Usuario</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body py-3 text-start">
                    <div class="row g-3">
                        <!-- Nombre -->
                        <div class="col-md-6">
                            <div class="form-group-admin">
                                <label for="create-nombre" class="form-label-admin">Nombre *</label>
                                <input type="text" name="nombre" id="create-nombre" class="form-control form-control-admin" required placeholder="Ej: Lucas">
                            </div>
                        </div>
                        <!-- Apellido -->
                        <div class="col-md-6">
                            <div class="form-group-admin">
                                <label for="create-apellido" class="form-label-admin">Apellido *</label>
                                <input type="text" name="apellido" id="create-apellido" class="form-control form-control-admin" required placeholder="Ej: Pérez">
                            </div>
                        </div>
                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group-admin">
                                <label for="create-email" class="form-label-admin">Email *</label>
                                <input type="email" name="email" id="create-email" class="form-control form-control-admin" required placeholder="nombre@correo.com">
                            </div>
                        </div>
                        <!-- Rol -->
                        <div class="col-md-6">
                            <div class="form-group-admin">
                                <label for="create-rol" class="form-label-admin">Rol *</label>
                                <select name="rol_id" id="create-rol" class="form-select form-select-admin" required>
                                    <option value="" disabled selected>Selecciona un rol</option>
                                    @foreach ($roles as $rol)
                                        <option value="{{ $rol->id }}">{{ ucfirst($rol->nombre) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Password -->
                        <div class="col-md-6">
                            <div class="form-group-admin">
                                <label for="create-password" class="form-label-admin">Contraseña *</label>
                                <input type="password" name="password" id="create-password" class="form-control form-control-admin" required placeholder="Mínimo 6 caracteres">
                            </div>
                        </div>
                        <!-- Confirmar Password -->
                        <div class="col-md-6">
                            <div class="form-group-admin">
                                <label for="create-password-confirm" class="form-label-admin">Confirmar Contraseña *</label>
                                <input type="password" name="password_confirmation" id="create-password-confirm" class="form-control form-control-admin" required placeholder="Repite la contraseña">
                            </div>
                        </div>
                        
                        <div class="col-12 mt-3 mb-1">
                            <h6 class="fw-bold border-bottom pb-2 text-secondary" style="font-size: 0.8rem; letter-spacing: 0.5px; font-family: var(--font-main);">DATOS DE ENVÍO / COMERCIO ELECTRÓNICO (OPCIONALES)</h6>
                        </div>

                        <!-- Telefono -->
                        <div class="col-md-4">
                            <div class="form-group-admin">
                                <label for="create-telefono" class="form-label-admin">Teléfono</label>
                                <input type="text" name="telefono" id="create-telefono" class="form-control form-control-admin" placeholder="Ej: 1123456789">
                            </div>
                        </div>
                        <!-- Dirección -->
                        <div class="col-md-8">
                            <div class="form-group-admin">
                                <label for="create-direccion" class="form-label-admin">Dirección</label>
                                <input type="text" name="direccion" id="create-direccion" class="form-control form-control-admin" placeholder="Calle, número, piso, depto">
                            </div>
                        </div>
                        <!-- Localidad -->
                        <div class="col-md-4">
                            <div class="form-group-admin">
                                <label for="create-localidad" class="form-label-admin">Localidad</label>
                                <input type="text" name="localidad" id="create-localidad" class="form-control form-control-admin" placeholder="Ej: Caballito">
                            </div>
                        </div>
                        <!-- Provincia -->
                        <div class="col-md-4">
                            <div class="form-group-admin">
                                <label for="create-provincia" class="form-label-admin">Provincia</label>
                                <input type="text" name="provincia" id="create-provincia" class="form-control form-control-admin" placeholder="Ej: CABA">
                            </div>
                        </div>
                        <!-- Código Postal -->
                        <div class="col-md-4">
                            <div class="form-group-admin">
                                <label for="create-cp" class="form-label-admin">Código Postal</label>
                                <input type="text" name="codigo_postal" id="create-cp" class="form-control form-control-admin" placeholder="Ej: C1405">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-admin btn-admin-primary">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <!-- Script de interactividad AJAX y filtros por recarga -->
    <script src="{{ asset('js/backend/clientes.js') }}"></script>
@endsection
