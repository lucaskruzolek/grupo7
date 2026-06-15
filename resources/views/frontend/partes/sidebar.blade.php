<div class="p-1 min-vh-100 filter-sidebar-container">
    
    {{-- FILA 1: Título y Limpiar Filtros --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('img/icons/filters.svg') }}" alt="Filtros" style="width: 20px; height: 20px; opacity: 0.7;">
            <h5 class="poppins-bold text-uppercase fs-6 mb-0 text-main" style="letter-spacing: 0.5px;">Filtrar Productos</h5>
        </div>
        <a href="{{ route('productos.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 poppins-semibold" style="font-size: 0.75rem;">
            Limpiar Filtros
        </a>
    </div>

    <hr class="my-3" style="opacity: 0.1;">

    {{-- FILA 2: ¿Para quién? --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="poppins-bold text-main fs-6 d-flex align-items-center gap-2">
                <span class="paw-icon" style="font-size: 0.95rem; width: 1.2em; height: 1.2em;"></span>¿PARA QUIÉN?
            </span>
            <i class="bi bi-chevron-up text-secondary small"></i>
        </div>
        <div class="row g-2">
            <div class="col-6">
                <input type="radio" name="mascota" value="perro" id="mascota_perro" class="d-none filtro-automatico" {{ request('mascota') == 'perro' ? 'checked' : '' }}>
                <label for="mascota_perro" class="btn btn-light w-100 py-3 border rounded-3 d-flex flex-column align-items-center justify-content-center gap-2 {{ request('mascota') == 'perro' ? 'active-filter-card border-primary bg-light' : '' }}" style="cursor: pointer;">
                    <img src="{{ asset('img/icons/dog.svg') }}" alt="Perros" style="width: 32px; height: 32px;">
                    <span class="poppins-semibold text-main small">Perros</span>
                </label>
            </div>
            <div class="col-6">
                <input type="radio" name="mascota" value="gato" id="mascota_gato" class="d-none filtro-automatico" {{ request('mascota') == 'gato' ? 'checked' : '' }}>
                <label for="mascota_gato" class="btn btn-light w-100 py-3 border rounded-3 d-flex flex-column align-items-center justify-content-center gap-2 {{ request('mascota') == 'gato' ? 'active-filter-card border-primary bg-light' : '' }}" style="cursor: pointer;">
                    <img src="{{ asset('img/icons/cat.svg') }}" alt="Gatos" style="width: 32px; height: 32px;">
                    <span class="poppins-semibold text-main small">Gatos</span>
                </label>
            </div>
        </div>
        @if(request()->filled('mascota'))
            <div class="text-end mt-1">
                <a href="{{ request()->fullUrlWithQuery(['mascota' => '']) }}" class="text-decoration-none text-muted" style="font-size: 0.7rem;">✕ Quitar filtro</a>
            </div>
        @endif
    </div>

    <hr class="my-3" style="opacity: 0.1;">

{{-- FILA 3: Tipo de Producto --}}
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="poppins-bold text-main fs-6 d-flex align-items-center gap-2">
            <img src="{{ asset('img/icons/shirt.svg') }}" alt="Ropa" style="width: 18px; height: 18px; opacity: 0.7;">TIPO DE PRODUCTO
        </span>
        <i class="bi bi-chevron-up text-secondary small"></i>
    </div>
    
    {{-- Categorías Padre (Ropa, Juguetes, etc.) --}}
    <div class="row g-2 mb-3">
        @foreach($categorias as $catPadre)
            <div class="col-6">
                <input type="radio" name="categoria" value="{{ $catPadre->id }}" id="cat_{{ $catPadre->id }}" class="d-none filtro-automatico" {{ request('categoria') == $catPadre->id ? 'checked' : '' }}>
                <label for="cat_{{ $catPadre->id }}" class="btn btn-light w-100 py-2 border rounded-3 d-flex align-items-center justify-content-center gap-2 {{ request('categoria') == $catPadre->id ? 'active-filter-card border-primary bg-light' : '' }}" style="cursor: pointer;">
                    <img src="{{ asset('img/icons/' . (Str::slug($catPadre->nombre) == 'ropa' ? 'shirt.svg' : 'juguetes.svg')) }}" alt="{{ $catPadre->nombre }}" style="width: 20px; height: 20px;">
                    <span class="poppins-semibold text-main small">{{ $catPadre->nombre }}</span>
                </label>
            </div>
        @endforeach
    </div>

    {{-- Grilla dinámica de Subcategorías (Buzos, Remeras, etc.) --}}
    <div class="row row-cols-3 g-2">
        @php
            $subcategoriasMostrar = collect();
            
            if (request()->filled('categoria')) {
                $idSeleccionado = request('categoria');
                // Buscamos si el ID seleccionado es una categoría principal
                $categoriaActual = $categorias->firstWhere('id', $idSeleccionado);
                
                if ($categoriaActual) {
                    // Si es principal, mostramos sus subcategorías hijas directas
                    $subcategoriasMostrar = $categoriaActual->children;
                } else {
                    // Si no la encontramos en las principales, el usuario seleccionó una subcategoría.
                    // Buscamos a sus "hermanas" encontrando la principal que la contiene.
                    foreach ($categorias as $padre) {
                        if ($padre->children->contains('id', $idSeleccionado)) {
                            $subcategoriasMostrar = $padre->children;
                            break;
                        }
                    }
                }
            } else {
                // Si no hay filtro activo, listamos todas las subcategorías del sistema de forma limpia
                $subcategoriasMostrar = $categorias->pluck('children')->flatten();
            }
        @endphp

        @forelse($subcategoriasMostrar as $subCat)
            <div class="col">
                <input type="radio" name="categoria" value="{{ $subCat->id }}" id="subcat_{{ $subCat->id }}" class="d-none filtro-automatico" {{ request('categoria') == $subCat->id ? 'checked' : '' }}>
                
                {{-- Vinculamos directamente el label al ID correcto mediante el atributo FOR --}}
                <label for="subcat_{{ $subCat->id }}" class="btn btn-light border rounded-pill w-100 py-1 px-0 text-center small {{ request('categoria') == $subCat->id ? 'border-primary bg-primary text-white' : '' }}" style="cursor: pointer; font-size: 0.72rem;">
                    {{ $subCat->nombre }}
                </label>
            </div>
        @empty
            <div class="col-12 text-center py-2 text-muted small" style="font-size: 0.75rem;">No hay subcategorías disp.</div>
        @endforelse
    </div>

    @if(request()->filled('categoria'))
        <div class="text-end mt-2">
            <a href="{{ request()->fullUrlWithQuery(['categoria' => '']) }}" class="text-decoration-none text-muted" style="font-size: 0.7rem;">✕ Quitar categoría</a>
        </div>
    @endif
</div>

    @php
        $mostrarTalle = true;
        $mostrarColor = true;
        if (request()->filled('categoria')) {
            $cat = $categorias->firstWhere('id', request('categoria'));
            if ($cat) {
                // Sincronizado con tus columnas reales de la base de datos
                $mostrarTalle = $cat->pide_talle ?? $cat->acepta_talle ?? true;
                $mostrarColor = $cat->pide_color ?? $cat->acepta_color ?? true;
            }
        }
    @endphp

    @if($mostrarTalle)
        <hr class="my-3" style="opacity: 0.1;">

        {{-- FILA 4: Talles Dinámicos --}}
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="poppins-bold text-main fs-6 d-flex align-items-center gap-2">
                    <img src="{{ asset('img/icons/ruler.svg') }}" alt="Talles" style="width: 18px; height: 18px; opacity: 0.7;">TALLE
                </span>
                <i class="bi bi-chevron-down text-secondary small"></i>
            </div>
            <div class="d-flex flex-wrap gap-1 justify-content-start">
                @foreach(\App\Models\Producto::TALLES ?? ['S', 'M', 'L', 'XL'] as $talle)
                    <div>
                        <input type="radio" name="talle" value="{{ $talle }}" id="talle_{{ $talle }}" class="d-none filtro-automatico" {{ request('talle') == $talle ? 'checked' : '' }}>
                        <label for="talle_{{ $talle }}" class="btn btn-light border py-2 px-0 text-center square-filter-btn {{ request('talle') == $talle ? 'active-filter-card border-primary bg-primary text-white' : '' }}" style="cursor: pointer; min-width: 40px; font-size: 0.8rem;">
                            {{ $talle }}
                        </label>
                    </div>
                @endforeach
            </div>
            @if(request()->filled('talle'))
                <div class="text-end mt-1">
                    <a href="{{ request()->fullUrlWithQuery(['talle' => '']) }}" class="text-decoration-none text-muted" style="font-size: 0.7rem;">✕ Quitar talle</a>
                </div>
            @endif
        </div>
    @endif

    @if($mostrarColor)
        <hr class="my-3" style="opacity: 0.1;">

        {{-- FILA 5: Color Dinámico --}}
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="poppins-bold text-main fs-6 d-flex align-items-center gap-2">
                    <img src="{{ asset('img/icons/palette.svg') }}" alt="Colores" style="width: 18px; height: 18px; opacity: 0.7;">COLOR
                </span>
                <i class="bi bi-chevron-down text-secondary small"></i>
            </div>
            <div class="d-flex gap-2 justify-content-start align-items-center flex-wrap">
                @foreach($colores as $colorBD)
                    <div>
                        <input type="radio" name="color" value="{{ $colorBD->id }}" id="color_{{ $colorBD->id }}" class="d-none filtro-automatico" {{ request('color') == $colorBD->id ? 'checked' : '' }}>
                        <label for="color_{{ $colorBD->id }}" class="rounded-circle color-filter-dot d-block position-relative" 
                               style="background-color: {{ $colorBD->hex_code }}; width: 24px; height: 24px; cursor: pointer; {{ request('color') == $colorBD->id ? 'box-shadow: 0 0 0 3px #ffffff, 0 0 0 5px #0d6efd;' : 'border: 1px solid #ddd;' }}" 
                               title="{{ $colorBD->nombre }}">
                               @if(request('color') == $colorBD->id)
                                   <span class="position-absolute top-50 start-50 translate-middle text-white" style="font-size: 9px;">✓</span>
                               @endif
                        </label>
                    </div>
                @endforeach
            </div>
            @if(request()->filled('color'))
                <div class="text-end mt-1">
                    <a href="{{ request()->fullUrlWithQuery(['color' => '']) }}" class="text-decoration-none text-muted" style="font-size: 0.7rem;">✕ Quitar color</a>
                </div>
            @endif
        </div>
    @endif

    <hr class="my-3" style="opacity: 0.1;">

    {{-- FILA 6: Rangos de Precio --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="poppins-bold text-main fs-6 d-flex align-items-center gap-2">
                <span class="fs-5" style="opacity: 0.7;">$</span>PRECIO
            </span>
            <i class="bi bi-chevron-down text-secondary small"></i>
        </div>
        <div class="d-flex flex-column gap-2">
            @php
                $rangos = [
                    ['label' => 'Hasta $5.000', 'value' => '0-5000'],
                    ['label' => '$5.000 a $12.000', 'value' => '5000-12000'],
                    ['label' => '$12.000 a $25.000', 'value' => '12000-25000'],
                    ['label' => 'Más de $25.000', 'value' => '25000-999999'],
                ];
            @endphp

            @foreach($rangos as $rango)
                <div>
                    <input type="radio" name="precio_rango" value="{{ $rango['value'] }}" id="precio_{{ $rango['value'] }}" class="d-none filtro-automatico" {{ request('precio_rango') == $rango['value'] ? 'checked' : '' }}>
                    <label for="precio_{{ $rango['value'] }}" class="btn btn-light border w-100 text-start py-2 px-3 small rounded-3 poppins-medium {{ request('precio_rango') == $rango['value'] ? 'active-filter-card border-primary bg-primary text-white' : '' }}" style="cursor: pointer; font-size: 0.82rem;">
                        {{ $rango['label'] }}
                    </label>
                </div>
            @endforeach
        </div>
        @if(request()->filled('precio_rango'))
            <div class="text-end mt-1">
                <a href="{{ request()->fullUrlWithQuery(['precio_rango' => '']) }}" class="text-decoration-none text-muted" style="font-size: 0.7rem;">✕ Quitar precio</a>
            </div>
        @endif
    </div>

    <hr class="my-3" style="opacity: 0.1;">

    {{-- FILA 7: Colecciones Dinámicas --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="poppins-bold text-main fs-6 d-flex align-items-center gap-2">
                <i class="bi bi-tags text-secondary" style="font-size: 18px; opacity: 0.7;"></i>COLECCIONES
            </span>
            <i class="bi bi-chevron-down text-secondary small"></i>
        </div>
        <div class="d-flex flex-column gap-2">
            @foreach($colecciones as $col)
                <div>
                    <input type="radio" name="coleccion" value="{{ $col->id }}" id="coleccion_{{ $col->id }}" class="d-none filtro-automatico" {{ request('coleccion') == $col->id ? 'checked' : '' }}>
                    <label for="coleccion_{{ $col->id }}" class="btn btn-light border w-100 text-start py-2 px-3 small rounded-3 poppins-medium {{ request('coleccion') == $col->id ? 'active-filter-card border-primary bg-primary text-white' : '' }}" style="cursor: pointer; font-size: 0.82rem;">
                        {{ $col->nombre }}
                    </label>
                </div>
            @endforeach
        </div>
        @if(request()->filled('coleccion'))
            <div class="text-end mt-1">
                <a href="{{ request()->fullUrlWithQuery(['coleccion' => '']) }}" class="text-decoration-none text-muted" style="font-size: 0.7rem;">✕ Quitar colección</a>
            </div>
        @endif
    </div>

    {{-- Mensaje de Envíos Gratis al pie del Sidebar --}}
    <div class="theme-green surface-card p-3 rounded-4 d-flex align-items-center justify-content-between text-start mt-4 border-0 shadow-sm" style="background-color: #e8f5e9;">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('img/icons/delivery-truck.svg') }}" alt="Envios" style="width: 32px; height: 32px;">
            <div>
                <h6 class="poppins-bold mb-0 text-success" style="font-size: 0.85rem;">Envíos gratis</h6>
                <p class="text-secondary mb-0 p-0" style="font-size: 0.72rem; line-height:1.2;">en compras mayores a $50.000 ARS</p>
            </div>
        </div>
        <img src="{{ asset('img/icons/heart.svg') }}" alt="Fav" style="width: 16px; height: 16px; opacity: 0.4;">
    </div>

</div>