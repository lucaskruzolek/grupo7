@extends('frontend.layout')

@section('styles')
<style>
    .btn-verde-tierra-outline {
        color: var(--green-500) !important;
        border-color: var(--green-500) !important;
        background-color: transparent !important;
        transition: all 0.2s ease-in-out;
    }
    .btn-verde-tierra-outline:hover,
    .btn-verde-tierra-outline:focus,
    .btn-verde-tierra-outline:active {
        color: #ffffff !important;
        background-color: var(--green-500) !important;
        border-color: var(--green-500) !important;
    }
    .btn-ver-mas-verde {
        font-size: 0.7rem !important;
        padding: 0.2rem 0.6rem !important;
    }

    /* Estilos personalizados para la paginación de la tienda */
    .custom-pagination .page-link {
        color: var(--neutral-700) !important;
        background-color: #ffffff !important;
        border: 1px solid var(--neutral-200) !important;
        font-size: 0.85rem !important;
        padding: 0.45rem 0.85rem !important;
        transition: all 0.2s ease !important;
        font-family: var(--font-main) !important;
        box-shadow: none !important;
    }

    .custom-pagination .page-link:hover {
        color: var(--green-600) !important;
        background-color: var(--neutral-50) !important;
        border-color: var(--neutral-300) !important;
    }

    .custom-pagination .page-item.active .page-link {
        background-color: var(--green-500) !important;
        border-color: var(--green-500) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }

    .custom-pagination .page-item.disabled .page-link {
        color: var(--neutral-400) !important;
        background-color: #ffffff !important;
        border-color: var(--neutral-200) !important;
        opacity: 0.7 !important;
    }

    /* Efecto hover de zoom in para las imágenes dentro de tarjetas */
    .card-zoom-hover img {
        transition: transform 0.5s ease !important;
    }
    .card-zoom-hover:hover img {
        transform: scale(1.08) !important;
    }
</style>
@endsection

@section('contenido')

