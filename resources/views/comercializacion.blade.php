@extends('layout')

@section('contenido')
    <!-- Banner Principal de Comercialización -->
    <section class="theme-neutral surface-card overflow-hidden p-0 ps-md-5 pt-2">
        <div class="row g-0 align-items-center w-100 m-0">
            <!-- Bloque de Texto (40%) -->
            <div class="col-md-6 ps-4 pe-2 banner-content">
                <h1 class="banner-title banner-title-nowrap mb-4 position-relative d-inline-block">Comercialización<span class="paw-icon"></span>
                </h1>
                
                <p class="banner-subtitle">
                    Todo lo que necesitas saber para recibir tus productos de forma fácil, segura y rápida.
                </p>
            </div>
 
            <!-- Bloque de Imagen (60%) -->
            <div class="col-md-6 align-self-stretch">
                <img src="{{ asset('img/ui/comercializacion/portada.webp') }}" 
                     alt="Portada Comercialización" 
                     class="img-fluid w-100 img-fade-left" 
                     style="display: block;">
            </div>
        </div>
    </section>

    <!-- 1. TIPOS DE ENTREGAS -->
    <section class="container mt-5">
        <div class="section-header">
            <h2 class="h5 poppins-semibold mb-0">1. TIPOS DE ENTREGAS</h2>
        </div>
        


                <div class="row row-cols-2 row-cols-md-4 g-4">
                    
                    <!-- Opción 1 -->
                    <div class="col">
                        <div class="surface-card">
                            <div class="mb-3 mt-auto">
                                <img src="{{ asset('img/icons/delivery-truck.svg') }}" alt="Entrega Estándar" width="48">
                            </div>
                            <h3 class="h6 poppins-semibold mb-2 text-main">ENTREGA ESTÁNDAR</h3>
                            <p class="text-secondary small mb-3">Ideal para pedidos del día a día.</p>
                            <div class="mt-auto">
                                <span class="theme-coral badge badge-pill-theme">2 a 5 días hábiles</span>
                            </div>
                        </div>
                    </div>

                    
                    <!-- Opción 2 -->
                    <div class="col">
                        <div class="surface-card">
                            <div class="mb-3 mt-auto">
                                <img src="{{ asset('img/icons/24-hours.svg') }}" alt="Entrega Express" width="48">
                            </div>
                            <h3 class="h6 poppins-semibold mb-2 text-main">ENTREGA EXPRESS</h3>
                            <p class="text-secondary small mb-3">Para cuando lo necesitas un poco más rápido.</p>
                            <div class="mt-auto ">
                                <span class="theme-coral badge badge-pill-theme">1 a 2 días hábiles</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Opción 3 -->
                    <div class="col">
                        <div class="surface-card">
                            <div class="mb-3 mt-auto">
                                <img src="{{ asset('img/icons/route.svg') }}" alt="Recolección" width="48">
                            </div>
                            <h3 class="h6 poppins-semibold mb-2 text-main">RECOLECCIÓN EN PUNTO</h3>
                            <p class="text-secondary small mb-3">Recoge tu pedido en puntos seleccionados.</p>
                            <div class="mt-auto">
                                <span class="theme-coral badge badge-pill-theme">2 a 4 días hábiles</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col">
                    <div class="theme-green surface-card justify-content-center rounded-4">
                    <div class="mb-3 d-flex justify-content-center align-items-center">
                        <img src="{{ asset('img/icons/paw.svg') }}" alt="Paw" width="20" class="me-2" style="opacity: 0.8;"> 
                        <h3 class="h6 poppins-semibold mb-0 text-main">IMPORTANTE</h3>
                    </div>
                    <p class="text-secondary small">
                        Los tiempos de entrega pueden variar según tu ubicación y la disponibilidad del producto.
                    </p>
                    </div>
                </div>
            </div>
        
    </section>

    <!-- 2. FORMAS DE ENVÍOS -->
    <section class="container mt-5 pt-4 border-top">
        <div class="section-header">
            <h2 class="h5 mb-0 poppins-semibold">2. FORMAS DE ENVÍOS</h2>
        </div>
        
        <div class="row align-items-center g-5">
            <!-- Columna 1: Logos -->
            <div class="col-md-5 d-flex flex-column align-items-center align-items-md-start">
                <div class="d-flex align-items-center text-center text-md-start mb-4 pb-3">
                    <div class="me-4 text-center" style="width: 120px;">
                        <img src="{{ asset('img/icons/Correo_Argentino_Logo.svg') }}" alt="Correo Argentino" class="img-fluid" style="max-height: 40px;">
                    </div>
                    <div>
                        <p class="feature-text small mb-0">Cobertura nacional.<br>Rastreo en línea de tu pedido.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center text-center text-md-start mb-4 pb-3">
                    <div class="me-4 text-center" style="width: 120px;">
                        <img src="{{ asset('img/icons/fundacion-andreani.png') }}" alt="Andreani" class="img-fluid" style="max-height: 40px;">
                    </div>
                    <div>
                        <p class="feature-text small mb-0">Envíos rápidos y seguros<br>a toda Argentina.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center text-center text-md-start">
                    <div class="me-4 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm" style="width: 80px; height: 80px; min-width: 80px;">
                        <img src="{{ asset('img/icons/delivery-truck-fast.svg') }}" alt="Envío Gratis" width="40">
                    </div>
                    <div>
                        <h4 class="h6 poppins-semibold mb-1 text-main">ENVÍO GRATIS</h4>
                        <p class="feature-text small mb-0">En compras mayores a $50.000<br>a todo el país.</p>
                    </div>
                </div>
            </div>
            
            <!-- Columna 2: Mapa -->
            <div class="col-md-3 text-center border-md-start">
                <img src="{{ asset('img/ui/comercializacion/mapa.webp') }}" alt="Mapa de cobertura" class="img-fluid" style="max-height: 250px; object-fit: contain;">
            </div>
            
            <!-- Columna 3: Info -->
            <div class="col-md-4 text-center text-md-start">
                <h3 class="h6 poppins-bold mb-3 text-main">¿A DÓNDE ENVIAMOS?</h3>
                <p class="feature-text small mb-4">Realizamos envíos a toda la República Argentina.</p>
                <p class="feature-text small mb-4">Consulta la disponibilidad de envío en tu código postal al finalizar tu compra.</p>
            </div>
        </div>
        
        <!-- Banner Footer -->
        <div class="row mt-5">
            <div class="col-12 d-flex justify-content-center">
                <div class="theme-green surface-card rounded-pill py-3 px-4 flex-md-row">
                    <img src="{{ asset('img/icons/gift.svg') }}" alt="Caja" width="28" class="me-md-3 mb-2 mb-md-0">
                    <p class="mb-0 text-main poppins-regular small">Todos nuestros pedidos se empacan con mucho amor para que lleguen en perfectas condiciones.</p>
                    <img src="{{ asset('img/icons/paw.svg') }}" alt="Paw" width="20" class="ms-md-3 mt-2 mt-md-0" style="opacity: 0.6;">
                </div>
            </div>
        </div>
    </section>

    <!-- 3. FORMAS DE PAGO -->
    <section class="container mt-5 pt-4 mb-5 border-top">
        <div class="section-header">
            <h2 class="h5 mb-1 poppins-semibold">3. FORMAS DE PAGO</h2>
        </div>
        
        <div class="row align-items-stretch">
            <!-- Columna Izquierda: Métodos -->
            <div class="col-lg-8">
                <div class="row row-cols-2 row-cols-md-4 g-4 text-center">
                    
                    <!-- Tarjetas -->
                    <div class="col">
                        <div class="feature-card">
                            <div class="icon-container">
                                <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/credit-card2.svg') }}'); mask-image: url('{{ asset('img/icons/credit-card2.svg') }}');"></div>
                            </div>
                            <h3 class="feature-title text-main poppins-semibold">Tarjetas de crédito y débito</h3>
                            <p class="feature-text text-center">Visa, Mastercard, American Express.</p>
                        </div>
                    </div>

                    <!-- Transferencia -->
                    <div class="col border-md-start">
                        <div class="feature-card">
                            <div class="icon-container">
                                <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/bank.svg') }}'); mask-image: url('{{ asset('img/icons/bank.svg') }}');"></div>
                            </div>
                            <h3 class="feature-title text-main poppins-semibold">Transferencia bancaria</h3>
                            <p class="feature-text text-center">Consulta los datos al finalizar tu compra.</p>
                        </div>
                    </div>

                    <!-- Mercado Pago -->
                    <div class="col border-md-start">
                        <div class="feature-card">
                            <div class="icon-container" style="width: 56px; height: 56px;">
                                <img src="{{ asset('img/icons/MP.svg') }}" alt="Mercado Pago">
                            </div>
                            <h3 class="feature-title text-main poppins-semibold">Mercado Pago</h3>
                            <p class="feature-text text-center">Paga de forma segura con tu cuenta de Mercado Pago.</p>
                        </div>
                    </div>

                    <!-- Pago Fácil -->
                    <div class="col border-md-start">
                        <div class="feature-card">
                            <div class="icon-container">
                                <img src="{{ asset('img/icons/pagofacil.svg') }}" alt="Pago Fácil">
                            </div>
                            <h3 class="feature-title text-main poppins-semibold">Pago Fácil</h3>
                            <p class="feature-text text-center">Realiza tu pago en efectivo en cualquier sucursal.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Columna Derecha: Compra Segura -->
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="theme-green surface-card justify-content-center rounded-4">
                    <div class="d-flex align-items-center justify-content-center justify-content-lg-start mb-3">
                        <img src="{{ asset('img/icons/shield.svg') }}" alt="Escudo" width="32" class="me-3 opacity-75">
                        <h3 class="h6 poppins-bold mb-0 text-main">TU COMPRA ES SEGURA</h3>
                    </div>
                    <p class="text-secondary small mb-0 text-center text-lg-start">
                        Nuestro sitio cuenta con certificados de seguridad para proteger tu información en todo momento.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- INFORMACIÓN ADICIONAL -->
    <section class="theme-coral surface-card mt-2 position-relative overflow-hidden" style="padding-top:1.5rem;">
        <div class="container position-relative">
            <div class="col-12">
                <h2 class="h6 poppins-bold text-main text-uppercase text-center text-lg-start mb-4">INFORMACIÓN ADICIONAL</h2>
            </div>
            <div class="row align-items-end">
                <div class="col-lg-9 pb-4 pe-lg-5 pt-2">
                    <div class="row g-4 text-center">
                        <div class="col-6 col-md-3">
                            <div class="info-card">
                                <div class="info-icon">
                                    <img src="{{ asset('img/icons/refresh.svg') }}" alt="Cambios">
                                </div>
                                <h3 class="info-title">CAMBIOS Y DEVOLUCIONES</h3>
                                <p class="info-text text-hyphenated">Tienes 15 días naturales para realizar cambios o devoluciones. Consulta nuestra política completa en el pie de página.</p>
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-3">
                            <div class="info-card">
                                <div class="info-icon">
                                    <img src="{{ asset('img/icons/support.svg') }}" alt="Ayuda">
                                </div>
                                <h3 class="info-title">¿NECESITAS AYUDA?</h3>
                                <p class="info-text text-hyphenated">Nuestro equipo está listo para ayudarte en lo que necesites. Contáctanos por WhatsApp, correo o redes sociales.</p>
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-3">
                            <div class="info-card">
                                <div class="info-icon">
                                    <img src="{{ asset('img/icons/heart.svg') }}" alt="Satisfacción">
                                </div>
                                <h3 class="info-title">TU SATISFACCIÓN ES NUESTRA PRIORIDAD</h3>
                                <p class="info-text text-hyphenated">Si algo no salió como esperabas, haremos todo lo posible para solucionarlo.</p>
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-3">
                            <div class="info-card">
                                <div class="info-icon">
                                    <img src="{{ asset('img/icons/gift.svg') }}" alt="Empaque">
                                </div>
                                <h3 class="info-title">EMPAQUE ESPECIAL</h3>
                                <p class="info-text text-hyphenated">Todos los pedidos incluyen empaque personalizado, perfecto para regalo.</p>
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
