@extends('frontend.layout')

@section('contenido')

{{-- Cuerpo de la página: Sidebar + Productos --}}
<section class="container-fluid">
    <div class="row">
        
        <aside class="col-md-3 bg-white border-end p-3">
            <form action="{{ route('productos.index') }}" method="GET" id="form-filtros-tienda">
                @include('frontend.partes.sidebar')
            </form>
        </aside>
    
        <main class="col-md-9">
            <div class="row row-cols-1 row-cols-md-3 g-4 mt-2">
                
                @forelse($productos as $prod)
                    <div class="col">
                        <div class="card h-100 border rounded-3 shadow-sm overflow-hidden backend-card-producto">
                            
                            <div class="position-relative bg-light" style="padding-top: 100%;">
            
                            @if($prod->imagenes->isNotEmpty())
                                {{-- Carrusel único por producto identificado por su SKU_BASE --}}
                                <div id="carrusel_{{ $loop->iteration }}" class="carousel slide position-absolute top-0 start-0 w-100 h-100" data-bs-interval="false" data-bs-touch="true">
                                    
                                    @if($prod->imagenes->count() > 1)
                                        <div class="carousel-indicators" style="margin-bottom: 0.5rem; z-index: 4;">
                                            @foreach($prod->imagenes as $index => $img)
                                                <button type="button" 
                                                        data-bs-target="#carrusel_{{ $loop->iteration }}" 
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
                                            <div class="carousel-item h-100 {{ $index == 0 ? 'active' : '' }}">
                                                <img src="{{ $img->url }}" 
                                                     class="d-block w-100 h-100 object-fit-cover" 
                                                     alt="{{ $prod->nombre }} - Ángulo {{ $index + 1 }}">
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
                                <img src="{{ asset('img/placeholder-petthreads.jpg') }}" 
                                     class="card-img-top position-absolute top-0 start-0 w-100 h-100 object-fit-cover" 
                                     alt="Sin imagen disponible">
                            @endif
                            
                            <span class="position-absolute top-0 end-0 bg-white text-main small poppins-semibold m-2 px-2 py-1 rounded-pill border shadow-sm" style="z-index: 3;">
                                {{ ucfirst($prod->tipo_mascota) }}
                            </span>
                            </div>

                            <div class="card-body d-flex flex-column justify-content-between p-3">
                                <div>
                                    <span class="text-muted small text-uppercase poppins-regular d-block mb-1">
                                        {{ $prod->categoria ? $prod->categoria->nombre : 'General' }}
                                    </span>
                                    
                                    <h5 class="card-title poppins-bold text-main fs-6 mb-2 text-truncate" title="{{ $prod->nombre }}">
                                        {{ $prod->nombre }}
                                    </h5>

                                    <div class="mt-2 mb-1">
                                        @php
                                            // Consultamos las variantes hermanas que comparten el mismo sku_base
                                            $variantes = \App\Models\Producto::where('sku_base', $prod->sku_base)->get();
                                            
                                            // Extraemos talles únicos y cargamos los colores únicos de la BD
                                            $tallesDisponibles = $variantes->pluck('talle')->unique();
                                            $coloresDisponibles = $variantes->load('color')->pluck('color')->unique('id');
                                        @endphp

                                        @if($coloresDisponibles->count() > 1)
                                            <div class="d-flex gap-1 align-items-center mb-2">
                                                <span class="text-muted" style="font-size: 11px;">Colores:</span>
                                                @foreach($coloresDisponibles as $colVariante)
                                                    <span class="rounded-circle border" 
                                                          style="background-color: {{ $colVariante->hex_code }}; width: 12px; height: 12px; display: inline-block;" 
                                                          title="{{ $colVariante->nombre }}">
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

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
                                    <span class="poppins-bold text-primary fs-5">
                                        ${{ number_format($prod->precio, 2, ',', '.') }}
                                    </span>
                                    
                                    <a href="{{ route('productos.show', $prod->sku_base) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 poppins-semibold">
                                        Ver más
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="mb-3">
                            <img src="{{ asset('img/icons/empty-search.svg') }}" alt="No hay resultados" style="width: 80px; opacity: 0.5;">
                        </div>
                        <h4 class="poppins-bold text-main fs-5">No encontramos productos</h4>
                        <p class="text-muted small">Probá cambiando o limpiando los filtros del sidebar.</p>
                        <a href="{{ route('productos.index') }}" class="btn btn-primary btn-sm rounded-pill mt-2 px-4">Limpiar Filtros</a>
                    </div>
                @endforelse

            </div>
        </main>
    </div> 
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filtros = document.querySelectorAll('.filtro-automatico');
        const formulario = document.getElementById('form-filtros-tienda');

        filtros.forEach(input => {
            input.addEventListener('change', function () {
                formulario.submit();
            });
        });
    });
</script>

@endsection

