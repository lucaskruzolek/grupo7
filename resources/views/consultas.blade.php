@extends('layout')
@section('contenido')

    <section class="theme-neutral surface-card overflow-hidden p-0 ps-md-5 pt-2">
        <div class="row g-0 align-items-center w-100 m-0">
            <!-- Bloque de Texto (40%) -->
            <div class="col-md-6 ps-4 pe-2 banner-content">
                <h1 class="banner-title banner-title-nowrap mb-4 position-relative d-inline-block">Estamos aqui para ayudarte <span class="paw-icon"></span>
                </h1>
                
                <p class="banner-subtitle">
                    ¿Tienes dudas sobre nuestros productos, envios o tus pedidos? Completá el formlario y nuestro equipo te responderá a la brevedad.
                </p>
            </div>
 
            <!-- Bloque de Imagen (60%) -->
            <div class="col-md-6 align-self-stretch">
                <img src="{{ asset('img/ui/consultas/portadaconsultas.png') }}" 
                     alt="Portada Consultas" 
                     class="img-fluid w-100 img-fade-left" 
                     style="display: block;">
            </div>
        </div>
    </section>
    <section>
        <div class="container my-5">
    <div class="row g-4">
        
        <div class="col-md-5">
            <div class="d-flex flex-column h-100">
                
                <div class="p-4 mb-4 rounded shadow-sm" style="background-color: var(--surface-card); border: 1px solid var(--border-color);">
                    <h2 class="h4 mb-4" style="color: var(--text-main-title);">Otros medios de contacto</h2>

                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/ui/contacto/icon-phone.png') }}" alt="Tel" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--brand-primary);">WhatsApp</p>
                            <p class="mb-0" style="color: var(--text-secondary);">+54 3782-123456</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('img/ui/contacto/icon-email.png') }}" alt="Email" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--brand-primary);">Email</p>
                            <p class="mb-0" style="color: var(--text-secondary);">contacto@petthreads.com.ar</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/ui/contacto/icon-phone.png') }}" alt="Tel" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--brand-primary);">Facebook</p>
                            <p class="mb-0" style="color: var(--text-secondary);">/@Petthreads.ok</p>
                        </div>
                    </div>                   
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/ui/contacto/icon-phone.png') }}" alt="Tel" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--brand-primary);">Instagram</p>
                            <p class="mb-0" style="color: var(--text-secondary);">@Petthreads.ok</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('img/ui/contacto/icon-phone.png') }}" alt="Tel" class="me-3" style="width: 24px; height: 24px;">
                        <div>
                            <p class="mb-0 fw-bold" style="color: var(--brand-primary);">Horarios de atención</p>
                            <p class="mb-0" style="color: var(--text-secondary);">Lunes a Viernes de 9.00 a 18.00hs, Sabados de 9.00 a 13.00hs</p>
                        </div>
                    </div>


                </div>

                <div class="p-4 rounded shadow-sm flex-grow-1" style="background-color: var(--surface-neutral); border: 1px solid var(--border-color);">
                    <h2 class="h4 mb-4" style="color: var(--text-main-title);">Otros medios de contactos</h2>
                    <div class="d-flex flex-column gap-3">
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="p-3 mb-3 rounded shadow-sm d-flex align-items-center" style="background-color: var(--surface-card); border: 1px solid var(--border-color);">
                <img src="{{ asset('img/ui/contacto/icon-location.png') }}" alt="Ubicación" class="me-3" style="width: 32px; height: 32px;">
                <div>
                    <h5 class="mb-1" style="color: var(--text-main-title);">Envianos tu consulta</h5>
                    <p class="mb-0 text-muted small">Completa el formulario y te responderemos lo antes posible.</p>
                </div>
            </div>

            <div class="rounded shadow-sm overflow-hidden" style="border: 1px solid var(--border-color); height: 400px;">
<div class="card shadow-sm border-0 p-4" style="background-color: var(--surface-card); border: 1px solid var(--border-color);"> 
    <h3 class="mb-4 fw-bold" style="color: var(--text-main-title);">Envianos tu consulta</h3>
    
    <form action="#" method="POST">
        
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

        <button type="submit" class="btn w-100 py-2 fw-bold" style="background-color: var(--brand-primary); color: white; border-radius: 8px;">
            Enviar Mensaje
        </button>
    </form>
</div>
            </div>
        </div>

    </div>
</div>
    </section>
    <section class="container my-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4 text-center fw-bold" style="color: var(--text-main-title);">Preguntas Frecuentes</h2>
            
            <div class="accordion accordion-flush shadow-sm rounded" id="accordionFAQ" style="border: 1px solid var(--border-color); overflow: hidden;">
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne" style="color: var(--brand-primary);">
                            ¿Cuáles son los métodos de pago?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body" style="color: var(--text-secondary);">
                            Aceptamos tarjetas de crédito, débito, transferencias bancarias y pagos a través de Mercado Pago.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="color: var(--brand-primary);">
                            ¿Hacen envíos a todo el país?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body" style="color: var(--text-secondary);">
                            Sí, realizamos envíos a todas las provincias de Argentina a través de Correo Argentino y Andreani.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" style="color: var(--brand-primary);">
                            ¿Cuánto tardan en entregar mi pedido?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body" style="color: var(--text-secondary);">
                            El tiempo de entrega estimado es de 3 a 7 días hábiles, dependiendo de tu ubicación.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour" style="color: var(--brand-primary);">
                            ¿Puedo cambiar o devolver un producto?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body" style="color: var(--text-secondary);">
                            ¡Claro! Tienes 30 días para realizar cambios. El producto debe estar en las mismas condiciones en que fue recibido.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive" style="color: var(--brand-primary);">
                            ¿Cómo sé qué talle llevar para mi mascota?
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body" style="color: var(--text-secondary);">
                            En cada producto encontrarás una tabla de talles. Te recomendamos medir el contorno del cuello y pecho de tu mascota.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix" style="color: var(--brand-primary);">
                            ¿Cómo puedo hacer el seguimiento de mi pedido?
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body" style="color: var(--text-secondary);">
                            Una vez despachado, recibirás un mail con el número de seguimiento y el link para rastrearlo en tiempo real.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>





    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
@endsection