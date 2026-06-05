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
let searchTimeout = null;


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

    handleNewProductCategoryChange();

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

    // Cargar grilla de imágenes locales para edición
    const editContainer = document.getElementById('edit-images-container');
    if (editContainer) {
        editContainer.innerHTML = '';
        const images = media.images || [];
        images.forEach(img => {
            const isCover = img.orden === 1;
            const div = document.createElement('div');
            div.className = `position-relative border rounded p-1 d-flex flex-column align-items-center bg-white ${isCover ? 'border-primary shadow-sm' : ''}`;
            div.style.width = '100px';
            div.style.height = '120px';

            div.innerHTML = `
                <img src="${img.url}" style="width: 100%; height: 70px; object-fit: cover;" class="rounded mb-1">
                <div class="d-flex justify-content-between w-100 px-1" style="font-size: 0.75rem;">
                    <button type="button" class="btn btn-xs p-0 border-0 text-danger" onclick="event.stopPropagation(); deleteProductImage(${img.id}, '${currentActiveColor}')" title="Eliminar">🗑️</button>
                    ${isCover
                    ? '<span class="badge bg-primary text-white" style="font-size: 0.6rem; padding: 2px 4px;">Portada</span>'
                    : `<button type="button" class="btn btn-xs p-0 border-0 text-primary" onclick="event.stopPropagation(); setAsCoverImage(${img.id}, '${currentActiveColor}')" title="Hacer Portada">⭐</button>`
                }
                </div>
            `;
            editContainer.appendChild(div);
        });
    }
}

/**
 * Escucha los eventos del selector de archivos y de arrastre para la carga local de imágenes.
 */
function setupImageUploadEvents() {
    const fileInput = document.getElementById('image-upload-input');
    const uploadZone = document.querySelector('.image-upload-zone');

    if (!fileInput || !uploadZone) return;

    fileInput.addEventListener('change', function () {
        handleFilesUpload(this.files);
    });

    // Drag and Drop styling and behavior
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadZone.style.backgroundColor = 'var(--neutral-200)';
            uploadZone.style.borderColor = 'var(--color-primary)';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadZone.style.backgroundColor = 'var(--neutral-100)';
            uploadZone.style.borderColor = 'var(--neutral-400)';
        }, false);
    });

    uploadZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFilesUpload(files);
    }, false);
}

/**
 * Realiza las llamadas ajax para subir imágenes locales.
 */
function handleFilesUpload(files) {
    if (!activeProduct || !currentActiveColor) {
        alert('Seleccione un producto y color primero.');
        return;
    }

    const skuBase = activeProduct.sku_base;
    const colorObj = activeProduct.colores.find(c => c.key === currentActiveColor);
    if (!colorObj) return;

    const skuColor = `${skuBase}-${colorObj.nombre.toUpperCase()}`;

    Array.from(files).forEach(file => {
        if (file.size > 5 * 1024 * 1024) {
            alert(`La imagen "${file.name}" supera el límite de 5MB.`);
            return;
        }

        const formData = new FormData();
        formData.append('sku_color', skuColor);
        formData.append('image', file);

        const editContainer = document.getElementById('edit-images-container');
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'position-relative border rounded p-1 d-flex flex-column align-items-center justify-content-center bg-light';
        loadingDiv.style.width = '100px';
        loadingDiv.style.height = '120px';
        loadingDiv.innerHTML = `<span class="spinner-border spinner-border-sm text-secondary mb-1" role="status"></span><span style="font-size: 0.6rem;">Subiendo...</span>`;
        editContainer.appendChild(loadingDiv);

        fetch(window.LaravelConfig.uploadImageUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.LaravelConfig.csrfToken
            },
            body: formData
        })
            .then(response => {
                if (!response.ok) throw new Error('Upload failed');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const media = activeProduct.colorMedia[currentActiveColor];
                    if (!media.images) media.images = [];
                    media.images.push(data.image);
                    media.images.sort((a, b) => a.orden - b.orden);

                    const urls = media.images.map(img => img.url);
                    media.main = urls.length > 0 ? urls[0] : window.LaravelConfig.defaultImagePath;
                    media.thumbs = urls.length > 0 ? urls : [window.LaravelConfig.defaultImagePath];
                    media.urls = urls.length > 0 ? urls : [window.LaravelConfig.defaultImagePath];

                    renderGalleryAndUrls();
                } else {
                    alert('Error al subir: ' + data.message);
                    renderGalleryAndUrls();
                }
            })
            .catch(error => {
                console.error('Error uploading:', error);
                alert('Ocurrió un error al subir la imagen.');
                renderGalleryAndUrls();
            });
    });
}

