<!DOCTYPE html>
<html lang="es">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Threads</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/nouislider/nouislider.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/principal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filter-sidebar.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo/favicon.png') }}">
    @yield('styles')
</head>

<body>
    @include('frontend.partes.navbar')
    
    <main class="container-fluid py-0 px-0">
        <!-- Contenido específico de cada página -->
        @yield('contenido')
    </main>
    @include('frontend.partes.footer')
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/nouislider/nouislider.min.js') }}"></script>
    <script>
        var prevScrollpos = window.pageYOffset;
        window.onscroll = function() {
            var currentScrollPos = window.pageYOffset;
            if (prevScrollpos > currentScrollPos) {
                document.getElementById("navbar").style.top = "0";
            } else {
                // -100px asegura que el navbar se oculte por completo (ya que mide más de 50px con el padding)
                document.getElementById("navbar").style.top = "-132px";
            }
            prevScrollpos = currentScrollPos;
        }

        // --- Intersection Observer para animaciones de entrada ---
        document.addEventListener('DOMContentLoaded', () => {
            const observerOptions = {
                threshold: 0.15 // Se activa cuando el 15% del elemento es visible
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('anim-visible');
                        observer.unobserve(entry.target); // Solo animar una vez
                    }
                });
            }, observerOptions);

            // Observar todos los elementos con la clase anim-fade-down
            document.querySelectorAll('.anim-fade-down').forEach(el => observer.observe(el));
        });
    </script>
</body>

</html>