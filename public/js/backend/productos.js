const DB_PRODUCTS = window.LaravelConfig.productosData;
const SYSTEM_COLORS = window.LaravelConfig.coloresSystem;
const SYSTEM_TALLES = window.LaravelConfig.tallesSystem;
const PRODUCT_CACHE = {};
const ALL_PRODUCTS = DB_PRODUCTS.sort((a, b) => a.nombre_base.localeCompare(b.nombre_base, 'es', { sensitivity: 'base' }));

let activeProduct = null;
let currentActiveColor = null;
let currentImageIndex = 0;

let newProductVariants = [];

let filteredProducts = [...ALL_PRODUCTS];
let currentPage = 1;
let hasMorePages = filteredProducts.length > 20;
let isLoading = false;


/**
 * Limpia el formulario de creación de productos, inicializa una variante por defecto
 * y abre el modal correspondiente de Bootstrap.
 * 
 * @function openCreateProductModal
 * @returns {void}
 */
function openCreateProductModal() {
    // Limpiar formulario
    document.getElementById('form-create-product').reset();

    // Inicializar con variante por defecto
    newProductVariants = [];

    renderNewProductVariants();

    const modal = new bootstrap.Modal(document.getElementById('modalCreateProduct'));
    modal.show();
}


/**
 * Valida y agrega una variante de color, talle y stock a la lista local de creación.
 * 
 * @function addVariantToNewProduct
 * @returns {void}
 */
function addVariantToNewProduct() {
    const colorSelect = document.getElementById('new-variant-color');
    const talleSelect = document.getElementById('new-variant-talle');
    const stockInput = document.getElementById('new-variant-stock');

    const color_id = parseInt(colorSelect.value);
    const color_name = colorSelect.options[colorSelect.selectedIndex].getAttribute('data-name');
    const talle = talleSelect.value;
    const stock = parseInt(stockInput.value);

    if (isNaN(color_id) || !talle || isNaN(stock) || stock < 0) {
        alert('Por favor ingrese valores válidos de variante.');
        return;
    }

    // Validar duplicado
    const duplicate = newProductVariants.some(v => v.color_id === color_id && v.talle === talle);
    if (duplicate) {
        alert('Esta combinación de Color y Talle ya ha sido agregada.');
        return;
    }

    newProductVariants.push({ color_id, color_name, talle, stock });
    renderNewProductVariants();

    // Resetear stock input a valor por defecto
    stockInput.value = 0;
}

/**
 * Remueve una variante específica de la lista temporal de creación por su índice.
 * 
 * @function removeVariantFromNewProduct
 * @param {number} index - Posición de la variante en el array.
 * @returns {void}
 */
function removeVariantFromNewProduct(index) {
    newProductVariants.splice(index, 1);
    renderNewProductVariants();
}

/**
 * Dibuja las variantes en la tabla del modal de creación y genera inputs ocultos en el DOM
 * para permitir que Laravel procese la matriz de variantes al hacer POST del formulario.
 * 
 * @function renderNewProductVariants
 * @returns {void}
 */
function renderNewProductVariants() {
    const tbody = document.getElementById('new-product-variants-table-body');
    const hiddenContainer = document.getElementById('new-prod-hidden-inputs');

    tbody.innerHTML = '';
    hiddenContainer.innerHTML = '';

    if (newProductVariants.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-3">No hay variantes agregadas.</td></tr>`;
        return;
    }

    newProductVariants.forEach((v, index) => {
        // Fila de la tabla
        const tr = document.createElement('tr');
        tr.innerHTML = `
                    <td style="padding: 0.5rem 1rem; vertical-align: middle;">${v.color_name}</td>
                    <td style="padding: 0.5rem 1rem; vertical-align: middle;">${v.talle}</td>
                    <td style="padding: 0.5rem 1rem; vertical-align: middle; text-align: center;">${v.stock}</td>
                    <td style="padding: 0.5rem 1rem; vertical-align: middle; text-align: right;">
                        <button type="button" class="btn btn-sm btn-outline-danger" style="padding: 0.1rem 0.4rem; font-size: 0.8rem;" onclick="removeVariantFromNewProduct(${index})">
                            Eliminar
                        </button>
                    </td>
                `;
        tbody.appendChild(tr);

        // Inputs ocultos para el post del formulario
        hiddenContainer.innerHTML += `
                    <input type="hidden" name="variantes[${index}][color_id]" value="${v.color_id}">
                    <input type="hidden" name="variantes[${index}][talle]" value="${v.talle}">
                    <input type="hidden" name="variantes[${index}][stock]" value="${v.stock}">
                `;
    });
}

/**
 * Valida que se haya agregado al menos una variante antes de permitir el envío del formulario
 * de creación del producto. Se vincula al evento submit del form.
 * 
 * @function validateCreateProductForm
 * @param {Event} event - Evento de submit del formulario.
 * @returns {boolean} - True si la validación es exitosa, false si se debe prevenir el envío.
 */
function validateCreateProductForm(event) {
    if (newProductVariants.length === 0) {
        alert('Debe agregar al menos una variante de Color/Talle para crear el producto.');
        event.preventDefault();
        return false;
    }
    const newStockMin = parseInt(document.getElementById('new-prod-stock-min').value);
    if (isNaN(newStockMin) || newStockMin < 0) {
        alert('Por favor ingrese un stock mínimo válido.');
        event.preventDefault();
        return false;
    }
    return true;
}

