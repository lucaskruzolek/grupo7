@extends('layout')

@section('contenido')
    <!-- Banner Principal de Comercialización -->
    <section class="theme-neutral surface-card overflow-hidden position-relative">
        <div class="row g-0 align-items-center">
            <!-- Bloque de Texto (40%) -->
            <div class="col-md-5 ps-4 pe-2">
                <h1 class="banner-title mb-4 position-relative d-inline-block">Comercialización<span class="paw-icon"></span>
                </h1>
                
                <p class="text-secondary fs-5">
                    Todo lo que necesitas saber para recibir tus productos de forma fácil, segura y rápida.
                </p>
            </div>
 
            <!-- Bloque de Imagen (60%) -->
            <div class="col-md-7">
                <img src="{{ asset('img/ui/comercializacion/portad.webp') }}" 
                     alt="Portada Comercialización" 
                     class="img-fluid w-100 img-fade-left" 
                     style="display: block;">
            </div>
        </div>
    </section>

    <!-- Puedes añadir más contenido aquí debajo -->
@endsection
