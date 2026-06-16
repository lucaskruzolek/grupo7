<div class="p-1 min-vh-100 filter-sidebar-container">
    
    {{-- FILA 1: Título y Limpiar Filtros --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            
            <h5 class="poppins-bold text-uppercase fs-6 mb-0 text-main" style="letter-spacing: 0.5px;">Filtros</h5>
        </div>
        <a href="{{ route('productos.index') }}" class="btn btn-sm btn-verde-tierra-outline rounded-pill px-3 poppins-semibold text-nowrap" style="font-size: 0.75rem;">
            Limpiar Filtros
        </a>
    </div>

    <hr class="my-3" style="opacity: 0.1;">

    {{-- FILA 1.5: Búsqueda Activa (Filtro Dinámico) --}}
    @if(request()->filled('buscar'))
        <div class="mb-4">
            <span class="poppins-bold text-main fs-6 d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-search text-secondary" style="font-size: 16px; opacity: 0.7;"></i>BÚSQUEDA ACTUAL
            </span>
            <div class="d-flex align-items-center justify-content-between p-2 border rounded-3 bg-light">
                <span class="poppins-semibold text-main small text-truncate pe-2">"{{ request('buscar') }}"</span>
                <a href="{{ request()->fullUrlWithQuery(['buscar' => '', 'page' => null]) }}" class="text-decoration-none text-danger fw-bold px-2" title="Quitar búsqueda" style="font-size: 0.95rem;">✕</a>
            </div>
        </div>
        <hr class="my-3" style="opacity: 0.1;">
    @endif

    {{-- FILA 2: ¿Para quién? --}}
    <div class="accordion" id="sidebarFiltersAccordion">
        
        {{-- FILA 2: ¿Para quién? --}}
        <div class="accordion-item border-0 bg-transparent mb-3">
            <h2 class="accordion-header" id="headingMascota">
                <button class="accordion-button px-0 py-2 bg-transparent text-main poppins-bold fs-6 shadow-none d-flex justify-content-between align-items-center"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapseMascota"
                        aria-expanded="true"
                        aria-controls="collapseMascota">
                    <span class="d-flex align-items-center gap-2">
                        <img src="{{ asset('img/icons/heart3.svg') }}" alt="Para quién" style="width: 18px; height: 18px; opacity: 0.7;">¿PARA QUIÉN?
                    </span>
                    <i class="bi bi-chevron-down chevron-icon text-secondary small transition-300"></i>
                </button>
            </h2>
            <div id="collapseMascota" 
                 class="accordion-collapse collapse show" 
                 aria-labelledby="headingMascota">
                <div class="accordion-body px-0 pt-2 pb-1">
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="radio" name="mascota" value="perro" id="mascota_perro" class="d-none filtro-automatico" {{ request('mascota') == 'perro' ? 'checked' : '' }}>
                            <label for="mascota_perro" class="btn btn-light w-100 py-3 border rounded-3 d-flex flex-column align-items-center justify-content-center gap-2 {{ request('mascota') == 'perro' ? 'active-filter-card' : '' }}" style="cursor: pointer; position: relative;">
                                @if(request('mascota') == 'perro')
                                    <img src="{{ asset('img/icons/check.svg') }}" class="position-absolute" style="top: 8px; right: 10px; width: 14px; height: 14px;" alt="Checked">
                                @endif
                                <img src="{{ asset('img/icons/dog.svg') }}" alt="Perros" style="width: 32px; height: 32px;">
                                <span class="poppins-semibold text-main small">Perros</span>
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" name="mascota" value="gato" id="mascota_gato" class="d-none filtro-automatico" {{ request('mascota') == 'gato' ? 'checked' : '' }}>
                            <label for="mascota_gato" class="btn btn-light w-100 py-3 border rounded-3 d-flex flex-column align-items-center justify-content-center gap-2 {{ request('mascota') == 'gato' ? 'active-filter-card' : '' }}" style="cursor: pointer; position: relative;">
                                @if(request('mascota') == 'gato')
                                    <img src="{{ asset('img/icons/check.svg') }}" class="position-absolute" style="top: 8px; right: 10px; width: 14px; height: 14px;" alt="Checked">
                                @endif
                                <img src="{{ asset('img/icons/cat.svg') }}" alt="Gatos" style="width: 32px; height: 32px;">
                                <span class="poppins-semibold text-main small">Gatos</span>
                            </label>
                        </div>
                    </div>
                    @if(request()->filled('mascota'))
                        <div class="text-end mt-2">
                            <a href="{{ request()->fullUrlWithQuery(['mascota' => '']) }}" class="text-decoration-none text-muted" style="font-size: 0.7rem;">✕ Quitar filtro</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- FILA 3: Tipo de Producto --}}
        <div class="accordion-item border-0 bg-transparent mb-3">
            <h2 class="accordion-header" id="headingCategoria">
                <button class="accordion-button px-0 py-2 bg-transparent text-main poppins-bold fs-6 shadow-none d-flex justify-content-between align-items-center {{ request()->filled('categoria') ? '' : 'collapsed' }}"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapseCategoria"
                        aria-expanded="{{ request()->filled('categoria') ? 'true' : 'false' }}"
                        aria-controls="collapseCategoria">
                    <span class="d-flex align-items-center gap-2">
                        <img src="{{ asset('img/icons/tag.svg') }}" alt="Tipo de Producto" style="width: 18px; height: 18px; opacity: 0.7;">TIPO DE PRODUCTO
                    </span>
                    <i class="bi bi-chevron-down chevron-icon text-secondary small transition-300"></i>
                </button>
            </h2>
            <div id="collapseCategoria" 
                 class="accordion-collapse collapse {{ request()->filled('categoria') ? 'show' : '' }}" 
                 aria-labelledby="headingCategoria">
                <div class="accordion-body px-0 pt-2 pb-1">
                    {{-- Categorías Padre (Ropa, Juguetes, etc.) --}}
                    <div class="row g-2 mb-3">
                        @foreach($categorias as $catPadre)
                            <div class="col-6">
                                <input type="radio" name="categoria" value="{{ $catPadre->id }}" id="cat_{{ $catPadre->id }}" class="d-none filtro-automatico" {{ request('categoria') == $catPadre->id ? 'checked' : '' }}>
                                <label for="cat_{{ $catPadre->id }}" class="btn btn-light w-100 py-2 border rounded-3 d-flex align-items-center justify-content-center gap-2 {{ request('categoria') == $catPadre->id ? 'active-filter-card' : '' }}" style="cursor: pointer;">
                                    <span class="poppins-semibold text-main small">{{ ucfirst($catPadre->nombre) }}</span>
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
                                $categoriaActual = $categorias->firstWhere('id', $idSeleccionado);
                                
                                if ($categoriaActual) {
                                    $subcategoriasMostrar = $categoriaActual->children;
                                } else {
                                    foreach ($categorias as $padre) {
                                        if ($padre->children->contains('id', $idSeleccionado)) {
                                            $subcategoriasMostrar = $padre->children;
                                            break;
                                        }
                                    }
                                }
                            } else {
                                $subcategoriasMostrar = $categorias->pluck('children')->flatten();
                            }
                        @endphp

                        @forelse($subcategoriasMostrar as $subCat)
                            <div class="col">
                                <input type="radio" name="categoria" value="{{ $subCat->id }}" id="subcat_{{ $subCat->id }}" class="d-none filtro-automatico" {{ request('categoria') == $subCat->id ? 'checked' : '' }}>
                                <label for="subcat_{{ $subCat->id }}" class="btn btn-light border rounded-pill w-100 py-1 px-0 text-center small {{ request('categoria') == $subCat->id ? 'active-filter-card' : '' }}" style="cursor: pointer; font-size: 0.72rem;">
                                    {{ ucfirst($subCat->nombre) }}
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
            </div>
        </div>

        @php
            $mostrarTalle = true;
            $mostrarColor = true;
            if (request()->filled('categoria')) {
                $cat = $categorias->firstWhere('id', request('categoria'));
                if ($cat) {
                    $mostrarTalle = $cat->pide_talle ?? $cat->acepta_talle ?? true;
                    $mostrarColor = $cat->pide_color ?? $cat->acepta_color ?? true;
                }
            }
        @endphp

        @if($mostrarTalle)
            {{-- FILA 4: Talles Dinámicos --}}
            <div class="accordion-item border-0 bg-transparent mb-3">
                <h2 class="accordion-header" id="headingTalle">
                    <button class="accordion-button px-0 py-2 bg-transparent text-main poppins-bold fs-6 shadow-none d-flex justify-content-between align-items-center {{ request()->filled('talle') ? '' : 'collapsed' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseTalle"
                            aria-expanded="{{ request()->filled('talle') ? 'true' : 'false' }}"
                            aria-controls="collapseTalle">
                        <span class="d-flex align-items-center gap-2">
                            <img src="{{ asset('img/icons/ruler.svg') }}" alt="Talles" style="width: 18px; height: 18px; opacity: 0.7;">TALLE
                        </span>
                        <i class="bi bi-chevron-down chevron-icon text-secondary small transition-300"></i>
                    </button>
                </h2>
                <div id="collapseTalle" 
                     class="accordion-collapse collapse {{ request()->filled('talle') ? 'show' : '' }}" 
                     aria-labelledby="headingTalle">
                    <div class="accordion-body px-0 pt-2 pb-1">
                        <div class="d-flex flex-wrap gap-1 justify-content-start">
                            @foreach(\App\Models\Producto::TALLES ?? ['S', 'M', 'L', 'XL'] as $talle)
                                <div>
                                    <input type="radio" name="talle" value="{{ $talle }}" id="talle_{{ $talle }}" class="d-none filtro-automatico" {{ request('talle') == $talle ? 'checked' : '' }}>
                                    <label for="talle_{{ $talle }}" class="btn btn-light border py-2 px-0 text-center square-filter-btn {{ request('talle') == $talle ? 'active-filter-card' : '' }}" style="cursor: pointer; min-width: 40px; font-size: 0.8rem;">
                                        {{ $talle }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @if(request()->filled('talle'))
                            <div class="text-end mt-2">
                                <a href="{{ request()->fullUrlWithQuery(['talle' => '']) }}" class="text-decoration-none text-muted" style="font-size: 0.7rem;">✕ Quitar talle</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if($mostrarColor)
            {{-- FILA 5: Color Dinámico --}}
            <div class="accordion-item border-0 bg-transparent mb-3">
                <h2 class="accordion-header" id="headingColor">
                    <button class="accordion-button px-0 py-2 bg-transparent text-main poppins-bold fs-6 shadow-none d-flex justify-content-between align-items-center {{ request()->filled('color') ? '' : 'collapsed' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseColor"
                            aria-expanded="{{ request()->filled('color') ? 'true' : 'false' }}"
                            aria-controls="collapseColor">
                        <span class="d-flex align-items-center gap-2">
                            <img src="{{ asset('img/icons/swatches.svg') }}" alt="Colores" style="width: 18px; height: 18px; opacity: 0.7;">COLOR
                        </span>
                        <i class="bi bi-chevron-down chevron-icon text-secondary small transition-300"></i>
                    </button>
                </h2>
                <div id="collapseColor" 
                     class="accordion-collapse collapse {{ request()->filled('color') ? 'show' : '' }}" 
                     aria-labelledby="headingColor">
                    <div class="accordion-body px-0 pt-2 pb-1">
                        <div class="d-flex gap-2 justify-content-start align-items-center flex-wrap">
                            @foreach($colores as $colorBD)
                                <div>
                                    <input type="radio" name="color" value="{{ $colorBD->id }}" id="color_{{ $colorBD->id }}" class="d-none filtro-automatico" {{ request('color') == $colorBD->id ? 'checked' : '' }}>
                                    <label for="color_{{ $colorBD->id }}" class="rounded-circle color-filter-dot d-block position-relative" 
                                           style="background-color: {{ $colorBD->hex_code }}; width: 24px; height: 24px; cursor: pointer; {{ request('color') == $colorBD->id ? 'box-shadow: 0 0 0 3px #ffffff, 0 0 0 5px var(--green-500);' : 'border: 1px solid #ddd;' }}" 
                                           title="{{ $colorBD->nombre }}">
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @if(request()->filled('color'))
                            <div class="text-end mt-2">
                                <a href="{{ request()->fullUrlWithQuery(['color' => '']) }}" class="text-decoration-none text-muted" style="font-size: 0.7rem;">✕ Quitar color</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- FILA 6: Rangos de Precio --}}
        <div class="accordion-item border-0 bg-transparent mb-3">
            <h2 class="accordion-header" id="headingPrecio">
                <button class="accordion-button px-0 py-2 bg-transparent text-main poppins-bold fs-6 shadow-none d-flex justify-content-between align-items-center {{ request()->filled('precio_min') || request()->filled('precio_max') ? '' : 'collapsed' }}"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapsePrecio"
                        aria-expanded="{{ request()->filled('precio_min') || request()->filled('precio_max') ? 'true' : 'false' }}"
                        aria-controls="collapsePrecio">
                    <span class="d-flex align-items-center gap-2">
                        <span class="fs-5" style="opacity: 0.7;">$</span>PRECIO
                    </span>
                    <i class="bi bi-chevron-down chevron-icon text-secondary small transition-300"></i>
                </button>
            </h2>
            <div id="collapsePrecio" 
                 class="accordion-collapse collapse {{ request()->filled('precio_min') || request()->filled('precio_max') ? 'show' : '' }}" 
                 aria-labelledby="headingPrecio">
                <div class="accordion-body px-0 pt-3 pb-1">
                    <div class="px-2">
                        {{-- Contenedor del noUiSlider --}}
                        <div id="slider-rango-precio" class="my-3"></div>
                        
                        {{-- Valores en texto debajo del slider --}}
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="small poppins-semibold text-muted" style="font-size: 0.8rem;">
                                $<span id="slider-val-min">0</span> - $<span id="slider-val-max">50000</span>
                            </span>
                            @if(request()->filled('precio_min') || request()->filled('precio_max'))
                                <a href="{{ request()->fullUrlWithQuery(['precio_min' => null, 'precio_max' => null]) }}" class="text-decoration-none text-muted" style="font-size: 0.7rem;">
                                    ✕ Quitar precio
                                </a>
                            @endif
                        </div>

                        {{-- Inputs ocultos vinculados al formulario --}}
                        <input type="hidden" name="precio_min" id="precio_min" value="{{ request('precio_min', 0) }}">
                        <input type="hidden" name="precio_max" id="precio_max" value="{{ request('precio_max', 50000) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- FILA 7: Colecciones Dinámicas --}}
        <div class="accordion-item border-0 bg-transparent mb-3">
            <h2 class="accordion-header" id="headingColeccion">
                <button class="accordion-button px-0 py-2 bg-transparent text-main poppins-bold fs-6 shadow-none d-flex justify-content-between align-items-center {{ request()->filled('coleccion') ? '' : 'collapsed' }}"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapseColeccion"
                        aria-expanded="{{ request()->filled('coleccion') ? 'true' : 'false' }}"
                        aria-controls="collapseColeccion">
                    <span class="d-flex align-items-center gap-2">
                        <img src="{{ asset('img/icons/rocket-launch.svg') }}" alt="Colecciones" style="width: 18px; height: 18px; opacity: 0.7;">COLECCIONES
                    </span>
                    <i class="bi bi-chevron-down chevron-icon text-secondary small transition-300"></i>
                </button>
            </h2>
            <div id="collapseColeccion" 
                 class="accordion-collapse collapse {{ request()->filled('coleccion') ? 'show' : '' }}" 
                 aria-labelledby="headingColeccion">
                <div class="accordion-body px-0 pt-2 pb-1">
                    <div class="row g-2">
                        @foreach($colecciones as $col)
                            <div class="col-6">
                                <input type="radio" name="coleccion" value="{{ $col->id }}" id="coleccion_{{ $col->id }}" class="d-none filtro-automatico" {{ request('coleccion') == $col->id ? 'checked' : '' }}>
                                <label for="coleccion_{{ $col->id }}" class="btn btn-light border w-100 text-center py-2 px-2 small rounded-3 poppins-medium {{ request('coleccion') == $col->id ? 'active-filter-card' : '' }}" style="cursor: pointer; font-size: 0.82rem; height: 100%; display: flex; align-items: center; justify-content: center;">
                                    {{ $col->nombre }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @if(request()->filled('coleccion'))
                        <div class="text-end mt-2">
                            <a href="{{ request()->fullUrlWithQuery(['coleccion' => '']) }}" class="text-decoration-none text-muted" style="font-size: 0.7rem;">✕ Quitar colección</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>