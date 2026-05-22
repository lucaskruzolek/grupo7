@extends('frontend.layout') {{-- Solo un extend al inicio --}}

@section('contenido')


{{-- Cuerpo de la página: Sidebar + Productos --}}
<section class="container-fluid">
    <div class="row">
    
        <aside class="col-md-3 bg-white border-end p-3 mt-4">
            @include('frontend.partes.sidebar')
        </aside>

        <main class="col-md-9 p-4">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                
                {{-- Tarjeta de ejemplo --}}
            <div class="col">
            <div class="card h-100 shadow-sm border-0">
                <img src="{{ asset('img/ui/productos/juguete-pulpo.webp') }}" 
                    id="img-prod-1" 
                    class="card-img-top" 
                    alt="Producto">

                <div class="card-body">
                    <h5 class="card-title fw-bold">juguetes</h5>
                    <p class="card-text text-muted">$10000</p>
            
                <div class="d-flex gap-2 mt-3">
                    <button type="button" 
                        class="rounded-circle border-0" 
                        style="width: 25px; height: 25px; background-color: #f8bbd0;" 
                        onclick="document.getElementById('img-prod-1').src='{{ asset('img/ui/productos/juguete-pulpo.webp') }}'"
                        title="Rosa">
                    </button>

                    <button type="button" 
                        class="rounded-circle border-0" 
                        style="width: 25px; height: 25px; background-color: #f5f5dc; border: 1px solid #ddd !important;" 
                        onclick="document.getElementById('img-prod-1').src='{{ asset('img/ui/productos/juguete-acordeon.webp') }}'"
                        title="Beige">
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card h-100 shadow-sm border-0">
            <img src="{{ asset('img/ui/productos/correa-urban.webp') }}" 
                id="img-prod-3" 
                class="card-img-top" 
                alt="Producto">

            <div class="card-body">
                <h5 class="card-title fw-bold">Correas y arneces</h5>
                <p class="card-text text-muted">$8000</p>
            
                <div class="d-flex gap-2 mt-3">
                    <button type="button" 
                        class="rounded-circle border-0" 
                        style="width: 25px; height: 25px; background-color: #f8bbd0;" 
                        onclick="document.getElementById('img-prod-3').src='{{ asset('img/ui/productos/correa-urban.webp') }}'"
                        title="Rosa">
                    </button>

                    <button type="button" 
                        class="rounded-circle border-0" 
                        style="width: 25px; height: 25px; background-color: #f8bbd0; border: 1px solid #ddd !important;" 
                        onclick="document.getElementById('img-prod-3').src='{{ asset('img/ui/productos/arnes-rosa.webp') }}'"
                        title="Beige">
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
    <div class="card h-100 shadow-sm border-0">
        <img src="{{ asset('img/ui/productos/gato-buzo-ratones.webp') }}" 
             id="img-prod-5" 
             class="card-img-top" 
             alt="Producto">

        <div class="card-body">
            <h5 class="card-title fw-bold">Buzos para gatos</h5>
            <p class="card-text text-muted">$10000</p>
            
            <div class="d-flex gap-2 mt-3">
                <button type="button" 
                    class="rounded-circle border-0" 
                    style="width: 25px; height: 25px; background-color: #f8bbd0;" 
                    onclick="document.getElementById('img-prod-5').src='{{ asset('img/ui/productos/gato-buzo-ratones.webp') }}'"
                    title="Rosa">
                </button>

            </div>
        </div>
    </div>
</div>

<div class="col">
    <div class="card h-100 shadow-sm border-0">
        <img src="{{ asset('img/ui/productos/perro-botones-beige.webp') }}" 
             id="img-prod-6" 
             class="card-img-top" 
             alt="Producto">

        <div class="card-body">
            <h5 class="card-title fw-bold">Buzos para perros</h5>
            <p class="card-text text-muted">$12000</p>
            
            <div class="d-flex gap-2 mt-3">
                <button type="button" 
                    class="rounded-circle border-0" 
                    style="width: 25px; height: 25px; background-color: #f5f5dc;" 
                    onclick="document.getElementById('img-prod-6').src='{{ asset('img/ui/productos/perro-botones-beige.webp') }}'"
                    title="Rosa">
                </button>

                <button type="button" 
                    class="rounded-circle border-0" 
                    style="width: 25px; height: 25px; background-color: var(--green-500); border: 1px solid #ddd !important;" 
                    onclick="document.getElementById('img-prod-6').src='{{ asset('img/ui/productos/perro-buzo-verde.webp') }}'"
                    title="Beige">
                </button>
            </div>
        </div>
    </div>
</div>

<div class="col">
    <div class="card h-100 shadow-sm border-0">
        <img src="{{ asset('img/ui/productos/perro-pechera-huesos.webp') }}" 
             id="img-prod-8" 
             class="card-img-top" 
             alt="Producto">

        <div class="card-body">
            <h5 class="card-title fw-bold">Pechera para perros</h5>
            <p class="card-text text-muted">$7000</p>
            
            <div class="d-flex gap-2 mt-3">
                <button type="button" 
                    class="rounded-circle border-0" 
                    style="width: 25px; height: 25px; background-color: #f8bbd0;" 
                    onclick="document.getElementById('img-prod-8').src='{{ asset('img/ui/productos/perro-pechera-huesos.webp') }}'"
                    title="Rosa">
                </button>

            </div>
        </div>
    </div>
</div>

<div class="col">
    <div class="card h-100 shadow-sm border-0">
        <img src="{{ asset('img/ui/productos/gato-sueter-rosa.webp') }}" 
             id="img-prod-9" 
             class="card-img-top" 
             alt="Producto">

        <div class="card-body">
            <h5 class="card-title fw-bold">Sueter para gatos</h5>
            <p class="card-text text-muted">$8000</p>
            
            <div class="d-flex gap-2 mt-3">
                <button type="button" 
                    class="rounded-circle border-0" 
                    style="width: 25px; height: 25px; background-color: #f8bbd0;" 
                    onclick="document.getElementById('img-prod-9').src='{{ asset('img/ui/productos/gato-sueter-rosa.webp') }}'"
                    title="Rosa">
                </button>

                <button type="button" 
                    class="rounded-circle border-0" 
                    style="width: 25px; height: 25px; background-color: #f5f5dc; border: 1px solid #ddd !important;" 
                    onclick="document.getElementById('img-prod-9').src='{{ asset('img/ui/productos/gato-sueter-beige.webp') }}'"
                    title="Beige">
                </button>
            </div>
        </div>
    </div>
</div>

            </div> 
        </main> 
    </div> 
</section>

@endsection