/**
 * Prepara e inicializa el modal para añadir un nuevo talle al producto seleccionado.
 * 
 * @function openAddTalleModal
 * @returns {void}
 */
function openAddTalleModal() {
    if (!activeProduct) return;
    populateTalleSelect();
    const modal = new bootstrap.Modal(document.getElementById('modalAddTalle'));
    modal.show();
}

/**
 * Rellena el selector de talles del modal omitiendo los que el producto activo ya posee.
 * 
 * @function populateTalleSelect
 * @returns {void}
 */
function populateTalleSelect() {
    const select = document.getElementById('select-talle-system');
    select.innerHTML = '';

    const availableTalles = SYSTEM_TALLES.filter(t => !activeProduct.talles.includes(t));

    if (availableTalles.length === 0) {
        select.innerHTML = '<option value="" disabled>No hay más talles disponibles</option>';
        document.getElementById('btn-confirm-add-talle').disabled = true;
        return;
    }
    document.getElementById('btn-confirm-add-talle').disabled = false;
    availableTalles.forEach(talle => {
        const opt = document.createElement('option');
        opt.value = talle;
        opt.innerText = talle;
        select.appendChild(opt);
    });
}

/**
 * Confirma la adición de un talle al producto activo, actualiza la interfaz y cierra el modal.
 * 
 * @function confirmAddTalle
 * @returns {void}
 */
function confirmAddTalle() {
    const select = document.getElementById('select-talle-system');
    const selectedTalle = select.value;
    if (!selectedTalle || !activeProduct) return;

    activeProduct.talles.push(selectedTalle);
    renderVariantsTable();

    const modalEl = document.getElementById('modalAddTalle');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
}


/**
 * Elimina localmente un talle (columna) de la grilla de variantes del producto activo,
 * siempre y cuando no existan variantes de stock definidas para ese talle.
 * 
 * @function deleteTalle
 * @param {string} talle - Nombre del talle a eliminar (ej. 'M').
 * @returns {void}
 */
function deleteTalle(talle) {
    if (!activeProduct) return;

    // Verificar si existen variantes cargadas para este talle
    let hasVariations = false;
    for (const colorKey in activeProduct.variantes) {
        if (activeProduct.variantes[colorKey] && activeProduct.variantes[colorKey][talle]) {
            hasVariations = true;
            break;
        }
    }

    if (hasVariations) {
        alert(`No se puede eliminar el talle "${talle}" porque tiene variantes de stock asociadas. Elimine primero esas variantes.`);
        return;
    }

    // Filtrar el talle del array del producto
    activeProduct.talles = activeProduct.talles.filter(t => t !== talle);
    renderVariantsTable();
}

/**
 * Prepara e inicializa el modal para añadir un nuevo color al producto seleccionado.
 * 
 * @function openAddColorModal
 * @returns {void}
 */
function openAddColorModal() {
    if (!activeProduct) return;
    populateColorSelect();
    const modal = new bootstrap.Modal(document.getElementById('modalAddColor'));
    modal.show();
}

/**
 * Rellena el selector de colores del modal omitiendo los que el producto activo ya posee.
 * 
 * @function populateColorSelect
 * @returns {void}
 */
function populateColorSelect() {
    const select = document.getElementById('select-color-system');
    select.innerHTML = '';

    const activeColorIds = activeProduct.colores.map(c => c.id);
    const availableColors = SYSTEM_COLORS.filter(c => !activeColorIds.includes(c.id));

    if (availableColors.length === 0) {
        select.innerHTML = '<option value="" disabled>No hay más colores disponibles</option>';
        document.getElementById('btn-confirm-add-color').disabled = true;
        return;
    }
    document.getElementById('btn-confirm-add-color').disabled = false;
    availableColors.forEach(color => {
        const opt = document.createElement('option');
        opt.value = color.id;
        opt.innerText = color.name;
        select.appendChild(opt);
    });
}

/**
 * Confirma la adición de un color al producto activo, inicializa la estructura multimedia
 * por defecto para ese color si es necesario, y actualiza la UI.
 * 
 * @function confirmAddColor
 * @returns {void}
 */
function confirmAddColor() {
    const select = document.getElementById('select-color-system');
    const selectedColorId = parseInt(select.value);
    if (!selectedColorId || !activeProduct) return;

    const colorObj = SYSTEM_COLORS.find(c => c.id === selectedColorId);
    if (!colorObj) return;

    activeProduct.colores.push({
        id: colorObj.id,
        nombre: colorObj.name,
        hex_code: colorObj.hex,
        key: colorObj.key
    });

    if (!activeProduct.colorMedia[colorObj.key]) {
        activeProduct.colorMedia[colorObj.key] = {
            main: window.LaravelConfig.defaultImagePath,
            thumbs: [window.LaravelConfig.defaultImagePath],
            urls: [window.LaravelConfig.defaultImagePath]
        };
    }

    currentActiveColor = colorObj.key;
    renderVariantsTable();
    renderGalleryAndUrls();

    const modalEl = document.getElementById('modalAddColor');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
}

/**
 * Elimina localmente un color (fila) de la grilla de variantes del producto activo,
 * validando previamente que no existan variantes de stock definidas para ese color.
 * También limpia el color de la galería multimedia y reasigna el color activo.
 * 
 * @function deleteColor
 * @param {string} colorKey - Identificador clave del color a eliminar (ej. 'negro').
 * @returns {void}
 */