{{-- Cuerpo de la página: Sidebar + Productos --}}
<section class="container-fluid">
    <div class="row flex-md-nowrap">
        
        <aside class="sidebar-fixed bg-white border-end p-3">
            <form action="{{ route('productos.index') }}" method="GET" id="form-filtros-tienda">
                {{--INPUT OCULTO: Guarda la búsqueda activa --}}
                <input type="hidden" name="buscar" value="{{ request('buscar') }}">
                @include('frontend.partes.sidebar')
            </form>
        </aside>
    
        <main class="main-fluid ps-md-4 pt-3 pt-md-0">
            @if(!empty($busquedaFallida))
                <div class="alert alert-light border rounded-3 d-flex align-items-center justify-content-between p-3 mt-3 mb-0 poppins-medium alert-dismissible fade show text-secondary" role="alert" style="background-color: #f8f9fa; font-size: 0.85rem; border-color: var(--neutral-200) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-info-circle text-primary fs-5"></i>
                        <span>
                            No encontramos productos para tu búsqueda: <strong>"{{ $busquedaFallida }}"</strong>. Mostrando los demás productos disponibles.
                        </span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 1.25rem; font-size: 0.75rem;"></button>
                </div>
            @endif

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-lg-4 g-3 mt-1">
                
                @forelse($productos as $prod)
                    <div class="col anim-fade-down" style="--anim-order: {{ $loop->iteration }};">
                        {{-- Añadimos la clase 'backend-card-producto' para el selector de JS --}}
                        <div class="card h-100 border rounded-3 shadow-sm overflow-hidden backend-card-producto card-zoom-hover">
                            
                            <div class="position-relative bg-light" style="padding-top: 125%;">
            
                            @if($prod->imagenes->isNotEmpty())
                                {{-- Carrusel único por producto identificado por el bucle --}}
                                <div id="carrusel_{{ $loop->iteration }}" class="carousel slide position-absolute top-0 start-0 w-100 h-100" data-bs-interval="false" data-bs-touch="true">
                                    
                                    @if($prod->imagenes->count() > 1)
                                        <div class="carousel-indicators" style="margin-bottom: 0.5rem; z-index: 4;">
                                            @foreach($prod->imagenes as $index => $img)
                                                <button type="button" 
                                                        data-bs-target="#carrusel_{{ $loop->parent->iteration ?? $loop->iteration }}" 
                                                        data-bs-slide-to="{{ $index }}" 
                                                        class="{{ $index == 0 ? 'active' : '' }}" 
                                                        aria-current="{{ $index == 0 ? 'true' : 'false' }}" 
                                                        aria-label="Ángulo {{ $index + 1 }}">
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="carousel-inner h-100">
                                        @foreach($prod->imagenes as $index => $img)
                                            {{-- Guardamos el sku_color del slide en un atributo de datos para que JavaScript lo lea al deslizar --}}
                                            <div class="carousel-item h-100 {{ $index == 0 ? 'active' : '' }}" data-sku-color="{{ $img->sku_color }}">
                                                <a href="{{ route('productos.show', $img->sku_color) }}" class="d-block w-100 h-100">
                                                    <img src="{{ $img->url }}" 
                                                         class="d-block w-100 h-100 object-fit-cover" 
                                                         alt="{{ $prod->nombre_base }} {{ $prod->color ? $prod->color->nombre : '' }} - Principal Color">
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($prod->imagenes->count() > 1)
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carrusel_{{ $loop->iteration }}" data-bs-slide="prev" style="width: 12%; z-index: 5;">
                                            <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true" style="width: 1.5rem; height: 1.5rem; background-size: 60%;"></span>
                                            <span class="visually-hidden">Anterior</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carrusel_{{ $loop->iteration }}" data-bs-slide="next" style="width: 12%; z-index: 5;">
                                            <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true" style="width: 1.5rem; height: 1.5rem; background-size: 60%;"></span>
                                            <span class="visually-hidden">Siguiente</span>
                                        </button>
                                    @endif

                                </div>
                            @else
                                <a href="{{ route('productos.show', $prod->sku_color) }}" class="d-block w-100 h-100">
                                    <img src="{{ asset('img/placeholder-petthreads.jpg') }}" 
                                         class="card-img-top position-absolute top-0 start-0 w-100 h-100 object-fit-cover" 
                                         alt="Sin imagen disponible">
                                </a>
                            @endif
                            
                            <span class="position-absolute top-0 end-0 bg-white text-main small poppins-semibold m-2 px-2 py-1 rounded-pill border shadow-sm" style="z-index: 3;">
                                {{ ucfirst($prod->tipo_mascota) }}
                            </span>
                            </div>

                            <div class="card-body d-flex flex-column justify-content-between p-3">
                                <div>
                                    <span class="text-muted text-uppercase poppins-regular d-block mb-1" style="font-size: 12px;">
                                        {{ $prod->categoria ? $prod->categoria->nombre : 'General' }}
                                    </span>
                                    
                                    <h5 class="card-title poppins-bold mb-2" title="{{ $prod->nombre_base }} {{ $prod->color ? $prod->color->nombre : '' }}"
                                    style="font-size: 16px;">
                                        <a href="{{ route('productos.show', $prod->sku_color) }}" class="text-decoration-none text-main link-titulo-dinamico">
                                            {{ $prod->nombre_base }} {{ $prod->color ? $prod->color->nombre : '' }}
                                        </a>
                                    </h5>

                                    <div class="mt-2 mb-1">
                                        @php
                                            // Obtenemos solo los talles en stock para este color específico de producto
                                            $variantesColor = \App\Models\Producto::where('sku_color', $prod->sku_color)->get();
                                            $tallesDisponibles = $variantesColor->pluck('talle')->unique();
                                            
                                            // Obtenemos todos los colores del modelo base (excluyendo el color actual) para mostrar las opciones alternativas
                                            $todosLosColoresModelo = \App\Models\Producto::where('sku_base', $prod->sku_base)
                                                ->where('color_id', '!=', $prod->color_id)
                                                ->with('color')
                                                ->get()
                                                ->pluck('color')
                                                ->unique('id');
                                        @endphp

                                        {{-- CÍRCULOS DE COLORES DISPONIBLES --}}
                                        @if($todosLosColoresModelo->isNotEmpty())
                                            <div class="mt-2 mb-3">
                                                <p class="text-muted small mb-1 poppins-medium" style="font-size: 0.7rem;">Otros colores:</p>
                                                <div class="d-flex gap-1 flex-wrap">
                                                    @foreach($todosLosColoresModelo as $colorVariante)
                                                        @if($colorVariante)
                                                            <span class="rounded-circle border d-inline-block shadow-xs" 
                                                                style="background-color: {{ $colorVariante->hex_code }}; width: 14px; height: 14px;" 
                                                                title="{{ $colorVariante->nombre }}">
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        {{-- LISTADO DE TALLES --}}
                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                            <span class="text-muted" style="font-size: 11px;">Talles:</span>
                                            @foreach($tallesDisponibles as $talleVar)
                                                <span class="badge bg-light text-secondary border font-monospace" style="font-size: 10px;">
                                                    {{ $talleVar }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                 <div class="d-flex align-items-center justify-content-between mt-3">
                                     <span class="poppins-bold fs-5" style="color: var(--green-500);">
                                         ${{ number_format($prod->precio, 2, ',', '.') }}
                                     </span>
                                     
                                     {{-- El botón "Ver más" inicia apuntando al sku_color del primer elemento del carrusel --}}
                                     <a href="{{ route('productos.show', $prod->sku_color) }}" 
                                        class="btn btn-verde-tierra-outline btn-ver-mas-verde rounded-pill poppins-semibold btn-ver-mas-dinamico text-nowrap">
                                         Ver más
                                     </a>
                                     
                                 </div>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        
                        <h4 class="poppins-bold text-main fs-5">No encontramos productos</h4>
                        <p class="text-muted small">Probá cambiando o limpiando los filtros del sidebar.</p>
                        <a href="{{ route('productos.index') }}" class="btn btn-primary btn-sm rounded-pill mt-2 px-4">Limpiar Filtros</a>
                    </div>
                @endforelse

            </div>

            {{-- Renderizado de la botonera de paginación --}}
            <div class="d-flex justify-content-center my-4">
                {{ $productos->appends(request()->query())->links('backend.admin.pagination') }}
            </div>
        </main>
    </div> 
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Envío automático para los filtros del Sidebar
        const filtros = document.querySelectorAll('.filtro-automatico');
        const formulario = document.getElementById('form-filtros-tienda');

        filtros.forEach(input => {
            input.addEventListener('change', function () {
                formulario.submit();
            });
        });

        // 1.5. Inicializar noUiSlider para el rango de precios
        const sliderPrecio = document.getElementById('slider-rango-precio');
        if (sliderPrecio) {
            const inputMin = document.getElementById('precio_min');
            const inputMax = document.getElementById('precio_max');
            const valMin = document.getElementById('slider-val-min');
            const valMax = document.getElementById('slider-val-max');
            
            // Valores iniciales basados en request o default (0 a 50000)
            const defaultMin = 0;
            const defaultMax = 50000;
            
            // Leemos los inputs ocultos (precargados por Laravel)
            const currentMin = parseInt(inputMin.value);
            const currentMax = parseInt(inputMax.value);

            noUiSlider.create(sliderPrecio, {
                start: [
                    isNaN(currentMin) ? defaultMin : currentMin, 
                    isNaN(currentMax) ? defaultMax : currentMax
                ],
                connect: true,
                range: {
                    'min': 0,
                    'max': 50000
                },
                step: 1000,
                format: {
                    to: function (value) {
                        return Math.round(value);
                    },
                    from: function (value) {
                        return Number(value);
                    }
                }
            });

            // Escuchar cambios de arrastre para actualizar las etiquetas e inputs en tiempo real
            sliderPrecio.noUiSlider.on('update', function (values, handle) {
                const currentMinVal = Math.round(values[0]);
                const currentMaxVal = Math.round(values[1]);

                if (handle === 0) {
                    valMin.innerText = currentMinVal;
                    inputMin.value = currentMinVal;
                } else {
                    valMax.innerText = currentMaxVal;
                    inputMax.value = currentMaxVal;
                }

                // Práctica recomendada: Deshabilitar inputs si están en su valor por defecto
                // para que no se envíen en la URL ni mantengan activo el accordion innecesariamente.
                inputMin.disabled = (currentMinVal === defaultMin);
                inputMax.disabled = (currentMaxVal === defaultMax);
            });

            // Disparar envío automático de formulario únicamente al soltar el manejador
            sliderPrecio.noUiSlider.on('change', function () {
                formulario.submit();
            });
        }

        // 2. Escucha interactiva de los carruseles para reescribir el botón "Ver más" y el título del producto
        const carruseles = document.querySelectorAll('.carousel');

        carruseles.forEach(carrusel => {
            carrusel.addEventListener('slide.bs.carousel', function (event) {
                // Capturamos el slide que está por entrar activamente
                const siguienteSlide = event.relatedTarget;
                const skuColor = siguienteSlide.getAttribute('data-sku-color');
                
                // Buscamos el botón y el link del título específicos dentro de esta misma tarjeta
                const tarjetaContenedora = carrusel.closest('.backend-card-producto');
                const botonVerMas = tarjetaContenedora.querySelector('.btn-ver-mas-dinamico');
                const linkTitulo = tarjetaContenedora.querySelector('.link-titulo-dinamico');
                
                if (skuColor) {
                    const nuevaUrl = `/productos/${skuColor}`;
                    if (botonVerMas) {
                        botonVerMas.setAttribute('href', nuevaUrl);
                    }
                    if (linkTitulo) {
                        linkTitulo.setAttribute('href', nuevaUrl);
                    }
                }
            });
        });

        // 3. Forzar animación inmediata para las tarjetas de esta página (sin esperar al scroll)
        document.querySelectorAll('.anim-fade-down').forEach(el => el.classList.add('anim-visible'));
    });
</script>

@endsection

