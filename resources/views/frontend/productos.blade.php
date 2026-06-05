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
                    
                    <div class="position-relative bg-light" style="padding-top: 100%; /* Ratio 1:1 Cuadrado */">
                        @if($prod->imagenPortada)
                            <img src="{{ $prod->imagenPortada->url }}" 
                                 class="card-img-top position-absolute top-0 start-0 w-100 h-100 object-fit-cover" 
                                 alt="{{ $prod->nombre }}">
                        @else
                            <img src="{{ asset('img/placeholder-petthreads.jpg') }}" 
                                 class="card-img-top position-absolute top-0 start-0 w-100 h-100 object-fit-cover" 
                                 alt="Sin imagen disponible">
                        @endif
                        
                        <span class="position-absolute top-0 end-0 bg-white text-main small poppins-semibold m-2 px-2 py-1 rounded-pill border shadow-sm">
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
    //---------- Script para filtro automatico sin necesidad de recargar el sitio-------------//
    document.addEventListener('DOMContentLoaded', function () {
        // Buscamos todos los inputs que tengan la clase 'filtro-automatico'
        const filtros = document.querySelectorAll('.filtro-automatico');
        // Buscamos el formulario contenedor
        const formulario = document.getElementById('form-filtros-tienda');

        // Escuchamos cuando cualquiera de ellos cambie de estado
        filtros.forEach(input => {
            input.addEventListener('change', function () {
                // Forzamos el envío del formulario inmediatamente
                formulario.submit();
            });
        });
    });
</script>

@endsection

