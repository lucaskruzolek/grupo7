@extends('layout')

@section('contenido')
    <!-- Banner Principal de Quiénes Somos -->
    <section class="banner-hero theme-neutral">
        <div class="banner-grid">
            <!-- Bloque de Texto (40%) -->
            <div class="banner-content">
                
                <p class="section-tag mb-2 pt-2 anim-fade-down" style="--anim-order: 1;">
                    Quiénes somos <span class="text-coral-light">♥</span>
                </p>
                <h1 class="banner-title mb-4 position-relative anim-fade-down" style="--anim-order: 2;">
                    Hecho con amor para quienes los aman como <span class="text-coral-light">familia</span>
                    <span class="paw-icon"></span>
                </h1>
                
                <p class="banner-subtitle anim-fade-down" style="--anim-order: 3;">
                    Pet Threads nace del amor por los animales y del deseo de ofrecerles lo mejor. Creamos ropa y accesorios que combinan estilo, comodidad y calidad para que cada mascota se sienta única y feliz.
                </p>
            </div>
 
            <!-- Bloque de Imagen (60%) -->
            <div class="banner-img-container anim-fade-down">
                <img src="{{ asset('img/ui/quienes-somos/portada.webp') }}" 
                     alt="Portada Quiénes Somos" 
                     class="banner-img">
            </div>
        </div>
    </section>

    <!-- 1. NUESTRA HISTORIA -->
    <section class="container mt-5 anim-fade-down">
        <div class="text-start p-4 pt-md-3 px-md-0">
            <div class="row align-items-center g-4">
                <div class="col-md-6 order-2 order-md-1">
                    <img src="{{ asset('img/ui/quienes-somos/historia.webp') }}" 
                         alt="Nuestra Historia" 
                         class="img-fluid rounded-2 w-100 object-fit-cover"
                         style="max-height: 450px;">
                </div>
                <div class="col-md-6 order-1 order-md-2">
                    <p class="section-tag mb-3 fs-5">NUESTRA HISTORIA <span class="text-coral-light fs-4">♥</span></p>
                    <h2 class="h3 playfair-display-medium text-main mb-4" style="line-height: 1.2; letter-spacing: 1px;">Todo comenzó con un vínculo incondicional</h2>
                    <p class="content-text mb-3" style="line-height: 1.7;">
                        Pet Threads comenzó como un pequeño proyecto impulsado por amantes de los animales que buscaban productos más bonitos, cómodos y duraderos para sus propios compañeros.
                    </p>
                    <p class="content-text mb-3" style="line-height: 1.7;">
                        Lo que inició en casa, entre bocetos, telas y muchas pruebas con nuestras propias mascotas, hoy es una marca dedicada a miles de amigos peludos que merecen lo mejor.
                    </p>
                    <p class="content-text mb-0" style="line-height: 1.7;">
                        Cada día, trabajamos con el mismo objetivo: diseñar prendas para momentos especiales, paseos, aventuras y siestas.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. POR QUÉ ELEGIR PET THREADS -->
    <section class="container mt-5 anim-fade-down">
        <div class="theme-green surface-pill p-3 p-md-3">
            <h6 class="h6 poppins-bold mb-4 text-center text-uppercase">¿Por qué elegir Pet Threads?</h4>
            
            <div class="row row-cols-2 row-cols-md-5 g-4 text-center justify-content-center">
                <!-- Card 1 -->
                <div class="col">
                    <div class="content-card  p-2 h-100" style="--content-scale: 1.1;">
                        <div class="content-icon">
                            <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/star.svg') }}'); mask-image: url('{{ asset('img/icons/star.svg') }}');"></div>
                        </div>
                        <h3 class="content-title poppins-semibold">Diseños Exclusivos</h3>
                        <p class="content-text text-center">Prendas únicas pensadas para destacar.</p>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="col">
                    <div class="content-card  p-2 h-100" style="--content-scale: 1.1;">
                        <div class="content-icon">
                            <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/medal.svg') }}'); mask-image: url('{{ asset('img/icons/medal.svg') }}');"></div>
                        </div>
                        <h3 class="content-title poppins-semibold">Calidad Premium</h3>
                        <p class="content-text text-center">Materiales duraderos y amigables.</p>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="col">
                    <div class="content-card  p-2 h-100" style="--content-scale: 1.1;">
                        <div class="content-icon">
                            <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/heart.svg') }}'); mask-image: url('{{ asset('img/icons/heart.svg') }}');"></div>
                        </div>
                        <h3 class="content-title poppins-semibold">Máxima Comodidad</h3>
                        <p class="content-text text-center">Ajuste perfecto para total libertad.</p>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="col">
                    <div class="content-card  p-2 h-100" style="--content-scale: 1.1;">
                        <div class="content-icon">
                            <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/delivery-truck.svg') }}'); mask-image: url('{{ asset('img/icons/delivery-truck.svg') }}');"></div>
                        </div>
                        <h3 class="content-title poppins-semibold">Envíos a todo el país</h3>
                        <p class="content-text text-center">Llegamos a tu hogar de forma segura.</p>
                    </div>
                </div>
                <!-- Card 5 -->
                <div class="col">
                    <div class="content-card  p-2 h-100" style="--content-scale: 1.1;">
                        <div class="content-icon">
                            <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/support.svg') }}'); mask-image: url('{{ asset('img/icons/support.svg') }}');"></div>
                        </div>
                        <h3 class="content-title poppins-semibold">Atención Dedicada</h3>
                        <p class="content-text text-center">Estamos para ayudarte siempre.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. VISIÓN + MISIÓN -->
    <section class="container-fluid p-0 mt-5 overflow-hidden">
        <div class="row g-0 align-items-stretch anim-fade-down">
            <!-- Bloque de Texto (Misión + Visión) -->
            <div class="col-lg-6">
                <div class="theme-coral surface-flat h-100 px-md-5 d-flex flex-column justify-content-center">
                    <div class="row row-cols-2 g-4">
                        <!-- Misión -->
                        <div class="col">
                            <div class="content-card h-100" style="--content-scale: 1.3;">
                                <div class="content-icon mb-3">
                                    <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/startup.svg') }}'); mask-image: url('{{ asset('img/icons/startup.svg') }}');"></div>
                                </div>
                                <h3 class="content-title poppins-semibold h6">Nuestra Misión</h3>
                                <p class="content-text text-center">
                                    Brindar productos de alta calidad que mejoren la vida de las mascotas.
                                </p>
                            </div>
                        </div>
                        <!-- Visión -->
                        <div class="col">
                            <div class="content-card p-2 h-100" style="--content-scale: 1.3;">
                                <div class="content-icon mb-3">
                                    <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/visibility.svg') }}'); mask-image: url('{{ asset('img/icons/visibility.svg') }}');"></div>
                                </div>
                                <h3 class="content-title poppins-semibold h6">Nuestra Visión</h3>
                                <p class="content-text text-center">
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
        <div class="row justify-content-center mb-3 border-top pt-4">
            <div class="col-md-8 text-center">
                <h1 class="anim-fade-down" style="--anim-order: 1;">Nuestro Equipo <span class="text-coral-light fs-1 anim-fade-down" style="--anim-order: 1;">♥</span></h1>
                <h5 class="poppins-regular text-center anim-fade-down" style="--anim-order: 2;">Somos un equipo apasionado por los animales y por crear experiencias que los hagan felices</h5>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 theme-neutral justify-content-center g-2">
                    
                    <!-- Staff 1: Andrea García -->
                    <div class="col anim-fade-down" style="--anim-order: 3;">
                        <div class="content-card mx-auto" style="max-width: 250px; --content-scale: 1.35;">
                            <div class="mb-3">
                                <img src="{{ asset('img/ui/quienes-somos/staff-1.webp') }}" alt="Andrea García" class="rounded-4 img-fluid" width="250" height="250" style="object-fit: cover;">
                            </div>
                            <div class="px-1">
                                <h3 class="content-title h6 mb-2">Andrea García</h3>
                                <div>
                                    <span class="theme-coral badge badge-pill-theme">Fundadora & CEO</span>
                                </div>
                                <p class="content-text mb-3 mt-2">Amante de los animales y diseñadora de corazón. Dio vida a Pet Threads con un sueño y mucho amor.</p>
                            </div>
                        </div>
                    </div>

                    
                    <!-- Staff 2: Miguel Hernández -->
                    <div class="col border-md-start border-md-end px-md-4 anim-fade-down" style="--anim-order: 4;">
                        <div class="content-card mx-auto" style="max-width: 250px; --content-scale: 1.35;">
                            <div class="mb-3">
                                <img src="{{ asset('img/ui/quienes-somos/staff-2.webp') }}" alt="Miguel Hernández" class="rounded-4 img-fluid" width="250" height="250" style="object-fit: cover;">
                            </div>
                            <div class="px-1">
                                <h3 class="content-title h6 mb-2">Miguel Hernández</h3>
                                <div>
                                    <span class="theme-coral badge badge-pill-theme">Director de Operaciones</span>
                                </div>
                                <p class="content-text mb-3 mt-2">Se asegura de que todo funcione perfecto para que cada pedido llegue con calidad y a tiempo.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Staff 3: Sofía López -->
                    <div class="col anim-fade-down" style="--anim-order: 5;">
                        <div class="content-card mx-auto" style="max-width: 250px; --content-scale: 1.35;">
                            <div class="mb-3">
                                <img src="{{ asset('img/ui/quienes-somos/staff-5.webp') }}" alt="Sofía López" class="rounded-4 img-fluid" width="250" height="250" style="object-fit: cover;">
                            </div>
                            <div class="px-1">
                                <h3 class="content-title h6 mb-2">Carlos López</h3>
                                <div>
                                    <span class="theme-coral badge badge-pill-theme">Diseñador de Producto</span>
                                </div>
                                <p class="content-text mb-3 mt-2">Crea cada colección pensando en estilo, comodidad y durabilidad para cada mascota.</p>
                            </div>
                        </div>
                    </div>

                    
                    
                </div>
            </div>
        
    </section>
@endsection
