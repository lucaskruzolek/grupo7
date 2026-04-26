@extends('layout')

@section('contenido')
    <!-- Banner Principal de Quiénes Somos -->
    <section class="theme-neutral surface-card overflow-hidden p-0 ps-md-5">
        <div class="row g-0 align-items-center w-100 m-0">
            <!-- Bloque de Texto (40%) -->
            <div class="col-md-6 ps-4 pe-2 banner-content">
                
                <p class="section-tag mb-2 pt-2">
                    Quiénes somos <span class="text-coral-light">♥</span>
                </p>
                <h1 class="banner-title mb-4 position-relative" >
                    Hecho con amor para quienes los aman como <span class="text-coral-light">familia</span>
                    <span class="paw-icon"></span>
                </h1>
                
                <p class="banner-subtitle">
                    Pet Threads nace del amor por los animales y del deseo de ofrecerles lo mejor. Creamos ropa y accesorios que combinan estilo, comodidad y calidad para que cada mascota se sienta única y feliz.
                </p>
            </div>
 
            <!-- Bloque de Imagen (60%) -->
            <div class="col-md-6 align-self-stretch">
                <img src="{{ asset('img/ui/quienes-somos/portada.webp') }}" 
                     alt="Portada Quiénes Somos" 
                     class="img-fluid w-100 img-fade-left" 
                     style="display: block;">
            </div>
        </div>
    </section>

    <!-- 1. NUESTRA HISTORIA -->
    <section class="container mt-5">
        <div class="text-start p-4 pt-md-3 px-md-0">
            <div class="row align-items-center g-4">
                <div class="col-md-6">
                    <img src="{{ asset('img/ui/quienes-somos/historia.webp') }}" 
                         alt="Nuestra Historia" 
                         class="img-fluid rounded-2 w-100 object-fit-cover"
                         style="max-height: 450px;">
                </div>
                <div class="col-md-6">
                    <p class="section-tag mb-3 fs-5">NUESTRA HISTORIA <span class="text-coral-light fs-4">♥</span></p>
                    <h2 class="h3 playfair-display-medium text-main mb-4" style="line-height: 1.2; letter-spacing: 1px;">Todo comenzó con un vínculo incondicional</h2>
                    <p class="feature-text mb-3" style="line-height: 1.7;">
                        Pet Threads comenzó como un pequeño proyecto impulsado por amantes de los animales que buscaban productos más bonitos, cómodos y duraderos para sus propios compañeros.
                    </p>
                    <p class="feature-text mb-3" style="line-height: 1.7;">
                        Lo que inició en casa, entre bocetos, telas y muchas pruebas con nuestras propias mascotas, hoy es una marca dedicada a miles de amigos peludos que merecen lo mejor.
                    </p>
                    <p class="feature-text mb-0" style="line-height: 1.7;">
                        Cada día, trabajamos con el mismo objetivo: diseñar prendas para momentos especiales, paseos, aventuras y siestas.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. POR QUÉ ELEGIR PET THREADS -->
    <section class="container mt-5">
        <div class="theme-green surface-card rounded-4 p-3 p-md-3">
            <h6 class="h6 poppins-bold text-main mb-4 text-center text-uppercase">¿Por qué elegir Pet Threads?</h4>
            
            <div class="row row-cols-2 row-cols-md-5 g-4 text-center justify-content-center">
                <!-- Card 1 -->
                <div class="col">
                    <div class="feature-card  p-2 h-100" style="--feature-scale: 1.1;">
                        <div class="icon-container">
                            <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/star.svg') }}'); mask-image: url('{{ asset('img/icons/star.svg') }}');"></div>
                        </div>
                        <h3 class="feature-title text-main poppins-semibold">Diseños Exclusivos</h3>
                        <p class="feature-text text-center">Prendas únicas pensadas para destacar.</p>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="col">
                    <div class="feature-card  p-2 h-100" style="--feature-scale: 1.1;">
                        <div class="icon-container">
                            <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/medal.svg') }}'); mask-image: url('{{ asset('img/icons/medal.svg') }}');"></div>
                        </div>
                        <h3 class="feature-title text-main poppins-semibold">Calidad Premium</h3>
                        <p class="feature-text text-center">Materiales duraderos y amigables.</p>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="col">
                    <div class="feature-card  p-2 h-100" style="--feature-scale: 1.1;">
                        <div class="icon-container">
                            <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/heart.svg') }}'); mask-image: url('{{ asset('img/icons/heart.svg') }}');"></div>
                        </div>
                        <h3 class="feature-title text-main poppins-semibold">Máxima Comodidad</h3>
                        <p class="feature-text text-center">Ajuste perfecto para total libertad.</p>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="col">
                    <div class="feature-card  p-2 h-100" style="--feature-scale: 1.1;">
                        <div class="icon-container">
                            <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/delivery-truck.svg') }}'); mask-image: url('{{ asset('img/icons/delivery-truck.svg') }}');"></div>
                        </div>
                        <h3 class="feature-title text-main poppins-semibold">Envíos a todo el país</h3>
                        <p class="feature-text text-center">Llegamos a tu hogar de forma segura.</p>
                    </div>
                </div>
                <!-- Card 5 -->
                <div class="col">
                    <div class="feature-card  p-2 h-100" style="--feature-scale: 1.1;">
                        <div class="icon-container">
                            <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/support.svg') }}'); mask-image: url('{{ asset('img/icons/support.svg') }}');"></div>
                        </div>
                        <h3 class="feature-title text-main poppins-semibold">Atención Dedicada</h3>
                        <p class="feature-text text-center">Estamos para ayudarte siempre.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. VISIÓN + MISIÓN -->
    <section class="container-fluid mt-5 overflow-hidden">
        <div class="row g-0 align-items-stretch">
            <!-- Bloque de Texto (Misión + Visión) -->
            <div class="col-lg-6">
                <div class="theme-coral surface-card h-100 py-5 px-4 px-md-5 d-flex flex-column justify-content-center">
                    <div class="row row-cols-2 g-4">
                        <!-- Misión -->
                        <div class="col">
                            <div class="feature-card p-2 h-100" style="--feature-scale: 1.3;">
                                <div class="icon-container mb-3">
                                    <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/startup.svg') }}'); mask-image: url('{{ asset('img/icons/startup.svg') }}');"></div>
                                </div>
                                <h3 class="feature-title text-main poppins-semibold h6 text-uppercase">Nuestra Misión</h3>
                                <p class="feature-text text-center">
                                    Brindar productos de alta calidad que mejoren la vida de las mascotas.
                                </p>
                            </div>
                        </div>
                        <!-- Visión -->
                        <div class="col">
                            <div class="feature-card p-2 h-100" style="--feature-scale: 1.3;">
                                <div class="icon-container mb-3">
                                    <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/visibility.svg') }}'); mask-image: url('{{ asset('img/icons/visibility.svg') }}');"></div>
                                </div>
                                <h3 class="feature-title text-main poppins-semibold h6 text-uppercase">Nuestra Visión</h3>
                                <p class="feature-text text-center">
                                    Ser la marca líder en indumentaria para mascotas a nivel nacional.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bloque de Imagen -->
            <div class="col-lg-6 d-none d-lg-block">
                <img src="{{ asset('img/ui/quienes-somos/mision.webp') }}" 
                     alt="Misión y Visión" 
                     class="img-fluid w-100 h-100 object-fit-cover" 
                     style="max-height: 300px;">
            </div>
            <!-- Imagen para móviles (debajo) -->
            <div class="col-12 d-lg-none">
                <img src="{{ asset('img/ui/quienes-somos/mision.webp') }}" 
                     alt="Misión y Visión" 
                     class="img-fluid w-100 object-fit-cover" 
                     style="max-height: 300px;">
            </div>
        </div>
    </section>

    <!-- 4. NUESTRO EQUIPO -->
    <section class="container mt-5 mb-3">
        <div class="row justify-content-center mb-3">
            <div class="col-md-8 text-center">
                <h2 class="h5 poppins-semibold mb-2">NUESTRO EQUIPO <span class="text-coral-light fs-4">♥</span></h2>
                <p class="h7 poppins-semibold feature-text text-center mb-0">Somos un equipo apasionado por los animales y por crear experiencias que los hagan felices</p>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 theme-neutral justify-content-center g-2">
                    
                    <!-- Staff 1: Andrea García -->
                    <div class="col">
                        <div class="feature-card">
                            <div class="mb-3 mt-auto">
                                <img src="{{ asset('img/ui/quienes-somos/staff-1.webp') }}" alt="Andrea García" class="rounded-4 img-fluid" width="250" height="250" style="object-fit: cover;">
                            </div>
                            <h3 class="h6 poppins-semibold mb-2 text-main">Andrea García</h3>
                            <div class="mt-auto">
                                <span class="theme-coral badge badge-pill-theme">Fundadora & CEO</span>
                            </div>
                            <p class="feature-text small mb-3 mt-2">Amante de los animales y diseñadora de corazón. Ella dio vida a Pet Threads con un sueño y mucho amor.</p>
                        </div>
                    </div>

                    
                    <!-- Staff 2: Miguel Hernández -->
                    <div class="col border-md-start border-md-end px-md-4">
                        <div class="feature-card">
                            <div class="mb-3 mt-auto">
                                <img src="{{ asset('img/ui/quienes-somos/staff-2.webp') }}" alt="Miguel Hernández" class="rounded-4 img-fluid" width="250" height="250" style="object-fit: cover;">
                            </div>
                            <h3 class="h6 poppins-semibold mb-2 text-main">Miguel Hernández</h3>
                            <div class="mt-auto">
                                <span class="theme-coral badge badge-pill-theme">Director de Operaciones</span>
                            </div>
                            <p class="feature-text small mb-3 mt-2">Se asegura de que todo funcione perfecto para que cada pedido llegue con calidad y a tiempo.</p>
                        </div>
                    </div>
                    
                    <!-- Staff 3: Sofía López -->
                    <div class="col">
                        <div class="feature-card">
                            <div class="mb-3 mt-auto">
                                <img src="{{ asset('img/ui/quienes-somos/staff-3.webp') }}" alt="Sofía López" class="rounded-4 img-fluid" width="250" height="250" style="object-fit: cover;">
                            </div>
                            <h3 class="h6 poppins-semibold mb-2 text-main">Sofía López</h3>
                            <div class="mt-auto">
                                <span class="theme-coral badge badge-pill-theme">Diseñadora de Producto</span>
                            </div>
                            <p class="feature-text small mb-3 mt-2">Crea cada colección pensando en estilo, comodidad y durabilidad para cada mascota.</p>
                        </div>
                    </div>

                    <!-- Staff 4: Paula Ramírez -->
                    <div class="col">
                        <div class="feature-card">
                            <div class="mb-3 mt-auto">
                                <img src="{{ asset('img/ui/quienes-somos/staff-4.webp') }}" alt="Paula Ramírez" class="rounded-4 img-fluid" width="250" height="250" style="object-fit: cover;">
                            </div>
                            <h3 class="h6 poppins-semibold mb-2 text-main">Paula Ramírez</h3>
                            <div class="mt-auto">
                                <span class="theme-coral badge badge-pill-theme">Atención al Cliente</span>
                            </div>
                            <p class="feature-text small mb-3 mt-2">Siempre lista para ayudarte y hacer que tu experiencia con nosotros sea increíble.</p>
                        </div>
                    </div>

                    <!-- Staff 5: Carlos Méndez -->
                    <div class="col border-md-start px-md-4">
                        <div class="feature-card">
                            <div class="mb-3 mt-auto">
                                <img src="{{ asset('img/ui/quienes-somos/staff-5.webp') }}" alt="Carlos Méndez" class="rounded-4 img-fluid" width="250" height="250" style="object-fit: cover;">
                            </div>
                            <h3 class="h6 poppins-semibold mb-2 text-main">Carlos Méndez</h3>
                            <div class="mt-auto">
                                <span class="theme-coral badge badge-pill-theme">Marketing & Comunidad</span>
                            </div>
                            <p class="feature-text small mb-3 mt-2">Cuenta nuestras historias y crea contenido para una comunidad de amantes de mascotas.</p>
                        </div>
                    </div>
                    
                </div>
            </div>
        
    </section>
@endsection