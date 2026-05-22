<nav id="navbar" class="navbar navbar-expand-lg navbar-light bg-white p-0 sticky-top" style="transition: top 0.3s ease-in-out;">
    <!-- VISTA MOBILE (d-lg-none) -->
    <div class="container-fluid px-4 py-2 d-lg-none">
        <!-- Logo a la izquierda -->
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('img/logo/logo-text.webp') }}" alt="Petthreads Logo" style="max-height: 45px;">
        </a>
        
        <!-- Botón hamburguesa -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavMobile" aria-controls="navbarNavMobile" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Colapsable Mobile -->
        <div class="collapse navbar-collapse" id="navbarNavMobile">
            <ul class="navbar-nav ms-auto mt-2">
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
                    <a class="nav-link" href="{{ url('/consultas') }}">Consultas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/terminos') }}">Términos de Uso</a>
                </li>
                <hr class="my-2 text-muted">
                <!-- Secciones extra en mobile: Mi cuenta y Carrito -->
                @if(auth()->check())
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="{{ url('/mi-cuenta') }}">
                            <img src="{{ asset('img/icons/user.svg') }}" alt="User" style="width: 20px; height: 20px;">
                            Mi cuenta
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="{{ url('/login') }}">
                            <img src="{{ asset('img/icons/user.svg') }}" alt="User" style="width: 20px; height: 20px;">
                            Ingresar
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2" href="{{ url('/carrito') }}">
                        <img src="{{ asset('img/icons/cart.svg') }}" alt="Cart" style="width: 20px; height: 20px;">
                        Carrito
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- VISTA DESKTOP (d-none d-lg-block) -->
    <div class="w-100 d-none d-lg-block">
        <!-- Fila 1: Logo (2/12), Searchbar (8/12), Dinámica (1/12), Carrito (1/12) -->
        <div class="row align-items-center w-100 mx-0 py-3 px-4 navbar-top-row">
            <!-- Logo (2/12) -->
            <div class="col-lg-2 d-flex align-items-center justify-content-center">
                <a class="navbar-brand m-0" href="{{ url('/') }}">
                    <img src="{{ asset('img/logo/logo-text.webp') }}" alt="Petthreads Logo" style="max-height: 50px;">
                </a>
            </div>

            <!-- Searchbar (6/12) -->
            <div class="col-lg-8 d-flex align-items-center justify-content-center">
                <div class="navbar-search-container w-100 px-lg-4">
                    <div class="input-group search-bar-wrapper">
                        <input type="text" class="form-control search-input" placeholder="Buscar productos..." aria-label="Buscar productos">
                        <button class="btn search-btn" type="button">
                            <img src="{{ asset('img/icons/search.svg') }}" alt="Buscar" style="width: 18px; height: 18px;">
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sección Dinámica (1/12) -->
            <div class="col-lg-1 d-flex align-items-center justify-content-center">
                @if(auth()->check())
                    <a href="{{ url('/mi-cuenta') }}" class="nav-desktop-btn icon-only-btn" title="Mi cuenta">
                        <img src="{{ asset('img/icons/user.svg') }}" alt="Mi cuenta" class="nav-icon">
                    </a>
                @else
                    <a href="{{ url('/login') }}" class="nav-desktop-btn text-btn" title="Ingresar">
                        Ingresar
                    </a>
                @endif
            </div>

            <!-- Carrito (1/12) -->
            <div class="col-lg-1 d-flex align-items-center justify-content-center">
                <a href="{{ url('/carrito') }}" class="nav-desktop-btn icon-only-btn" title="Carrito">
                    <img src="{{ asset('img/icons/cart.svg') }}" alt="Carrito" class="nav-icon">
                </a>
            </div>
        </div>

        <!-- Fila 2: Botones de navegación. Background green-500, hover green-600, sin margenes, letra blanca, poppins, centrado, menos altura -->
        <div class="navbar-bottom-row w-100 py-1">
            <div class="container-fluid d-flex justify-content-center align-items-center">
                <ul class="nav nav-links-desktop">
                    <li class="nav-item-desktop">
                        <a href="{{ url('/') }}" class="nav-link-desktop-btn">Inicio</a>
                    </li>
                    <li class="nav-item-desktop">
                        <a href="{{ url('/productos') }}" class="nav-link-desktop-btn">Productos</a>
                    </li>
                    <li class="nav-item-desktop">
                        <a href="{{ url('/comercializacion') }}" class="nav-link-desktop-btn">Comercialización</a>
                    </li>
                    <li class="nav-item-desktop">
                        <a href="{{ url('/quienes-somos') }}" class="nav-link-desktop-btn">Quiénes Somos</a>
                    </li>
                    <li class="nav-item-desktop">
                        <a href="{{ url('/contacto') }}" class="nav-link-desktop-btn">Contacto</a>
                    </li>
                    <li class="nav-item-desktop">
                        <a href="{{ url('/consultas') }}" class="nav-link-desktop-btn">Consultas</a>
                    </li>
                    <li class="nav-item-desktop">
                        <a href="{{ url('/terminos') }}" class="nav-link-desktop-btn">Términos de Uso</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>