/**
 * Elimina una imagen del backend de forma asíncrona.
 */
function deleteProductImage(id, colorKey) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta imagen?')) return;

    fetch(`${window.LaravelConfig.deleteImageUrl}/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': window.LaravelConfig.csrfToken,
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) throw new Error('Delete failed');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const media = activeProduct.colorMedia[colorKey];
                media.images = media.images.filter(i => i.id !== id);
                media.images.sort((a, b) => a.orden - b.orden);
                media.images.forEach((img, idx) => img.orden = idx + 1);

                const urls = media.images.map(img => img.url);
                media.main = urls.length > 0 ? urls[0] : window.LaravelConfig.defaultImagePath;
                media.thumbs = urls.length > 0 ? urls : [window.LaravelConfig.defaultImagePath];
                media.urls = urls.length > 0 ? urls : [window.LaravelConfig.defaultImagePath];

                renderGalleryAndUrls();
            } else {
                alert('Error al eliminar: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error deleting:', error);
            alert('Ocurrió un error al intentar eliminar la imagen.');
        });
}

/**
 * Define una imagen como portada asíncronamente.
 */
function setAsCoverImage(id, colorKey) {
    fetch(`${window.LaravelConfig.coverImageUrl}/${id}/cover`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.LaravelConfig.csrfToken,
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) throw new Error('Set cover failed');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const media = activeProduct.colorMedia[colorKey];
                media.images.forEach(img => {
                    if (img.id === id) {
                        img.orden = 1;
                    } else {
                        if (img.orden < 2) img.orden = 2;
                    }
                });
                media.images.sort((a, b) => a.orden - b.orden);
                media.images.forEach((img, idx) => img.orden = idx + 1);

                const urls = media.images.map(img => img.url);
                media.main = urls.length > 0 ? urls[0] : window.LaravelConfig.defaultImagePath;
                media.thumbs = urls.length > 0 ? urls : [window.LaravelConfig.defaultImagePath];
                media.urls = urls.length > 0 ? urls : [window.LaravelConfig.defaultImagePath];

                renderGalleryAndUrls();
            } else {
                alert('Error al establecer portada: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error setting cover:', error);
            alert('Ocurrió un error al intentar establecer la imagen de portada.');
        });
}

/**

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

/**
 * Renderiza o actualiza la tabla de variantes del producto.
 * 
 * @function renderVariantsTable
 * @returns {void}
 */
function renderVariantsTable() {
    if (!activeProduct) return;

    const headerRow = document.getElementById('variants-table-header-row');
    const tbody = document.getElementById('variants-table-body');

    const acceptsSize = categoryAcceptsSize(activeProduct.categoria_id);
    const acceptsColor = categoryAcceptsColor(activeProduct.categoria_id);

    const isEditing = document.getElementById('detail-card-container').classList.contains('is-editing');

    // Obtener stock mínimo del producto (por defecto 2 si no está definido)
    const minStock = activeProduct.stock_minimo !== undefined && activeProduct.stock_minimo !== null ? parseInt(activeProduct.stock_minimo) : 2;

    // Control de visibilidad de botones para agregar talle/color
    const btnAddTalle = document.getElementById('btn-add-talle-trigger');
    const btnAddColor = document.getElementById('btn-add-color-trigger');
    if (btnAddTalle) btnAddTalle.style.display = acceptsSize ? 'inline-block' : 'none';
    if (btnAddColor) btnAddColor.style.display = acceptsColor ? 'inline-block' : 'none';

    // 1. Dibujar Encabezado
    let headerHtml = '';
    if (acceptsColor) {
        headerHtml += '<th>Color</th>';
    }

    if (acceptsSize) {
        const standardOrder = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        activeProduct.talles.sort((a, b) => standardOrder.indexOf(a) - standardOrder.indexOf(b));
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
    } else {
        headerHtml += '<th class="text-center">Stock</th>';
    }
    headerRow.innerHTML = headerHtml;

    // 2. Dibujar Body Rows
    let bodyHtml = '';
    activeProduct.colores.forEach((color, idx) => {
        const isActive = color.key === currentActiveColor;
        let rowHtml = `<tr class="${isActive ? 'color-row-active' : ''}" data-color="${color.key}" data-color-id="${color.id}" data-color-name="${color.nombre}" onclick="selectColorRow(this)">`;

        if (acceptsColor) {
            rowHtml += `
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
                </td>`;
        }

        if (acceptsSize) {
            activeProduct.talles.forEach(talle => {
                const variant = activeProduct.variantes[color.key] ? activeProduct.variantes[color.key][talle] : null;
                rowHtml += `<td class="text-center" data-talle="${talle}">`;
                if (variant) {
                    const stock = variant.stock;
                    rowHtml += `
                        <span class="stock-badge-text ${stock <= minStock ? 'stock-badge-low' : ''}">${stock} un</span>
                        <div class="stock-edit-wrapper" style="display: ${isEditing ? 'inline-flex' : 'none'}; align-items: center; justify-content: center; gap: 4px;">
                            <input type="number" class="stock-badge-input form-control shadow-none" value="${stock}" data-color-id="${color.id}" data-talle="${talle}" style="display: inline-block; width: 60px; padding: 0.2rem 0.4rem; font-size: 0.85rem; text-align: center; border: 1px solid var(--neutral-300); border-radius: 6px;">
                            <button type="button" class="btn btn-sm p-0 lh-1 border-0 text-danger" 
                                    style="font-size: 1.15rem; background: none; font-weight: bold; cursor: pointer;"
                                    onclick="event.stopPropagation(); deleteVariant('${color.key}', '${talle}')"
                                    title="Eliminar variante">
                                    ×
                            </button>
                        </div>`;
                } else {
                    rowHtml += `<button class="btn btn-add-var-cell" onclick="openCreateVariationModal(this)">+</button>`;
                }
                rowHtml += `</td>`;
            });
        } else {
            const talle = '-';
            const variant = activeProduct.variantes[color.key] ? activeProduct.variantes[color.key][talle] : null;
            rowHtml += `<td class="text-center" data-talle="${talle}">`;
            if (variant) {
                const stock = variant.stock;
                rowHtml += `
                    <span class="stock-badge-text ${stock <= minStock ? 'stock-badge-low' : ''}">${stock} un</span>
                    <div class="stock-edit-wrapper" style="display: ${isEditing ? 'inline-flex' : 'none'}; align-items: center; justify-content: center; gap: 4px;">
                        <input type="number" class="stock-badge-input form-control shadow-none" value="${stock}" data-color-id="${color.id}" data-talle="${talle}" style="display: inline-block; width: 60px; padding: 0.2rem 0.4rem; font-size: 0.85rem; text-align: center; border: 1px solid var(--neutral-300); border-radius: 6px;">
                    </div>`;
            } else {
                rowHtml += `<button class="btn btn-add-var-cell" onclick="openCreateVariationModal(this)">+</button>`;
            }
            rowHtml += `</td>`;
        }

        rowHtml += '</tr>';
        bodyHtml += rowHtml;
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
    const newCollectionId = document.getElementById('edit-collection').value;

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
        coleccion_id: newCollectionId ? parseInt(newCollectionId) : null,
        activo: document.getElementById('detail-active-toggle')
            ? (document.getElementById('detail-active-toggle').checked ? 1 : 0)
            : ((activeProduct && activeProduct.activo) ? 1 : 0),
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
                activeProduct.coleccion_id = payload.coleccion_id;
                const colSelect = document.getElementById('edit-collection');
                activeProduct.coleccion_nombre = (payload.coleccion_id && colSelect && colSelect.selectedIndex >= 0) ? colSelect.options[colSelect.selectedIndex].text : 'Sin colección';
                activeProduct.activo = (payload.activo == 1 || payload.activo === true);

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
                    ALL_PRODUCTS[localProdIndex].coleccion_id = payload.coleccion_id;
                    ALL_PRODUCTS[localProdIndex].coleccion_nombre = activeProduct.coleccion_nombre;
                    ALL_PRODUCTS[localProdIndex].activo = activeProduct.activo;
                }

                // Actualizar textos de sólo lectura en el DOM
                document.getElementById('detail-title').innerText = payload.nombre_base;
                document.getElementById('detail-desc').innerText = payload.descripcion;
                document.getElementById('detail-price').innerText = `$${parseFloat(payload.precio).toLocaleString('es-AR', { minimumFractionDigits: 2 })}`;
                document.getElementById('detail-collection').innerText = activeProduct.coleccion_nombre;
                const activeStatus = document.getElementById('detail-active-status');
                if (activeStatus) {
                    activeStatus.innerText = activeProduct.activo ? 'Activo' : 'Inactivo';
                    activeStatus.className = `badge ${activeProduct.activo ? 'bg-success text-white' : 'bg-secondary text-white'} border py-1 small`;
                }

                const petBadge = document.getElementById('detail-pet');
                const petSelect = document.getElementById('edit-pet');
                if (petSelect && petSelect.selectedIndex >= 0) {
                    petBadge.innerText = petSelect.options[petSelect.selectedIndex].text;
                }

                document.getElementById('detail-stock-min').innerText = payload.stock_minimo;

                // Sincronizar la tarjeta del listado izquierdo
                const card = document.querySelector(`.product-list-card[data-sku="${activeProduct.sku_base}"]`);
                if (card) {
                    card.querySelector('.product-list-title').innerText = payload.nombre_base;
                    card.setAttribute('data-pet', payload.tipo_mascota);
                    const badge = card.querySelector('.active-status-badge');
                    if (badge) {
                        badge.className = `badge ${activeProduct.activo ? 'bg-success text-white' : 'bg-secondary text-white'} border py-1 small active-status-badge`;
                        badge.innerText = activeProduct.activo ? 'Activo' : 'Inactivo';
                    }
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

    document.getElementById('detail-category-parent').innerText = product.categoria_padre || '-';
    document.getElementById('detail-category-child').innerText = product.categoria_nombre || '-';

    document.getElementById('detail-desc').innerText = product.descripcion || 'Sin descripción.';
    document.getElementById('edit-desc').value = product.descripcion || '';

    const petBadge = document.getElementById('detail-pet');
    const petSelect = document.getElementById('edit-pet');
    if (product.tipo_mascota === 'perro') {
        petBadge.innerText = 'Perros';
        petSelect.value = 'perro';
    } else if (product.tipo_mascota === 'gato') {
        petBadge.innerText = 'Gatos';
        petSelect.value = 'gato';
    } else {
        petBadge.innerText = 'Ambos';
        petSelect.value = 'ambos';
    }

    document.getElementById('detail-price').innerText = `$${parseFloat(product.precio).toLocaleString('es-AR', { minimumFractionDigits: 2 })}`;
    document.getElementById('edit-price').value = product.precio;

    document.getElementById('detail-stock-min').innerText = product.stock_minimo ?? '-';
    document.getElementById('edit-stock-min').value = product.stock_minimo ?? 0;

    document.getElementById('detail-created').innerText = product.created_at || '-';
    document.getElementById('detail-updated').innerText = product.updated_at || '-';

    const activeStatus = document.getElementById('detail-active-status');
    if (activeStatus) {
        activeStatus.innerText = product.activo ? 'Activo' : 'Inactivo';
        activeStatus.className = `badge ${product.activo ? 'bg-success text-white' : 'bg-secondary text-white'} border py-1 small`;
    }
    const activeToggle = document.getElementById('detail-active-toggle');
    if (activeToggle) {
        activeToggle.checked = !!product.activo;
    }
    document.getElementById('detail-collection').innerText = product.coleccion_nombre || 'Sin colección';
    document.getElementById('edit-collection').value = product.coleccion_id || '';

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
            if (pet === 'perro') {
                if (prod.tipo_mascota !== 'perro' && prod.tipo_mascota !== 'ambos') return false;
            } else if (pet === 'gato') {
                if (prod.tipo_mascota !== 'gato' && prod.tipo_mascota !== 'ambos') return false;
            } else {
                if (prod.tipo_mascota !== pet) return false;
            }
        }

        return true;
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
 * Implementa un mecanismo antirrebote (debounce) básico para agrupar las llamadas
 * de filtrado de productos cuando el usuario escribe en el buscador o cambia filtros.
 * 
 * @function filterProducts
 * @returns {void}
 */
function filterProducts() {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        triggerLocalSearch();
    }, 250);
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
    document.getElementById('detail-category-parent').innerText = '-';
    document.getElementById('detail-category-child').innerText = '-';
    document.getElementById('detail-desc').innerText = '';
    document.getElementById('edit-desc').value = '';
    document.getElementById('detail-pet').innerText = '-';
    document.getElementById('detail-price').innerText = '-';
    document.getElementById('edit-price').value = '0';
    document.getElementById('detail-created').innerText = '-';
    document.getElementById('detail-updated').innerText = '-';
    const activeStatus = document.getElementById('detail-active-status');
    if (activeStatus) {
        activeStatus.innerText = '-';
        activeStatus.className = 'badge border py-1 small';
    }
    const activeToggle = document.getElementById('detail-active-toggle');
    if (activeToggle) {
        activeToggle.checked = false;
    }
    document.getElementById('detail-collection').innerText = '-';
    document.getElementById('edit-collection').value = '';
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
                            <span class="badge ${prod.activo ? 'bg-success text-white' : 'bg-secondary text-white'} border py-1 small active-status-badge">
                                ${prod.activo ? 'Activo' : 'Inactivo'}
                            </span>
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
    setupImageUploadEvents();

    const categorySelector = document.getElementById('new-prod-category');
    if (categorySelector) {
        categorySelector.addEventListener('change', handleNewProductCategoryChange);
    }

    document.getElementById('search-prod-input').addEventListener('input', filterProducts);
    document.getElementById('filter-category').addEventListener('change', filterProducts);
    document.getElementById('filter-pet').addEventListener('change', filterProducts);
};

/**
 * Verifica si una categoría requiere talle.
 * 
 * @function categoryAcceptsSize
 * @param {number} catId - ID de la categoría.
 * @returns {boolean} True si la categoría requiere talle, false en caso contrario.
 */
function categoryAcceptsSize(catId) {
    if (!catId || !window.LaravelConfig.categoriasSystem) return true;
    const cat = window.LaravelConfig.categoriasSystem.find(c => c.id == catId);
    if (!cat) return true;
    if (cat.pide_talle !== null && cat.pide_talle !== undefined) {
        return cat.pide_talle == 1 || cat.pide_talle === true;
    }
    if (cat.parent_id) {
        const parent = window.LaravelConfig.categoriasSystem.find(c => c.id == cat.parent_id);
        if (parent && parent.pide_talle !== null && parent.pide_talle !== undefined) {
            return parent.pide_talle == 1 || parent.pide_talle === true;
        }
    }
    return true;
}

/**
 * Verifica si una categoría requiere color.
 * 
 * @function categoryAcceptsColor
 * @param {number} catId - ID de la categoría.
 * @returns {boolean} True si la categoría requiere color, false en caso contrario.
 */
function categoryAcceptsColor(catId) {
    if (!catId || !window.LaravelConfig.categoriasSystem) return true;
    const cat = window.LaravelConfig.categoriasSystem.find(c => c.id == catId);
    if (!cat) return true;
    if (cat.pide_color !== null && cat.pide_color !== undefined) {
        return cat.pide_color == 1 || cat.pide_color === true;
    }
    if (cat.parent_id) {
        const parent = window.LaravelConfig.categoriasSystem.find(c => c.id == cat.parent_id);
        if (parent && parent.pide_color !== null && parent.pide_color !== undefined) {
            return parent.pide_color == 1 || parent.pide_color === true;
        }
    }
    return true;
}

/**
 * Maneja el cambio de categoría en el formulario de creación de productos.
 * Actualiza la visibilidad de los campos de talle y color según la categoría seleccionada.
 * 
 * @function handleNewProductCategoryChange
 * @returns {void}
 */
function handleNewProductCategoryChange() {
    const categorySelector = document.getElementById('new-prod-category');
    if (!categorySelector) return;
    const categoryId = parseInt(categorySelector.value);
    const acceptsSize = categoryAcceptsSize(categoryId);
    const acceptsColor = categoryAcceptsColor(categoryId);

    const talleContainer = document.getElementById('new-variant-talle').closest('.col-3');
    const colorContainer = document.getElementById('new-variant-color').closest('.col-5');

    if (talleContainer) {
        if (!acceptsSize) {
            talleContainer.style.setProperty('display', 'none', 'important');
            document.getElementById('new-variant-talle').value = '-';
        } else {
            talleContainer.style.display = 'block';
        }
    }

    if (colorContainer) {
        if (!acceptsColor) {
            colorContainer.style.setProperty('display', 'none', 'important');
            const unicoOption = Array.from(document.getElementById('new-variant-color').options).find(o => o.text.toLowerCase() === 'único');
            if (unicoOption) {
                document.getElementById('new-variant-color').value = unicoOption.value;
            } else if (document.getElementById('new-variant-color').options.length > 0) {
                document.getElementById('new-variant-color').selectedIndex = 0;
            }
        } else {
            colorContainer.style.display = 'block';
        }
    }
}