function deleteColor(colorKey) {
    if (!activeProduct) return;

    // Verificar si existen variantes cargadas para este color
    let hasVariations = false;
    if (activeProduct.variantes[colorKey]) {
        const variants = activeProduct.variantes[colorKey];
        for (const talle in variants) {
            if (variants[talle]) {
                hasVariations = true;
                break;
            }
        }
    }

    if (hasVariations) {
        alert("No se puede eliminar el color porque tiene variantes de stock asociadas. Elimine primero esas variantes.");
        return;
    }

    // Si no tiene variantes, eliminar color, su multimedia y su entrada de variantes
    activeProduct.colores = activeProduct.colores.filter(c => c.key !== colorKey);
    if (activeProduct.colorMedia && activeProduct.colorMedia[colorKey]) {
        delete activeProduct.colorMedia[colorKey];
    }
    if (activeProduct.variantes && activeProduct.variantes[colorKey]) {
        delete activeProduct.variantes[colorKey];
    }

    // Si el color eliminado era el activo, cambiar al primero disponible
    if (currentActiveColor === colorKey) {
        if (activeProduct.colores.length > 0) {
            currentActiveColor = activeProduct.colores[0].key;
            currentImageIndex = 0;
        } else {
            currentActiveColor = null;
            currentImageIndex = 0;
        }
    }

    renderVariantsTable();
    renderGalleryAndUrls();
}

/**
 * Elimina una variante de stock individual (celda) de la matriz del producto activo
 * solicitando previamente confirmación al usuario.
 * 
 * @function deleteVariant
 * @param {string} colorKey - Clave identificadora del color.
 * @param {string} talle - Nombre del talle.
 * @returns {void}
 */
function deleteVariant(colorKey, talle) {
    if (!activeProduct) return;

    if (confirm(`¿Estás seguro de que deseas eliminar la variante Color: ${colorKey} / Talle: ${talle}?`)) {
        if (activeProduct.variantes[colorKey] && activeProduct.variantes[colorKey][talle]) {
            delete activeProduct.variantes[colorKey][talle];
            renderVariantsTable();
        }
    }
}

/**
 * Abre el modal de creación de variantes (color/talle/stock) asignando el contexto
 * de la celda pulsada para la inserción posterior de datos.
 * 
 * @function openCreateVariationModal
 * @param {HTMLButtonElement} button - Botón que disparó el evento (el `+` de la celda).
 * @returns {void}
 */
function openCreateVariationModal(button) {
    const cell = button.closest('td');
    const row = button.closest('tr');
    const colorName = row.getAttribute('data-color-name');
    const colorId = parseInt(row.getAttribute('data-color-id'));
    const talle = cell.getAttribute('data-talle');
    const productName = activeProduct.nombre_base;

    document.getElementById('modal-variation-product').innerText = productName;
    document.getElementById('modal-variation-details').innerText = `Color: ${colorName} / Talle: ${talle}`;
    document.getElementById('variation-stock').value = 0;

    window.targetCellForVariation = {
        colorId: colorId,
        colorKey: row.getAttribute('data-color'),
        talle: talle,
        cell: cell
    };

    const modal = new bootstrap.Modal(document.getElementById('modalCreateVariation'));
    modal.show();
}

/**
 * Valida el stock ingresado y crea la nueva variante en el objeto de producto,
 * calculando su SKU y actualizando la visualización de la tabla.
 * 
 * @function confirmCreateVariation
 * @returns {void}
 */
function confirmCreateVariation() {
    const stockInput = document.getElementById('variation-stock');
    const stockVal = stockInput.value.trim();
    if (stockVal === '' || isNaN(stockVal) || parseInt(stockVal) < 1) {
        alert('Por favor ingrese un stock válido.');
        return;
    }

    const stock = parseInt(stockVal);
    const target = window.targetCellForVariation;
    if (target && activeProduct) {
        if (!activeProduct.variantes[target.colorKey]) {
            activeProduct.variantes[target.colorKey] = {};
        }

        activeProduct.variantes[target.colorKey][target.talle] = {
            sku: `${activeProduct.sku_base}-${target.colorKey.toUpperCase()}-${target.talle}`,
            stock: stock,
            id: null
        };

        renderVariantsTable();
    }

    const createModalEl = document.getElementById('modalCreateVariation');
    const createModal = bootstrap.Modal.getInstance(createModalEl);
    if (createModal) createModal.hide();

    const successModal = new bootstrap.Modal(document.getElementById('modalSuccessVariation'));
    successModal.show();
}

/**
 * Resalta la fila del color seleccionado y actualiza la galería de imágenes y URLs,
 * permitiendo la gestión visual de los recursos multimedia del color.
 * 
 * @function selectColorRow
 * @param {HTMLTableRowElement} row - Fila de la tabla correspondiente al color seleccionado.
 * @returns {void}
 */
function selectColorRow(row) {
    const color = row.getAttribute('data-color');
    if (!color || !activeProduct || !activeProduct.colorMedia[color]) return;

    const allRows = document.querySelectorAll('#variants-table-body tr');
    allRows.forEach(r => r.classList.remove('color-row-active'));
    row.classList.add('color-row-active');

    currentActiveColor = color;
    currentImageIndex = 0;
    renderGalleryAndUrls();
}

