/**
 * Pet Threads - Shopping Cart AJAX Handler
 * Handles quantity adjustments and product deletion asynchronously.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Click handler for +/- buttons: modify the input value and trigger form submit
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-qty-decrement, .btn-qty-increment');
        if (!btn || btn.disabled) return;

        const form = btn.closest('.form-qty-update');
        if (!form) return;

        const input = form.querySelector('.qty-input-field');
        if (!input) return;

        let val = parseInt(input.value);
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max);

        if (btn.classList.contains('btn-qty-increment')) {
            val++;
        } else {
            val--;
        }

        // Enforce limits before submitting
        if (val < min || (max && val > max)) return;

        input.value = val;

        // Trigger form submit via the submit event listener
        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
        } else {
            const submitEvent = new Event('submit', { cancelable: true, bubbles: true });
            form.dispatchEvent(submitEvent);
        }
    });

    // 2. Listen for changes in the quantity input field directly (manual entry)
    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('qty-input-field')) {
            const input = e.target;
            const form = input.closest('.form-qty-update');
            
            if (!form) return;

            const val = parseInt(input.value);
            const min = parseInt(input.min) || 1;
            const max = parseInt(input.max);

            // Client-side validations before hitting the server
            if (isNaN(val) || val < min) {
                input.value = input.dataset.originalValue;
                return;
            }

            if (max && val > max) {
                showAjaxAlert(`No puedes seleccionar más de ${max} unidades (límite de stock).`, 'danger');
                input.value = input.dataset.originalValue;
                return;
            }

            // Submit using requestSubmit to trigger the submit event listener
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                const submitEvent = new Event('submit', { cancelable: true, bubbles: true });
                form.dispatchEvent(submitEvent);
            }
        }
    });

    // 3. Intercept form submissions for quantity updates and item removal
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form.classList.contains('form-qty-update')) {
            e.preventDefault();
            updateQuantity(form);
        } else if (form.classList.contains('form-delete-item')) {
            e.preventDefault();
            deleteItem(form);
        }
    });
});

/**
 * Handles the Fetch API request for quantity modifications.
 * @param {HTMLFormElement} form 
 */
function updateQuantity(form) {
    const row = form.closest('.cart-item-row');
    if (!row) return;

    // Capture form data BEFORE disabling inputs (disabled inputs are excluded from FormData)
    const url = form.action;
    const methodInput = form.querySelector('input[name="_method"]');
    const method = methodInput ? methodInput.value : form.method;
    const formData = new URLSearchParams(new FormData(form));
    const csrfToken = form.querySelector('input[name="_token"]')?.value || '';

    // Now disable buttons and inputs to prevent multiple requests
    const interactiveElements = row.querySelectorAll('input, button');
    interactiveElements.forEach(el => el.disabled = true);

    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': csrfToken,
            'X-HTTP-METHOD-OVERRIDE': method.toUpperCase()
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update input and its backing original value cache
            const qtyInput = row.querySelector('.qty-input-field');
            if (qtyInput) {
                qtyInput.value = data.cantidad;
                qtyInput.dataset.originalValue = data.cantidad;
            }

            // Update subtotal text content in this row
            const subtotalTag = row.querySelector('.subtotal-tag');
            if (subtotalTag) {
                subtotalTag.textContent = formatCurrency(data.subtotal);
            }

            // Update checkout summary values
            updateSummaryTotals(data.total);

            // Clear any active ajax errors
            clearAjaxAlert();
        } else {
            throw data;
        }
    })
    .catch(err => {
        console.error('Error al actualizar cantidad del carrito:', err);
        const errMsg = err.message || 'No se pudo actualizar la cantidad en este momento.';
        showAjaxAlert(errMsg, 'danger');

        // Revert quantity input back to its original state
        const qtyInput = row.querySelector('.qty-input-field');
        if (qtyInput) {
            qtyInput.value = qtyInput.dataset.originalValue;
        }
    })
    .finally(() => {
        // Re-enable elements and update button disabled states based on thresholds
        interactiveElements.forEach(el => {
            if (el.classList.contains('btn-qty-decrement')) {
                const qtyInput = row.querySelector('.qty-input-field');
                const val = qtyInput ? parseInt(qtyInput.value) : 1;
                el.disabled = (val <= 1);
            } else if (el.classList.contains('btn-qty-increment')) {
                const qtyInput = row.querySelector('.qty-input-field');
                const val = qtyInput ? parseInt(qtyInput.value) : 1;
                const max = qtyInput ? parseInt(qtyInput.max) : Infinity;
                el.disabled = (val >= max);
            } else {
                el.disabled = false;
            }
        });
    });
}

