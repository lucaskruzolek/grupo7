<aside class="admin-sidebar" id="admin-sidebar">
    <script>
        // Evitar parpadeo (FOIC) aplicando la clase de inmediato antes de pintar la UI
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.getElementById('admin-sidebar').classList.add('collapsed');
        }
    </script>
    <a href="{{ url('/') }}" class="sidebar-brand">
        <img src="{{ asset('img/logo/favicon.png') }}" alt="Pet Threads Logo" style="height: 32px; object-fit: contain;">
        <span class="fw-bold h3 mb-0" style="letter-spacing: 0.5px;">PetThreads</span>
    </a>
    
    <!-- Botón de Colapsar Flotante -->
    <button id="toggle-sidebar-btn" class="sidebar-toggle-btn" aria-label="Colapsar menú">
        <img src="{{ asset('img/icons/chevron-left.svg') }}" id="sidebar-toggle-icon" alt="Toggle">
        <script>
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                document.getElementById('sidebar-toggle-icon').src = "{{ asset('img/icons/chevron-right.svg') }}";
            }
        </script>
    </button>
    
    <ul class="sidebar-menu">
        <!-- Dashboard -->
        <li class="sidebar-item {{ Request::is('admin/dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
            <a href="{{ url('/admin/dashboard') }}">
                <span class="sidebar-icon" style="-webkit-mask-image: url('{{ asset('img/icons/reporte.svg') }}'); mask-image: url('{{ asset('img/icons/reporte.svg') }}');"></span>
                <span class="sidebar-text">Dashboard</span>
            </a>
        </li>
        
        <!-- Catálogo (Colapsable) -->
        @php
            $isCatalogActive = Request::is('admin/productos*') || Request::is('productos*') || Request::is('admin/categorias*') || Request::is('categorias*') || Request::is('admin/colecciones*') || Request::is('colecciones*');
        @endphp
        <li class="sidebar-item has-submenu {{ $isCatalogActive ? 'open submenu-active' : '' }}" data-tooltip="Catálogo">
            <a href="#" class="submenu-toggle" onclick="toggleSubmenu(event, this)">
                <span class="sidebar-icon" style="-webkit-mask-image: url('{{ asset('img/icons/catalogo.svg') }}'); mask-image: url('{{ asset('img/icons/catalogo.svg') }}');"></span>
                <span class="sidebar-text">Catálogo</span>
                <img src="{{ asset('img/icons/chevron-right.svg') }}" class="submenu-arrow" alt="Chevron">
            </a>
            <ul class="sidebar-submenu">
                <li class="{{ Request::is('admin/productos*') || Request::is('productos*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/productos') }}">
                        <span class="sidebar-text">Productos</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/categorias*') || Request::is('categorias*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/categorias') }}">
                        <span class="sidebar-text">Categorías</span>
                    </a>
                </li>
                <li class="{{ Request::is('admin/colecciones*') || Request::is('colecciones*') ? 'active' : '' }}">
                    <a href="{{ url('/admin/colecciones') }}">
                        <span class="sidebar-text">Colecciones</span>
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Ventas -->
        <li class="sidebar-item {{ Request::is('admin/ventas*') ? 'active' : '' }}" data-tooltip="Ventas">
            <a href="{{ url('/admin/ventas') }}">
                <span class="sidebar-icon" style="-webkit-mask-image: url('{{ asset('img/icons/pedidos.svg') }}'); mask-image: url('{{ asset('img/icons/pedidos.svg') }}');"></span>
                <span class="sidebar-text">Ventas</span>
            </a>
        </li>
        
        <!-- Consultas -->
        <li class="sidebar-item {{ Request::is('admin/consultas*') ? 'active' : '' }}" data-tooltip="Consultas">
            <a href="{{ url('/admin/consultas') }}">
                <span class="sidebar-icon" style="-webkit-mask-image: url('{{ asset('img/icons/consultas.svg') }}'); mask-image: url('{{ asset('img/icons/consultas.svg') }}');"></span>
                <span class="sidebar-text">Consultas</span>
            </a>
        </li>
        
        <!-- Clientes -->
        <li class="sidebar-item {{ Request::is('admin/clientes*') ? 'active' : '' }}" data-tooltip="Clientes">
            <a href="{{ url('/admin/clientes') }}">
                <span class="sidebar-icon" style="-webkit-mask-image: url('{{ asset('img/icons/clientes.svg') }}'); mask-image: url('{{ asset('img/icons/clientes.svg') }}');"></span>
                <span class="sidebar-text">Clientes</span>
            </a>
        </li>
    </ul>
    
    
    <!-- Footer de Usuario -->
    <div class="sidebar-footer">
        <div class="d-flex align-items-center justify-content-between">
            <div class="user-info text-truncate me-2">
                <small class="d-block text-muted" style="font-size: 0.75rem;">Sesión de</small>
                <span class="fw-bold text-dark small" title="{{ Auth::user()->nombre ?? 'Administrador' }}">
                    {{ Auth::user()->nombre ?? 'Administrador' }}
                </span>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn-logout btn btn-sm btn-link text-muted p-0 border-0 d-flex align-items-center justify-content-center" title="Cerrar Sesión">
                    <span class="sidebar-icon" style="-webkit-mask-image: url('{{ asset('img/icons/exit.svg') }}'); mask-image: url('{{ asset('img/icons/exit.svg') }}');"></span>
                </button>
            </form>
        </div>
    </div>