/**
 * Dibuja el carrusel de imágenes y la lista de URLs asociadas al color activo,
 * permitiendo la navegación entre imágenes y la gestión de las mismas.
 * 
 * @function renderGalleryAndUrls
 * @returns {void}
 */
function renderGalleryAndUrls() {
    if (!activeProduct || !currentActiveColor) return;

    const media = activeProduct.colorMedia[currentActiveColor];
    if (!media) return;

    const mainImg = document.getElementById('gallery-main-img');
    const thumbContainer = document.getElementById('gallery-thumb-container');

    mainImg.style.opacity = 0;
    setTimeout(() => {
        mainImg.src = media.main;
        mainImg.style.opacity = 1;
    }, 100);

    thumbContainer.innerHTML = '';
    media.thumbs.forEach((src, idx) => {
        const img = document.createElement('img');
        img.src = src;
        img.className = `thumbnail-item ${idx === currentImageIndex ? 'active' : ''}`;
        img.onclick = function () { setMainImage(this, idx); };
        thumbContainer.appendChild(img);
    });

    const container = document.getElementById('edit-image-urls-container');
    container.innerHTML = '';
    media.urls.forEach(url => {
        const div = document.createElement('div');
        div.className = 'image-url-item mb-1';
        div.innerHTML = `
                    <input type="text" class="form-control form-control-sm form-control-admin" value="${url}" readonly>
                    <button class="btn btn-sm btn-outline-danger p-1 py-0" disabled>🗑️</button>
                `;
        container.appendChild(div);
    });
}

/**
 * Actualiza la imagen principal del carrusel y resalta la miniatura correspondiente.
 * 
 * @function setMainImage
 * @param {HTMLImageElement} thumb - Miniatura seleccionada.
 * @param {number} index - Índice de la miniatura.
 * @returns {void}
 */
function setMainImage(thumb, index) {
    const mainImg = document.getElementById('gallery-main-img');
    mainImg.src = thumb.src;
    currentImageIndex = index;
    const thumbs = document.querySelectorAll('.thumbnail-item');
    thumbs.forEach((t, i) => i === index ? t.classList.add('active') : t.classList.remove('active'));
}

/**
 * Avanza a la siguiente imagen del carrusel de forma circular.
 * 
 * @function nextImage
 * @returns {void}
 */
function nextImage() {
    if (!activeProduct || !currentActiveColor) return;
    const media = activeProduct.colorMedia[currentActiveColor];
    if (media && media.thumbs.length > 0) {
        currentImageIndex = (currentImageIndex + 1) % media.thumbs.length;
        const targetThumb = document.querySelectorAll('.thumbnail-item')[currentImageIndex];
        if (targetThumb) setMainImage(targetThumb, currentImageIndex);
    }
}

/**
 * Retrocede a la imagen anterior del carrusel de forma circular.
 * 
 * @function prevImage
 * @returns {void}
 */
function prevImage() {
    if (!activeProduct || !currentActiveColor) return;
    const media = activeProduct.colorMedia[currentActiveColor];
    if (media && media.thumbs.length > 0) {
        currentImageIndex = (currentImageIndex - 1 + media.thumbs.length) % media.thumbs.length;
        const targetThumb = document.querySelectorAll('.thumbnail-item')[currentImageIndex];
        if (targetThumb) setMainImage(targetThumb, currentImageIndex);
    }
}

