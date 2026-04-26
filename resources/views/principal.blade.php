@extends('layout')
@section('contenido')

<div id="myCarousel" class="carousel slide mb-6" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="0" class="active" aria-current="true"
            aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="{{ asset('img/ui/principal/carousel-1.webp') }}" alt="Imagen 1">
            <div class="container">
                <div class="carousel-caption">                    
                    <p><a class="btn btn-lg btn-primary" href="{{ url('/productos') }}">Ver Colección</a></p>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <img src="{{ asset('img/ui/principal/carousel-2.webp') }}" alt="Imagen 2">
            <div class="container">
                <div class="carousel-caption">                    
                    <p><a class="btn btn-lg btn-primary" href="{{ url('/quienes-somos') }}">Ver más</a></p>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <img src="{{ asset('img/ui/principal/carousel-3.webp') }}" alt="Imagen 3">
            <div class="container">
                <div class="carousel-caption">                    
                    <p><a class="btn btn-lg btn-primary" href="{{ url('/comercializacion') }}">Más info</a></p>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
    </button>
</div>

<header class="container my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="display-4 fw-bold">Nuestra Colección</h1>
            <p class="lead text-muted">
                Explora nuestra amplia variedad de productos seleccionados cuidadosamente para el bienestar de tus
                mascotas.
            </p>
        </div>
    </div>
</header>

<main class="container mb-5">
    <h2 class="mb-4">Nuestros Productos Destacados</h2>
    <div class="row g-4">

        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm product-card surface-card">
                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Producto">
                <div class="card-body">
                    <h5 class="card-title">Producto Uno</h5>
                    <p class="card-text">Descripción breve y concisa del artículo que estamos ofreciendo.</p>
                    <div class="d-flex justify-content-between align-items-center w-100 mt-auto">
                        <span class="h5 mb-0 text-primary">$15.00</span>
                        <a href="#" class="btn btn-outline-dark btn-sm">Ver más</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm product-card surface-card">
                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Producto">
                <div class="card-body">
                    <h5 class="card-title">Producto Dos</h5>
                    <p class="card-text">Otro ejemplo de producto con un texto descriptivo de prueba.</p>
                    <div class="d-flex justify-content-between align-items-center w-100 mt-auto">
                        <span class="h5 mb-0 text-primary">$22.50</span>
                        <a href="#" class="btn btn-outline-dark btn-sm">Ver más</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card h-100 shadow-sm product-card surface-card">
                <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Producto">
                <div class="card-body">
                    <h5 class="card-title">Producto Tres</h5>
                    <p class="card-text">Detalles adicionales que ayudan a convencer al cliente de la compra.</p>
                    <div class="d-flex justify-content-between align-items-center w-100 mt-auto">
                        <span class="h5 mb-0 text-primary">$10.00</span>
                        <a href="#" class="btn btn-outline-dark btn-sm">Ver más</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

@endsection