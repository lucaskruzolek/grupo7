/**
 * Server-side filters and interaction for Consultas Module
 */

function applyFilters() {
    const searchInput = document.getElementById('search-consulta');
    const filterEstado = document.getElementById('filter-estado');
    const filterAsunto = document.getElementById('filter-asunto');
    const filterPeriod = document.getElementById('filter-period');
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');

    const search = searchInput ? searchInput.value.trim() : '';
    const estado = filterEstado ? filterEstado.value : 'all';
    const asunto = filterAsunto ? filterAsunto.value : 'all';
    const period = filterPeriod ? filterPeriod.value : 'month';

    let url = `?period=${period}&estado=${estado}&asunto=${asunto}&search=${encodeURIComponent(search)}`;

    if (period === 'custom' && startDateInput && endDateInput) {
        const start = startDateInput.value;
        const end = endDateInput.value;
        if (start && end) {
            url += `&start_date=${start}&end_date=${end}`;
        }
    }

    window.location.href = url;
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

    // Escuchar cambios de estado y asunto
    if (filterEstado) filterEstado.addEventListener('change', applyFilters);
    if (filterAsunto) filterAsunto.addEventListener('change', applyFilters);

    // Escuchar enter en el buscador
    if (searchInput) {
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyFilters();
            }
        });
    }

    // Escuchar click en el botón de buscar
    if (btnSearchConsulta) {
        btnSearchConsulta.addEventListener('click', applyFilters);
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
});