function renderVariantsTable() {
    if (!activeProduct) return;

    const headerRow = document.getElementById('variants-table-header-row');
    const tbody = document.getElementById('variants-table-body');

    const standardOrder = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'];
    activeProduct.talles.sort((a, b) => standardOrder.indexOf(a) - standardOrder.indexOf(b));

    const isEditing = document.getElementById('detail-card-container').classList.contains('is-editing');

    // Obtener stock mínimo del producto (por defecto 2 si no está definido)
    const minStock = activeProduct.stock_minimo !== undefined && activeProduct.stock_minimo !== null ? parseInt(activeProduct.stock_minimo) : 2;

    let headerHtml = '<th>Color</th>';
    activeProduct.talles.forEach(talle => {
        headerHtml += `
            <th class="text-center" data-talle="${talle}">
                <div class="d-flex align-items-center justify-content-center gap-1">
                    <span>${talle}</span>
                    <button type="button" class="btn btn-sm p-0 lh-1 border-0 edit-mode text-danger" 
                            style="font-size: 0.95rem; background: none; margin-left: 4px; font-weight: bold; cursor: pointer; display: ${isEditing ? 'inline-block' : 'none'};"
                            onclick="event.stopPropagation(); deleteTalle('${talle}')" 
                            title="Eliminar talle ${talle}">
                        ×
                    </button>
                </div>
            </th>`;
    });
    headerRow.innerHTML = headerHtml;

    let bodyHtml = '';
    activeProduct.colores.forEach((color, idx) => {
        const isActive = color.key === currentActiveColor;
        bodyHtml += `
                    <tr class="${isActive ? 'color-row-active' : ''}" data-color="${color.key}" data-color-id="${color.id}" data-color-name="${color.nombre}" onclick="selectColorRow(this)">
                        <td>
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="color-dot-indicator" style="background-color: ${color.hex_code};"></span>
                                    <span>${color.nombre}</span>
                                </div>
                                <button type="button" class="btn btn-sm p-0 lh-1 border-0 edit-mode text-danger"
                                        style="font-size: 0.95rem; background: none; margin-right: 4px; font-weight: bold; cursor: pointer; display: ${isEditing ? 'inline-block' : 'none'};"
                                        onclick="event.stopPropagation(); deleteColor('${color.key}')"
                                        title="Eliminar color ${color.nombre}">
                                    ×
                                </button>
                            </div>
                        </td>
                `;

        activeProduct.talles.forEach(talle => {
            const variant = activeProduct.variantes[color.key] ? activeProduct.variantes[color.key][talle] : null;
            bodyHtml += `<td class="text-center" data-talle="${talle}">`;
            if (variant) {
                const stock = variant.stock;
                bodyHtml += `
                            <span class="stock-badge-text ${stock <= minStock ? 'stock-badge-low' : ''}">${stock} un</span>
                            <div class="stock-edit-wrapper" style="display: ${isEditing ? 'inline-flex' : 'none'}; align-items: center; justify-content: center; gap: 4px;">
                                <input type="number" class="stock-badge-input form-control shadow-none" value="${stock}" data-color-id="${color.id}" data-talle="${talle}" style="display: inline-block; width: 60px; padding: 0.2rem 0.4rem; font-size: 0.85rem; text-align: center; border: 1px solid var(--neutral-300); border-radius: 6px;">
                                <button type="button" class="btn btn-sm p-0 lh-1 border-0 text-danger" 
                                        style="font-size: 1.15rem; background: none; font-weight: bold; cursor: pointer;"
                                        onclick="event.stopPropagation(); deleteVariant('${color.key}', '${talle}')"
                                        title="Eliminar variante">
                                    ×
                                </button>
                            </div>
                        `;
            } else {
                bodyHtml += `<button class="btn btn-add-var-cell" onclick="openCreateVariationModal(this)">+</button>`;
            }
            bodyHtml += `</td>`;
        });

        bodyHtml += '</tr>';
    });
    tbody.innerHTML = bodyHtml;
}

/**
 * Cambia el estado del panel de detalle entre modo lectura y modo edición.
 * Actúa como despachador delegando en funciones especializadas según el estado.
 * 
 * @function toggleEditMode
 * @returns {void}
 */
function toggleEditMode() {
    const container = document.getElementById('detail-card-container');
    const isEditing = container.classList.contains('is-editing');

    if (!isEditing) {
        enterEditMode();
    } else {
        saveProductChanges();
    }
}

/**
 * Habilita el modo de edición en la vista detallada del producto.
 * 
 * @function enterEditMode
 * @returns {void}
 */
function enterEditMode() {
    const container = document.getElementById('detail-card-container');
    container.classList.add('is-editing');
    document.getElementById('edit-title').value = document.getElementById('detail-title').innerText.trim();
    document.getElementById('edit-desc').value = document.getElementById('detail-desc').innerText.trim();

    const tbody = document.getElementById('variants-table-body');
    const headerRow = document.getElementById('variants-table-header-row');

    // Mostrar botones de borrar talle
    headerRow.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'inline-block');

    // Mostrar botones de borrar color
    tbody.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'inline-block');

    // Mostrar stock inputs y sus botones de borrar variante
    tbody.querySelectorAll('.stock-edit-wrapper').forEach(w => w.style.setProperty('display', 'inline-flex', 'important'));
    tbody.querySelectorAll('.stock-badge-text').forEach(t => t.style.display = 'none');
}

/**
 * Cancela el modo de edición actual del producto, descartando cualquier cambio
 * y restaurando la visualización del estado anterior de la base de datos.
 * 
 * @function cancelEditMode
 * @returns {void}
 */
function cancelEditMode() {
    const container = document.getElementById('detail-card-container');
    container.classList.remove('is-editing');

    // Restaurar los detalles y la tabla utilizando el objeto activeProduct original
    if (activeProduct) {
        renderProductDetails(activeProduct);
    }
}

/**
 * Recopila los datos ingresados en el formulario de edición y construye el payload.
 * 
 * @function collectProductPayload
 * @returns {Object} El payload estructurado para enviar al servidor.
 */
function collectProductPayload() {
    const newName = document.getElementById('edit-title').value.trim();
    const newDesc = document.getElementById('edit-desc').value.trim();
    const newPrice = parseFloat(document.getElementById('edit-price').value);
    const newPet = document.getElementById('edit-pet').value;
    const newStockMin = parseInt(document.getElementById('edit-stock-min').value);

    const variantsList = [];
    const inputs = document.querySelectorAll('.stock-badge-input');
    inputs.forEach(input => {
        const colorId = parseInt(input.getAttribute('data-color-id'));
        const talle = input.getAttribute('data-talle');
        const stock = parseInt(input.value);
        variantsList.push({
            color_id: colorId,
            talle: talle,
            stock: stock
        });
    });

    return {
        sku_base: activeProduct.sku_base,
        nombre_base: newName,
        descripcion: newDesc,
        precio: newPrice,
        tipo_mascota: newPet,
        stock_minimo: newStockMin,
        variantes: variantsList
    };
}

/**
 * Valida los datos recopilados del formulario de edición.
 * 
 * @function validateProductPayload
 * @param {Object} payload - Los datos del producto a validar.
 * @returns {boolean} True si pasa la validación, false en caso contrario.
 */
