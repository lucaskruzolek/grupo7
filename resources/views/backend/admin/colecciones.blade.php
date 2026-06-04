@extends('backend.admin.layout')

@section('styles')
    <!-- Estilos específicos de la gestión de colecciones -->
    <link rel="stylesheet" href="{{ asset('css/backend/colecciones.css') }}">
@endsection

@section('contenido')
<div class="container-fluid px-0">
    <!-- Encabezado Principal -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="section-title">Gestión de Colecciones</h1>
            <p class="section-subtitle">Crea, edita y administra las colecciones y sus portadas visuales.</p>
        </div>
        <button class="btn-admin btn-admin-primary" onclick="openCreateColeccionModal()">
            + Nueva colección
        </button>
    </div>

    <!-- Grilla de Colecciones -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse($colecciones as $coleccion)
            @php
                $prodCount = $coleccion->productos_count ?? 0;
            @endphp
            <div class="col">
                <div class="collection-card">
                    <!-- Imagen de Portada -->
                    <div class="collection-card-img-wrapper">
                        @if($coleccion->url_imagen)
                            <img src="{{ $coleccion->url_imagen }}" alt="{{ $coleccion->nombre }}" class="collection-card-img">
                        @else
                            <!-- Placeholder premium con degradado suave e icono -->
                            <div class="d-flex align-items-center justify-content-center w-100 h-100" style="background: linear-gradient(135deg, #e3dfd8 0%, #f4f1eb 100%);">
                                <img src="{{ asset('img/icons/paw.svg') }}" alt="Sin imagen" style="width: 48px; opacity: 0.35;">
                            </div>
                        @endif

                        <!-- Badge con contador de productos -->
                        <span class="collection-count-badge">
                            🛍️ {{ $prodCount }} {{ $prodCount === 1 ? 'producto' : 'productos' }}
                        </span>
                    </div>

                    <!-- Cuerpo de la tarjeta -->
                    <div class="collection-card-body">
                        <div>
                            <h3 class="collection-title">{{ $coleccion->nombre }}</h3>
                            <p class="collection-description">
                                {{ $coleccion->descripcion ?: 'Sin descripción provista para esta colección.' }}
                            </p>
                        </div>

                        <!-- Acciones -->
                        <div class="collection-card-actions">
                            <button type="button" class="btn-link-action btn-link-action-secondary" title="Editar" 
                                onclick="openEditColeccionModal({{ $coleccion->id }}, '{{ addslashes($coleccion->nombre) }}', '{{ addslashes($coleccion->descripcion) }}', '{{ $coleccion->url_imagen }}')">
                                ✏️ Editar
                            </button>
                            <form action="{{ route('admin.colecciones.destroy', $coleccion->id) }}" method="POST" class="d-inline" 
                                onsubmit="return confirm('¿Estás seguro de que deseas eliminar la colección &quot;{{ $coleccion->nombre }}&quot;? Los productos asociados se mantendrán activos pero se desvincularán.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-link-action btn-link-action-danger" title="Eliminar">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 w-100">
                <div class="d-flex flex-column align-items-center justify-content-center bg-white p-5 text-center shadow-sm border-0 rounded-3" style="min-height: 320px; border-radius: 16px;">
                    <div class="fs-1 mb-3">🏷️</div>
                    <h2 class="h4 text-dark fw-bold mb-2">No hay colecciones creadas</h2>
                    <p class="text-muted mb-4" style="max-width: 420px;">
                        Comienza creando tu primera colección agrupando productos con una hermosa imagen de portada.
                    </p>
                    <button class="btn-admin btn-admin-primary" onclick="openCreateColeccionModal()">
                        + Crear primera colección
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal CRUD Colección -->
<div class="modal fade" id="modalColeccion" tabindex="-1" aria-labelledby="modalColeccionTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <form id="form-coleccion" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="method-field-container"></div>
                
                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4 position-relative">
                    <h5 class="modal-title fw-bold text-dark poppins-bold" id="modalColeccionTitle">Nueva Colección</h5>
                    <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3" data-bs-dismiss="modal" aria-label="Cerrar" style="box-shadow: none;"></button>
                </div>
                
                <div class="modal-body px-4 py-3 text-start">
                    <!-- Campo Nombre -->
                    <div class="form-group-admin">
                        <label for="collection-name" class="form-label-admin">Nombre de la colección <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="collection-name" class="form-control form-control-admin" required placeholder="Ej: Invierno 2026, Especial de Lluvia...">
                    </div>
                    
                    <!-- Campo Descripción -->
                    <div class="form-group-admin mt-3">
                        <label for="collection-description" class="form-label-admin">Descripción</label>
                        <textarea name="descripcion" id="collection-description" rows="3" class="form-control form-control-admin" placeholder="Breve detalle sobre la temática de esta colección..."></textarea>
                    </div>

                    <!-- Toggle Origen de Imagen -->
                    <div class="form-group-admin mt-3">
                        <label class="form-label-admin">Portada de la colección</label>
                        <div class="source-toggle-container mb-3">
                            <button type="button" id="toggle-source-file" class="btn-source-toggle active" onclick="setSourceMode('file')">
                                📁 Subir archivo
                            </button>
                            <button type="button" id="toggle-source-url" class="btn-source-toggle" onclick="setSourceMode('url')">
                                🔗 URL externa
                            </button>
                        </div>

                        <!-- Opción A: Archivo local -->
                        <div id="group-source-file">
                            <div class="image-upload-wrapper" onclick="document.getElementById('collection-imagen-file').click()">
                                <div class="image-upload-icon">📤</div>
                                <span class="d-block fw-semibold text-dark" style="font-size: 0.82rem;">Selecciona una imagen de portada</span>
                                <span class="text-muted d-block" style="font-size: 0.72rem;">Formatos admitidos: PNG, JPG, WebP. Peso máximo 5MB.</span>
                                <input type="file" id="collection-imagen-file" class="d-none" accept="image/*">
                            </div>
                        </div>

                        <!-- Opción B: URL Externa -->
                        <div id="group-source-url" style="display: none;">
                            <input type="url" id="collection-url-imagen" class="form-control form-control-admin" placeholder="https://ejemplo.com/imagen.jpg">
                            <small class="text-muted mt-1 d-block" style="font-size: 0.72rem;">Ingresa la ruta absoluta de una imagen alojada en un servidor externo.</small>
                        </div>

                        <!-- Vista previa de imagen -->
                        <div id="image-preview-container" class="image-preview-box" style="display: none;">
                            <img id="image-preview-img" src="" alt="Vista previa de portada">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4 d-flex justify-content-between gap-2">
                    <button type="button" class="btn-admin btn-admin-secondary flex-grow-1" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-admin btn-admin-primary flex-grow-1">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/backend/colecciones.js') }}"></script>
@endsection
