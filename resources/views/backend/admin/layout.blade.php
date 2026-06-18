<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Pet Threads</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <!-- Estilos Globales & Tokens del Tema -->
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    
    <!-- Estilos de Administración Separados -->
    <link rel="stylesheet" href="{{ asset('css/backend/general.css') }}">
    <link rel="stylesheet" href="{{ asset('css/backend/sidebar.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo/favicon.png') }}">
    @yield('styles')
</head>
<body class="theme-neutral">
    <div class="admin-wrapper">
        <!-- Sidebar Izquierdo Estático -->
        @include('backend.admin.sidebar')
        
        <!-- Contenido Derecho Dinámico -->
        <main class="admin-content" id="admin-content">
            <script>
                if (localStorage.getItem('sidebar-collapsed') === 'true') {
                    document.getElementById('admin-content').classList.add('sidebar-collapsed');
                }
            </script>
            <!-- Alertas Flash del Sistema -->
            @if (session('exito'))
                <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 8px;">
                    <strong>✨ ¡Éxito!</strong> {{ session('exito') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 8px;">
                    <strong>⚠️ Error:</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 8px;">
                    <strong>⚠️ Errores de Validación:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('contenido')
        </main>
    </div>
    
    <!-- Bootstrap JS Bundle -->
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @yield('scripts')
</body>
</html>