function validateProductPayload(payload) {
    if (payload.nombre_base === '') {
        alert('El nombre no puede estar vacío.');
        return false;
    }
    if (isNaN(payload.precio) || payload.precio < 0) {
        alert('Por favor ingrese un precio válido.');
        return false;
    }
    if (isNaN(payload.stock_minimo) || payload.stock_minimo < 0) {
        alert('Por favor ingrese un stock mínimo válido.');
        return false;
    }
    return true;
}

/**
 * Gestiona el envío asíncrono de cambios al servidor y actualiza el estado/UI correspondientes.
 * 
 * @function saveProductChanges
 * @returns {void}
 */
function saveProductChanges() {
    const payload = collectProductPayload();

    if (!validateProductPayload(payload)) {
        return;
    }

    const container = document.getElementById('detail-card-container');
    const btnSave = document.getElementById('btn-toggle-edit');
    const inputs = document.querySelectorAll('.stock-badge-input');

    btnSave.disabled = true;
    btnSave.innerText = '💾 Guardando...';

    fetch(window.LaravelConfig.updateGroupUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.LaravelConfig.csrfToken
        },
        body: JSON.stringify(payload)
    })
        .then(response => response.json())
        .then(data => {
            btnSave.disabled = false;
            btnSave.innerHTML = '<span class="view-mode">✏️ Editar</span><span class="edit-mode" style="color: var(--color-primary);">💾 Guardar</span>';

            if (data.success) {
                // Invalidar caché local del producto para forzar consulta fresca
                delete PRODUCT_CACHE[activeProduct.sku_base];

                // Actualizar estado local
                activeProduct.nombre_base = payload.nombre_base;
                activeProduct.descripcion = payload.descripcion;
                activeProduct.precio = payload.precio;
                activeProduct.tipo_mascota = payload.tipo_mascota;
                activeProduct.stock_minimo = payload.stock_minimo;

                inputs.forEach(input => {
                    const colorId = parseInt(input.getAttribute('data-color-id'));
                    const colorObj = activeProduct.colores.find(c => c.id === colorId);
                    const talle = input.getAttribute('data-talle');
                    const stock = parseInt(input.value);
                    if (colorObj) {
                        if (!activeProduct.variantes[colorObj.key]) {
                            activeProduct.variantes[colorObj.key] = {};
                        }
                        activeProduct.variantes[colorObj.key][talle] = {
                            sku: `${activeProduct.sku_base}-${colorObj.key.toUpperCase()}-${talle}`,
                            stock: stock,
                            id: activeProduct.variantes[colorObj.key][talle] ? activeProduct.variantes[colorObj.key][talle].id : null
                        };
                    }
                });

                // Sincronizar en ALL_PRODUCTS para la búsqueda/filtrado local inmediato
                const localProdIndex = ALL_PRODUCTS.findIndex(p => p.sku_base === activeProduct.sku_base);
                if (localProdIndex !== -1) {
                    ALL_PRODUCTS[localProdIndex].nombre_base = payload.nombre_base;
                    ALL_PRODUCTS[localProdIndex].tipo_mascota = payload.tipo_mascota;
                    ALL_PRODUCTS[localProdIndex].colores_count = activeProduct.colores.length;
                    ALL_PRODUCTS[localProdIndex].talles_count = activeProduct.talles.length;
                }

                // Actualizar textos de sólo lectura en el DOM
                document.getElementById('detail-title').innerText = payload.nombre_base;
                document.getElementById('detail-desc').innerText = payload.descripcion;
                document.getElementById('detail-price').innerText = `$${payload.precio.toLocaleString('es-AR', { minimumFractionDigits: 2 })}`;

                const petBadge = document.getElementById('detail-pet');
                const petSelect = document.getElementById('edit-pet');
                petBadge.innerText = petSelect.options[petSelect.selectedIndex].text;

                document.getElementById('detail-stock-min').innerText = payload.stock_minimo;

                // Sincronizar la tarjeta del listado izquierdo
                const card = document.querySelector(`.product-list-card[data-sku="${activeProduct.sku_base}"]`);
                if (card) {
                    card.querySelector('.product-list-title').innerText = payload.nombre_base;
                    card.setAttribute('data-pet', payload.tipo_mascota);
                    const countLabel = card.querySelector('.text-muted.fw-bold');
                    if (countLabel) {
                        const colCount = activeProduct.colores.length;
                        const talCount = activeProduct.talles.length;
                        countLabel.innerText = `${colCount} ${colCount === 1 ? 'Color' : 'Colores'} | ${talCount} ${talCount === 1 ? 'Talle' : 'Talles'}`;
                    }
                }

                // Salir del modo edición y re-renderizar tabla de variantes en modo lectura
                container.classList.remove('is-editing');
                renderVariantsTable();
                alert('¡Producto guardado exitosamente!');
            } else {
                alert('Error al guardar: ' + data.message);
            }
        })
        .catch(error => {
            btnSave.disabled = false;
            btnSave.innerHTML = '<span class="view-mode">✏️ Editar</span><span class="edit-mode" style="color: var(--color-primary);">💾 Guardar</span>';
            console.error('Error:', error);
            alert('Ocurrió un error de red al intentar guardar los cambios.');
        });
}

