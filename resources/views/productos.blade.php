@extends('layout') {{-- Solo un extend al inicio --}}

@section('contenido')

{{-- 1. Banner Principal (Ancho completo arriba) --}}
<section class="theme-neutral surface-card overflow-hidden position-relative border-bottom">
    <div class="row g-0 align-items-center">
        <div class="col-md-5 ps-4 pe-2 banner-content">
            <h1 class="banner-title mb-4 position-relative d-inline-block">
                Productos<span class="paw-icon"></span>
            </h1>
            <p class="text-secondary fs-5">
                Descubrí nuestra colección de ropa y accesorios diseñados para que tu mascota se vea increíble y se sienta aún mejor.
            </p>
        </div>

        <div class="col-md-7">
            <img src="{{ asset('img/ui/productos/portada.png') }}" 
                 alt="Portada Productos" 
                 class="img-fluid w-100 img-fade-left" 
                 style="display: block;">
        </div>
    </div>
</section>

{{-- 2. Cuerpo de la página: Sidebar + Productos --}}
<section class="container-fluid">
    <div class="row">
        
        <aside class="col-md-3 bg-light border-end min-vh-100 p-3">
            @include('partes.sidebar')
        </aside>

        <main class="col-md-9 p-4">
            <div class="row row-cols-1 row-cols-md-3 g-4">
                
                {{-- Tarjeta de ejemplo --}}
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="..." class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Nombre del Producto</h5>
                            <p class="card-text text-muted">Breve descripción del producto...</p>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="..." class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Nombre del Producto</h5>
                            <p class="card-text text-muted">Breve descripción del producto...</p>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="..." class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Nombre del Producto</h5>
                            <p class="card-text text-muted">Breve descripción del producto...</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="..." class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Nombre del Producto</h5>
                            <p class="card-text text-muted">Breve descripción del producto...</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="..." class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Nombre del Producto</h5>
                            <p class="card-text text-muted">Breve descripción del producto...</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="..." class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Nombre del Producto</h5>
                            <p class="card-text text-muted">Breve descripción del producto...</p>
                        </div>
                    </div>
                </div>

            </div> 
        </main> 
    </div> 
</section>

@endsection

