<!DOCTYPE html>
<html lang="es">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petthreads</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">

</head>

<body>
    @include('partes.navbar')
    
    <main class="container mt-4">
        <!-- Contenido específico de cada página -->
        @yield('contenido')
    </main>
    @include('partes.footer')
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>