/**
 * Selecciona una tarjeta de producto en el listado principal,
 * oculta los detalles del producto seleccionado anteriormente,
 * y carga la información del nuevo producto seleccionado.
 * 
 * @function selectProduct
 * @param {HTMLDivElement} card - La tarjeta de producto que se desea seleccionar.
 * @returns {void}
 */
function selectProduct(card) {
    const cards = document.querySelectorAll('.product-list-card');
    cards.forEach(c => c.classList.remove('active'));
    card.classList.add('active');

    const skuBase = card.getAttribute('data-sku');

    // Si ya está en la caché local, renderizarlo inmediatamente
    if (PRODUCT_CACHE[skuBase]) {
        renderProductDetails(PRODUCT_CACHE[skuBase]);
        return;
    }

    // Si no está, mostrar spinner de carga y pedir por AJAX
    const detailContainer = document.getElementById('detail-card-container');
    const loadingSpinner = document.getElementById('detail-loading-spinner');

    detailContainer.classList.add('d-none');
    loadingSpinner.classList.remove('d-none');

    fetch(`/admin/productos/${skuBase}/details`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(product => {
            PRODUCT_CACHE[skuBase] = product;

            // Asegurar que seguimos con la tarjeta activa correspondiente
            const activeCard = document.querySelector('.product-list-card.active');
            if (activeCard && activeCard.getAttribute('data-sku') === skuBase) {
                renderProductDetails(product);
                detailContainer.classList.remove('d-none');
                loadingSpinner.classList.add('d-none');
            }
        })
        .catch(error => {
            console.error('Error fetching details:', error);
            loadingSpinner.classList.add('d-none');
            detailContainer.classList.remove('d-none');
            alert('Error al cargar los detalles del producto.');
        });
}

/**
 * Renderiza los detalles completos del producto seleccionado en el panel derecho.
 * 
 * @function renderProductDetails
 * @param {Object} product - El objeto de producto con toda su información.
 * @returns {void}
 */
function renderProductDetails(product) {
    activeProduct = product;

    document.getElementById('detail-sku-base').innerText = `SKU base: ${product.sku_base}`;
    document.getElementById('detail-title').innerText = product.nombre_base;
    document.getElementById('edit-title').value = product.nombre_base;

    document.getElementById('delete-product-form').action = window.LaravelConfig.productosUrl + "/" + product.sku_base;

    const catContainer = document.getElementById('detail-categories');
    let catHtml = '';
    if (product.categoria_padre) {
        catHtml += `<span class="badge badge-completed" style="font-size: 0.75rem;">${product.categoria_padre}</span> `;
    }
    if (product.categoria_nombre) {
        catHtml += `<span class="badge badge-completed" style="font-size: 0.75rem;">${product.categoria_nombre}</span>`;
    }
    catContainer.innerHTML = catHtml;

    document.getElementById('detail-desc').innerText = product.descripcion || 'Sin descripción.';
    document.getElementById('edit-desc').value = product.descripcion || '';

    const petBadge = document.getElementById('detail-pet');
    const petSelect = document.getElementById('edit-pet');
    if (product.tipo_mascota === 'perro') {
        petBadge.innerText = '🐶 Perros';
        petSelect.value = 'perro';
    } else if (product.tipo_mascota === 'gato') {
        petBadge.innerText = '🐱 Gatos';
        petSelect.value = 'gato';
    } else {
        petBadge.innerText = '🐶 🐱 Ambos';
        petSelect.value = 'ambos';
    }

    document.getElementById('detail-price').innerText = `$${parseFloat(product.precio).toLocaleString('es-AR', { minimumFractionDigits: 2 })}`;
    document.getElementById('edit-price').value = product.precio;

    document.getElementById('detail-stock-min').innerText = product.stock_minimo ?? '-';
    document.getElementById('edit-stock-min').value = product.stock_minimo ?? 0;

    document.getElementById('detail-created').innerText = product.created_at || '-';
    document.getElementById('detail-updated').innerText = product.updated_at || '-';

    if (product.colores.length > 0) {
        currentActiveColor = product.colores[0].key;
        currentImageIndex = 0;
    } else {
        currentActiveColor = null;
        currentImageIndex = 0;
    }

    renderVariantsTable();
    renderGalleryAndUrls();
}

/**
 * Obtiene el array filtrado de productos basado en los criterios actuales
 * de búsqueda, categoría y tipo de mascota.
 * 
 * @function getFilteredProducts
 * @returns {Object[]} Array de productos que coinciden con los filtros.
 */
function getFilteredProducts() {
    const query = document.getElementById('search-prod-input').value.toLowerCase().trim();
    const categoryId = document.getElementById('filter-category').value;
    const pet = document.getElementById('filter-pet').value;

    return ALL_PRODUCTS.filter(prod => {
        // 1. Filtro de Texto (nombre o sku)
        if (query) {
            const matchName = prod.nombre_base.toLowerCase().includes(query);
            const matchSku = prod.sku_base.toLowerCase().includes(query);
            if (!matchName && !matchSku) return false;
        }

        // 2. Filtro de Categoría (directo o subcategorías de un padre)
        if (categoryId) {
            if (prod.categoria_id != categoryId && prod.categoria_padre_id != categoryId) {
                return false;
            }
        }

        // 3. Filtro de Mascota
        if (pet) {
            if (prod.tipo_mascota !== pet) return false;
        }

    }).sort((a, b) => a.nombre_base.localeCompare(b.nombre_base, 'es', { sensitivity: 'base' }));
}

/**
 * Dispara la búsqueda local, reiniciando la paginación y mostrando
 * los primeros 20 productos filtrados.
 * 
 * @function triggerLocalSearch
 * @returns {void}
 */
function triggerLocalSearch() {
    isLoading = true;
    currentPage = 1;

    const wrapper = document.getElementById('product-list-cards-wrapper');
    wrapper.innerHTML = '';

    // Obtener productos filtrados localmente
    filteredProducts = getFilteredProducts();

    // Mostrar los primeros 20
    const firstChunk = filteredProducts.slice(0, 20);
    appendProductsToList(firstChunk);

    hasMorePages = filteredProducts.length > 20;
    isLoading = false;

    // Auto-seleccionar primer producto de los resultados
    const firstCard = wrapper.querySelector('.product-list-card');
    if (firstCard) {
        selectProduct(firstCard);
    } else {
        clearProductDetails();
    }
}

/**
 * Limpia todos los campos del panel de detalles del producto.
 * 
 * @function clearProductDetails
 * @returns {void}
 */
function clearProductDetails() {
    activeProduct = null;
    document.getElementById('detail-sku-base').innerText = '-';
    document.getElementById('detail-title').innerText = 'No se encontraron productos';
    document.getElementById('edit-title').value = '';
    document.getElementById('detail-categories').innerHTML = '';
    document.getElementById('detail-desc').innerText = '';
    document.getElementById('edit-desc').value = '';
    document.getElementById('detail-pet').innerText = '-';
    document.getElementById('detail-price').innerText = '-';
    document.getElementById('edit-price').value = '0';
    document.getElementById('detail-created').innerText = '-';
    document.getElementById('detail-updated').innerText = '-';
    document.getElementById('gallery-main-img').src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    document.getElementById('gallery-thumb-container').innerHTML = '';
    document.getElementById('variants-table-header-row').innerHTML = '<th>Color</th>';
    document.getElementById('variants-table-body').innerHTML = '';
}


/**
 * Inicializa el scroll infinito para la lista de productos.
 * 
 * @function initInfiniteScroll
 * @returns {void}
 */
function initInfiniteScroll() {
    const sentinel = document.getElementById('scroll-sentinel');
    if (!sentinel) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !isLoading && hasMorePages) {
                loadNextPage();
            }
        });
    }, {
        root: document.getElementById('product-list-container'),
        rootMargin: '100px'
    });

    observer.observe(sentinel);
}

