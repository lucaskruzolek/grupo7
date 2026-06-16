@extends('frontend.layout')

@section('contenido')
<div class="container mt-4 mb-5 poppins-regular">
    
    {{-- 1. BREADCRUMB (Migas de pan estéticas como en tu captura) --}}
    <nav aria-label="breadcrumb" class="mb-4 small text-capitalize">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('productos.index') }}" class="text-decoration-none text-muted">Productos</a></li>
            @if($productoBase->categoria)
                @if($productoBase->categoria->parent)
                    {{-- Categoría Padre (Ej: Ropa) --}}
                    <li class="breadcrumb-item">
                        <a href="{{ route('productos.index', ['categoria' => $productoBase->categoria->parent->id]) }}" class="text-decoration-none text-muted">
                            {{ ucfirst($productoBase->categoria->parent->nombre) }}
                        </a>
                    </li>
                    {{-- Subcategoría (Ej: Buzos) --}}
                    <li class="breadcrumb-item">
                        <a href="{{ route('productos.index', ['categoria' => $productoBase->categoria->id]) }}" class="text-decoration-none text-muted">
                            {{ ucfirst($productoBase->categoria->nombre) }}
                        </a>
                    </li>
                @else
                    {{-- Categoría Única --}}
                    <li class="breadcrumb-item">
                        <a href="{{ route('productos.index', ['categoria' => $productoBase->categoria->id]) }}" class="text-decoration-none text-muted">
                            {{ ucfirst($productoBase->categoria->nombre) }}
                        </a>
                    </li>
                @endif
            @endif
            <li class="breadcrumb-item active text-main poppins-semibold" aria-current="page">
                {{ ucfirst(\Illuminate\Support\Str::before($productoBase->nombre, ' - ')) }}
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
                    <div class="border rounded-3 overflow-hidden bg-light position-relative" style="padding-top: 125%;">
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
                            {{ ucfirst($productoBase->tipo_mascota) }}
                        </span>
                    </div>
                </div>

            </div>
        </div>

        {{-- 3. COLUMNA DERECHA: PANEL DE COMPRA E DETALLES COMERCIALES --}}
        <div class="col-md-5">
            
            <h1 class="poppins-bold text-main h3 mb-1">
                {{ \Illuminate\Support\Str::before($productoBase->nombre, ' - ') }}
            </h1>
            
            <p class="text-muted small mb-3">Categoría: <span class="poppins-medium text-secondary">{{ $productoBase->categoria ? $productoBase->categoria->nombre : 'General' }}</span></p>
            
            <div class="my-4">
                <span class="poppins-bold display-6" style="color: var(--green-500);">${{ number_format($productoBase->precio, 2, ',', '.') }}</span>
            </div>

            <hr class="my-4" style="opacity: 0.1;">

            <div class="mb-4">
                <h6 class="poppins-bold text-main small text-uppercase tracking-wider mb-2">Descripción</h6>
                <p class="text-muted small lh-base">
                    {{ $productoBase->descripcion ?? 'Prenda de alta calidad confeccionada por Pet Threads. Diseñada especialmente para ofrecer la máxima comodidad y abrigo a tu mascota sin perder el estilo.' }}
                </p>
            </div>

            <div class="mb-4">
                <h6 class="poppins-bold text-main small text-uppercase mb-2">Color:</h6>
                <div class="d-flex gap-3">
                    @foreach($coloresDisponibles as $col)
                        @php
                            // Buscamos una variante física de este color para extraer su sku_color correspondiente
                            $varianteColor = $variantesDisponibles->firstWhere('color_id', $col->id);
                        @endphp
                        
                        @if($varianteColor)
                            <a href="{{ route('productos.show', $varianteColor->sku_color) }}" 
                               class="btn-seleccionar-color rounded-circle border shadow-sm d-inline-block {{ $productoBase->color_id == $col->id ? 'ring-active' : '' }}" 
                               style="background-color: {{ $col->hex_code }}; width: 32px; height: 32px;"
                               title="{{ $col->nombre }}">
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Errores de Validación/Stock --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show small poppins-medium mb-4" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="mb-4">
                <h6 class="poppins-bold text-main small text-uppercase mb-2">Talle:</h6>
                <div class="d-flex flex-wrap gap-2">
                    @php $primerTalleActivo = false; @endphp
                    @foreach($tallesDisponibles as $talle)
                        @php
                            // Buscar la variante específica para el color actual y este talle
                            $variante = $variantesDisponibles->first(function($v) use ($productoBase, $talle) {
                                return $v->color_id == $productoBase->color_id && $v->talle == $talle;
                            });
                            $esActivo = $variante && !$primerTalleActivo;
                            if ($esActivo) {
                                $primerTalleActivo = true;
                            }
                        @endphp
                        
                        <button type="button" 
                                class="btn px-3 py-2 poppins-medium small rounded-3 dynamic-talle-btn 
                                       {{ !$variante ? 'btn-light text-muted opacity-50' : ($esActivo ? 'talle-activo' : 'bg-white btn-outline-secondary text-main') }}"
                                {{ !$variante ? 'disabled' : '' }}
                                data-producto-id="{{ $variante ? $variante->id : '' }}"
                                data-stock="{{ $variante ? $variante->stock : 0 }}"
                                onclick="seleccionarTalle(this)">
                            {{ $talle }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 4. CONTADOR DE UNIDADES Y BOTÓN DE ACCIÓN --}}
            <form action="{{ route('carrito.agregar') }}" method="POST" id="form-agregar-carrito" class="d-flex gap-2 mt-5">
                @csrf
                <input type="hidden" name="producto_id" id="input-producto-id" value="">
                
                <div class="input-group border rounded-3 bg-white" style="width: 120px;">
                    <button class="btn btn-link text-decoration-none text-main fw-bold" type="button" onclick="modificarCantidad(-1)">-</button>
                    <input type="text" class="form-control border-0 text-center bg-transparent font-monospace fw-bold" id="input-cantidad" name="cantidad" value="1" readonly>
                    <button class="btn btn-link text-decoration-none text-main fw-bold" type="button" onclick="modificarCantidad(1)">+</button>
                </div>
                
                <button type="submit" class="btn btn-primary flex-grow-1 py-3 rounded-3 poppins-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <img src="{{ asset('img/icons/cart-simple.svg') }}" style="width: 20px; height: 20px; filter: brightness(0) invert(1);" alt="Cart"> Agregar al carrito
                </button>
            </form>          
        </div>
    </div>
