@extends('layout')
@section('contenido')

<body>

    <header class="container my-5 text-center">
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="display-4 fw-bold">Bienvenidos a nuestra tienda</h1>
                <p class="lead text-muted">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                </p>
            </div>
        </div>
    </header>

    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="img/carousel/imagen2.jpg" alt="First slide">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="img/carousel/imagen1.jpeg" alt="Second slide">
            </div>
          
        </div>
        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <main class="container mb-5">
        <h2 class="mb-4">Nuestros Productos</h2>
        <div class="row g-4">

            <div class="col-12 col-md-4">
                <div class="card h-100 shadow-sm product-card">
                    <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Producto">
                    <div class="card-body">
                        <h5 class="card-title">Producto Uno</h5>
                        <p class="card-text">Descripción breve y concisa del artículo que estamos ofreciendo.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 text-primary">$15.00</span>
                            <a href="#" class="btn btn-outline-dark btn-sm">Ver más</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card h-100 shadow-sm product-card">
                    <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Producto">
                    <div class="card-body">
                        <h5 class="card-title">Producto Dos</h5>
                        <p class="card-text">Otro ejemplo de producto con un texto descriptivo de prueba.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 text-primary">$22.50</span>
                            <a href="#" class="btn btn-outline-dark btn-sm">Ver más</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card h-100 shadow-sm product-card">
                    <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Producto">
                    <div class="card-body">
                        <h5 class="card-title">Producto Tres</h5>
                        <p class="card-text">Detalles adicionales que ayudan a convencer al cliente de la compra.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 text-primary">$10.00</span>
                            <a href="#" class="btn btn-outline-dark btn-sm">Ver más</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>



    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
@endsection