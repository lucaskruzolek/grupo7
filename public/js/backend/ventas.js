// 1. Global state for Sales Module
let activeOrder = null;

/**
 * Selects an order row, highlights it visually, and loads its details.
 * @function selectOrder
 * @param {HTMLElement} row - The selected table row.
 */
function selectOrder(row) {
    const tableRows = document.querySelectorAll('.orders-table tbody tr');
    tableRows.forEach(r => r.classList.remove('row-active'));
    row.classList.add('row-active');
    
    const orderId = row.dataset.orderId;
    loadOrderDetails(orderId);
}

/**
 * Handles the asynchronous AJAX loading of order details and manages spinner feedback.
 * @function loadOrderDetails
 * @param {number|string} id - The ID of the order to fetch.
 */
function loadOrderDetails(id) {
    clearOrderDetails();
    
    const detailContent = document.getElementById('detail-content');
    const detailEmpty = document.getElementById('detail-empty');

    detailEmpty.classList.add('d-none');
    detailContent.classList.remove('d-none');
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
            const order = data.pedido;
            activeOrder = order;

            renderOrderDetails(order);
            renderOrderItems(order.detalles);
            renderOrderTimeline(order);
        } else {
            clearOrderDetails();
            alert('No se pudieron recuperar los detalles del pedido.');
        }
    })
    .catch(err => {
        detailContent.classList.remove('opacity-50');
        clearOrderDetails();
        console.error('Error al cargar detalles del pedido:', err);
        alert('Ocurrió un error al comunicarse con el servidor.');
    });
}

/**
 * Renders general client information, payment method, and total of the order.
 * Configures actions for links and forms.
 * @function renderOrderDetails
 * @param {Object} order - The order data object.
 */
function renderOrderDetails(order) {
    document.getElementById('det-order-id').innerText = `#${order.id.toString().padStart(6, '0')}`;
    document.getElementById('det-cliente-nombre').innerText = order.cliente.nombre;
    document.getElementById('det-cliente-email').innerText = order.cliente.email;
    document.getElementById('det-forma-pago').innerText = order.forma_pago;
    document.getElementById('det-total').innerText = `$${parseFloat(order.total).toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;

    // Configure invoice link and state form action
    document.getElementById('det-btn-factura').href = `/admin/ventas/${order.id}/factura`;
    document.getElementById('det-form-estado').action = `/admin/ventas/${order.id}/estado`;
}

/**
 * Iterates and renders the purchased product items inside the details container.
 * @function renderOrderItems
 * @param {Array<Object>} details - Array of purchase detail items.
 */
function renderOrderItems(details) {
    const itemsContainer = document.getElementById('det-items-container');
    itemsContainer.innerHTML = '';
    
    details.forEach(item => {
        const thumb = item.imagen ? item.imagen : '/img/ui/productos/perro-buzo-verde.webp';
        const priceFormatted = parseFloat(item.precio_unitario).toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        
        itemsContainer.innerHTML += `
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
}

/**
 * Manages timeline classes (active, completed) and date texts.
 * @function renderOrderTimeline
 * @param {Object} order - The order data object.
 */
function renderOrderTimeline(order) {
    document.getElementById('tl-carrito-date').innerText = order.created_at;
    
    const tlPagoItem = document.getElementById('tl-pago-item');
    tlPagoItem.classList.add('completed');
    document.getElementById('tl-pago-date').innerText = order.fecha_venta || '';

    const tlDespachoItem = document.getElementById('tl-despacho-item');
    const tlDespachoDate = document.getElementById('tl-despacho-date');
    const detInputEstado = document.getElementById('det-input-estado');
    const detBtnSubmitEstado = document.getElementById('det-btn-submit-estado');

    if (order.estado === 'DESPACHADO') {
        tlDespachoItem.classList.remove('active');
        tlDespachoItem.classList.add('completed');
        tlDespachoDate.innerText = order.fecha_despacho;

        detInputEstado.value = 'CONFIRMADO';
        detBtnSubmitEstado.innerHTML = '🚚 Revertir despacho';
        detBtnSubmitEstado.className = 'btn-admin btn-admin-secondary w-100 mt-3';
    } else {
        tlDespachoItem.classList.remove('completed');
        tlDespachoItem.classList.add('active');
        tlDespachoDate.innerText = 'Pendiente de despacho';

        detInputEstado.value = 'DESPACHADO';
        detBtnSubmitEstado.innerHTML = '📦 Marcar como Despachado';
        detBtnSubmitEstado.className = 'btn-admin btn-admin-primary w-100 mt-3';
    }
}

/**
 * Resets the order details UI back to its initial empty placeholder state.
 * @function clearOrderDetails
 */
function clearOrderDetails() {
    activeOrder = null;
    
    // Hide content and show empty container placeholder
    document.getElementById('detail-content').classList.add('d-none');
    document.getElementById('detail-empty').classList.remove('d-none');

    // Clean up texts
    document.getElementById('det-order-id').innerText = '#000000';
    document.getElementById('det-cliente-nombre').innerText = '-';
    document.getElementById('det-cliente-email').innerText = '-';
    document.getElementById('det-forma-pago').innerText = '-';
    document.getElementById('det-total').innerText = '$0,00';
    document.getElementById('det-items-container').innerHTML = '';
    document.getElementById('tl-carrito-date').innerText = '-';
    document.getElementById('tl-pago-date').innerText = '-';
    document.getElementById('tl-despacho-date').innerText = '-';
    document.getElementById('det-btn-factura').href = '#';
    document.getElementById('det-form-estado').action = '#';
}

