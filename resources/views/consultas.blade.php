@extends('layout')
@section('contenido')

    <section class="banner-hero theme-neutral">
        <div class="banner-grid">
            <!-- Bloque de Texto (40%) -->
            <div class="banner-content">
                <h1 class="banner-title mb-4 position-relative d-inline-block anim-fade-down" style="--anim-order: 1;">Estamos aquí para ayudarte <span class="paw-icon"></span>
                </h1>
                
                <p class="banner-subtitle anim-fade-down" style="--anim-order: 2;">
                    ¿Tenés dudas sobre nuestros productos, envíos o tus pedidos? Completá el formulario y nuestro equipo te responderá a la brevedad.
                </p>
            </div>
 
            <!-- Bloque de Imagen (60%) -->
            <div class="banner-img-container anim-fade-down">
                <img src="{{ asset('img/ui/consultas/portada.webp') }}" 
                     alt="Portada Consultas" 
                     class="banner-img">
            </div>
        </div>
    </section>
    <section>
        <div class="container my-5">
    <div class="row g-4">
        
        <div class="col-md-5">
            <div class="d-flex flex-column h-100">
                
                <div class="p-4 mb-4 rounded shadow-sm" style="background-color: var(--green-200); border: 1px solid var(--border-color);">
                    <h2 class="h4 mb-4" style="color: var(--text-main-title);">Otros medios de contacto</h2>

                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/icons/whatsapp.svg') }}" alt="Tel" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--color-primary);">WhatsApp</p>
                            <p class="mb-0" style="color: var(--text-secondary);">+54 3782-123456</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('img/icons/email.svg') }}" alt="Email" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--color-primary);">Email</p>
                            <p class="mb-0" style="color: var(--text-secondary);">contacto@petthreads.com.ar</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/icons/facebook.svg') }}" alt="Tel" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--color-primary);">Facebook</p>
                            <p class="mb-0" style="color: var(--text-secondary);">/@Petthreads.ok</p>
                        </div>
                    </div>                   
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/icons/instagram.svg') }}" alt="Tel" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--color-primary);">Instagram</p>
                            <p class="mb-0" style="color: var(--text-secondary);">@Petthreads.ok</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/icons/clock.svg') }}" alt="Tel" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--color-primary);">Horarios de atención</p>
                            <p class="mb-0" style="color: var(--text-secondary);">Lunes a Viernes de 9.00 a 18.00hs, Sabados de 9.00 a 13.00hs</p>
                        </div>
                    </div>


                </div>

                <div class="p-4 surfacerounded shadow-sm flex-grow-1" style="background-color: var(--coral-200); border: 1px solid var(--border-color);">
                    <h2 class="h4 mb-4" style="color: var(--text-main-title);">Tu satisfaccion es nuestra prioridad</h2>
                    <p class="mb-0 fw-bold" style="color: var(--brand-primary);">Nos comprometemos a brindarte una atención rápida, cercana y personalizada.</p>
                    <div class="d-flex flex-column gap-3">
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="p-3 mb-3 rounded shadow-sm d-flex align-items-center" style="background-color: var(--coral-200); border: 1px solid var(--border-color);">
                <img src="{{ asset('img/icons/headphones.svg') }}" alt="Ubicación" class="me-3" style="width: 32px; height: 32px;">
                <div>
                    <h5 class="mb-1 text-center" style="color: var(--text-main-title);">Contactate con nosotros</h5>
                    <p class="mb-0 text-muted small">Completa el formulario y te responderemos lo antes posible.</p>
                </div>
            </div>

            <div class="card shadow-sm border-0 p-4 rounded" style="background-color: #fff; border: 1px solid var(--border-color);">
    <h3 class="mb-4 fw-bold text-center" style="color: var(--text-main-title);">Envianos tu consulta</h3>
    
    <form action="/consultas" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label fw-bold" style="color: var(--text-secondary);">Nombre y Apellido</label>
                <input type="text" class="form-control shadow-none" id="nombre" name="nombre" placeholder="Ej: Juan Pérez" required style="border-color: var(--border-subtle);">
            </div>

            <div class="col-md-6 mb-3">
                <label for="email" class="form-label fw-bold" style="color: var(--text-secondary);">Email</label>
                <input type="email" class="form-control shadow-none" id="email" name="email" placeholder="nombre@ejemplo.com" required style="border-color: var(--border-subtle);">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="telefono" class="form-label fw-bold" style="color: var(--text-secondary);">Teléfono</label>
                <input type="tel" class="form-control shadow-none" id="telefono" name="telefono" placeholder="Cod. área + número">
            </div>

            <div class="col-md-6 mb-3">
                <label for="pedido" class="form-label fw-bold" style="color: var(--text-secondary);">Número de pedido (opcional)</label>
                <input type="text" class="form-control shadow-none" id="pedido" name="pedido" placeholder="#12345">
            </div>
        </div>

        <div class="mb-3">
            <label for="asunto" class="form-label fw-bold" style="color: var(--text-secondary);">Asunto</label>
            <select class="form-select shadow-none" id="asunto" name="asunto" required>
                <option value="" selected disabled>Seleccioná una opción</option>
                <option value="consulta">Consulta general</option>
                <option value="reclamo">Reclamo por pedido</option>
                <option value="devolucion">Devoluciones</option>
                <option value="otro">Otro</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="mensaje" class="form-label fw-bold" style="color: var(--text-secondary);">Mensaje</label>
            <textarea class="form-control shadow-none" id="mensaje" name="mensaje" rows="4" placeholder="Escribí tu mensaje aquí..." required></textarea>
        </div>

        <button type="submit" class="btn w-100 py-2 fw-bold btn-nature">
            Enviar Mensaje
        </button>
    </form>
    
            </div>
        </div>
    </div>
