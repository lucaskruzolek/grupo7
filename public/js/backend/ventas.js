document.addEventListener('DOMContentLoaded', function () {
    const tableRows = document.querySelectorAll('.orders-table tbody tr');
    const searchInput = document.getElementById('search-pedido');
    const filterEstado = document.getElementById('filter-estado');
    const filterPago = document.getElementById('filter-pago');

    const detailPanel = document.querySelector('.orders-detail-panel');
    const detailEmpty = document.getElementById('detail-empty');
    const detailContent = document.getElementById('detail-content');

    // DOM Elements in Detail Drawer
    const detOrderId = document.getElementById('det-order-id');
    const detClienteNombre = document.getElementById('det-cliente-nombre');
    const detClienteEmail = document.getElementById('det-cliente-email');
    const detFormaPago = document.getElementById('det-forma-pago');
    const detTotal = document.getElementById('det-total');
    const detItemsContainer = document.getElementById('det-items-container');
    const detBtnFactura = document.getElementById('det-btn-factura');
    const detFormEstado = document.getElementById('det-form-estado');
    const detInputEstado = document.getElementById('det-input-estado');
    const detBtnSubmitEstado = document.getElementById('det-btn-submit-estado');

    // Timeline elements
    const tlCarritoDate = document.getElementById('tl-carrito-date');
    const tlPagoItem = document.getElementById('tl-pago-item');
    const tlPagoDate = document.getElementById('tl-pago-date');
    const tlDespachoItem = document.getElementById('tl-despacho-item');
    const tlDespachoDate = document.getElementById('tl-despacho-date');

    // ── 1. CLICK DE FILA: CARGA DE DETALLE VÍA AJAX ─────────────────────
    tableRows.forEach(row => {
        row.addEventListener('click', function () {
            // Activar fila
            tableRows.forEach(r => r.classList.remove('row-active'));
            this.classList.add('row-active');

            const orderId = this.dataset.orderId;
            cargarDetallePedido(orderId);
        });
    });

    function cargarDetallePedido(id) {
        // Spinner o feedback de carga
        detailEmpty.classList.add('d-none');
        detailContent.classList.add('opacity-50');

        fetch(`/admin/ventas/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            detailContent.classList.remove('opacity-50');
            if (data.success) {
                const p = data.pedido;

                // Cargar datos en la UI del panel de detalles
                detOrderId.innerText = `#${p.id.toString().padStart(6, '0')}`;
                detClienteNombre.innerText = p.cliente.nombre;
                detClienteEmail.innerText = p.cliente.email;
                detFormaPago.innerText = p.forma_pago;
                detTotal.innerText = `$${parseFloat(p.total).toLocaleString('es-AR', { minimumFractionDigits: 2 })}`;

                // Renderizar items comprados
                detItemsContainer.innerHTML = '';
                p.detalles.forEach(item => {
                    const thumb = item.imagen ? item.imagen : '/img/ui/productos/perro-buzo-verde.webp';
                    const priceFormatted = parseFloat(item.precio_unitario).toLocaleString('es-AR', { minimumFractionDigits: 2 });
                    
                    detItemsContainer.innerHTML += `
                        <div class="detail-item-row">
                            <img src="${thumb}" class="detail-item-thumb" alt="${item.producto}">
                            <div class="detail-item-info">
                                <div class="detail-item-name" title="${item.producto}">${item.producto}</div>
                                <div class="detail-item-meta">Talle: ${item.talle} | Color: ${item.color} | Cant: ${item.cantidad}</div>
                            </div>
                            <div class="detail-item-price">$${priceFormatted}</div>
                        </div>
                    `;
                });

                // Renderizar Línea de Tiempo
                tlCarritoDate.innerText = p.created_at;
                
                // Confirmación de pago (si está en pedidos, ya está pagado)
                tlPagoItem.classList.add('completed');
                tlPagoDate.innerText = p.fecha_venta ? p.fecha_venta : '';

                // Despacho
                if (p.estado === 'DESPACHADO') {
                    tlDespachoItem.classList.remove('active');
                    tlDespachoItem.classList.add('completed');
                    tlDespachoDate.innerText = p.fecha_despacho;

                    // Formulario de estado (permitir revertir)
                    detInputEstado.value = 'CONFIRMADO';
                    detBtnSubmitEstado.innerHTML = '🚚 Revertir despacho';
                    detBtnSubmitEstado.className = 'btn-admin btn-admin-secondary w-100 mt-3';
                } else {
                    tlDespachoItem.classList.remove('completed');
                    tlDespachoItem.classList.add('active');
                    tlDespachoDate.innerText = 'Pendiente de despacho';

                    // Formulario de estado (permitir despachar)
                    detInputEstado.value = 'DESPACHADO';
                    detBtnSubmitEstado.innerHTML = '📦 Marcar como Despachado';
                    detBtnSubmitEstado.className = 'btn-admin btn-admin-primary w-100 mt-3';
                }

                // Configurar Botón de Factura
                detBtnFactura.href = `/admin/ventas/${p.id}/factura`;

                // Configurar Acción de Formulario de Estado
                detFormEstado.action = `/admin/ventas/${p.id}/estado`;

                // Mostrar panel de detalle
                detailContent.classList.remove('d-none');
            } else {
                detailEmpty.classList.remove('d-none');
                detailContent.classList.add('d-none');
            }
        })
        .catch(err => {
            detailContent.classList.remove('opacity-50');
            console.error('Error al cargar detalles del pedido:', err);
            alert('No se pudieron recuperar los detalles del pedido.');
        });
    }

    // ── 2. FILTRADO CLIENT-SIDE EN TIEMPO REAL ──────────────────────────
    function aplicarFiltros() {
        const query = searchInput.value.toLowerCase().trim();
        const estadoVal = filterEstado.value;
        const pagoVal = filterPago.value;

        tableRows.forEach(row => {
            const id = row.dataset.orderId;
            const cliente = row.querySelector('.client-name').innerText.toLowerCase();
            const email = row.querySelector('.client-email').innerText.toLowerCase();
            const estado = row.dataset.estado;
            const pagoId = row.dataset.pagoId;

            // Filtro de búsqueda texto (por ID de pedido o nombre/email de cliente)
            const matchSearch = id.includes(query) || cliente.includes(query) || email.includes(query);

            // Filtro de estado
            const matchEstado = (estadoVal === 'all') || (estado === estadoVal);

            // Filtro de método de pago
            const matchPago = (pagoVal === 'all') || (pagoId === pagoVal);

            if (matchSearch && matchEstado && matchPago) {
                row.classList.remove('d-none');
            } else {
                row.classList.add('d-none');
                // Si la fila activa se oculta, cerrar el detalle
                if (row.classList.contains('row-active')) {
                    row.classList.remove('row-active');
                    detailContent.classList.add('d-none');
                    detailEmpty.classList.remove('d-none');
                }
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', aplicarFiltros);
    if (filterEstado) filterEstado.addEventListener('change', aplicarFiltros);
    if (filterPago) filterPago.addEventListener('change', aplicarFiltros);

    // Auto-seleccionar primer pedido de la lista al cargar si existe
    if (tableRows.length > 0) {
        tableRows[0].click();
    }
});