/**
 * Helper function to debounce execution of search.
 */
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

/**
 * Binds pagination links to fetch dynamically via AJAX.
 */
function bindPaginationLinks() {
    const paginationLinks = document.querySelectorAll('.custom-pagination-container a');
    paginationLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const url = link.getAttribute('href');
            if (url) {
                fetchAjaxResults(url);
            }
        });
    });
}

/**
 * Fetch new results and swap the table & pagination dynamically.
 */
function fetchAjaxResults(url) {
    window.history.replaceState(null, '', url);
    
    const tableResponsive = document.querySelector('.table-responsive');
    const paginationWrapper = document.querySelector('.custom-pagination-container');
    
    if (tableResponsive) tableResponsive.style.opacity = '0.5';
    if (paginationWrapper) paginationWrapper.style.opacity = '0.5';

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Swap table
        const newTable = doc.querySelector('.table-responsive');
        if (newTable && tableResponsive) {
            tableResponsive.innerHTML = newTable.innerHTML;
            tableResponsive.style.opacity = '1';
        }
        
        // Swap pagination
        const newPagination = doc.querySelector('.custom-pagination-container');
        if (newPagination && paginationWrapper) {
            paginationWrapper.innerHTML = newPagination.innerHTML;
            paginationWrapper.style.opacity = '1';
        } else if (paginationWrapper) {
            paginationWrapper.innerHTML = '';
            paginationWrapper.style.opacity = '1';
        }

        // Re-bind pagination clicks
        bindPaginationLinks();

        // Restore row selection (select first order row)
        const tableRows = document.querySelectorAll('.orders-table tbody tr');
        if (tableRows.length > 0 && tableRows[0].cells.length > 1) {
            selectOrder(tableRows[0]);
        } else {
            clearOrderDetails();
        }
    })
    .catch(err => {
        console.error('Error during AJAX swap:', err);
        if (tableResponsive) tableResponsive.style.opacity = '1';
        if (paginationWrapper) paginationWrapper.style.opacity = '1';
    });
}

/**
 * Collects active inputs and reloads page or requests AJAX swap with URL query parameters for filtering.
 * @function applyFilters
 */
function applyFilters(useAjax = false) {
    const searchInput = document.getElementById('search-pedido');
    const filterEstado = document.getElementById('filter-estado');
    const filterPago = document.getElementById('filter-pago');
    const filterPeriod = document.getElementById('filter-period');
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');

    const search = searchInput ? searchInput.value.trim() : '';
    const estado = filterEstado ? filterEstado.value : 'all';
    const pago = filterPago ? filterPago.value : 'all';
    const period = filterPeriod ? filterPeriod.value : 'all';

    let url = `?period=${period}&estado=${estado}&pago=${pago}&search=${encodeURIComponent(search)}`;

    if (period === 'custom' && startDateInput && endDateInput) {
        const start = startDateInput.value;
        const end = endDateInput.value;
        if (start && end) {
            url += `&start_date=${start}&end_date=${end}`;
        }
    }

    if (useAjax) {
        fetchAjaxResults(url);
    } else {
        window.location.href = url;
    }
}

// 3. Set up event listeners on page load
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-pedido');
    const btnSearchPedido = document.getElementById('btn-search-pedido');
    const filterEstado = document.getElementById('filter-estado');
    const filterPago = document.getElementById('filter-pago');
    const filterPeriod = document.getElementById('filter-period');
    const customDateContainer = document.getElementById('custom-date-container');
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');
    const btnApplyCustomDate = document.getElementById('btn-apply-custom-date');

    // Escuchar cambios de estado y pago por AJAX
    if (filterEstado) filterEstado.addEventListener('change', () => applyFilters(true));
    if (filterPago) filterPago.addEventListener('change', () => applyFilters(true));

    // Escuchar enter o escritura en el buscador
    if (searchInput) {
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyFilters(true);
            }
        });
        searchInput.addEventListener('input', debounce(() => {
            applyFilters(true);
        }, 350));
    }

    // Escuchar click en el botón de buscar
    if (btnSearchPedido) {
        btnSearchPedido.addEventListener('click', () => applyFilters(true));
    }

    // Gestor de períodos de fecha
    if (filterPeriod) {
        filterPeriod.addEventListener('change', function() {
            const val = this.value;
            if (val === 'custom') {
                customDateContainer.classList.remove('d-none');
                customDateContainer.classList.add('d-flex');
            } else {
                customDateContainer.classList.remove('d-flex');
                customDateContainer.classList.add('d-none');
                applyFilters();
            }
        });
    }

    // Gestor de aplicar rango personalizado
    if (btnApplyCustomDate) {
        btnApplyCustomDate.addEventListener('click', () => {
            const start = startDateInput.value;
            const end = endDateInput.value;
            
            if (!start || !end) {
                alert('Por favor, selecciona tanto la fecha de inicio como la de fin.');
                return;
            }
            
            if (new Date(start) > new Date(end)) {
                alert('La fecha de inicio no puede ser posterior a la fecha de fin.');
                return;
            }

            applyFilters();
        });
    }

    // Bind inicial de links de paginación
    bindPaginationLinks();

    // Auto-select first order row on page load if one exists
    const tableRows = document.querySelectorAll('.orders-table tbody tr');
    if (tableRows.length > 0 && tableRows[0].cells.length > 1) {
        selectOrder(tableRows[0]);
    }
});
