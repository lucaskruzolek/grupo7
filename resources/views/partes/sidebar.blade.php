      
<aside class="col-md-3 bg-light border-end min-vh-100 p-4">
            <div class="mb-4">
    <h4 class="fw-bold" style="color: var(--text-main-title);">Filtros</h4>
    <hr style="color: var(--border-color);">
</div>

<div class="filter-section">
    <h6 class="text-uppercase fw-bold mb-3" style="color: var(--text-secondary); font-size: 0.8rem; letter-spacing: 1px;">
        Categorías
    </h6>
    
    <div class="d-flex flex-column gap-2">
        
        <button class="btn btn-light d-flex align-items-center text-start p-2 border-0 bg-transparent shadow-none hover-filter">
            <img src="{{ asset('img/ui/productos/icon-dog-clothes.png') }}" alt="Perros" class="me-3" style="width: 24px; height: 24px;">
            <span style="color: var(--brand-primary); font-weight: 500;">Ropa para Perros</span>
        </button>

        <button class="btn btn-light d-flex align-items-center text-start p-2 border-0 bg-transparent shadow-none hover-filter">
            <img src="{{ asset('img/ui/productos/icon-cat-clothes.png') }}" alt="Gatos" class="me-3" style="width: 24px; height: 24px;">
            <span style="color: var(--brand-primary); font-weight: 500;">Ropa para Gatos</span>
        </button>

        <button class="btn btn-light d-flex align-items-center text-start p-2 border-0 bg-transparent shadow-none hover-filter">
            <img src="{{ asset('img/ui/productos/icon-dog-acc.png') }}" alt="Acc. Perros" class="me-3" style="width: 24px; height: 24px;">
            <span style="color: var(--brand-primary); font-weight: 500;">Accesorios Perros</span>
        </button>

        <button class="btn btn-light d-flex align-items-center text-start p-2 border-0 bg-transparent shadow-none hover-filter">
            <img src="{{ asset('img/ui/productos/icon-cat-acc.png') }}" alt="Acc. Gatos" class="me-3" style="width: 24px; height: 24px;">
            <span style="color: var(--brand-primary); font-weight: 500;">Accesorios Gatos</span>
        </button>

        <button class="btn btn-light d-flex align-items-center text-start p-2 border-0 bg-transparent shadow-none hover-filter">
            <img src="{{ asset('img/ui/productos/icon-toys.png') }}" alt="Juguetes" class="me-3" style="width: 24px; height: 24px;">
            <span style="color: var(--brand-primary); font-weight: 500;">Juguetes</span>
        </button>

    </div>
</div>

<div class="mt-5">
    <button class="btn btn-outline-secondary btn-sm w-100 rounded-pill">
        Limpiar Filtros
    </button>
</div>

</aside>
