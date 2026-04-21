<!DOCTYPE html>
<html lang="es">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petthreads</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">PetThreads</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/productos') }}">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/comercializacion') }}">Comercialización</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/quienes-somos') }}">Quiénes Somos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/contacto') }}">Contacto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/terminos') }}">Términos de Uso</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <header class="container my-5 text-center">
        <img src="{{ asset('imagenes/banner-petthreads.jpeg') }}" alt="Banner" class="hero-image mb-4 shadow img-fluid">
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="display-4 fw-bold">Bienvenidos a nuestra tienda</h1>
                <p class="lead text-muted">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                </p>
            </div>
        </div>
    </header>

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

    <footer class="bg-light py-4 border-top text-center">
        <p class="text-muted mb-0">&copy; 2026</p>
    </footer>

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>