</aside>

<script>
    // Toggle para Submenús colapsables
    function toggleSubmenu(event, element) {
        event.preventDefault();
        const sidebar = document.querySelector('.admin-sidebar');
        const parent = element.parentElement;
        
        // Si el sidebar está colapsado, lo descolapsamos primero
        // y diferimos la apertura del submenú hasta que termine la transición
        if (sidebar.classList.contains('collapsed')) {
            sidebar.classList.remove('collapsed');
            localStorage.setItem('sidebar-collapsed', 'false');
            
            const toggleIcon = document.getElementById('sidebar-toggle-icon');
            if (toggleIcon) {
                toggleIcon.src = "{{ asset('img/icons/chevron-left.svg') }}";
            }
            
            const content = document.querySelector('.admin-content');
            if (content) content.classList.remove('sidebar-collapsed');

            // Esperar a que la transición de expansión termine antes de abrir el submenú
            const onExpanded = () => {
                sidebar.removeEventListener('transitionend', onExpanded);
                clearTimeout(fallback);
                parent.classList.add('open');
            };
            // Timeout de seguridad en caso de que transitionend no se dispare
            const fallback = setTimeout(onExpanded, 350);
            sidebar.addEventListener('transitionend', onExpanded, { once: true });
            
            return; // No hacer toggle inmediato
        }
        
        parent.classList.toggle('open');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.admin-sidebar');
        const toggleBtn = document.getElementById('toggle-sidebar-btn');
        const toggleIcon = document.getElementById('sidebar-toggle-icon');
        const content = document.querySelector('.admin-content');
        
        // Restaurar estado de colapso de la sesión anterior
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            sidebar.classList.add('collapsed');
            if (content) content.classList.add('sidebar-collapsed');
            if (toggleIcon) {
                toggleIcon.src = "{{ asset('img/icons/chevron-right.svg') }}";
            }
        }

        // Evento de clic para colapsar/descolapsar
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebar-collapsed', isCollapsed);
                
                if (content) {
                    if (isCollapsed) {
                        content.classList.add('sidebar-collapsed');
                    } else {
                        content.classList.remove('sidebar-collapsed');
                    }
                }
                
                if (toggleIcon) {
                    if (isCollapsed) {
                        toggleIcon.src = "{{ asset('img/icons/chevron-right.svg') }}";
                    } else {
                        toggleIcon.src = "{{ asset('img/icons/chevron-left.svg') }}";
                    }
                }
            });
        }
    });
</script>
