@extends('backend.admin.layout')

@section('styles')
    <!-- Estilos específicos de la gestión de categorías -->
    <link rel="stylesheet" href="{{ asset('css/backend/categorias.css') }}">
@endsection

@section('contenido')
<div class="container-fluid px-0">
    <!-- Encabezado Principal -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="section-title">Gestión de Categorías</h1>
            <p class="section-subtitle">Administra las categorías principales y subcategorías de Pet Threads.</p>
        </div>
        <button class="btn-admin btn-admin-primary" onclick="openCreateParentModal()">
            + Nueva categoría principal
        </button>
    </div>

    <!-- Contenedor del listado en árbol -->
    <div class="row g-3">
        @forelse($categorias as $parent)
            @php
                $parentSlug = Str::slug($parent->nombre);
                $subCount = $parent->children->count();
            @endphp
            <div class="col-12 col-lg-6">
                <div class="card category-parent-card shadow-sm border-0">
                    <!-- Cabecera de Categoría Principal -->
                    <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between collapsed" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $parent->id }}" aria-expanded="false" aria-controls="collapse-{{ $parent->id }}">
                        <div class="d-flex align-items-center gap-3">
                            <!-- Flecha colapsable -->
                            <div class="collapse-icon-wrapper text-muted me-1">
                                <span class="collapse-chevron" id="chevron-{{ $parent->id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
                                      <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </span>
                            </div>
                            
                            <!-- Icono de la Categoría Principal -->
                            <div class="category-icon-circle">
                                @if($parent->icono)
                                    <img src="{{ $parent->icono }}" alt="{{ $parent->nombre }}" class="category-icon-img">
                                @else               
                                    <img src="{{ asset('img/icons/catalogo.svg') }}" alt="Categoría">
                                @endif
                            </div>

                            <!-- Nombre y cantidad de subcategorías -->
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark text-uppercase fs-6 mb-0 poppins-bold">{{ $parent->nombre }}</span>
                                <span class="badge bg-light text-muted border rounded-pill px-3 py-1 font-main" style="font-size: 0.65rem;">
                                    {{ $subCount }} {{ $subCount === 1 ? 'subcategoría' : 'subcategorías' }}
                                </span>
                            </div>
                        </div>

                        <!-- Acciones a la derecha de la Categoría Principal -->
                        <div class="d-flex gap-2 align-items-center" onclick="event.stopPropagation()">
                            <button type="button" class="btn-link-action btn-link-action-secondary" title="Editar" onclick="openEditModal({{ $parent->id }}, '{{ $parent->nombre }}', null, '{{ $parent->icono }}', {{ $parent->pide_talle ? 1 : 0 }}, {{ $parent->pide_color ? 1 : 0 }})">
                                ✏️
                            </button>
                            <form action="{{ route('admin.categorias.destroy', $parent->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas dar de baja la categoría principal &quot;{{ $parent->nombre }}&quot; y todas sus subcategorías asociadas? Esto no borrará productos pero sí se desvincularán.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-link-action btn-link-action-danger" title="Eliminar">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Cuerpo colapsable (Subcategorías) -->
                    <div class="collapse" id="collapse-{{ $parent->id }}">
                        <div class="card-body bg-white pt-1 pb-4 px-4">
                            <div class="subcategories-tree-container">
                                @if($parent->children->isEmpty())
                                    <p class="text-muted small ps-5 py-2 mb-0">No hay subcategorías agregadas en esta sección.</p>
                                @else
                                    <ul class="list-unstyled mb-0 subcategories-list">
                                        @foreach($parent->children as $child)
                                            <li class="subcategory-item py-2 d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <!-- Nombre de la Subcategoría (Sin Icono ni Drag handle) -->
                                                    <span class="text-dark poppins-semibold text-capitalize" style="font-size: 0.95rem;">{{ $child->nombre }}</span>
                                                </div>

                                                <!-- Acciones de la Subcategoría -->
                                                <div class="d-flex gap-2 align-items-center">
                                                    <button type="button" class="btn-link-action btn-link-action-secondary" title="Editar" onclick="openEditModal({{ $child->id }}, '{{ $child->nombre }}', {{ $parent->id }}, null, {{ $child->pide_talle ? 1 : 0 }}, {{ $child->pide_color ? 1 : 0 }})">
                                                        ✏️
                                                    </button>
                                                    <form action="{{ route('admin.categorias.destroy', $child->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar la subcategoría &quot;{{ $child->nombre }}&quot;?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-link-action btn-link-action-danger" title="Eliminar">
                                                            🗑️
                                                        </button>
                                                    </form>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                                
                                <!-- Botón de agregar subcategoría -->
                                <div class="add-subcategory-wrapper mt-3 ps-5">
                                    <button class="btn btn-outline-dashed-light" onclick="openCreateSubcategoryModal({{ $parent->id }}, '{{ $parent->nombre }}')">
                                        <span class="fs-5">+</span> Agregar subcategoría a {{ ucfirst($parent->nombre) }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="surface-card p-5 text-center align-items-center justify-content-center shadow-sm" style="min-height: 300px;">
                    <div class="fs-1 mb-3">📁</div>
                    <h2 class="h3 text-dark fw-bold mb-2">No hay categorías</h2>
                    <p class="text-muted mb-0" style="max-width: 480px;">
                        Comienza creando una categoría principal usando el botón de arriba.
                    </p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal CRUD Categoría -->
<div class="modal fade" id="modalCategory" tabindex="-1" aria-labelledby="modalCategoryTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <form id="form-category" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="method-field-container"></div>
                
                <div class="modal-header border-bottom-0 pb-0 px-4 pt-4 position-relative">
                    <h5 class="modal-title fw-bold text-dark poppins-bold" id="modalCategoryTitle">Editar categoría</h5>
                    <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3" data-bs-dismiss="modal" aria-label="Close" style="box-shadow: none;"></button>
                </div>
                
                <div class="modal-body px-4 py-3 text-start">
                    <!-- Campo Tipo -->
                    <div class="form-group-admin">
                        <label for="category-type" class="form-label-admin">Tipo</label>
                        <div class="position-relative">
                            <select id="category-type" class="form-select form-control-admin" style="padding-right: 2.5rem;" onchange="handleTypeChange()">
                                <option value="parent">Categoría Principal</option>
                                <option value="child">Subcategoría</option>
                            </select>
                            <span class="position-absolute end-0 top-50 translate-middle-y me-3 text-muted" id="type-lock-icon" style="display: none;">
                                🔒
                            </span>
                        </div>
                    </div>
                    
                    <!-- Campo Nombre -->
                    <div class="form-group-admin mt-3">
                        <label for="category-name" class="form-label-admin">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="category-name" class="form-control form-control-admin" required placeholder="Ej: Ropa, Accesorios...">
                    </div>
                    
                    <!-- Campo Icono SVG (Solo para principales) -->
                    <div class="form-group-admin mt-3" id="icon-file-group">
                        <label for="category-icono" class="form-label-admin">Icono (.svg) <span class="text-danger" id="icon-required-asterisk" style="display:none;">*</span></label>
                        <input type="file" name="icono" id="category-icono" class="form-control form-control-admin" accept=".svg">
                        <small class="text-muted mt-1 d-block" style="font-size: 0.72rem;">Sube un archivo vectorial en formato SVG para representar visualmente la categoría principal.</small>
                        
                        <!-- Vista previa del icono actual -->
                        <div id="icon-preview-container" class="mt-3 p-2 border rounded d-flex align-items-center gap-3 bg-light" style="display: none !important;">
                            <span class="form-label-admin mb-0 text-muted" style="font-size: 0.75rem;">Icono actual:</span>
                            <div class="bg-white rounded border d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                                <img id="icon-preview-img" src="" style="width: 28px; height: 28px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campo Categoría Padre (Solo para subcategorías) -->
                    <div class="form-group-admin mt-3" id="parent-select-group" style="display: none;">
                        <label for="category-parent" class="form-label-admin">Categoría padre</label>
                        <div class="position-relative">
                            <select name="parent_id" id="category-parent" class="form-select form-control-admin" style="padding-right: 2.5rem;">
                                @foreach($categorias as $p)
                                    <option value="{{ $p->id }}">{{ ucfirst($p->nombre) }}</option>
                                @endforeach
                            </select>
                            <span class="position-absolute end-0 top-50 translate-middle-y me-3 text-muted" id="parent-lock-icon" style="display: none;">
                                🔒
                            </span>
                        </div>
                        <small class="text-muted mt-1 d-block" id="parent-helper-text" style="font-size: 0.72rem; display: none;">
                            La categoría padre no puede modificarse.
                        </small>
                    </div>

                    <!-- Toggles para variaciones de Talle y Color -->
                    <div class="form-group-admin mt-4">
                        <label class="form-label-admin mb-2">Variaciones permitidas</label>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="pide_talle" id="category-pide-talle" value="1" checked>
                            <label class="form-check-label text-dark small" for="category-pide-talle">Permitir variaciones por Talle</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="pide_color" id="category-pide-color" value="1" checked>
                            <label class="form-check-label text-dark small" for="category-pide-color">Permitir variaciones por Color</label>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4 d-flex justify-content-between gap-2">
                    <button type="button" class="btn-admin btn-admin-secondary flex-grow-1" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-admin btn-admin-primary flex-grow-1" id="btn-save-category">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/backend/categorias.js') }}"></script>
@endsection
