// Global state for Clients Module
let activeClient = null;

/**
 * Selects a client row, highlights it visually, and loads their details.
 * @function selectClient
 * @param {HTMLElement} row - The selected table row.
 */
function selectClient(row) {
    const tableRows = document.querySelectorAll('.clients-table tbody tr');
    tableRows.forEach(r => r.classList.remove('row-active'));
    row.classList.add('row-active');
    
    const clientId = row.dataset.clientId;
    loadClientDetails(clientId);
}

/**
 * Handles the asynchronous AJAX loading of client details and manages spinner feedback.
 * @function loadClientDetails
 * @param {number|string} id - The ID of the client to fetch.
 */
function loadClientDetails(id) {
    clearClientDetails();
    
    const detailContent = document.getElementById('detail-content');
    const detailEmpty = document.getElementById('detail-empty');

    detailEmpty.classList.add('d-none');
    detailContent.classList.remove('d-none');
    detailContent.classList.add('opacity-50');

    fetch(`/admin/usuarios/${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Respuesta del servidor no válida');
        }
        return response.json();
    })
    .then(data => {
        detailContent.classList.remove('opacity-50');
        if (data.success) {
            const client = data.usuario;
            activeClient = client;

            renderClientDetails(client);
            renderClientPurchaseHistory(client.ventas);
        } else {
            clearClientDetails();
            alert('No se pudieron recuperar los detalles del cliente.');
        }
    })
    .catch(err => {
        detailContent.classList.remove('opacity-50');
        clearClientDetails();
        console.error('Error al cargar detalles del cliente:', err);
        alert('Ocurrió un error al comunicarse con el servidor.');
    });
}

/**
 * Renders general client information, role, dates, and address details.
 * Configures the deletion form action and safety conditions.
 * @function renderClientDetails
 * @param {Object} client - The client data object.
 */
function renderClientDetails(client) {
    document.getElementById('det-client-id').innerText = `#${client.id.toString().padStart(5, '0')}`;
    document.getElementById('det-cliente-nombre').innerText = `${client.nombre} ${client.apellido}`;
    document.getElementById('det-cliente-email').innerText = client.email;
    document.getElementById('det-cliente-email').href = `mailto:${client.email}`;
    document.getElementById('det-cliente-rol').innerText = client.rol_nombre;
    
    // Apply role specific styling
    const rolBadge = document.getElementById('det-cliente-rol');
    rolBadge.className = 'role-badge';
    if (client.rol_nombre.toLowerCase() === 'admin') {
        rolBadge.classList.add('role-badge-admin');
    } else {
        rolBadge.classList.add('role-badge-cliente');
    }

    document.getElementById('det-cliente-registro').innerText = client.created_at;
    document.getElementById('det-cliente-telefono').innerText = client.telefono;
    
    // Build full address string
    let fullAddress = client.direccion;
    if (client.localidad && client.localidad !== 'No especificada') {
        fullAddress += `, ${client.localidad}`;
    }
    if (client.provincia && client.provincia !== 'No especificada') {
        fullAddress += `, ${client.provincia}`;
    }
    if (client.codigo_postal && client.codigo_postal !== 'No especificado') {
        fullAddress += ` (${client.codigo_postal})`;
    }
    
    document.getElementById('det-cliente-direccion').innerText = fullAddress;

    // Safety checks for delete button & purchase history (admin cannot delete admins & has no purchase history)
    const deleteSection = document.getElementById('det-delete-section');
    const deleteForm = document.getElementById('det-form-delete');
    const historySection = document.getElementById('det-history-section');
    
    if (client.rol_nombre.toLowerCase() === 'admin') {
        deleteSection.classList.add('d-none');
        deleteForm.action = '#';
        if (historySection) historySection.classList.add('d-none');
    } else {
        deleteSection.classList.remove('d-none');
        deleteForm.action = `/admin/usuarios/${client.id}`;
        if (historySection) historySection.classList.remove('d-none');
    }
}

/**
 * Iterates and renders the purchase history items inside the details container.
 * @function renderClientPurchaseHistory
 * @param {Array<Object>} sales - Array of client purchases.
 */
function renderClientPurchaseHistory(sales) {
    const historyContainer = document.getElementById('det-history-container');
    historyContainer.innerHTML = '';
    
    if (sales.length === 0) {
        historyContainer.innerHTML = `
            <div class="text-center py-4 text-muted">
                <span class="fs-4 d-block mb-1">📦</span>
                <span class="small poppins-medium">Sin compras confirmadas en el sistema.</span>
            </div>
        `;
        return;
    }
    
    sales.forEach(sale => {
        const totalFormatted = parseFloat(sale.total).toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        let badgeClass = 'bg-secondary';
        
        if (sale.estado === 'DESPACHADO') {
            badgeClass = 'bg-success';
        } else if (sale.estado === 'CONFIRMADO') {
            badgeClass = 'bg-primary';
        }
        
        historyContainer.innerHTML += `
            <div class="purchase-history-item">
                <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                    <div>
                        <span class="purchase-item-id">#${sale.id.toString().padStart(6, '0')}</span>
                        <span class="purchase-item-date ms-2">${sale.fecha_venta}</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="purchase-item-total">$${totalFormatted}</span>
                        <span class="badge ${badgeClass} text-white purchase-item-status">${sale.estado}</span>
                    </div>
                </div>
                <div class="small text-muted mt-1" style="font-size: 0.7rem;">
                    Método de Pago: ${sale.forma_pago}
                </div>
            </div>
        `;
    });
}

/**
 * Resets the client details UI back to its initial empty placeholder state.
 * @function clearClientDetails
 */
function clearClientDetails() {
    activeClient = null;
    
    // Hide content and show empty container placeholder
    document.getElementById('detail-content').classList.add('d-none');
    document.getElementById('detail-empty').classList.remove('d-none');

    // Clean up text content
    document.getElementById('det-client-id').innerText = '#00000';
    document.getElementById('det-cliente-nombre').innerText = '-';
    document.getElementById('det-cliente-email').innerText = '-';
    document.getElementById('det-cliente-email').href = '#';
    document.getElementById('det-cliente-rol').innerText = '-';
    document.getElementById('det-cliente-rol').className = 'role-badge';
    document.getElementById('det-cliente-registro').innerText = '-';
    document.getElementById('det-cliente-telefono').innerText = '-';
    document.getElementById('det-cliente-direccion').innerText = '-';
    document.getElementById('det-history-container').innerHTML = '';
    document.getElementById('det-form-delete').action = '#';
    document.getElementById('det-delete-section').classList.add('d-none');
    
    const historySection = document.getElementById('det-history-section');
    if (historySection) historySection.classList.remove('d-none');
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

        // Restore row selection (select first client row)
        const tableRows = document.querySelectorAll('.clients-table tbody tr');
        if (tableRows.length > 0 && tableRows[0].cells.length > 1) {
            selectClient(tableRows[0]);
        } else {
            clearClientDetails();
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
    const searchInput = document.getElementById('search-cliente');
    const filterRol = document.getElementById('filter-rol');
    const filterPeriod = document.getElementById('filter-period');
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');

    const search = searchInput ? searchInput.value.trim() : '';
    const rol = filterRol ? filterRol.value : 'all';
    const period = filterPeriod ? filterPeriod.value : 'all';

    let url = `?period=${period}&rol=${rol}&search=${encodeURIComponent(search)}`;

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

// Set up event listeners on page load
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-cliente');
    const btnSearchCliente = document.getElementById('btn-search-cliente');
    const filterRol = document.getElementById('filter-rol');
    const filterPeriod = document.getElementById('filter-period');
    const customDateContainer = document.getElementById('custom-date-container');
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');
    const btnApplyCustomDate = document.getElementById('btn-apply-custom-date');

    // Escuchar cambios de select de rol por AJAX
    if (filterRol) filterRol.addEventListener('change', () => applyFilters(true));

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
    if (btnSearchCliente) {
        btnSearchCliente.addEventListener('click', () => applyFilters(true));
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

    // Auto-select first client row on page load if one exists
    const tableRows = document.querySelectorAll('.clients-table tbody tr');
    if (tableRows.length > 0 && tableRows[0].cells.length > 1) {
        selectClient(tableRows[0]);
    }
});
