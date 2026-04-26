<footer class="footer-main py-5 mt-auto">
    <div class="container">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-5 py-3">
            <!-- Columna 1: Logo y Copyright -->
            <div class="col mb-3">
                <a href="{{ url('/') }}" class="d-flex align-items-center mb-3 link-body-emphasis text-decoration-none">
                    <img src="{{ asset('img/logo/logo.webp') }}" alt="Pet Threads" class="footer-logo">
                </a>
                <p class="footer-copyright">&copy; {{ date('Y') }} Pet Threads.</p>
                
                <!-- Social Links -->
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="d-inline-flex align-items-center justify-content-center rounded-circle footer-social-link">
                        <div class="icon-mask footer-social-icon" style="-webkit-mask-image: url('{{ asset('img/icons/instagram.svg') }}'); mask-image: url('{{ asset('img/icons/instagram.svg') }}');"></div>
                    </a>
                    <a href="#" class="d-inline-flex align-items-center justify-content-center rounded-circle footer-social-link">
                        <div class="icon-mask footer-social-icon" style="-webkit-mask-image: url('{{ asset('img/icons/facebook.svg') }}'); mask-image: url('{{ asset('img/icons/facebook.svg') }}');"></div>
                    </a>
                    <a href="#" class="d-inline-flex align-items-center justify-content-center rounded-circle footer-social-link">
                        <div class="icon-mask footer-social-icon" style="-webkit-mask-image: url('{{ asset('img/icons/whatsapp.svg') }}'); mask-image: url('{{ asset('img/icons/whatsapp.svg') }}');"></div>
                    </a>
                    <a href="mailto:info@petthreads.com" class="d-inline-flex align-items-center justify-content-center rounded-circle footer-social-link">
                        <div class="icon-mask footer-social-icon" style="-webkit-mask-image: url('{{ asset('img/icons/mail.svg') }}'); mask-image: url('{{ asset('img/icons/mail.svg') }}');"></div>
                    </a>
                </div>
            </div>

            <!-- Columna 2: Espaciador -->
            <div class="col mb-3"></div>

            <!-- Columna 3: Tienda -->
            <div class="col mb-3">
                <h6 class="poppins-bold text-uppercase mb-4 footer-heading">Tienda</h6>
                <ul class="nav flex-column gap-2">
                    <li class="nav-item"><a href="#" class="footer-link p-0">Perros</a></li>
                    <li class="nav-item"><a href="#" class="footer-link p-0">Gatos</a></li>
                    <li class="nav-item"><a href="#" class="footer-link p-0">Accesorios</a></li>
                    <li class="nav-item"><a href="#" class="footer-link p-0">Nuevos Arribos</a></li>
                    <li class="nav-item"><a href="#" class="footer-link p-0">Ofertas</a></li>
                </ul>
            </div>

            <!-- Columna 4: Ayuda -->
            <div class="col mb-3">
                <h6 class="poppins-bold text-uppercase mb-4 footer-heading">Ayuda</h6>
                <ul class="nav flex-column gap-2">
                    <li class="nav-item"><a href="#" class="footer-link p-0">Comercialización</a></li>
                    <li class="nav-item"><a href="#" class="footer-link p-0">Términos y Condiciones</a></li>
                    <li class="nav-item"><a href="#" class="footer-link p-0">Contacto</a></li>
                </ul>
            </div>

            <!-- Columna 5: Nosotros -->
            <div class="col mb-3">
                <h6 class="poppins-bold text-uppercase mb-4 footer-heading">Nosotros</h6>
                <ul class="nav flex-column gap-2">
                    <li class="nav-item"><a href="#" class="footer-link p-0">Quiénes somos</a></li>
                    <li class="nav-item"><a href="#" class="footer-link p-0">Nuestra Historia</a></li>
                    <li class="nav-item"><a href="#" class="footer-link p-0">Nuestra Visión</a></li>
                    <li class="nav-item"><a href="#" class="footer-link p-0">Trabaja con Nosotros</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>