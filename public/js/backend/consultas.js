/**
 * Server-side filters and interaction for Consultas Module
 */

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
    const searchInput = document.getElementById('search-consulta');
    const filterEstado = document.getElementById('filter-estado');
    const filterAsunto = document.getElementById('filter-asunto');
    const filterPeriod = document.getElementById('filter-period');
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');

    const search = searchInput ? searchInput.value.trim() : '';
    const estado = filterEstado ? filterEstado.value : 'all';
    const asunto = filterAsunto ? filterAsunto.value : 'all';
    const period = filterPeriod ? filterPeriod.value : 'all';

    let url = `?period=${period}&estado=${estado}&asunto=${asunto}&search=${encodeURIComponent(search)}`;

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

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-consulta');
    const btnSearchConsulta = document.getElementById('btn-search-consulta');
    const filterEstado = document.getElementById('filter-estado');
    const filterAsunto = document.getElementById('filter-asunto');
    const filterPeriod = document.getElementById('filter-period');
    const customDateContainer = document.getElementById('custom-date-container');
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');
    const btnApplyCustomDate = document.getElementById('btn-apply-custom-date');

    // Escuchar cambios de estado y asunto por AJAX
    if (filterEstado) filterEstado.addEventListener('change', () => applyFilters(true));
    if (filterAsunto) filterAsunto.addEventListener('change', () => applyFilters(true));

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
    if (btnSearchConsulta) {
        btnSearchConsulta.addEventListener('click', () => applyFilters(true));
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
});