/**
 * Handles the Fetch API request for product removal from the cart.
 * @param {HTMLFormElement} form 
 */
function deleteItem(form) {
    if (!confirm('¿Estás seguro de que deseas eliminar este producto del carrito?')) {
        return;
    }

    const row = form.closest('.cart-item-row');
    if (!row) return;

    // Capture form data BEFORE disabling inputs
    const url = form.action;
    const methodInput = form.querySelector('input[name="_method"]');
    const method = methodInput ? methodInput.value : form.method;
    const formData = new URLSearchParams(new FormData(form));
    const csrfToken = form.querySelector('input[name="_token"]')?.value || '';

    const interactiveElements = row.querySelectorAll('input, button');
    interactiveElements.forEach(el => el.disabled = true);

    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': csrfToken,
            'X-HTTP-METHOD-OVERRIDE': method.toUpperCase()
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Apply fade-out and slide-up transition animation
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '0';
            row.style.transform = 'translateY(-20px)';

            setTimeout(() => {
                row.remove();

                // If cart is empty, reload to show empty state view
                if (data.items_count === 0 || document.querySelectorAll('.cart-item-row').length === 0) {
                    window.location.reload();
                    return;
                }

                // Update summary card totals
                updateSummaryTotals(data.total);

                // Show success alert
                showAjaxAlert(data.message, 'success');
            }, 300);
        } else {
            throw data;
        }
    })
    .catch(err => {
        console.error('Error al eliminar producto del carrito:', err);
        const errMsg = err.message || 'No se pudo eliminar el producto en este momento.';
        showAjaxAlert(errMsg, 'danger');
        
        interactiveElements.forEach(el => el.disabled = false);
    });
}

/**
 * Formats a numeric value to Argentinian Peso locale (no decimals).
 * @param {number|string} val 
 * @returns {string} Formatted price
 */
function formatCurrency(val) {
    return '$' + parseFloat(val).toLocaleString('es-AR', { 
        minimumFractionDigits: 0, 
        maximumFractionDigits: 0 
    });
}

/**
 * Updates summary subtotal and total texts.
 * @param {number|string} total 
 */
function updateSummaryTotals(total) {
    const formatted = formatCurrency(total);
    const subtotalSummary = document.querySelector('.summary-subtotal');
    const totalSummary = document.querySelector('.summary-total');

    if (subtotalSummary) subtotalSummary.textContent = formatted;
    if (totalSummary) totalSummary.textContent = formatted;
}

/**
 * Displays a dynamic Bootstrap alert message.
 * @param {string} message 
 * @param {'success'|'danger'} type 
 */
function showAjaxAlert(message, type = 'danger') {
    const container = document.getElementById('ajax-alert-container');
    if (!container) return;

    const prefix = type === 'danger' ? '⚠️ Error:' : '¡Éxito!';
    
    container.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px;">
            <strong>${prefix}</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Auto-scroll to alert if out of view
    container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/**
 * Clears the active dynamic AJAX alert container.
 */
function clearAjaxAlert() {
    const container = document.getElementById('ajax-alert-container');
    if (container) container.innerHTML = '';
}
