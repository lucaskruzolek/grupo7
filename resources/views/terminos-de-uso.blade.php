@extends('layout')

@section('contenido')
    <!-- Banner Principal de Términos de Uso -->
    <section class="banner-hero theme-neutral">
        <div class="banner-grid">
            <!-- Bloque de Texto (40%) -->
            <div class="banner-content">
                <h1 class="banner-title mb-4 position-relative anim-fade-down" style="--anim-order: 1;">Términos y Condiciones de Uso del Sitio Web<span class="paw-icon"></span>
                </h1>
                
                <p class="banner-subtitle anim-fade-down" style="--anim-order: 2;">
                    Al acceder y utilizar el sitio web de Pet Threads, aceptas los presentes términos y condiciones. Te recomendamos leerlos atentamente.
                </p>
            </div>
 
            <!-- Bloque de Imagen (60%) -->
            <div class="banner-img-container anim-fade-down">
                <img src="{{ asset('img/ui/terminos/portada.webp') }}" 
                     alt="Portada Términos y Condiciones" 
                     class="banner-img">
            </div>
        </div>
    </section>

    <!-- AVISOS LEGALES -->
    <section class="container mt-5">
        
        <div class="row row-cols-2 row-cols-md-3 g-4 mb-5 justify-content-center">
            <!-- Opción 1 -->
            <div class="col">
                <div class="content-card">
                    <div class="content-icon">
                        <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/file-interface.svg') }}'); mask-image: url('{{ asset('img/icons/file-interface.svg') }}');"></div>
                    </div>
                    <h3 class="content-title text-main poppins-semibold">1. AVISO LEGAL</h3>
                    <p class="content-text">Pet Threads SRL, con domicilio en Corrientes, Argentina, se reserva el derecho de modificar los presentes Términos y Condiciones en cualquier momento.</p>
                </div>
            </div>

            <!-- Opción 2 -->
            <div class="col border-md-start">
                <div class="content-card">
                    <div class="content-icon">
                        <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/shopping-bag.svg') }}'); mask-image: url('{{ asset('img/icons/shopping-bag.svg') }}');"></div>
                    </div>
                    <h3 class="content-title text-main poppins-semibold">2. SERVICIOS OFRECIDOS</h3>
                    <p class="content-text">Ofrecemos venta online de ropa y accesorios. Los productos publicados están sujetos a disponibilidad de stock y cambios de precio sin previo aviso.</p>
                </div>
            </div>
            
            <!-- Opción 3 -->
            <div class="col border-md-start">
                <div class="content-card">
                    <div class="content-icon">
                        <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/incognito.svg') }}'); mask-image: url('{{ asset('img/icons/incognito.svg') }}');"></div>
                    </div>
                    <h3 class="content-title text-main poppins-semibold">3. POLÍTICA DE PRIVACIDAD</h3>
                    <p class="content-text">Tu privacidad es importante. Tratamos tus datos personales cumpliendo con la Ley 25.326, únicamente para procesar tu compra y mejorar tu experiencia.</p>
                </div>
            </div>
        </div>

        <div class="row mt-4 mb-5">
            <div class="col-12 d-flex justify-content-center">
                <div class="theme-green surface-pill rounded-pill py-3 flex-md-row">
                    <img src="{{ asset('img/icons/padlock.svg') }}" alt="Caja" width="28" class="me-md-3 mb-2 mb-md-0">
                    <p class="mb-0 text-main poppins-regular small">Tus datos están protegidos. No compartimos tu información con terceros sin tu consentimiento.</p>
                    <img src="{{ asset('img/icons/paw.svg') }}" alt="Paw" width="20" class="ms-md-3 mt-2 mt-md-0" style="opacity: 0.6;">
                </div>
            </div>
        </div>

        <div class="row row-cols-2 row-cols-md-4 g-4 mb-5 justify-content-center">
            <!-- Opción 1 -->
            <div class="col">
                <div class="content-card">
                    <div class="content-icon">
                        <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/credit-card2.svg') }}'); mask-image: url('{{ asset('img/icons/credit-card2.svg') }}');"></div>
                    </div>
                    <h3 class="content-title text-main poppins-semibold">4. FORMAS DE PAGO</h3>
                    <p class="content-text">Tarjetas de crédito y débito, Transferencia bancaria, Mercado Pago y Pago Fácil. Todos los pagos se procesan de forma segura.</p>
                </div>
            </div>

            <!-- Opción 2 -->
            <div class="col border-md-start">
                <div class="content-card">
                    <div class="content-icon">
                        <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/delivery-truck.svg') }}'); mask-image: url('{{ asset('img/icons/delivery-truck.svg') }}');"></div>
                    </div>
                    <h3 class="content-title text-main poppins-semibold">5. FORMAS DE ENTREGA</h3>
                    <p class="content-text">Envíos a todo el país a través de Correo Argentino y Andreani. Envío gratis en compras mayores a $50.000.</p>
                </div>
            </div>
            
            <!-- Opción 3 -->
            <div class="col border-md-start">
                <div class="content-card">
                    <div class="content-icon">
                        <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/clock.svg') }}'); mask-image: url('{{ asset('img/icons/clock.svg') }}');"></div>
                    </div>
                    <h3 class="content-title text-main poppins-semibold">6. TIEMPOS DE ENTREGA</h3>
                    <p class="content-text">Entrega Estándar (2 a 5 días hábiles), Entrega Express (1 a 2 días hábiles) o Recolección en punto de entrega (2 a 4 días hábiles).</p>
                </div>
            </div>

            <!-- Opción 4 -->
            <div class="col border-md-start">
                <div class="content-card">
                    <div class="content-icon">
                        <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/refresh.svg') }}'); mask-image: url('{{ asset('img/icons/refresh.svg') }}');"></div>
                    </div>
                    <h3 class="content-title text-main poppins-semibold">7. CAMBIOS Y DEVOLUCIONES</h3>
                    <p class="content-text">Tenés 15 días corridos para solicitar un cambio o devolución. Los productos deben estar sin uso y en su empaque original.</p>
                </div>
            </div>
        </div>

        <div class="row row-cols-2 row-cols-md-3 g-4 justify-content-center">
            <!-- Opción 1 -->
            <div class="col">
                <div class="content-card">
                    <div class="content-icon">
                        <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/star.svg') }}'); mask-image: url('{{ asset('img/icons/star.svg') }}');"></div>
                    </div>
                    <h3 class="content-title text-main poppins-semibold">8. GARANTÍAS</h3>
                    <p class="content-text">Todos nuestros productos cuentan con garantía por fallos de fabricación. Si recibís un producto con defectos, contactanos dentro de los 7 días corridos.</p>
                </div>
            </div>

            <!-- Opción 2 -->
            <div class="col border-md-start">
                <div class="content-card">
                    <div class="content-icon">
                        <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/support.svg') }}'); mask-image: url('{{ asset('img/icons/support.svg') }}');"></div>
                    </div>
                    <h3 class="content-title text-main poppins-semibold">9. SOPORTE POSTVENTA</h3>
                    <p class="content-text">Nuestro equipo está para ayudarte antes, durante y después de tu compra. Podés contactarnos por cualquiera de nuestros canales oficiales.</p>
                    <div class="mt-auto pt-3">
                        <span class="theme-coral badge badge-pill-theme d-inline-flex align-items-center">
                            <img src="{{ asset('img/icons/heart.svg') }}" alt="Heart" width="14" class="me-2">
                            Tu satisfacción es nuestra prioridad.
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Opción 3 -->
            <div class="col border-md-start">
                <div class="content-card">
                    <div class="content-icon">
                        <div class="icon-mask" style="-webkit-mask-image: url('{{ asset('img/icons/computer.svg') }}'); mask-image: url('{{ asset('img/icons/computer.svg') }}');"></div>
                    </div>
                    <h3 class="content-title text-main poppins-semibold">10. USO DEL SITIO</h3>
                    <p class="content-text">El contenido de este sitio es propiedad de Pet Threads SRL y está protegido por las leyes de propiedad intelectual. No está permitido su uso sin autorización previa.</p>
                </div>
            </div> 
        </div>

    </section>
    
    <!-- SECCIÓN ¿TENÉS DUDAS? -->
    <section class="theme-coral surface-flat mt-2 position-relative overflow-hidden" style="padding-top:2.5rem; padding-bottom: 2.5rem;">
        <div class="container position-relative">
            <div class="row align-items-center">
                <!-- Bloque de Texto y Botón -->
                <div class="col-lg-5 pb-4 text-center text-lg-start">
                    <h2 class="h2 playfair-display-semibold text-main mb-3">¿Tenés dudas?</h2>
                    <p class="text-secondary mb-4 pe-lg-4">
                        Estamos para ayudarte. Contáctanos por cualquiera de nuestros canales y te responderemos a la brevedad.
                    </p>
                    <a href="{{ url('consultas') }}" class="btn btn-primary rounded-pill px-5 py-3 poppins-bold shadow-sm text-uppercase" style="font-size: 0.9rem;">Escríbenos</a>
                </div>

                <!-- Bloque de Canales de Contacto -->
                <div class="col-lg-4 pb-4">
                    <div class="d-flex flex-column gap-4">
                        <!-- WhatsApp -->
                        <div class="contact-item justify-content-center justify-content-lg-start">
                            <div class="icon-box">
                                <img src="{{ asset('img/icons/whatsapp.svg') }}" alt="WhatsApp" width="22">
                            </div>
                            <div class="contact-info text-start">
                                <span class="contact-label">WhatsApp</span>
                                <span class="contact-value">+54 9 379 4123 456</span>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="contact-item justify-content-center justify-content-lg-start">
                            <div class="icon-box">
                                <img src="{{ asset('img/icons/email.svg') }}" alt="Email" width="22">
                            </div>
                            <div class="contact-info text-start">
                                <span class="contact-label">Email</span>
                                <span class="contact-value">hola@petthreads.com.ar</span>
                            </div>
                        </div>

                        <!-- Instagram -->
                        <div class="contact-item justify-content-center justify-content-lg-start">
                            <div class="icon-box">
                                <img src="{{ asset('img/icons/instagram.svg') }}" alt="Instagram" width="22">
                            </div>
                            <div class="contact-info text-start">
                                <span class="contact-label">Instagram</span>
                                <span class="contact-value">@petthreads.ok</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endsection
