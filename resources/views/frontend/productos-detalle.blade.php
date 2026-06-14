@extends('frontend.layout')

@section('contenido')
<div class="container my-5 poppins-regular">
    
    {{-- 1. BREADCRUMB (Migas de pan estéticas como en tu captura) --}}
    <nav aria-label="breadcrumb" class="mb-4 small">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('productos.index') }}" class="text-decoration-none text-muted">Productos</a></li>
            <li class="breadcrumb-item active text-main poppins-semibold" aria-current="page">
                {{ \Illuminate\Support\Str::before($productoBase->nombre, ' - ') }}
            </li>
        </ol>
    </nav>

    <div class="row g-5">
        
        {{-- 2. COLUMNA IZQUIERDA: GALERÍA DE IMÁGENES (Ángulos dinámicos) --}}
        <div class="col-md-7">
            <div class="row">
                
                <div class="col-2 d-flex flex-column gap-2">
                    @foreach($imagenes as $index => $img)
                        <button type="button" 
                                class="btn p-0 border rounded overflow-hidden miniatura-detalle-btn {{ $index == 0 ? 'border-primary shadow-sm' : '' }}"
                                onclick="cambiarImagenPrincipal('{{ $img->url }}', this)"
                                style="aspect-ratio: 1/1; position: relative;">
                            <img src="{{ $img->url }}" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" alt="Ángulo {{ $img->orden }}">
                        </button>
                    @endforeach
                </div>

                <div class="col-10">
                    <div class="border rounded-3 overflow-hidden bg-light position-relative" style="padding-top: 100%;">
                        @if($imagenes->isNotEmpty())
                            <img id="imagen-principal-pantalla" 
                                 src="{{ $imagenes->first()->url }}" 
                                 class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" 
                                 alt="{{ $productoBase->nombre }}">
                        @else
                            <img src="{{ asset('img/placeholder-petthreads.jpg') }}" 
                                 class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" 
                                 alt="Sin imagen disponible">
                        @endif
                        
                        <span class="position-absolute top-0 end-0 bg-white text-main small poppins-semibold m-3 px-3 py-1 rounded-pill border shadow-sm">
                            Para {{ ucfirst($productoBase->tipo_mascota) }}
                        </span>
                    </div>
                </div>

            </div>
        </div>

        {{-- 3. COLUMNA DERECHA: PANEL DE COMPRA E DETALLES COMERCIALES --}}
        <div class="col-md-5">
            
            <span class="badge bg-success-subtle text-success poppins-semibold mb-2 rounded-pill px-3 py-1 text-uppercase" style="font-size: 11px;">Nuevo</span>
            
            <h1 class="poppins-bold text-main h3 mb-1">
                {{ \Illuminate\Support\Str::before($productoBase->nombre, ' - ') }}
            </h1>
            
            <p class="text-muted small mb-3">Categoría: <span class="poppins-medium text-secondary">{{ $productoBase->categoria ? $productoBase->categoria->nombre : 'General' }}</span></p>
            
            <div class="my-4">
                <span class="poppins-bold text-primary display-6">${{ number_format($productoBase->precio, 2, ',', '.') }}</span>
            </div>

            <hr class="my-4" style="opacity: 0.1;">

            <div class="mb-4">
                <h6 class="poppins-bold text-main small text-uppercase tracking-wider mb-2">Descripción</h6>
                <p class="text-muted small lh-base">
                    {{ $productoBase->descripcion ?? 'Prenda de alta calidad confeccionada por Pet Threads. Diseñada especialmente para ofrecer la máxima comodidad y abrigo a tu mascota sin perder el estilo.' }}
                </p>
            </div>

            <div class="mb-4">
                <h6 class="poppins-bold text-main small text-uppercase mb-2">Colores Disponibles:</h6>
                <div class="d-flex gap-3">
                    @foreach($coloresDisponibles as $col)
                        @php
                            // Buscamos una variante física de este color para extraer su sku_color correspondiente
                            $varianteColor = $variantesDisponibles->firstWhere('color_id', $col->id);
                        @endphp
                        
                        @if($varianteColor)
                            <button type="button" 
                                    class="btn-seleccionar-color rounded-circle border shadow-sm position-relative {{ $productoBase->color_id == $col->id ? 'active-color-ring' : '' }}" 
                                    style="background-color: {{ $col->hex_code }}; width: 32px; height: 32px;"
                                    data-sku-color="{{ $varianteColor->sku_color }}"
                                    title="{{ $col->nombre }}">
                            </button>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="poppins-bold text-main small text-uppercase m-0">Talles Disponibles:</h6>
                    <a href="#" class="text-muted small text-decoration-underline" style="font-size: 12px;">Guía de talles</a>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($tallesDisponibles as $index => $talle)
                        <button type="button" 
                                class="btn btn-outline-secondary px-3 py-2 poppins-medium small rounded-3 bg-white text-main dynamic-talle-btn {{ $index == 0 ? 'btn-primary text-white border-primary' : '' }}"
                                onclick="seleccionarTalle(this)">
                            {{ $talle }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 4. CONTADOR DE UNIDADES Y BOTÓN DE ACCIÓN --}}
            <div class="d-flex gap-2 mt-5">
                <div class="input-group border rounded-3 bg-white" style="width: 120px;">
                    <button class="btn btn-link text-decoration-none text-main fw-bold" type="button" onclick="modificarCantidad(-1)">-</button>
                    <input type="text" class="form-control border-0 text-center bg-transparent font-monospace fw-bold" id="input-cantidad" value="1" readonly>
                    <button class="btn btn-link text-decoration-none text-main fw-bold" type="button" onclick="modificarCantidad(1)">+</button>
                </div>
                
                <button type="button" class="btn btn-primary flex-grow-1 py-3 rounded-3 poppins-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-bag-plus-fill"></i> Agregar al carrito
                </button>
            </div>

            <div class="mt-4 text-muted small d-flex align-items-center gap-2 border-top pt-3" style="opacity: 0.8;">
                <i class="bi bi-shield-check text-success fs-5"></i>
                <span>Compra garantizada. Retiro gratis en sucursal o envío a domicilio.</span>
            </div>

        </div>
    </div>
