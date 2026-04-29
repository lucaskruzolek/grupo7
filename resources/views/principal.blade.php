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
        <!-- Slide 1 -->
        <div class="carousel-item active">
            <img src="{{ asset('img/ui/principal/carousel-1.webp') }}" alt="Imagen 1">
            <div class="container">
                <div class="carousel-caption caption-half-left">
                    <div class="carousel-offset" style="--x: 0px; --y: -50px;">
                        <h2 class="banner-title mb-2 anim-fade-down" style="--anim-order: 1;">Nueva colección de invierno</h2>
                        <p class="banner-subtitle anim-fade-down" style="--anim-order: 2;">Diseños exclusivos en lana y algodón.</p>
                    </div>
                </div>
            </div>
            <div class="carousel-cta cta-half-left" style="--cta-x: 0px; --cta-y: -60px;">
                <a class="btn btn-lg btn-primary anim-fade-down" style="--anim-order: 3;" href="{{ url('/productos') }}">Ver Colección</a>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="carousel-item">
            <img src="{{ asset('img/ui/principal/carousel-2.webp') }}" alt="Imagen 2">
            <div class="container">
                <div class="carousel-caption caption-half-right">
                    <div class="carousel-offset" style="--x: 0px; --y: -50px;">
                        <h2 class="banner-title mb-2 anim-fade-down" style="--anim-order: 1;">Paseos sin tirones</h2>
                        <p class="banner-subtitle anim-fade-down" style="--anim-order: 2;">Diseño ergonómico que protege su columna y te da el control total.</p>
                    </div>
                </div>
            </div>
            <div class="carousel-cta cta-half-right" style="--cta-x: 0px; --cta-y: -60px;">
                <a class="btn btn-lg btn-primary anim-fade-down" style="--anim-order: 3;" href="{{ url('/productos') }}">Ver más</a>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="carousel-item">
            <img src="{{ asset('img/ui/principal/carousel-3.webp') }}" alt="Imagen 3">
            <div class="container">
                <div class="carousel-caption caption-half-left">
                    <div class="carousel-offset" style="--x: 0px; --y: -60px;">
                        <h2 class="banner-title mb-2 anim-fade-down" style="--anim-order: 1;">Envíos directos, sin complicaciones</h2>
                        <p class="banner-subtitle anim-fade-down" style="--anim-order: 2;">Cambios de talle gratis y envíos con descuento a todo el país.</p>
                    </div>
                </div>
            </div>
            <div class="carousel-cta cta-half-left" style="--cta-x: 0px; --cta-y: -60px;">
                <a class="btn btn-lg btn-primary anim-fade-down" style="--anim-order: 3;" href="{{ url('/comercializacion') }}">Más info</a>
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