</div>

{{-- 5. INTERACTIVIDAD LIGERA EN FRONTEND --}}
<style>
    /* Estilo auxiliar para destacar el círculo de color seleccionado */
    .ring-active {
        outline: 2px solid var(--green-500);
        outline-offset: 2px;
    }
    
    /* Estilo para el talle seleccionado */
    .talle-activo {
        background-color: var(--green-500) !important;
        border-color: var(--green-600) !important;
        color: #ffffff !important;
    }
</style>

<script>
    // Inicializar el producto_id al cargar la página con el talle activo por defecto
    document.addEventListener('DOMContentLoaded', function() {
        const activeTalleBtn = document.querySelector('.dynamic-talle-btn.talle-activo');
        if (activeTalleBtn) {
            document.getElementById('input-producto-id').value = activeTalleBtn.getAttribute('data-producto-id');
        }
    });

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
        if (elemento.disabled) return;
        
        document.querySelectorAll('.dynamic-talle-btn').forEach(btn => {
            btn.classList.remove('talle-activo');
            if (!btn.disabled) {
                btn.classList.add('bg-white', 'btn-outline-secondary', 'text-main');
            }
        });
        
        elemento.classList.remove('bg-white', 'btn-outline-secondary', 'text-main');
        elemento.classList.add('talle-activo');
        
        // Sincronizar input oculto
        const prodId = elemento.getAttribute('data-producto-id');
        document.getElementById('input-producto-id').value = prodId;
        
        // Validar cantidad respecto al stock del nuevo talle seleccionado
        const maxStock = parseInt(elemento.getAttribute('data-stock')) || 0;
        const cantInput = document.getElementById('input-cantidad');
        if (parseInt(cantInput.value) > maxStock) {
            cantInput.value = maxStock > 0 ? 1 : 0;
        }
    }

    // Control del contador de prendas (+ / -)
    function modificarCantidad(valor) {
        const input = document.getElementById('input-cantidad');
        const activeTalleBtn = document.querySelector('.dynamic-talle-btn.talle-activo');
        const maxStock = activeTalleBtn ? parseInt(activeTalleBtn.getAttribute('data-stock')) : 10;
        
        let actual = parseInt(input.value) + valor;
        if(actual >= 1 && actual <= maxStock) {
            input.value = actual;
        }
    }
</script>
@endsection