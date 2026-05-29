<aside class="admin-sidebar">
    <a href="{{ url('/') }}" class="sidebar-brand">
        <img src="{{ asset('img/logo/favicon.png') }}" alt="Pet Threads Logo" style="height: 32px; object-fit: contain;">
        <span class="fw-bold h5 mb-0 text-white" style="letter-spacing: 0.5px;">Pet Threads</span>
    </a>
    
    <ul class="sidebar-menu">
        <li class="sidebar-item {{ Request::is('admin/dashboard') ? 'active' : '' }}">
            <a href="{{ url('/admin/dashboard') }}">
                <span>📊</span> Dashboard
            </a>
        </li>
        <li class="sidebar-item {{ Request::is('admin/productos*') || Request::is('productos*') ? 'active' : '' }}">
            <a href="{{ url('/admin/productos') }}">
                <span>👕</span> Productos
            </a>
        </li>
        <li class="sidebar-item {{ Request::is('admin/categorias*') || Request::is('categorias*') ? 'active' : '' }}">
            <a href="{{ url('/admin/categorias') }}">
                <span>📁</span> Categorías
            </a>
        </li>
        <li class="sidebar-item {{ Request::is('admin/pedidos*') ? 'active' : '' }}">
            <a href="{{ url('/admin/pedidos') }}">
                <span>📦</span> Pedidos
            </a>
        </li>
        <li class="sidebar-item {{ Request::is('admin/consultas*') ? 'active' : '' }}">
            <a href="{{ url('/admin/consultas') }}">
                <span>💬</span> Consultas
            </a>
        </li>
        <li class="sidebar-item {{ Request::is('admin/clientes*') ? 'active' : '' }}">
            <a href="{{ url('/admin/clientes') }}">
                <span>👥</span> Clientes
            </a>
        </li>
    </ul>
    
    <div class="sidebar-footer">
        <div class="d-flex align-items-center justify-content-between">
            <div class="user-info text-truncate me-2">
                <small class="d-block text-muted" style="font-size: 0.75rem;">Sesión de</small>
                <span class="fw-bold text-white small" title="{{ Auth::user()->nombre ?? 'Administrador' }}">
                    {{ Auth::user()->nombre ?? 'Administrador' }}
                </span>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn-logout btn btn-sm btn-link text-white-50 p-0 border-0" title="Cerrar Sesión">
                    🚪
                </button>
            </form>
        </div>
    </div>
</aside>