</div>
</section>
  
<section class="container my-5">
    <h2 class="mb-5 text-center fw-bold" style="color: var(--text-main-title);">Preguntas Frecuentes</h2>
    
    <div class="row g-4">
        
        <div class="col-md-6">
            <div class="accordion accordion-flush shadow-sm rounded border" id="accordionFAQLeft">
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" style="color: var(--brand-primary);">
                            <img src="{{ asset('img/icons/credit-card.svg') }}" alt="Pago" class="me-3" style="width: 24px; height: 24px;">
                            ¿Cuáles son los métodos de pago?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFAQLeft">
                        <div class="accordion-body" style="color: var(--text-secondary);">
                            Aceptamos tarjetas de crédito, débito y Mercado Pago.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" style="color: var(--brand-primary);">
                            <img src="{{ asset('img/icons/delivery-truck-fast.svg') }}" alt="Envío" class="me-3" style="width: 24px; height: 24px;">
                            ¿Hacen envíos a todo el país?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFAQLeft">
                        <div class="accordion-body" style="color: var(--text-secondary);">
                            Sí, llegamos a toda Argentina vía Correo Argentino.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" style="color: var(--brand-primary);">
                            <img src="{{ asset('img/icons/hourglass-svgrepo-com.svg') }}" alt="Tiempo" class="me-3" style="width: 24px; height: 24px;">
                            ¿Cuánto tardan en entregar mi pedido?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFAQLeft">
                        <div class="accordion-body" style="color: var(--text-secondary);">
                            De 3 a 7 días hábiles según la zona.
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-6">
            <div class="accordion accordion-flush shadow-sm rounded border" id="accordionFAQRight">
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" style="color: var(--brand-primary);">
                            <img src="{{ asset('img/icons/shopping-bag.svg') }}" alt="Cambio" class="me-3" style="width: 24px; height: 24px;">
                            ¿Puedo cambiar o devolver un producto?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionFAQRight">
                        <div class="accordion-body" style="color: var(--text-secondary);">
                            Sí, tienes 30 días para realizar cambios.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" style="color: var(--brand-primary);">
                            <img src="{{ asset('img/icons/paw.svg') }}" alt="Talles" class="me-3" style="width: 24px; height: 24px;">
                            ¿Cómo sé qué talle llevar para mi mascota?
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionFAQRight">
                        <div class="accordion-body" style="color: var(--text-secondary);">
                            Consulta nuestra tabla de talles en la descripción de cada producto.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" style="color: var(--brand-primary);">
                            <img src="{{ asset('img/icons/route.svg') }}" alt="Seguimiento" class="me-3" style="width: 24px; height: 24px;">
                            ¿Cómo puedo hacer el seguimiento de mi pedido?
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" data-bs-parent="#accordionFAQRight">
                        <div class="accordion-body" style="color: var(--text-secondary);">
                            Te enviaremos un código de seguimiento por email una vez despachado.
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>







    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
@endsection