/**
 * Carga la siguiente página de productos en el listado.
 * 
 * @function loadNextPage
 * @returns {void}
 */
function loadNextPage() {
    isLoading = true;
    document.getElementById('list-loading-spinner').classList.remove('d-none');

    // Simular un efecto suave de retardo para la UX (50ms)
    setTimeout(() => {
        const start = currentPage * 20;
        const end = (currentPage + 1) * 20;
        const nextChunk = filteredProducts.slice(start, end);

        appendProductsToList(nextChunk);

        currentPage++;
        hasMorePages = filteredProducts.length > end;

        isLoading = false;
        document.getElementById('list-loading-spinner').classList.add('d-none');
    }, 50);
}

/**
 * Añade productos a la lista de productos.
 * 
 * @function appendProductsToList
 * @param {Object[]} products - Productos a añadir a la lista.
 * @returns {void}
 */
function appendProductsToList(products) {
    const wrapper = document.getElementById('product-list-cards-wrapper');
    products.forEach(prod => {
        if (wrapper.querySelector(`.product-list-card[data-sku="${prod.sku_base}"]`)) {
            return;
        }

        const card = document.createElement('div');
        card.className = 'product-list-card';
        card.setAttribute('data-sku', prod.sku_base);
        card.onclick = function () { selectProduct(this); };

        card.innerHTML = `
                    <img src="${prod.thumb}" class="product-list-thumb" alt="${prod.nombre_base}">
                    <div class="product-list-info">
                        <h4 class="product-list-title">${prod.nombre_base}</h4>
                        <span class="product-list-sku">${prod.sku_base}</span>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="badge bg-light text-dark border py-1 small">Activo</span>
                            <small class="text-muted fw-bold">
                                ${prod.colores_count} ${prod.colores_count === 1 ? 'Color' : 'Colores'} | 
                                ${prod.talles_count} ${prod.talles_count === 1 ? 'Talle' : 'Talles'}
                            </small>
                        </div>
                    </div>
                `;
        wrapper.appendChild(card);
    });
    updateProductsCount();
}


/**
 * Actualiza el contador de productos.
 * 
 * @function updateProductsCount
 * @returns {void}
 */
function updateProductsCount() {
    document.getElementById('products-count-text').innerText = `${filteredProducts.length} productos`;
}

/**
 * Inicializa el scroll infinito para la lista de productos.
 * 
 * @function initInfiniteScroll
 * @returns {void}
 */
window.onload = function () {
    const firstCard = document.querySelector('.product-list-card');
    if (firstCard) {
        selectProduct(firstCard);
    }
    updateProductsCount();
    initInfiniteScroll();

    document.getElementById('search-prod-input').addEventListener('input', filterProducts);
    document.getElementById('filter-category').addEventListener('change', filterProducts);
    document.getElementById('filter-pet').addEventListener('change', filterProducts);
};
