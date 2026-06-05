<div class="p-1 min-vh-100 filter-sidebar-container">
    
    {{-- FILA 1: Título y Limpiar Filtros --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('img/icons/filters.svg') }}" alt="Filtros" style="width: 20px; height: 20px; opacity: 0.7;">
            <h5 class="poppins-bold text-uppercase fs-6 mb-0 text-main" style="letter-spacing: 0.5px;">Filtrar Productos</h5>
        </div>
        <a href="#" class="poppins-regular small text-success text-decoration-underline" style="font-size: 0.8rem;">Limpiar filtros</a>
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
                <button class="btn btn-light w-100 py-3 border rounded-3 d-flex flex-column align-items-center justify-content-center gap-2 active-filter-card {{ request('mascota') == 'perro' ? 'active-filter-card border-primary' : '' }}" 
                    type="submit" 
                    name="mascota" 
                    value="perro">
                    <img src="{{ asset('img/icons/dog.svg') }}" alt="Perros" style="width: 32px; height: 32px;">
                    <span class="poppins-semibold text-main small">Perros</span>
                </button>
            </div>
            <div class="col-6">
                <button class="btn btn-light w-100 py-3 border rounded-3 d-flex flex-column align-items-center justify-content-center gap-2{{ request('mascota') == 'gato' ? 'active-filter-card border-primary' : '' }}" 
                    type="submit" 
                    name="mascota" 
                    value="gato">
                    <img src="{{ asset('img/icons/cat.svg') }}" alt="Gatos" style="width: 32px; height: 32px;">
                    <span class="poppins-semibold text-main small">Gatos</span>
                </button>
            </div>
        </div>
    </div>

    <hr class="my-3" style="opacity: 0.1;">

{{-- FILA 3: Tipo de Producto (Ropa / Accesorios + Grilla subcategorías dinámicas) --}}
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="poppins-bold text-main fs-6 d-flex align-items-center gap-2">
            <img src="{{ asset('img/icons/shirt.svg') }}" alt="Ropa" style="width: 18px; height: 18px; opacity: 0.7;">TIPO DE PRODUCTO
        </span>
        <i class="bi bi-chevron-up text-secondary small"></i>
    </div>
    
    {{-- 1. BOTONES PRINCIPALES (Categorías Padre extraídas de la BD) --}}
    <div class="row g-2 mb-3">
        @foreach($categorias->whereNull('parent_id') as $catPadre)
            <div class="col-6">
                <button class="btn btn-light w-100 py-2 border rounded-3 d-flex align-items-center justify-content-center gap-2 {{ request('categoria') == $catPadre->id ? 'active-filter-card border-primary' : '' }}" 
                    type="submit" 
                    name="categoria" 
                    value="{{ $catPadre->id }}">
                    
                    <img src="{{ asset('img/icons/' . (Str::slug($catPadre->nombre) == 'ropa' ? 'shirt.svg' : 'juguetes.svg')) }}" alt="{{ $catPadre->nombre }}" style="width: 20px; height: 20px;">
                    <span class="poppins-semibold text-main small">{{ $catPadre->nombre }}</span>
                </button>
            </div>
        @endforeach
    </div>

    {{-- 2. GRILLA DINÁMICA DE SUBCATEGORÍAS (Children en tiempo de ejecución) --}}
    <div class="row row-cols-3 g-2">
        @php
            if (request()->filled('categoria')) {
                // Si seleccionó Ropa o Accesorios, mostramos solo sus subcategorías correspondientes
                $subcategoriasMostrar = $categorias->where('parent_id', request('categoria'));
            } else {
                // Si no seleccionó nada, mostramos todas las subcategorías existentes
                $subcategoriasMostrar = $categorias->whereNotNull('parent_id');
            }
        @endphp

        @forelse($subcategoriasMostrar as $subCat)
            <div class="col">
                <button class="btn btn-light border rounded-pill w-100 py-1 px-0 small-filter-btn {{ request('categoria') == $subCat->id ? 'btn-active-pill border-primary' : '' }}" 
                    type="submit"
                    name="categoria"
                    value="{{ $subCat->id }}">
                    {{ $subCat->nombre }}
                </button>
            </div>
        @empty
            <div class="col-12 text-center py-2 text-muted small">No hay subcategorías disponibles</div>
        @endforelse
    </div>
</div>

