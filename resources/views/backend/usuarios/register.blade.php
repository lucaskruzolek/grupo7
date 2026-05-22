<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Petthreads</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
</head>
<body class="theme-neutral">
<main class="container-fluid py-0 px-0">
<section class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 anim-fade-down" style="--anim-order: 1;">
                <div class="surface-card pb-2">
                    <div class="content-card w-100 py-3 px-2">
                        <!-- Logo Centrado -->
                        <div class="content-icon mb-4" style="height: 100px;">
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('img/logo/logo.webp') }}" alt="Pet Threads Logo" class="img-fluid" style="max-height: 100px; object-fit: contain;">
                            </a>
                        </div>
                        
                        <!-- Título y Mensaje de Bienvenida -->
                        <h1 class="content-title h2 mb-2" style="color: var(--brand-dark);">Crear Cuenta</h1>
                        <p class="content-text text-center text-muted mb-4" style="font-size: 0.95rem;">Unite a la comunidad de Pet Threads</p>
                        
                        <!-- Formulario de Registro -->
                        <form action="{{ url('/register') }}" method="POST" class="w-100 text-start">
                            @csrf
                            
                            <!-- Input Nombre -->
                            <div class="mb-3">
                                <label for="nombre" class="form-label fw-bold" style="color: var(--color-text-secondary); font-size: 0.9rem;">Nombre Completo</label>
                                <input type="text" class="form-control shadow-none" id="nombre" name="nombre" placeholder="Juan Pérez" required style="border-color: var(--neutral-300); border-radius: 8px; padding: 0.5rem 1rem;">
                            </div>
                            
                            <!-- Input Correo -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold" style="color: var(--color-text-secondary); font-size: 0.9rem;">Correo Electrónico</label>
                                <input type="email" class="form-control shadow-none" id="email" name="email" placeholder="correo@ejemplo.com" required style="border-color: var(--neutral-300); border-radius: 8px; padding: 0.5rem 1rem;">
                            </div>
                            
                            <!-- Input Contraseña -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold" style="color: var(--color-text-secondary); font-size: 0.9rem;">Contraseña</label>
                                <input type="password" class="form-control shadow-none" id="password" name="password" placeholder="Mínimo 8 caracteres" required style="border-color: var(--neutral-300); border-radius: 8px; padding: 0.5rem 1rem;">
                            </div>
                            
                            <!-- Input Confirmar Contraseña -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-bold" style="color: var(--color-text-secondary); font-size: 0.9rem;">Confirmar Contraseña</label>
                                <input type="password" class="form-control shadow-none" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required style="border-color: var(--neutral-300); border-radius: 8px; padding: 0.5rem 1rem;">
                            </div>
                            
                            <!-- Botón Registrarse -->
                            <button type="submit" class="btn btn-nature w-100 py-2.5 fw-bold mb-3" style="height: 48px; border-radius: 8px;">
                                Registrarse
                            </button>
                        </form>
                        
                        <!-- Texto para redirección a login -->
                        <div class="mt-3 text-center">
                            <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                                ¿Ya tenés cuenta? 
                                <a href="{{ url('/login') }}" class="fw-bold" style="color: var(--color-primary); text-decoration: none; transition: color 0.2s;">
                                    Iniciar Sesión
                                </a>
                            </p>
                        </div>

                        <!-- Volver al sitio -->
                        <div class="mt-3 text-center border-top pt-3">
                            <a href="{{ url('/') }}" class="text-muted d-inline-flex align-items-center gap-1" style="font-size: 0.85rem; text-decoration: none; transition: color 0.2s;">
                                &larr; Volver al sitio
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</main>

<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script>
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
