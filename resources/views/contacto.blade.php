@extends('layout')
@section('contenido')

    <section class="banner-hero theme-neutral">
        <div class="banner-grid">
            <!-- Bloque de Texto (40%) -->
            <div class="banner-content">
                <h1 class="banner-title mb-4 position-relative d-inline-block anim-fade-down" style="--anim-order: 1;">Estamos aquí para ayudarte <span class="paw-icon"></span>
                </h1>
                
                <p class="banner-subtitle anim-fade-down" style="--anim-order: 2;">
                    ¿Tienes dudas sobre nuestros productos, envíos o tus pedidos? Nuestro equipo estará encantado de atenderte
                </p>
            </div>
 
            <!-- Bloque de Imagen (60%) -->
            <div class="banner-img-container anim-fade-down">
                <img src="{{ asset('img/ui/contactos/portadacontacto.png') }}" 
                     alt="Portada Contactos" 
                     class="banner-img">
            </div>
        </div>
    </section>
    <section>
        <div class="container my-5">
    <div class="row g-4">
        
        <div class="col-md-5">
            <div class="d-flex flex-column h-100">
                
                <div class="p-4 mb-4 rounded shadow-sm" style="background-color: #fff; border: 1px solid var(--border-color);">
                    <h2 class="h4 mb-4" style="color: var(--text-main-title);">Información de la empresa</h2>

                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/icons/titular.svg') }}" alt="Tel" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--brand-primary);">Titular</p>
                            <p class="mb-0" style="color: var(--text-secondary);">Andrea García</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/icons/razon.svg') }}" alt="Tel" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--brand-primary);">Razón social</p>
                            <p class="mb-0" style="color: var(--text-secondary);">Petthread SRL</p>
                        </div>
                    </div>                   
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/icons/cuil.svg') }}" alt="Tel" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--brand-primary);">CUIT</p>
                            <p class="mb-0" style="color: var(--text-secondary);">30-12345678-2</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/icons/domicilio.svg') }}" alt="Tel" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--brand-primary);">Domicilio</p>
                            <p class="mb-0" style="color: var(--text-secondary);">Salta 123</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/icons/telefono.svg') }}" alt="Tel" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--brand-primary);">Teléfono</p>
                            <p class="mb-0" style="color: var(--text-secondary);">+54 3794 000000</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <img src="{{ asset('img/icons/email.svg') }}" alt="Email" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--brand-primary);">Email</p>
                            <p class="mb-0" style="color: var(--text-secondary);">contacto@petthreads.com.ar</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded shadow-sm flex-grow-1" style="background-color: #fff; border: 1px solid var(--border-color);">
                    <h2 class="h4 mb-4" style="color: var(--text-main-title);">Otros medios de contacto</h2>
                    <div class="d-flex flex-column gap-3">
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <img src="{{ asset('img/icons/instagram.svg') }}" alt="IG" class="me-2" style="width: 20px; height: 20px;">
                            <p class="mb-0 fw-bold" style="color: var(--brand-primary);">Instagram</p>
                            <p class="mb-0" style="color: var(--text-secondary);">@Petthreads.ok</p>
                        </a>
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <img src="{{ asset('img/icons/facebook.svg') }}" alt="FB" class="me-2" style="width: 20px; height: 20px;">
                            <p class="mb-0 fw-bold" style="color: var(--brand-primary);">Facebook</p>
                            <p class="mb-0" style="color: var(--text-secondary);">/@Petthreads.ok</p>
                        </a>
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <img src="{{ asset('img/icons/whatsapp.svg') }}" alt="FB" class="me-2" style="width: 20px; height: 20px;">
                            <p class="mb-0 fw-bold" style="color: var(--brand-primary);">WhatsApp</p>
                            <p class="mb-0" style="color: var(--text-secondary);">+54 3782-123456</p>
                        </a>
                        <a href="#" class="d-flex align-items-center text-decoration-none">
                            <img src="{{ asset('img/icons/clock.svg') }}" alt="FB" class="me-2" style="width: 20px; height: 20px;">
                            <p class="mb-0 fw-bold" style="color: var(--brand-primary);">Horarios de atención</p>
                            <p class="mb-0" style="color: var(--text-secondary);">Lunes a Viernes de 9.00 a 18.00hs, Sábados de 9.00 a 13.00hs</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="p-3 mb-3 rounded shadow-sm d-flex align-items-center" style="background-color: var(--coral-200); border: 1px solid var(--border-color);">
                <img src="{{ asset('img/icons/route.svg') }}" alt="Ubicación" class="me-3" style="width: 32px; height: 32px;">
                <div>
                    <h5 class="mb-1" style="color: var(--text-main-title);">¿Dónde estamos?</h5>
                    <p class="mb-0 text-muted small">Nuestro domicilio legal se encuentra en Corrientes, Argentina.</p>
                </div>
            </div>

            <div class="rounded shadow-sm overflow-hidden" style="border: 1px solid var(--border-color); height: 400px;">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d113264.44522967667!2d-58.8351!3d-27.4692!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94456b79d5bed36b%3A0xfa999f51fd9213!2sCorrientes!5e0!3m2!1ses!2sar!4v1650000000000" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>

    </div>
</div>
    </section>





    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
@endsection