<main class="mb-5">
    <section class="container">
        <div class="mt-5 border-top ">
            <div class="col mt-5 mb-3 d-flex justify-content-center gap-2 anim-fade-down" style="--anim-order: 1;">
                <span class="paw-icon" style="font-size: 2.8rem; transform: rotate(0deg); opacity: 1;"></span>
                <h1 class="mb-4 text-center">Categorías</h1>
                <span class="paw-icon" style="font-size: 2.8rem; transform: rotate(0deg); opacity: 1;"></span>
            </div>
        </div>
       
        <div class="row row-cols-2 row-cols-md-4 g-4 justify-content-center anim-fade-down">

            <div class="col anim-fade-down" style="--anim-order: 2;">
                <div class="category-reveal-card" style="--card-hover-color: var(--brand-dark);">
                    <img src="{{ asset('img/ui/principal/para-perros.webp') }}" alt="Ropa Para Perros">
                    <div class="info">
                    <h3 class="content-title h5 poppins-semibold">Ropa Para Perros</h3>
                    <p>Diseños exclusivos y comodidad total para cada paseo de tu mejor amigo.</p>
                    <a href="{{ url('/productos') }}" class="learn-more">
                        <span class="circle" aria-hidden="true">
                            <span class="icon arrow"></span>
                        </span>
                        <span class="button-text">Ver todo</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="col anim-fade-down" style="--anim-order: 3;">
            <div class="category-reveal-card" style="--card-hover-color: #ffe600e0;">
                <img src="{{ asset('img/ui/principal/para-gatos.webp') }}" alt="Ropa Para Gatos">
                <div class="info">
                    <h3 class="content-title h5 poppins-semibold">Ropa Para Gatos</h3>
                    <p>Prendas suaves y con estilo pensadas especialmente para la libertad felina.</p>
                    <a href="{{ url('/productos') }}" class="learn-more">
                        <span class="circle" aria-hidden="true">
                            <span class="icon arrow"></span>
                        </span>
                        <span class="button-text">Ver todo</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="col anim-fade-down" style="--anim-order: 4;">
            <div class="category-reveal-card" >
                <img src="{{ asset('img/ui/principal/accesorios.webp') }}" alt="Accesorios">
                <div class="info">
                    <h3 class="content-title h5 poppins-semibold">Accesorios</h3>
                    <p>Correas, bandanas y complementos únicos que marcan la diferencia.</p>
                    <a href="{{ url('/productos') }}" class="learn-more">
                        <span class="circle" aria-hidden="true">
                            <span class="icon arrow"></span>
                        </span>
                        <span class="button-text">Ver todo</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="col anim-fade-down" style="--anim-order: 5;">
            <div class="category-reveal-card" style="--card-hover-color: #f50e0eff;">
                <img src="{{ asset('img/ui/principal/nuevos.webp') }}" alt="Nuevos Arribos">
                <div class="info">
                    <h3 class="content-title h5 poppins-semibold">Nuevos Arribos</h3>
                    <p>Descubre lo último de nuestra colección y mantén a tu mascota a la moda.</p>
                    <a href="{{ url('/productos') }}" class="learn-more">
                        <span class="circle" aria-hidden="true">
                            <span class="icon arrow"></span>
                        </span>
                        <span class="button-text">Ver todo</span>
                    </a>
                </div>
            </div>
        </div>

        

    </div>
    </section>
     <!-- Fin container Categorías -->



<section class="container-fluid px-0 mt-5 overflow-hidden banner-curved-row theme-green surface-flat">
    <div class="row g-0 align-items-stretch">
        <!-- Bloque de Texto -->
        <div class="col-md-6">
            <div class="banner-curved-content h-100 d-flex flex-column justify-content-center">
                <!-- Tagline -->
                <p class="text-uppercase small fw-bold mb-3 anim-fade-down" style="letter-spacing: 1.5px; color: var(--color-text-muted); --anim-order: 1;">
                    Confort para cada aventura <span class="text-coral">♥</span>
                </p>
                
                <!-- Título -->
                <h2 class="display-4 mb-3 playfair-display-bold anim-fade-down" style="--anim-order: 2;">
                    Comodidad que se ve bien
                </h2>
                
                <!-- Subtítulo -->
                <p class="mb-4 pe-md-5 anim-fade-down" style="--anim-order: 3;">
                    Ropa ligera, suave y funcional para que jueguen, descansen y disfruten con estilo.
                </p>
                
                <!-- CTA -->
                <div class="mb-5 anim-fade-down" style="--anim-order: 4;">
                    <a href="{{ url('/productos') }}" class="btn btn-lg px-4 py-2 btn-nature">
                        DESCUBRIR COLECCIÓN
                    </a>
                </div>
                
                <!-- Features Row -->
                <div class="d-flex flex-wrap gap-4 mt-2 anim-fade-down" style="--anim-order: 5;">
                    <div class="feature-item">
                        <div class="icon-mask feature-icon-sm" style="mask-image: url('{{ asset('img/icons/sparkles.svg') }}'); -webkit-mask-image: url('{{ asset('img/icons/sparkles.svg') }}');"></div>
                        <span class="feature-text">Suave al tacto</span>
                    </div>
                    <div class="feature-item">
                        <div class="icon-mask feature-icon-sm" style="mask-image: url('{{ asset('img/icons/wind.svg') }}'); -webkit-mask-image: url('{{ asset('img/icons/wind.svg') }}');"></div>
                        <span class="feature-text">Fresca</span>
                    </div>
                    <div class="feature-item">
                        <div class="icon-mask feature-icon-sm" style="mask-image: url('{{ asset('img/icons/laundry.svg') }}'); -webkit-mask-image: url('{{ asset('img/icons/laundry.svg') }}');"></div>
                        <span class="feature-text">Fácil de lavar</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bloque de Imagen -->
        <div class="col-md-6 banner-curved-image-wrapper anim-fade-down">
            <img src="{{ asset('img/ui/principal/banner.webp') }}" 
                 alt="Mascota con indumentaria Pet Threads" 
                 class="img-fluid w-100 h-100 object-fit-cover" 
                 style="min-height: 300px;">
        </div>
    </div>
