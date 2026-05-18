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
                <button class="btn btn-light w-100 py-3 border rounded-3 d-flex flex-column align-items-center justify-content-center gap-2 active-filter-card" type="button">
                    <img src="{{ asset('img/icons/dog.svg') }}" alt="Perros" style="width: 32px; height: 32px;">
                    <span class="poppins-semibold text-main small">Perros</span>
                </button>
            </div>
            <div class="col-6">
                <button class="btn btn-light w-100 py-3 border rounded-3 d-flex flex-column align-items-center justify-content-center gap-2" type="button">
                    <img src="{{ asset('img/icons/cat.svg') }}" alt="Gatos" style="width: 32px; height: 32px;">
                    <span class="poppins-semibold text-main small">Gatos</span>
                </button>
            </div>
        </div>
    </div>

    <hr class="my-3" style="opacity: 0.1;">

    {{-- FILA 3: Tipo de Producto (Ropa / Accesorios + Grilla subcategorías) --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="poppins-bold text-main fs-6 d-flex align-items-center gap-2">
                <img src="{{ asset('img/icons/shirt.svg') }}" alt="Ropa" style="width: 18px; height: 18px; opacity: 0.7;">TIPO DE PRODUCTO
            </span>
            <i class="bi bi-chevron-up text-secondary small"></i>
        </div>
        
        {{-- Botones principales Superiores --}}
        <div class="row g-2 mb-3">
            <div class="col-6">
                <button class="btn btn-light w-100 py-2 border rounded-3 d-flex align-items-center justify-content-center gap-2 active-filter-card" type="button">
                    <img src="{{ asset('img/icons/shirt.svg') }}" alt="Ropa" style="width: 20px; height: 20px;">
                    <span class="poppins-semibold text-main small">Ropa</span>
                </button>
            </div>
            <div class="col-6">
                <button class="btn btn-light w-100 py-2 border rounded-3 d-flex align-items-center justify-content-center gap-2" type="button">
                    <img src="{{ asset('img/icons/juguetes.svg') }}" alt="Accesorios" style="width: 20px; height: 20px;">
                    <span class="poppins-semibold text-main small">Accesorios</span>
                </button>
            </div>
        </div>

        {{-- Grilla Dinámica Subcategorías (3x3) --}}
        <div class="row row-cols-3 g-2">
            <div class="col"><button class="btn btn-light border rounded-pill w-100 py-1 px-0 small-filter-btn btn-active-pill" type="button">Buzos</button></div>
            <div class="col"><button class="btn btn-light border rounded-pill w-100 py-1 px-0 small-filter-btn" type="button">Suéteres</button></div>
            <div class="col"><button class="btn btn-light border rounded-pill w-100 py-1 px-0 small-filter-btn" type="button">Pecheras</button></div>
            <div class="col"><button class="btn btn-light border rounded-pill w-100 py-1 px-0 small-filter-btn" type="button">Impermeables</button></div>
            <div class="col"><button class="btn btn-light border rounded-pill w-100 py-1 px-0 small-filter-btn" type="button">Camisetas</button></div>
            <div class="col"><button class="btn btn-light border rounded-pill w-100 py-1 px-0 small-filter-btn" type="button">Abrigos</button></div>
            <div class="col"><button class="btn btn-light border rounded-pill w-100 py-1 px-0 small-filter-btn" type="button">Pijamas</button></div>
            <div class="col"><button class="btn btn-light border rounded-pill w-100 py-1 px-0 small-filter-btn" type="button">Vestidos</button></div>
            <div class="col"><button class="btn btn-light border rounded-pill w-100 py-1 px-0 small-filter-btn" type="button">Otros</button></div>
        </div>
    </div>

    <hr class="my-3" style="opacity: 0.1;">

    {{-- FILA 4: Talles --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="poppins-bold text-main fs-6 d-flex align-items-center gap-2">
                <img src="{{ asset('img/icons/ruler.svg') }}" alt="Talles" style="width: 18px; height: 18px; opacity: 0.7;">TALLE
            </span>
            <i class="bi bi-chevron-down text-secondary small"></i>
        </div>
        <div class="d-flex justify-content-between gap-1">
            <button class="btn btn-light border py-2 px-0 text-center square-filter-btn" type="button">XS</button>
            <button class="btn btn-light border py-2 px-0 text-center square-filter-btn" type="button">S</button>
            <button class="btn btn-light border py-2 px-0 text-center square-filter-btn" type="button">M</button>
            <button class="btn btn-light border py-2 px-0 text-center square-filter-btn" type="button">L</button>
            <button class="btn btn-light border py-2 px-0 text-center square-filter-btn" type="button">XL</button>
            <button class="btn btn-light border py-2 px-0 text-center square-filter-btn" type="button">XXL</button>
        </div>
    </div>

    <hr class="my-3" style="opacity: 0.1;">

    {{-- FILA 5: Color --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="poppins-bold text-main fs-6 d-flex align-items-center gap-2">
                <img src="{{ asset('img/icons/palette.svg') }}" alt="Colores" style="width: 18px; height: 18px; opacity: 0.7;">COLOR
            </span>
            <i class="bi bi-chevron-down text-secondary small"></i>
        </div>
        <div class="d-flex gap-2 justify-content-start align-items-center flex-wrap">
            <button type="button" class="rounded-circle border-0 color-filter-dot" style="background-color: #556b2f;" title="Verde musgo"></button>
            <button type="button" class="rounded-circle border-0 color-filter-dot" style="background-color: #e3a393;" title="Coral"></button>
            <button type="button" class="rounded-circle border-0 color-filter-dot" style="background-color: #f5f5dc; border: 1px solid #ddd !important;" title="Beige"></button>
            <button type="button" class="rounded-circle border-0 color-filter-dot" style="background-color: #1a2941;" title="Azul Marino"></button>
            <button type="button" class="rounded-circle border-0 color-filter-dot" style="background-color: #8b9fba;" title="Gris"></button>
            <button type="button" class="rounded-circle border-0 color-filter-dot" style="background-color: #000000;" title="Negro"></button>
            <button type="button" class="rounded-circle border bg-white d-flex align-items-center justify-content-center color-filter-dot" style="border-style: dashed !important;" title="Más colores">+</button>
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
