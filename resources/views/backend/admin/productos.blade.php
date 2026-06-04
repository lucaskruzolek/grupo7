@extends('backend.admin.layout')

@section('styles')
    <!-- Estilos específicos de la gestión de productos -->
    <link rel="stylesheet" href="{{ asset('css/backend/productos.css') }}">
@endsection

@section('contenido')
<div class="container-fluid px-0">
    
    <!-- Encabezado Principal -->
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">
        <div>
            <h1 class="section-title">Gestión de Productos</h1>
        </div>
        <button class="btn-admin btn-admin-primary" id="btn-new-product" onclick="openCreateProductModal()">
            <span>+</span> Nuevo Producto
        </button>
    </div>

    <!-- Layout Master-Detail de dos columnas -->
    <div class="layout-split">
        
        <!-- COLUMNA IZQUIERDA: Buscador y Listado de Productos (Master) -->
        <div class="layout-split-left">
            <div class="surface-card p-3 pb-1 rounded-bottom-0">
                <div class="input-group search-bar-wrapper mb-2">
                    <input type="text" id="search-prod-input" class="form-control search-input" placeholder="Buscar por nombre, SKU o código..." aria-label="Buscar productos">
                    <button class="btn search-btn" type="button">
                        <img src="{{ asset('img/icons/search.svg') }}" alt="Buscar" style="width: 18px; height: 18px;">
                    </button>
                </div>
                
                <!-- Selectores de Filtro -->
                <div class="d-flex gap-2 mb-1">
                    <select class="filter-select flex-grow-1" id="filter-category">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $parent)
                            <optgroup label="{{ ucfirst($parent->nombre) }}">
                                <option value="{{ $parent->id }}">Ver Todo {{ ucfirst($parent->nombre) }}</option>
                                @foreach($parent->children as $child)
                                    <option value="{{ $child->id }}">{{ ucfirst($child->nombre) }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    
                    <select class="filter-select flex-grow-1" id="filter-pet">
                        <option value="">Todos</option>
                        <option value="perro">Perros</option>
                        <option value="gato">Gatos</option>
                        <option value="ambos">Ambos</option>
                    </select>
                    <span class="small text-muted" id="products-count-text"></span>                    
                </div>
            </div>

            <!-- Lista de Productos Scrolleable -->
            <div class="product-list-scroll" id="product-list-container">
                <div id="product-list-cards-wrapper">
                    @foreach(array_slice($productosData, 0, 20) as $index => $prod)
                        <div class="product-list-card {{ $index === 0 ? 'active' : '' }}" 
                             data-sku="{{ $prod['sku_base'] }}" 
                             onclick="selectProduct(this)">
                            
                            <img src="{{ $prod['thumb'] }}" class="product-list-thumb" alt="{{ $prod['nombre_base'] }}">
                            
                            <div class="product-list-info">
                                <h4 class="product-list-title">{{ $prod['nombre_base'] }}</h4>
                                <span class="product-list-sku">{{ $prod['sku_base'] }}</span>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge bg-light text-dark border py-1 small">Activo</span>
                                    <small class="text-muted fw-bold">
                                        {{ $prod['colores_count'] }} {{ $prod['colores_count'] === 1 ? 'Color' : 'Colores' }} | 
                                        {{ $prod['talles_count'] }} {{ $prod['talles_count'] === 1 ? 'Talle' : 'Talles' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Sentinel para scroll infinito -->
                <div id="scroll-sentinel" style="height: 10px; margin-bottom: 2rem;"></div>
                <div id="list-loading-spinner" class="text-center py-3 d-none">
                    <div class="spinner-border spinner-border-sm text-secondary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Detalle y Variantes del Producto (Detail) -->
        <div class="layout-split-right" id="product-detail-panel">
            <!-- Spinner de carga del panel derecho -->
            <div id="detail-loading-spinner" class="d-none flex-grow-1 d-flex flex-column align-items-center justify-content-center text-center py-5">
                <div class="spinner-border text-secondary mb-2" role="status" style="width: 2.5rem; height: 2.5rem;">
                    <span class="visually-hidden">Cargando detalles...</span>
                </div>
                <p class="text-muted small">Cargando información del producto...</p>
            </div>

            <div class="detail-card text-start" id="detail-card-container">
                
                <!-- Encabezado de Detalle -->
                <div class="mb-3">
                    <span class="badge bg-light text-muted border mb-2" id="detail-sku-base">-</span>
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <div class="flex-grow-1 min-w-0">
                            <h2 class="h3 text-dark fw-bold mb-0 view-mode" id="detail-title">-</h2>
                            <div class="edit-mode">
                                <input type="text" class="form-control form-control-admin fw-bold" id="edit-title" value="">
                            </div>
                        </div>
                        <div class="d-flex gap-2 align-items-center flex-shrink-0">
                            <button type="button" class="btn-admin btn-admin-secondary edit-mode" id="btn-cancel-edit" onclick="cancelEditMode()">
                                ❌ <span class="d-none d-md-inline">Cancelar</span>
                            </button>
                            <button class="btn-admin btn-admin-secondary" id="btn-toggle-edit" onclick="toggleEditMode()">
                                <span class="view-mode">✏️ <span class="d-none d-md-inline">Editar</span></span>
                                <span class="edit-mode" style="color: var(--color-primary);">💾 <span class="d-none d-md-inline">Guardar</span></span>
                            </button>
                            <form id="delete-product-form" action="" method="POST" class="edit-mode d-inline" onsubmit="return confirm('¿Estás seguro de que deseas dar de baja este producto y todas sus variantes?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-admin btn-admin-secondary" style="height: 38px; border-radius: 8px;">
                                    🗑️ <span class="d-none d-md-inline">Eliminar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Cuerpo de Detalle (Galería + Info General) -->
                <div class="row g-4 mb-4">
                    <!-- Galería de Fotos -->
                    <div class="col-12 col-md-6">
                        <div class="media-container">
                            <div class="main-image-wrapper">
                                <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" class="main-image" id="gallery-main-img" alt="Main Producto">
                                <button class="gallery-nav-btn gallery-nav-prev" onclick="prevImage()">‹</button>
                                <button class="gallery-nav-btn gallery-nav-next" onclick="nextImage()">›</button>
                            </div>
                            
                            <div class="thumbnail-list" id="gallery-thumb-container">
                                <!-- Thumbnails dinámicos -->
                            </div>
                        </div>

                        <!-- Precio  -->
                        <div class="price-container-wrapper">
                            <div class="form-group-admin mb-0">
                                <span class="form-label-admin mb-1">Precio</span>
                                <div class="view-mode">
                                    <span class="fs-4 fw-bold text-success" id="detail-price">-</span>
                                </div>
                                <div class="edit-mode">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">$</span>
                                        <input type="number" step="0.01" class="form-control form-control-sm form-control-admin border-start-0" id="edit-price" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Carga/Edición de Imágenes (Visible en Modo Edición) -->
                        <div class="edit-mode mt-3">
                            <label class="form-label-admin d-block mb-2">Imágenes del Color Activo</label>
                            <div class="uploaded-images-grid d-flex flex-wrap gap-2 mb-3" id="edit-images-container">
                                <!-- Renderizado dinámico de miniaturas -->
                            </div>
                            <div class="image-upload-zone p-3 border border-dashed rounded text-center position-relative" style="border-style: dashed !important; border-color: var(--neutral-400) !important; background-color: var(--neutral-100); cursor: pointer;">
                                <input type="file" id="image-upload-input" accept="image/*" multiple style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                                <div class="upload-zone-content">
                                    <span class="upload-icon fs-3 d-block mb-1">📤</span>
                                    <span class="small text-muted d-block">Haz clic o arrastra imágenes aquí</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">Máx. 5MB por imagen. Se optimizarán automáticamente a WebP.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información General -->
                    <div class="col-12 col-md-6">
                        <div class="p-3 border rounded" style="background-color: var(--neutral-100);">
                            <h4 class="h6 fw-bold mb-3 border-bottom pb-2" style="font-family: poppins">Información general</h4>
                            
                            <div class="form-group-admin view-mode">
                                <span class="form-label-admin">Descripción</span>
                                <p class="text-secondary small mb-0 text-hyphenated" id="detail-desc">-</p>
                            </div>

                            <div class="form-group-admin edit-mode">
                                <label class="form-label-admin">Descripción</label>
                                <textarea class="form-control form-control-admin" id="edit-desc" rows="3"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group-admin">
                                        <span class="form-label-admin">Categoría</span>
                                        <div>
                                            <span class="text-secondary small mb-0" id="detail-category-parent">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group-admin">
                                        <span class="form-label-admin">Subcategoría</span>
                                        <div>
                                            <span class="text-secondary small mb-0" id="detail-category-child">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group-admin">
                                        <span class="form-label-admin">Tipo de mascota</span>
                                        <div class="view-mode">
                                            <span class="text-secondary small mb-0" id="detail-pet">-</span>
                                        </div>
                                        <div class="edit-mode">
                                            <select class="form-select form-select-sm shadow-none" id="edit-pet">
                                                <option value="perro">Perros</option>
                                                <option value="gato">Gatos</option>
                                                <option value="ambos">Ambos</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group-admin">
                                        <span class="form-label-admin">Stock Mínimo</span>
                                        <div class="view-mode">
                                            <span class="text-secondary small mb-0" id="detail-stock-min">-</span>
                                        </div>
                                        <div class="edit-mode">
                                            <input type="number" class="form-control form-control-sm form-control-admin" id="edit-stock-min" value="0" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-0 pb-0 mt-2" style="font-size: 0.8rem;">
                                <div class="col-6 text-muted">Creación: <span id="detail-created">-</span></div>
                                <div class="col-6 text-muted text-end">Modificación: <span id="detail-updated">-</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Variantes (Color por Talle) -->
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-1 flex-wrap gap-2">
                        <h3 class="h5 text-dark fw-bold mb-0">Stock por talle y color</h3>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-sm btn-outline-secondary" id="btn-add-talle-trigger" style="font-size: 0.75rem;" onclick="openAddTalleModal()">+ Agregar Talle</button>
                            <button class="btn btn-sm btn-outline-secondary" id="btn-add-color-trigger" style="font-size: 0.75rem;" onclick="openAddColorModal()">+ Agregar Color</button>
                        </div>
                    </div>

                    <!-- Tabla Matriz -->
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr id="variants-table-header-row">
                                    <th>Color</th>
                                </tr>
                            </thead>
                            <tbody id="variants-table-body">
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- Modal: Agregar Talle -->
<div class="modal fade" id="modalAddTalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Agregar Talle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3 text-start">
                <p class="text-muted small mb-3">Selecciona uno de los talles definidos en el sistema para agregar como columna a este producto:</p>
                <div class="form-group-admin">
                    <label for="select-talle-system" class="form-label-admin">Talle disponible</label>
                    <select id="select-talle-system" class="form-select form-select-admin">
                        <!-- Llenado dinámicamente -->
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-admin btn-admin-primary" id="btn-confirm-add-talle" onclick="confirmAddTalle()">Agregar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Agregar Color -->
<div class="modal fade" id="modalAddColor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Agregar Color</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3 text-start">
                <p class="text-muted small mb-3">Selecciona uno de los colores definidos en el sistema para agregar como fila a este producto:</p>
                <div class="form-group-admin">
                    <label for="select-color-system" class="form-label-admin">Color disponible</label>
                    <select id="select-color-system" class="form-select form-select-admin">
                        <!-- Llenado dinámicamente -->
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-admin btn-admin-primary" id="btn-confirm-add-color" onclick="confirmAddColor()">Agregar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Crear Variación -->
<div class="modal fade" id="modalCreateVariation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">Crear Variación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3 text-start">
                <p class="text-muted small mb-3">Estás a punto de habilitar una nueva combinación física en el inventario:</p>
                <div class="p-3 rounded mb-3" style="background-color: var(--neutral-100);">
                    <strong class="d-block text-dark" id="modal-variation-product">Buzo Tejido Invierno</strong>
                    <span class="text-secondary small d-block mt-1" id="modal-variation-details">Color: Rojo / Talle: XS</span>
                </div>
                <div class="form-group-admin">
                    <label for="variation-stock" class="form-label-admin">Stock Inicial</label>
                    <input type="number" id="variation-stock" class="form-control form-control-admin" value="0" min="0">
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-admin btn-admin-primary" onclick="confirmCreateVariation()">Crear Variación</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Éxito -->
<div class="modal fade" id="modalSuccessVariation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4" style="border-radius: 12px; border: none;">
            <div class="fs-1 mb-2">✨</div>
            <h4 class="fw-bold text-dark mb-2">¡Variación Creada!</h4>
            <p class="text-muted small mb-4" id="modal-success-message">La variante ha sido agregada con éxito al inventario.</p>
            <button type="button" class="btn-admin btn-admin-primary w-100" data-bs-dismiss="modal">Aceptar</button>
        </div>
    </div>
</div>

<!-- Modal: Crear Producto -->
<div class="modal fade" id="modalCreateProduct" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <form action="{{ route('admin.productos.store') }}" method="POST" id="form-create-product" onsubmit="return validateCreateProductForm(event)">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3 text-start">
                    <div class="form-group-admin mb-2">
                        <label for="new-prod-name" class="form-label-admin">Nombre del Producto</label>
                        <input type="text" name="nombre" id="new-prod-name" class="form-control form-control-admin" required placeholder="Ej: Buzo Polar Térmico">
                    </div>
                    <div class="form-group-admin mb-2">
                        <label for="new-prod-sku" class="form-label-admin">SKU Base</label>
                        <input type="text" name="sku_base" id="new-prod-sku" class="form-control form-control-admin" required placeholder="Ej: BUZO-POLAR">
                    </div>
                    <div class="form-group-admin mb-2">
                        <label for="new-prod-desc" class="form-label-admin">Descripción</label>
                        <textarea name="descripcion" id="new-prod-desc" class="form-control form-control-admin" rows="2" placeholder="Descripción corta del producto..."></textarea>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <div class="form-group-admin">
                                <label for="new-prod-category" class="form-label-admin">Categoría</label>
                                <select name="categoria_id" id="new-prod-category" class="form-select form-select-admin" required>
                                    @foreach($categorias as $parent)
                                        <optgroup label="{{ ucfirst($parent->nombre) }}">
                                            @foreach($parent->children as $child)
                                                <option value="{{ $child->id }}">{{ ucfirst($child->nombre) }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group-admin">
                                <label for="new-prod-collection" class="form-label-admin">Colección</label>
                                <select name="coleccion_id" id="new-prod-collection" class="form-select form-select-admin">
                                    <option value="">Ninguna</option>
                                    @foreach($colecciones as $col)
                                        <option value="{{ $col->id }}">{{ $col->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <div class="form-group-admin">
                                <label for="new-prod-pet" class="form-label-admin">Tipo de Mascota</label>
                                <select name="tipo_mascota" id="new-prod-pet" class="form-select form-select-admin" required>
                                    <option value="ambos">Ambos</option>
                                    <option value="perro">Perros</option>
                                    <option value="gato">Gatos</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group-admin">
                                <label for="new-prod-price" class="form-label-admin">Precio</label>
                                <input type="number" name="precio" id="new-prod-price" class="form-control form-control-admin" required min="0" step="0.01" placeholder="Ej: 12500">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <div class="form-group-admin">
                                <label for="new-prod-stock-min" class="form-label-admin">Stock Mínimo</label>
                                <input type="number" name="stock_minimo" id="new-prod-stock-min" class="form-control form-control-admin" required min="0" value="2" placeholder="Ej: 2">
                            </div>
                        </div>
                    </div>
                    <hr class="my-3">
                    <p class="text-muted small mb-2 fw-bold">Variantes del Producto:</p>
                    
                    <!-- Formulario local para agregar variantes a la lista -->
                    <div class="row align-items-end g-2 mb-3">
                        <div class="col-5">
                            <div class="form-group-admin m-0">
                                <label for="new-variant-color" class="form-label-admin">Color</label>
                                <select id="new-variant-color" class="form-select form-select-admin">
                                    @foreach($coloresSystem as $col)
                                        <option value="{{ $col['id'] }}" data-name="{{ $col['name'] }}">{{ $col['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group-admin m-0">
                                <label for="new-variant-talle" class="form-label-admin">Talle</label>
                                <select id="new-variant-talle" class="form-select form-select-admin">
                                    @foreach($tallesSystem as $talle)
                                        <option value="{{ $talle }}">{{ $talle === '-' ? '-' : $talle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group-admin m-0">
                                <label for="new-variant-stock" class="form-label-admin">Stock</label>
                                <input type="number" id="new-variant-stock" class="form-control form-control-admin" min="0" value="10">
                            </div>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn-admin btn-admin-secondary w-100" style="padding: 0.5rem 0; height: 35px; display: flex; align-items: center; justify-content: center;" onclick="addVariantToNewProduct()">
                                +
                            </button>
                        </div>
                    </div>

                    <!-- Tabla de variantes agregadas -->
                    <div class="table-responsive mb-2" style="max-height: 200px; overflow-y: auto; border: 1px solid var(--neutral-200); border-radius: 8px;">
                        <table class="table admin-table mb-0" style="font-size: 0.85rem;">
                            <thead class="sticky-top bg-white">
                                <tr>
                                    <th style="padding: 0.5rem 1rem;">Color</th>
                                    <th style="padding: 0.5rem 1rem;">Talle</th>
                                    <th style="padding: 0.5rem 1rem; text-align: center;">Stock</th>
                                    <th style="padding: 0.5rem 1rem; text-align: right;">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="new-product-variants-table-body">
                                <!-- Se llena dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Contenedor de inputs ocultos que se enviarán al servidor -->
                    <div id="new-prod-hidden-inputs"></div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-admin btn-admin-primary">Crear Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        window.LaravelConfig = {
            productosData: @json($productosData),
            coloresSystem: @json($coloresSystem),
            tallesSystem: @json($tallesSystem),
            categoriasSystem: @json($categoriasSystem),
            defaultImagePath: "{{ asset('img/ui/productos/perro-buzo-verde.webp') }}",
            updateGroupUrl: "{{ route('admin.productos.updateGroup') }}",
            uploadImageUrl: "{{ route('admin.productos.images.upload') }}",
            deleteImageUrl: "{{ url('admin/productos/images') }}",
            coverImageUrl: "{{ url('admin/productos/images') }}",
            csrfToken: "{{ csrf_token() }}",
            productosUrl: "{{ url('admin/productos') }}"
        };
    </script>
    <script src="{{ asset('js/backend/productos.js') }}"></script>
@endsection