@php
    $mostrarTalle = true;
    $mostrarColor = true;
    if (request()->filled('categoria')) {
        $cat = $categorias->firstWhere('id', request('categoria'));
        if ($cat) {
            $mostrarTalle = $cat->acepta_talle;
            $mostrarColor = $cat->acepta_color;
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
        <div class="d-flex justify-content-between gap-1">
            @foreach(\App\Models\Producto::TALLES as $talle)
                <div>
                    <input type="radio" name="talle" value="{{ $talle }}" id="talle_{{ $talle }}" class="d-none filtro-automatico" {{ request('talle') == $talle ? 'checked' : '' }}>
                    
                    <label for="talle_{{ $talle }}" class="btn btn-light border py-2 px-0 text-center square-filter-btn w-100 {{ request('talle') == $talle ? 'active-filter-card border-primary bg-primary text-white' : '' }}" style="cursor: pointer; min-width: 40px;">
                        {{ $talle }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if($mostrarColor)
    <hr class="my-3" style="opacity: 0.1;">

{{-- FILA 5: Color Dinámico (Conectado a la Base de Datos) --}}
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="poppins-bold text-main fs-6 d-flex align-items-center gap-2">
            <img src="{{ asset('img/icons/palette.svg') }}" alt="Colores" style="width: 18px; height: 18px; opacity: 0.7;">COLOR
        </span>
        <i class="bi bi-chevron-down text-secondary small"></i>
    </div>
    <div class="d-flex gap-2 justify-content-start align-items-center flex-wrap">
        
        {{-- Iteramos sobre los colores reales insertados por el seeder --}}
        @foreach($colores as $colorBD)
            <div>
                <input type="radio" 
                       name="color" 
                       value="{{ $colorBD->id }}" 
                       id="color_{{ $colorBD->id }}" 
                       class="d-none filtro-automatico" 
                       {{ request('color') == $colorBD->id ? 'checked' : '' }}>
                
                <label for="color_{{ $colorBD->id }}" 
                       class="rounded-circle color-filter-dot d-block" 
                       style="background-color: {{ $colorBD->hex_code }}; width: 24px; height: 24px; cursor: pointer; {{ request('color') == $colorBD->id ? 'ring 3px #0d6efd;' : 'border: 1px solid #ddd;' }}" 
                       title="{{ $colorBD->nombre }}">
                </label>
            </div>
        @endforeach

    </div>
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
            // Definimos los rangos estáticos para los botones
            // El formato 'min-max' nos facilitará la lectura en el controlador
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
                
                <label for="precio_{{ $rango['value'] }}" class="btn btn-light border w-100 text-start py-2 px-3 small rounded-3 poppins-medium {{ request('precio_rango') == $rango['value'] ? 'active-filter-card border-primary bg-primary text-white' : '' }}" style="cursor: pointer;">
                    {{ $rango['label'] }}
                </label>
            </div>
        @endforeach
    </div>
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
        {{-- Opción por defecto para limpiar el filtro de colección --}}
        <div>
            <input type="radio" name="coleccion" value="" id="coleccion_todas" class="d-none filtro-automatico" {{ !request()->filled('coleccion') ? 'checked' : '' }}>
            <label for="coleccion_todas" class="btn btn-light border w-100 text-start py-2 px-3 small rounded-3 poppins-medium {{ !request()->filled('coleccion') ? 'active-filter-card' : '' }}" style="cursor: pointer;">
                Todas las colecciones
            </label>
        </div>

        {{-- Listamos las colecciones reales traídas desde la base de datos --}}
        @foreach($colecciones as $col)
            <div>
                <input type="radio" name="coleccion" value="{{ $col->id }}" id="coleccion_{{ $col->id }}" class="d-none filtro-automatico" {{ request('coleccion') == $col->id ? 'checked' : '' }}>
                
                <label for="coleccion_{{ $col->id }}" class="btn btn-light border w-100 text-start py-2 px-3 small rounded-3 poppins-medium {{ request('coleccion') == $col->id ? 'active-filter-card border-primary bg-primary text-white' : '' }}" style="cursor: pointer;">
                    {{ $col->nombre }}
                </label>
            </div>
        @endforeach
    </div>
</div>

    {{-- Mensaje de Envíos Gratis al pie del Sidebar --}}
    <div class="theme-green surface-card p-3 rounded-4 d-flex align-items-center justify-content-between text-start mt-4 border-0 shadow-sm" style="background-color: var(--green-100);">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('img/icons/delivery-truck.svg') }}" alt="Envios" style="width: 32px; height: 32px;">
            <div>
                <h6 class="poppins-bold mb-0 text-success" style="font-size: 0.85rem;">Envíos gratis</h6>
                <p class="text-secondary mb-0 p-0" style="font-size: 0.72rem; line-height:1.2;">en compras mayores a $50000 ARS</p>
            </div>
        </div>
        <img src="{{ asset('img/icons/heart.svg') }}" alt="Fav" style="width: 16px; height: 16px; opacity: 0.4;">
    </div>

</div>