</div>

{{-- 5. INTERACTIVIDAD LIGERA EN FRONTEND --}}
<style>
    /* Estilo auxiliar para destacar el círculo de color seleccionado */
    .ring-active {
        outline: 2px solid var(--bs-primary);
        outline-offset: 2px;
    }
</style>

<script>
    // Manejo de la Galería de Ángulos (Miniaturas)
    function cambiarImagenPrincipal(url, botonClickeado) {
        document.getElementById('imagen-principal-pantalla').src = url;
        document.querySelectorAll('.miniatura-detalle-btn').forEach(btn => btn.classList.remove('border-primary', 'shadow-sm'));
        botonClickeado.classList.add('border-primary', 'shadow-sm');
    }

    // Selector de Color Estético
    function seleccionarColor(elemento) {
        document.querySelectorAll('.boton-color-selector').forEach(btn => btn.classList.remove('border-primary', 'ring-active'));
        elemento.classList.add('border-primary', 'ring-active');
    }

    // Selector de Talle con estilos de Bootstrap
    function seleccionarTalle(elemento) {
        document.querySelectorAll('.dynamic-talle-btn').forEach(btn => {
            btn.classList.remove('btn-primary', 'text-white', 'border-primary');
            btn.classList.add('btn-outline-secondary', 'text-main');
        });
        elemento.classList.remove('btn-outline-secondary', 'text-main');
        elemento.classList.add('btn-primary', 'text-white', 'border-primary');
    }

    // Control del contador de prendas (+ / -)
    function modificarCantidad(valor) {
        const input = document.getElementById('input-cantidad');
        let actual = parseInt(input.value) + valor;
        if(actual >= 1 && actual <= 10) {
            input.value = actual;
        }
    }
</script>
@endsection