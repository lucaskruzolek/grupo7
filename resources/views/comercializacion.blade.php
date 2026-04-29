@extends('layout')

@section('contenido')
    <!-- Banner Principal de Comercialización -->
    <section class="banner-hero theme-neutral">
        <div class="banner-grid">
            <!-- Bloque de Texto -->
            <div class="banner-content">
                <h1 class="banner-title banner-title-nowrap mb-4 position-relative d-inline-block anim-fade-down" style="--anim-order: 1;">Comercialización<span class="paw-icon"></span>
                </h1>                
                <p class="banner-subtitle anim-fade-down" style="--anim-order: 2;">
                    Todo lo que necesitás saber para recibir tus productos de forma fácil, segura y rápida.
                </p>
            </div>
 
            <!-- Bloque de Imagen -->
            <div class="banner-img-container anim-fade-down">
                <img src="{{ asset('img/ui/comercializacion/portada.webp') }}" 
                     alt="Portada Comercialización" 
                     class="banner-img">
            </div>
        </div>
    </section>

    <!-- 1. TIPOS DE ENTREGAS -->
    <section class="container mt-5">
        <div class="section-header">
            <h2 class="h5 poppins-semibold">1. TIPOS DE ENTREGAS</h2>
        </div>
                <div class="row row-cols-2 row-cols-md-3 g-4 justify-content-center">
                    
                    <!-- Opción 1 -->
                    <div class="col">
                        <div class="surface-card">
                            <div class="content-card">
                                <div class="content-icon">
                                    <img src="{{ asset('img/icons/delivery-truck.svg') }}" alt="Entrega Estándar">
                                </div>
                                <h3 class="content-title">ENTREGA ESTÁNDAR</h3>
                                <p class="content-text text-center">Ideal para pedidos del día a día.</p>
                            </div>
                            <div class="mt-auto">
                                <span class="theme-coral badge badge-pill-theme">2 a 5 días hábiles</span>
                            </div>
                        </div>
                    </div>

                    
                    <!-- Opción 2 -->
                    <div class="col">
                        <div class="surface-card">
                            <div class="content-card">
                                <div class="content-icon">
                                    <img src="{{ asset('img/icons/24-hours.svg') }}" alt="Entrega Express">
                                </div>
                                <h3 class="content-title">ENTREGA EXPRESS</h3>
                                <p class="content-text text-center">Para cuando lo necesitas un poco más rápido.</p>
                            </div>
                            <div class="mt-auto">
                                <span class="theme-coral badge badge-pill-theme">1 a 2 días hábiles</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Opción 3 -->
                    <div class="col">
                        <div class="surface-card">
                            <div class="content-card">
                                <div class="content-icon">
                                    <img src="{{ asset('img/icons/route.svg') }}" alt="Recolección">
                                </div>
                                <h3 class="content-title">RECOLECCIÓN EN PUNTO</h3>
                                <p class="content-text text-center">Recoge tu pedido en puntos seleccionados.</p>
                            </div>
                            <div class="mt-auto">
                                <span class="theme-coral badge badge-pill-theme">2 a 4 días hábiles</span>
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
                
        <!-- FOOTER COMPRA SEGURA -->
        <div class="row mt-5">
            <div class="col-12 d-flex justify-content-center">
                <div class="theme-green surface-pill rounded-pill py-3 px-4">
                    <h3 class="h6 poppins-bold mb-1">¡IMPORTANTE!</h3>
                    <div class="d-flex align-items-center justify-content-center">
                        <img src="{{ asset('img/icons/paw.svg') }}" alt="Paw" width="20" class="me-md-3 mt-2 mt-md-0" style="opacity: 0.6;transform: translateY(-10px);">
                        <p class="mb-0 text-main poppins-regular small">
                            Los tiempos de entrega pueden variar según tu ubicación y la disponibilidad del producto.
                        </p>
                        <img src="{{ asset('img/icons/paw.svg') }}" alt="Paw" width="20" class="ms-md-3 mt-2 mt-md-0" style="opacity: 0.6; transform: translateY(-10px);">
                    </div>
                </div>
            </div>
        </div>
        
    </section>

    <!-- 2. FORMAS DE ENVÍOS -->
    <section class="container mt-5 pt-4 border-top">
        <div class="section-header">
            <h2 class="h5 poppins-semibold">2. FORMAS DE ENVÍOS</h2>
        </div>
        
        <div class="row align-items-center g-5">
            <!-- Columna 1: Logos -->
            <div class="col-md-6">
                <!-- Fila 1: Logos principales -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="content-card" style="--content-scale: 1.2;">
                            <div class="content-icon" style="height: 50px; transform: scale(1.4);">
                                <img src="{{ asset('img/icons/Correo_Argentino_Logo.svg') }}" alt="Correo Argentino" class="img-fluid" style="max-height: 35px;">
                            </div>
                            <p class="content-text text-center">Cobertura nacional.<br>Rastreo en línea de tu pedido.</p>
                        </div>
                    </div>
                    <div class="col-6 border-md-start">
                        <div class="content-card" style="--content-scale: 1.2;">
                            <div class="content-icon" style="height: 50px;">
                                <img src="{{ asset('img/icons/fundacion-andreani.png') }}" alt="Andreani" class="img-fluid" style="max-height: 35px;">
                            </div>
                            <p class="content-text text-center">Envíos rápidos y seguros<br>a toda Argentina.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Fila 2: Envío Gratis -->
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="d-flex align-items-center text-center text-md-start">
                            <div class="me-4 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm" style="width: 80px; height: 80px; min-width: 80px;">
                                <img src="{{ asset('img/icons/delivery-truck-fast.svg') }}" alt="Envío Gratis" width="40">
                            </div>
                            <div>
                                <h4 class="h6 poppins-semibold mb-1 text-main">ENVÍO GRATIS</h4>
                                <p class="content-text small mb-0">En compras mayores a $50.000<br>a todo el país.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Columna 2: Mapa -->
            <div class="col-md-3 text-center border-md-start">
                <img src="{{ asset('img/ui/comercializacion/mapa.webp') }}" alt="Mapa de cobertura" class="img-fluid" style="max-height: 250px; object-fit: contain;">
            </div>
            
            <!-- Columna 3: Info -->
            <div class="col-md-3">
                <div class="surface-card w-100">
                <div class="content-card">
                    <h3 class="h6 poppins-bold mb-3">¿A DÓNDE ENVIAMOS?</h3>
                    <p class="content-text">Realizamos envíos a toda la República Argentina.</p>
                    <p class="content-text">Consulta la disponibilidad de envío en tu código postal al finalizar tu compra.</p>
                </div>
                </div>
            </div>
        </div>
        
        <!-- Banner Footer -->
        <div class="row mt-5">
            <div class="col-12 d-flex justify-content-center">
                <div class="theme-green surface-pill rounded-pill py-3 px-4 flex-md-row">
                    <img src="{{ asset('img/icons/paw.svg') }}" alt="Paw" width="20" class="me-md-3 mt-2 mt-md-0" style="opacity: 0.6;">
                    <p class="mb-0 text-main poppins-regular small">Todos nuestros pedidos se empacan con mucho amor para que lleguen en perfectas condiciones.</p>
                    <img src="{{ asset('img/icons/paw.svg') }}" alt="Paw" width="20" class="ms-md-3 mt-2 mt-md-0" style="opacity: 0.6;">
                </div>
            </div>
        </div>
    </section>

    <!-- 3. FORMAS DE PAGO -->
    <section class="container mt-5 pt-4 mb-5 border-top">
        <div class="section-header">
            <h2 class="h5 poppins-semibold">3. FORMAS DE PAGO</h2>
        </div>
        
        <div class="row row-cols-2 row-cols-md-4 g-4">
                    <!-- Tarjetas -->
                    <div class="col">
                        <div class="content-card">
                            <div class="content-icon">
                                <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/credit-card2.svg') }}'); mask-image: url('{{ asset('img/icons/credit-card2.svg') }}');"></div>
                            </div>
                            <h3 class="content-title">Tarjetas de crédito y débito</h3>
                            <p class="content-text text-center">Visa, Mastercard, American Express.</p>
                        </div>
                    </div>

                    <!-- Transferencia -->
                    <div class="col border-md-start">
                        <div class="content-card">
                            <div class="content-icon">
                                <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/bank.svg') }}'); mask-image: url('{{ asset('img/icons/bank.svg') }}');"></div>
                            </div>
                            <h3 class="content-title">Transferencia bancaria</h3>
                            <p class="content-text text-center">Consulta los datos al finalizar tu compra.</p>
                        </div>
                    </div>

                    <!-- Mercado Pago -->
                    <div class="col border-md-start">
                        <div class="content-card">
                            <div class="content-icon" style="width: 56px; height: 56px;">
                                <img src="{{ asset('img/icons/MP.svg') }}" alt="Mercado Pago">
                            </div>
                            <h3 class="content-title">Mercado Pago</h3>
                            <p class="content-text text-center">Paga de forma segura con tu cuenta de Mercado Pago.</p>
                        </div>
                    </div>

                    <!-- Pago Fácil -->
                    <div class="col border-md-start">
                        <div class="content-card">
                            <div class="content-icon">
                                <img src="{{ asset('img/icons/pagofacil.svg') }}" alt="Pago Fácil">
                            </div>
                            <h3 class="content-title">Pago Fácil</h3>
                            <p class="content-text text-center">Realiza tu pago en efectivo en cualquier sucursal.</p>
                        </div>
                    </div>
                </div>
            
            
        </div>

        <!-- FOOTER COMPRA SEGURA -->
        <div class="row mt-5">
            <div class="col-12 d-flex justify-content-center">
                <div class="theme-green surface-pill rounded-pill py-3 px-4">
                    <h3 class="h6 poppins-bold mb-1">TU COMPRA ES SEGURA</h3>
                    <div class="d-flex align-items-center justify-content-center">
                        <img src="{{ asset('img/icons/paw.svg') }}" alt="Paw" width="20" class="me-md-3 mt-2 mt-md-0" style="opacity: 0.6;transform: translateY(-10px);">
                        <p class="mb-0 text-main poppins-regular small">
                            Nuestro sitio cuenta con certificados de seguridad para proteger tu información en todo momento.
                        </p>
                        <img src="{{ asset('img/icons/paw.svg') }}" alt="Paw" width="20" class="ms-md-3 mt-2 mt-md-0" style="opacity: 0.6; transform: translateY(-10px);">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- INFORMACIÓN ADICIONAL -->
    <section class=" surface-flat theme-coral mt-2 position-relative overflow-hidden" style="padding-top:1.5rem;">
        <div class="container position-relative">
            <div class="col-12 ">
                <h2 class="h5 poppins-semibold">INFORMACIÓN ADICIONAL</h2>
            </div>
            <div class="row align-items-end">
                <div class="col-lg-9 pb-4 pe-lg-5 pt-2">
                    <div class="row g-4 text-center">
                        
                        <!--cambios y devoluciones -->
                        <div class="col-6 col-md-3">
                            <div class="content-card" style="--content-scale: 1.1;">
                                <div class="content-icon">
                                    <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/refresh.svg') }}'); mask-image: url('{{ asset('img/icons/refresh.svg') }}');"></div>
                                </div>
                                <h3 class="content-title">CAMBIOS Y DEVOLUCIONES</h3>
                                <p class="content-text text-hyphenated">Tienes 15 días naturales para realizar cambios o devoluciones. Consulta nuestra política completa en el pie de página.</p>
                            </div>
                        </div>
                        
                        <!--necesitas ayuda -->
                        <div class="col-6 col-md-3">
                            <div class="content-card" style="--content-scale: 1.1;">
                                <div class="content-icon">
                                    <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/support.svg') }}'); mask-image: url('{{ asset('img/icons/support.svg') }}');"></div>
                                </div>
                                <h3 class="content-title">¿NECESITAS AYUDA?</h3>
                                <p class="content-text text-hyphenated">Nuestro equipo está listo para ayudarte en lo que necesites. Contáctanos por WhatsApp, correo o redes sociales.</p>
                            </div>
                        </div>
                        
                        <!--tu satisfacción es nuestra prioridad -->
                        <div class="col-6 col-md-3">
                            <div class="content-card" style="--content-scale: 1.1;">
                                <div class="content-icon">
                                    <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/heart.svg') }}'); mask-image: url('{{ asset('img/icons/heart.svg') }}');"></div>
                                </div>
                                <h3 class="content-title">TU SATISFACCIÓN ES NUESTRA PRIORIDAD</h3>
                                <p class="content-text text-hyphenated">Si algo no salió como esperabas, haremos todo lo posible para solucionarlo.</p>
                            </div>
                        </div>
                        
                        <!--empaque especial-->
                        <div class="col-6 col-md-3">
                            <div class="content-card" style="--content-scale: 1.1;">
                                <div class="content-icon">
                                    <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/gift.svg') }}'); mask-image: url('{{ asset('img/icons/gift.svg') }}');"></div>
                                </div>
                                <h3 class="content-title">EMPAQUE ESPECIAL</h3>
                                <p class="content-text text-hyphenated">Todos los pedidos incluyen empaque personalizado, perfecto para regalo.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 position-relative d-flex justify-content-end align-items-end pe-0" style="min-height: 100%;">
                    <img src="{{ asset('img/ui/comercializacion/gato-en-caja.webp') }}" alt="Mascota en caja" class="img-cat-mockup" style="filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">
                </div>
            </div>
        </div>
    </section>
@endsection