</section>

<!-- Sección Productos Favoritos -->
<section class="container my-5 border-top border-neutral-200 anim-fade-down">
    
    <div class="d-flex align-items-center justify-content-center mt-5 mb-4 anim-fade-down" style="--anim-order: 1;">
        <!-- Componente de Título (Mantiene ubicación central) -->
        <div class="d-flex justify-content-center align-items-center gap-2">
            <span class="paw-icon" style="font-size: 2.8rem; transform: rotate(0deg); opacity: 1;"></span>
            <h1 class="mb-0 text-center">Productos Favoritos</h1>
            <span class="paw-icon" style="font-size: 2.8rem; transform: rotate(0deg); opacity: 1;"></span>
        </div>
    </div>

    <div class="position-relative px-md-5 anim-fade-down" style="--anim-order: 2;">
        <!-- Flecha Izquierda -->
        <button class="btn rounded-circle position-absolute start-0 top-50 translate-middle-y d-none d-md-flex btn-carousel-nav prev" 
                type="button" onclick="scrollNativeCarousel(-1)">
            <div class="icon-mask" style="width: 18px; height: 18px; mask-image: url('{{ asset('img/icons/chevron-left.svg') }}'); -webkit-mask-image: url('{{ asset('img/icons/chevron-left.svg') }}');"></div>
        </button>

        <div id="nativeFavoritesCarousel" class="native-carousel-wrapper">
        
        <!-- Tarjeta 1 -->
        <div class="native-carousel-item">
            <div class="category-reveal-card product-reveal-card" style="transform: none;">
                <img src="{{ asset('img/ui/productos/juguete-pulpo.webp') }}" alt="Capa Impermeable">
                <div class="info">
                    <a href="{{ url('/productos') }}" class="btn-premium"><span>Ver Producto</span></a>
                </div>
            </div>
            <div class="product-card-footer">
                <div class="product-title">Juguete Pulpo</div>
                <div class="product-subtitle">Perfecto para morder y lanzar</div>
            </div>
        </div>

        <!-- Tarjeta 2 -->
        <div class="native-carousel-item">
            <div class="category-reveal-card product-reveal-card" style="transform: none;">
                <img src="{{ asset('img/ui/productos/perro-pechera-huesos.webp') }}" alt="Cama Beige">
                <div class="info">
                    <a href="{{ url('/productos') }}" class="btn-premium"><span>Ver Producto</span></a>
                </div>
            </div>
            <div class="product-card-footer">
                <div class="product-title">Pechera Huesos</div>
                <div class="product-subtitle">Cómoda y divertida para el paseo</div>
            </div>
        </div>

        <!-- Tarjeta 3 -->
        <div class="native-carousel-item">
            <div class="category-reveal-card product-reveal-card" style="transform: none;">
                <img src="{{ asset('img/ui/productos/gato-sueter-beige.webp') }}" alt="Accesorios">
                <div class="info">
                    <a href="{{ url('/productos') }}" class="btn-premium"><span>Ver Producto</span></a>
                </div>
            </div>
            <div class="product-card-footer">
                <div class="product-title">Hoodie para Gatos</div>
                <div class="product-subtitle">Suave y abrigado para días fríos</div>
            </div>
        </div>

        <!-- Tarjeta 4 -->
        <div class="native-carousel-item">
            <div class="category-reveal-card product-reveal-card" style="transform: none;">
                <img src="{{ asset('img/ui/productos/correa-urban.webp') }}" alt="Para Gatos">
                <div class="info">
                    <a href="{{ url('/productos') }}" class="btn-premium"><span>Ver Producto</span></a>
                </div>
            </div>
            <div class="product-card-footer">
                <div class="product-title">Correa Urbana</div>
                <div class="product-subtitle">Resistente y con estilo</div>
            </div>
        </div>

        <!-- Tarjeta 5 -->
        <div class="native-carousel-item">
            <div class="category-reveal-card product-reveal-card" style="transform: none;">
                <img src="{{ asset('img/ui/productos/gato-buzo-ratones.webp') }}" alt="Nuevos Arribos">
                <div class="info">
                    <a href="{{ url('/productos') }}" class="btn-premium"><span>Ver Producto</span></a>
                </div>
            </div>
            <div class="product-card-footer">
                <div class="product-title">Buzo Raton</div>
                <div class="product-subtitle">Perfecto para días frescos</div>
            </div>
        </div>

        <!-- Tarjeta 6 -->
        <div class="native-carousel-item">
            <div class="category-reveal-card product-reveal-card" style="transform: none;">
                <img src="{{ asset('img/ui/productos/juguete-acordeon.webp') }}" alt="Nuevos Arribos">
                <div class="info">
                    <a href="{{ url('/productos') }}" class="btn-premium"><span>Ver Producto</span></a>
                </div>
            </div>
            <div class="product-card-footer">
                <div class="product-title">Juguete Acordeon</div>
                <div class="product-subtitle">Estira y diviértete sin parar</div>
            </div>
        </div>

        <!-- Tarjeta 7 -->
        <div class="native-carousel-item">
            <div class="category-reveal-card product-reveal-card" style="transform: none;">
                <img src="{{ asset('img/ui/productos/perro-buzo-verde.webp') }}" alt="Nuevos Arribos">
                <div class="info">
                    <a href="{{ url('/productos') }}" class="btn-premium"><span>Ver Producto</span></a>
                </div>
            </div>
            <div class="product-card-footer">
                <div class="product-title">Buzo Verde</div>
                <div class="product-subtitle">Cómodo y abrigado</div>
            </div>
        </div>

    </div>
        <!-- Flecha Derecha -->
        <button class="btn rounded-circle position-absolute end-0 top-50 translate-middle-y d-none d-md-flex btn-carousel-nav next" 
                type="button" onclick="scrollNativeCarousel(1)">
            <div class="icon-mask" style="width: 18px; height: 18px; mask-image: url('{{ asset('img/icons/chevron-right.svg') }}'); -webkit-mask-image: url('{{ asset('img/icons/chevron-right.svg') }}');"></div>
        </button>
    </div>
