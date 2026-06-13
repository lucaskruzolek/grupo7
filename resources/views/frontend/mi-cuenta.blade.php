@extends('frontend.layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mi-cuenta.css') }}">
@endsection

@section('contenido')
<!-- Banner Hero -->
<section class="banner-hero theme-neutral py-1 py-md-4 mb-4">
    <div class="container py-3">
        <div class="row align-items-center">
            <div class="col text-center text-md-start">
                <h1 class="banner-title mb-2 d-inline-block anim-fade-down" style="--anim-order: 1;">
                    Mi Cuenta <span class="paw-icon"></span>
                </h1>
                <p class="banner-subtitle anim-fade-down mb-0" style="--anim-order: 2;">
                    Administrá tus datos y hacé el seguimiento de tus pedidos.
                </p>
            </div>
        </div>
    </div>
</section>

<div class="container mb-5">
    @if(session('exito'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 anim-fade-down" role="alert" style="background-color: #d1fae5; color: #065f46; --anim-order: 1; border-radius: 12px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="fw-semibold small">
                {{ session('exito') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any() && !($errors->has('password_actual') || $errors->has('password_nueva') || $errors->has('nombre') || $errors->has('apellido') || $errors->has('email') || $errors->has('telefono') || $errors->has('direccion') || $errors->has('localidad') || $errors->has('provincia') || $errors->has('codigo_postal')))
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 anim-fade-down" role="alert" style="background-color: #fee2e2; color: #991b1b; --anim-order: 1; border-radius: 12px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div class="fw-semibold small">
                Ocurrió un error al procesar tu solicitud. Por favor reintentá.
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        
        <!-- Columna Izquierda: Información de Perfil -->
        <div class="col-lg-4">
            <div class="profile-card p-4 anim-fade-down" style="--anim-order: 1;">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="profile-avatar">
                        {{ strtoupper(substr($usuario->nombre, 0, 1) . substr($usuario->apellido, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold" style="color: var(--brand-dark);">{{ $usuario->nombre }} {{ $usuario->apellido }}</h4>
                        <span class="badge bg-light text-secondary border mt-2">Cliente registrado</span>
                    </div>
                </div>

                <div class="profile-details">
                    <div class="d-flex justify-content-between align-items-center mb-3 pt-3 pb-2 border-bottom">
                        <h5 class="fw-bold mb-0" style="font-size: 0.95rem; text-transform: uppercase; color: var(--color-text); font-family: var(--font-main);">
                            Datos Personales
                        </h5>
                        <button class="btn btn-sm btn-link text-decoration-none p-0 d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#editProfileModal" style="color: var(--color-primary); font-size: 0.85rem; font-weight: 600;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4z"/>
                            </svg>
                            <span>Editar</span>
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <span class="d-block text-muted small">Correo Electrónico</span>
                        <span class="fw-semibold text-dark">{{ $usuario->email }}</span>
                    </div>

                    <div class="mb-3">
                        <span class="d-block text-muted small">Teléfono</span>
                        <span class="fw-semibold text-dark">{{ $usuario->telefono ?? 'No registrado' }}</span>
                    </div>

                    <div class="mb-3">
                        <span class="d-block text-muted small">Dirección de Entrega</span>
                        @if($usuario->direccion)
                            <span class="fw-semibold text-dark d-block">{{ $usuario->direccion }}</span>
                            <span class="text-secondary small d-block">
                                {{ $usuario->localidad }}{{ $usuario->provincia ? ', ' . $usuario->provincia : '' }}
                                {{ $usuario->codigo_postal ? ' (CP ' . $usuario->codigo_postal . ')' : '' }}
                            </span>
                        @else
                            <span class="text-muted italic small">No hay dirección de entrega cargada.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Historial de Compras -->
        <div class="col-lg-8">
            <div class="orders-card p-4 anim-fade-down" style="--anim-order: 2;">
                <h5 class="fw-bold mb-4" style="font-family: var(--font-main); color: var(--brand-dark);">
                    Historial de Compras
                </h5>

                @if($ventas->isEmpty())
                    <!-- Estado vacío de compras -->
                    <div class="text-center py-5">
                        <div class="mb-3 d-flex justify-content-center">
                            <div style="background-color: var(--coral-100); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <span class="paw-icon" style="font-size: 2rem; opacity: 0.8;"></span>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-2" style="color: var(--brand-dark);">Aún no realizaste compras</h4>
                        <p class="text-muted mx-auto mb-4" style="max-width: 400px; font-size: 0.95rem;">
                            ¡No te pierdas de vestir a tu mascota con el mejor estilo! Revisá nuestro catálogo y encontrá la prenda perfecta.
                        </p>
                        <a href="{{ route('productos.index') }}" class="btn btn-primary rounded-pill px-4">
                            Explorar Tienda
                        </a>
                    </div>
                @else
                    <!-- Tabla de compras -->
                    <div class="table-responsive">
                        <table class="table align-middle text-start">
                            <thead>
                                <tr class="sales-header-row py-2">
                                    <th class="ps-2">Pedido</th>
                                    <th>Fecha</th>
                                    <th>Pago</th>
                                    <th>Estado</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventas as $v)
                                    <tr class="order-row" style="border-bottom: 1px solid var(--color-border);">
                                        <td class="py-3 ps-2 fw-semibold text-dark" style="font-size: 0.8rem;">
                                            #{{ str_pad($v->id, 6, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="py-3 fw-semibold text-dark" style="font-size: 0.8rem;">
                                            {{ $v->fecha_venta ? $v->fecha_venta->format('d/m/Y H:i') : $v->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="py-3 fw-semibold text-dark" style="font-size: 0.8rem;">
                                            {{ $v->formaPago ? $v->formaPago->descripcion : 'N/A' }}
                                        </td>
                                        <td class="py-3">
                                            @if($v->estado === 'DESPACHADO')
                                                <span class="badge-status badge-despachado">Despachado</span>
                                            @else
                                                <span class="badge-status badge-confirmado">Confirmado</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-end fw-bold text-dark">
                                            ${{ number_format($v->total, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 text-center">
                                            <a href="{{ route('compras.factura', $v->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.8rem;">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                    <polyline points="7 10 12 15 17 10"/>
                                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                                </svg>
                                                <span>Factura</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

<!-- Modal de Edición de Perfil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; background-color: var(--color-surface);">
            <div class="modal-header border-bottom-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="editProfileModalLabel" style="font-family: var(--font-main); color: var(--brand-dark);">
                    Actualizar Datos de Perfil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('usuario.actualizar') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body px-4 pt-3 pb-2">
                    
                    <!-- Pestañas de Navegación -->
                    <ul class="nav nav-tabs mb-4 border-bottom" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-semibold" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">
                                Datos Personales y Envío
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-semibold" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">
                                Seguridad y Contraseña
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="profileTabsContent">
                        
                        <!-- Pestaña 1: Datos Personales y de Envío -->
                        <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label small fw-semibold text-muted">Nombre</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required style="border-radius: 10px;">
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="apellido" class="form-label small fw-semibold text-muted">Apellido</label>
                                    <input type="text" class="form-control @error('apellido') is-invalid @enderror" id="apellido" name="apellido" value="{{ old('apellido', $usuario->apellido) }}" required style="border-radius: 10px;">
                                    @error('apellido')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label small fw-semibold text-muted">Correo Electrónico</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $usuario->email) }}" required style="border-radius: 10px;">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="telefono" class="form-label small fw-semibold text-muted">Teléfono</label>
                                    <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $usuario->telefono) }}" style="border-radius: 10px;">
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                               
                                <div class="col-12">
                                    <label for="direccion" class="form-label small fw-semibold text-muted">Dirección</label>
                                    <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{ old('direccion', $usuario->direccion) }}" placeholder="Calle, Número, Piso/Depto" style="border-radius: 10px;">
                                    @error('direccion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label for="provincia" class="form-label small fw-semibold text-muted">Provincia</label>
                                    <select class="form-select @error('provincia') is-invalid @enderror" id="provincia" name="provincia" style="border-radius: 10px;" required>
                                        <option value="" disabled selected>Seleccioná una provincia</option>
                                    </select>
                                    @error('provincia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="localidad" class="form-label small fw-semibold text-muted">Localidad</label>
                                    <select class="form-select @error('localidad') is-invalid @enderror" id="localidad" name="localidad" style="border-radius: 10px;" disabled required>
                                        <option value="" disabled selected></option>
                                    </select>
                                    @error('localidad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>                                
                                <div class="col-md-3">
                                    <label for="codigo_postal" class="form-label small fw-semibold text-muted">Código Postal</label>
                                    <input type="text" class="form-control @error('codigo_postal') is-invalid @enderror" id="codigo_postal" name="codigo_postal" value="{{ old('codigo_postal', $usuario->codigo_postal) }}" style="border-radius: 10px;">
                                    @error('codigo_postal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Pestaña 2: Seguridad y Contraseña -->
                        <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                            <div class="alert border-0 rounded-3 mb-4 d-flex align-items-start gap-2" style="background-color: var(--neutral-50); color: var(--color-text-secondary); font-size: 0.85rem; border: 1px solid var(--color-border) !important;">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="mt-0.5 flex-shrink-0" style="color: var(--color-primary);">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    Completá esta sección <strong>únicamente</strong> si deseás cambiar tu contraseña de acceso. De lo contrario, dejalos en blanco.
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="password_actual" class="form-label small fw-semibold text-muted">Contraseña Actual</label>
                                    <input type="password" class="form-control @error('password_actual') is-invalid @enderror" id="password_actual" name="password_actual" placeholder="Ingresá tu contraseña actual" style="border-radius: 10px;">
                                    @error('password_actual')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_nueva" class="form-label small fw-semibold text-muted">Nueva Contraseña</label>
                                    <input type="password" class="form-control @error('password_nueva') is-invalid @enderror" id="password_nueva" name="password_nueva" placeholder="Mínimo 6 caracteres" style="border-radius: 10px;">
                                    @error('password_nueva')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_nueva_confirmation" class="form-label small fw-semibold text-muted">Confirmar Nueva Contraseña</label>
                                    <input type="password" class="form-control" id="password_nueva_confirmation" name="password_nueva_confirmation" placeholder="Repetí la nueva contraseña" style="border-radius: 10px;">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 pt-3 gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal" style="font-weight: 600;">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="font-weight: 600;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar Modal si hay errores de validación de Laravel
        @if($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
            myModal.show();
            
            // Si hay errores específicos de contraseña, activar la pestaña de seguridad
            @if($errors->has('password_actual') || $errors->has('password_nueva'))
                var securityTab = new bootstrap.Tab(document.getElementById('security-tab'));
                securityTab.show();
            @endif
        @endif

        // Selectores dinámicos de Provincia y Localidad
        const provSelect = document.getElementById('provincia');
        const locSelect = document.getElementById('localidad');
        
        let datosJurisdicciones = [];

        // Cargar el JSON estático desde la carpeta pública
        fetch('/locations.json')
            .then(response => response.json())
            .then(data => {
                datosJurisdicciones = data.argentina_jurisdicciones;

                // Poblar el selector de Provincias
                datosJurisdicciones.forEach(j => {
                    const opt = document.createElement('option');
                    opt.value = j.provincia;
                    opt.textContent = j.provincia;
                    
                    // Preseleccionar si el usuario ya tenía este dato guardado
                    if (j.provincia === "{{ old('provincia', $usuario->provincia) }}") {
                        opt.selected = true;
                    }
                    provSelect.appendChild(opt);
                });

                // Si hay un valor preseleccionado, disparar la carga de localidades
                if (provSelect.value) {
                    provSelect.dispatchEvent(new Event('change'));
                }
            })
            .catch(err => console.error("Error cargando ubicaciones:", err));

        // Escuchar cambios en Provincia
        provSelect.addEventListener('change', function() {
            const provSeleccionada = provSelect.value;
            locSelect.innerHTML = '<option value="" disabled selected>Seleccioná una localidad</option>';
            locSelect.disabled = true;

            // Buscar los datos de la provincia seleccionada
            const jurisdiccion = datosJurisdicciones.find(j => j.provincia === provSeleccionada);

            if (jurisdiccion) {
                // Unir la capital con las ciudades principales y ordenarlas alfabéticamente
                const listaLocalidades = [jurisdiccion.capital, ...jurisdiccion.localidades_principales].sort((a, b) => a.localeCompare(b));

                listaLocalidades.forEach(loc => {
                    const opt = document.createElement('option');
                    opt.value = loc;
                    opt.textContent = loc;

                    // Preseleccionar si el usuario ya tenía esta localidad guardada
                    if (loc === "{{ old('localidad', $usuario->localidad) }}") {
                        opt.selected = true;
                    }
                    locSelect.appendChild(opt);
                });

                locSelect.disabled = false;
            }
        });
    });
</script>
@endsection
