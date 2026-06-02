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
        @foreach(['XS', 'S', 'M', 'L', 'L', 'XL', 'XXL'] as $talle)
            <div>
                <input type="radio" name="talle" value="{{ $talle }}" id="talle_{{ $talle }}" class="d-none filtro-automatico" {{ request('talle') == $talle ? 'checked' : '' }}>
                
                <label for="talle_{{ $talle }}" class="btn btn-light border py-2 px-0 text-center square-filter-btn w-100 {{ request('talle') == $talle ? 'active-filter-card border-primary bg-primary text-white' : '' }}" style="cursor: pointer; min-width: 40px;">
                    {{ $talle }}
                </label>
            </div>
        @endforeach
    </div>
</div>

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
        @php
            // Definimos tus colores estéticos mapeados con strings o IDs correspondientes
            $paleta = [
                ['nombre' => 'Verde musgo', 'hex' => '#556b2f'],
                ['nombre' => 'Coral', 'hex' => '#e3a393'],
                ['nombre' => 'Beige', 'hex' => '#f5f5dc'],
                ['nombre' => 'Azul Marino', 'hex' => '#1a2941'],
                ['nombre' => 'Gris', 'hex' => '#8b9fba'],
                ['nombre' => 'Negro', 'hex' => '#000000'],
            ];
        @endphp

        @foreach($paleta as $col)
            <div>
                <input type="radio" name="color" value="{{ Str::slug($col['nombre']) }}" id="color_{{ Str::slug($col['nombre']) }}" class="d-none filtro-automatico" {{ request('color') == Str::slug($col['nombre']) ? 'checked' : '' }}>
                
                <label for="color_{{ Str::slug($col['nombre']) }}" class="rounded-circle color-filter-dot d-block" style="background-color: {{ $col['hex'] }}; width: 24px; height: 24px; cursor: pointer; {{ request('color') == Str::slug($col['nombre']) ? 'ring 3px #0d6efd' : 'border: 1px solid #ddd;' }}" title="{{ $col['nombre'] }}">
                </label>
            </div>
        @endforeach
    </div>
</div>

    <hr class="my-3" style="opacity: 0.1;">

    {{-- FILA 6: Rango de Precio --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="poppins-bold text-main fs-6 d-flex align-items-center gap-2">
                <img src="{{ asset('img/icons/price-tag.svg') }}" alt="Precios" style="width: 18px; height: 18px; opacity: 0.7;">RANGO DE PRECIO
            </span>
            <i class="bi bi-chevron-down text-secondary small"></i>
        </div>
        
        {{-- Slider Nativo --}}
        <div class="px-2">
            <input type="range" class="form-range custom-range-slider" min="0" max="2500" value="2500">
            <div class="d-flex justify-content-between text-muted mt-1" style="font-size: 0.8rem;">
                <span class="poppins-semibold">$0</span>
                <span class="poppins-semibold">$2,500+</span>
            </div>
        </div>

        {{-- Rangos Rápidos Predefinidos --}}
        <div class="row g-2 mt-2">
            <div class="col-6"><button class="btn btn-light border rounded-3 w-100 py-1 px-1 text-center font-main text-muted" style="font-size:0.75rem;" type="button">$0 - $499</button></div>
            <div class="col-6"><button class="btn btn-light border rounded-3 w-100 py-1 px-1 text-center font-main text-muted" style="font-size:0.75rem;" type="button">$500 - $999</button></div>
            <div class="col-6"><button class="btn btn-light border rounded-3 w-100 py-1 px-1 text-center font-main text-muted" style="font-size:0.75rem;" type="button">$1,000 - $1,999</button></div>
            <div class="col-6"><button class="btn btn-light border rounded-3 w-100 py-1 px-1 text-center font-main text-muted" style="font-size:0.75rem;" type="button">$2,000+</button></div>
        </div>
    </div>

    <hr class="my-3" style="opacity: 0.1;">

    {{-- FILA 7: Colección --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="poppins-bold text-main fs-6 d-flex align-items-center gap-2">
                <img src="{{ asset('img/icons/heart.svg') }}" alt="Colección" style="width: 18px; height: 18px; opacity: 0.7;">COLECCIÓN
            </span>
            <i class="bi bi-chevron-down text-secondary small"></i>
        </div>
        
        <div class="row g-2">
            <div class="col-6">
                <button class="btn btn-light border rounded-3 w-100 py-2 px-1 text-start d-flex align-items-center gap-2 active-filter-card" type="button">
                    <img src="{{ asset('img/icons/accesorios.svg') }}" alt="Clásicos" style="width: 16px; height: 16px; opacity:0.6;">
                    <span class="poppins-semibold text-main" style="font-size:0.75rem;">Clásicos</span>
                </button>
            </div>
            <div class="col-6">
                <button class="btn btn-light border rounded-3 w-100 py-2 px-1 text-start d-flex align-items-center gap-2" type="button">
                    <img src="{{ asset('img/icons/snowflake.svg') }}" alt="Invierno 24" style="width: 16px; height: 16px; opacity:0.6;">
                    <span class="poppins-semibold text-main" style="font-size:0.75rem;">Invierno 24</span>
                </button>
            </div>
            <div class="col-6">
                <button class="btn btn-light border rounded-3 w-100 py-2 px-1 text-start d-flex align-items-center gap-2" type="button">
                    <img src="{{ asset('img/icons/daisy.svg') }}" alt="Primavera" style="width: 16px; height: 16px; opacity:0.6;">
                    <span class="poppins-semibold text-main" style="font-size:0.75rem;">Primavera</span>
                </button>
            </div>
            <div class="col-6">
                <button class="btn btn-light border rounded-3 w-100 py-2 px-1 text-start d-flex align-items-center gap-2" type="button">
                    <img src="{{ asset('img/icons/paw.svg') }}" alt="Picnic" style="width: 16px; height: 16px; opacity:0.6;">
                    <span class="poppins-semibold text-main" style="font-size:0.75rem;">Picnic</span>
                </button>
            </div>
            <div class="col-6">
                <button class="btn btn-light border rounded-3 w-100 py-2 px-1 text-start d-flex align-items-center gap-2" type="button">
                    <img src="{{ asset('img/icons/heart.svg') }}" alt="Esenciales" style="width: 16px; height: 16px; opacity:0.6;">
                    <span class="poppins-semibold text-main" style="font-size:0.75rem;">Esenciales</span>
                </button>
            </div>
            <div class="col-6">
                <button class="btn btn-light border rounded-3 w-100 py-2 px-1 text-start d-flex align-items-center gap-2" type="button">
                    <img src="{{ asset('img/icons/sparkles.svg') }}" alt="Nueva" style="width: 16px; height: 16px; opacity:0.6;">
                    <span class="poppins-semibold text-main" style="font-size:0.75rem;">Nueva Col. ✨</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Mensaje de Envíos Gratis al pie del Sidebar --}}
    <div class="theme-green surface-card p-3 rounded-4 d-flex align-items-center justify-content-between text-start mt-4 border-0 shadow-sm" style="background-color: var(--green-100);">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('img/icons/delivery-truck.svg') }}" alt="Envios" style="width: 32px; height: 32px;">
            <div>
                <h6 class="poppins-bold mb-0 text-success" style="font-size: 0.85rem;">Envíos gratis</h6>
                <p class="text-secondary mb-0 p-0" style="font-size: 0.72rem; line-height:1.2;">en compras mayores a $599 MXN</p>
            </div>
        </div>
        <img src="{{ asset('img/icons/heart.svg') }}" alt="Fav" style="width: 16px; height: 16px; opacity: 0.4;">
    </div>

</div>