</section>


<section class="container mt-5 border-top border-neutral-200 anim-fade-down">
    
    <div class="col mt-5 mb-0 d-flex justify-content-center gap-2">
        <span class="paw-icon" style="font-size: 2.8rem; transform: rotate(0deg); opacity: 1;"></span>
        <h1 class="mb-4 text-center">Lo que dicen los humanos</h1>
        <span class="paw-icon" style="font-size: 2.8rem; transform: rotate(0deg); opacity: 1;"></span>
    </div>
    <div class="d-flex justify-content-center gap-1 mb-4">
        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4 justify-content-center">
                    
        <!-- Opción 1 -->
        <div class="col anim-fade-down" style="--anim-order: 1;">
            <div class="surface-card hover-lift">
                <!-- Comillas ancladas -->
                <div class="quote-anchor top-left" style="mask-image: url('{{ asset('img/icons/quote.svg') }}'); -webkit-mask-image: url('{{ asset('img/icons/quote.svg') }}');"></div>
                <div class="quote-anchor bottom-right" style="mask-image: url('{{ asset('img/icons/quote.svg') }}'); -webkit-mask-image: url('{{ asset('img/icons/quote.svg') }}');"></div>

                <div class="content-card">
                    <img src="{{ asset('img/ui/principal/user1.png') }}" alt="Usuario" width="180" height="180" class="rounded-circle mb-4">
                    
                    <div class="d-flex justify-content-center gap-1 mb-3 align-items-center">
                        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
                        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
                        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
                        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
                        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
                    </div>
                    
                    <p class="poppins-semibold text-center mb-4">La calidad del algodón es increíble, a Bruno le encanta usar su hoddie en los días de frío.</p>
                    <p class="poppins-bold small mb-0">-Sofía y Bruno</p>
                </div>
            </div>
        </div>

                    
                    <!-- Opción 1 -->
        <div class="col anim-fade-down" style="--anim-order: 2;">
            <div class="surface-card hover-lift">
                <!-- Comillas ancladas -->
                <div class="quote-anchor top-left" style="mask-image: url('{{ asset('img/icons/quote.svg') }}'); -webkit-mask-image: url('{{ asset('img/icons/quote.svg') }}');"></div>
                <div class="quote-anchor bottom-right" style="mask-image: url('{{ asset('img/icons/quote.svg') }}'); -webkit-mask-image: url('{{ asset('img/icons/quote.svg') }}');"></div>

                <div class="content-card">
                    <img src="{{ asset('img/ui/principal/user3.png') }}" alt="Usuario" width="180" height="180" class="rounded-circle mb-4">
                    
                    <div class="d-flex justify-content-center gap-1 mb-3 align-items-center">
                        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
                        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
                        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
                        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
                        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
                    </div>
                    
                    <p class="poppins-semibold text-center mb-4">El suéter le quedó perfecto y es super suave. Se nota la calidad y que está hecho con mucha dedicación.</p>
                    <p class="poppins-bold small mb-0">-Mariana y Chispas</p>
                </div>
            </div>
        </div>
                    
                    <!-- Opción 1 -->
        <div class="col anim-fade-down" style="--anim-order: 3;">
            <div class="surface-card hover-lift">
                <!-- Comillas ancladas -->
                <div class="quote-anchor top-left" style="mask-image: url('{{ asset('img/icons/quote.svg') }}'); -webkit-mask-image: url('{{ asset('img/icons/quote.svg') }}');"></div>
                <div class="quote-anchor bottom-right" style="mask-image: url('{{ asset('img/icons/quote.svg') }}'); -webkit-mask-image: url('{{ asset('img/icons/quote.svg') }}');"></div>

                <div class="content-card">
                    <img src="{{ asset('img/ui/principal/user2.png') }}" alt="Usuario" width="180" height="180" class="rounded-circle mb-4">
                    
                    <div class="d-flex justify-content-center gap-1 mb-3 align-items-center">
                        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
                        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
                        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
                        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
                        <img src="{{ asset('img/icons/star3.svg') }}" alt="Estrella" width="25" style="opacity: 0.8;">
                    </div>
                    
                    <p class="poppins-semibold text-center mb-4">Compramos el arnés y cambió nuestras caminatas por completo. Seguro, cómodo y con un diseño hermoso.</p>
                    <p class="poppins-bold small mb-0">-Mateo y Luna</p>
                </div>
            </div>
        </div>
                    
                </div>
</section>


</main>

<script>
    function scrollNativeCarousel(direction) {
        const carousel = document.getElementById('nativeFavoritesCarousel');
        const scrollAmount = 300; // Aproximadamente el ancho de una tarjeta + gap
        carousel.scrollBy({ left: scrollAmount * direction, behavior: 'smooth' });
    }

    // Centrar un ítem específico al cargar (Opción 1)
    document.addEventListener("DOMContentLoaded", function() {
        const carousel = document.getElementById('nativeFavoritesCarousel');
        // Elegimos el tercer ítem (índice 2) como punto de partida centrado
        const targetItem = carousel.children[2]; 
        
        if (targetItem) {
            // Un pequeño delay asegura que el layout esté listo para el scroll
            setTimeout(() => {
                const scrollPos = targetItem.offsetLeft - (carousel.offsetWidth / 2) + (targetItem.offsetWidth / 2);
            
            carousel.scrollTo({
                left: scrollPos,
                behavior: 'smooth'
                });
            }, 100);
        }
    });
</script>
@endsection