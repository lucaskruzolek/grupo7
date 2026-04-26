<nav id="navbar" class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-2 sticky-top" style="transition: top 0.3s ease-in-out;">
    <div class="container-fluid px-4">
        <!-- Logo a la izquierda -->
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('img/logo/logo-text.webp') }}" alt="Petthreads Logo" style="max-height: 50px;">
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menú a la derecha -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
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