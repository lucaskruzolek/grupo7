let modalColeccionInstance = null;
let currentSource = 'file'; // 'file' or 'url'

document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('modalColeccion');
    if (modalElement) {
        modalColeccionInstance = new bootstrap.Modal(modalElement);
    }

    // Set up file input change listener for live preview
    const fileInput = document.getElementById('collection-imagen-file');
    if (fileInput) {
        fileInput.addEventListener('change', handleFilePreview);
    }
});

/**
 * Alterna entre la carga de archivo local y el ingreso de una URL externa
 * @param {string} source - 'file' o 'url'
 */
function setSourceMode(source) {
    currentSource = source;
    
    const fileGroup = document.getElementById('group-source-file');
    const urlGroup = document.getElementById('group-source-url');
    const fileToggle = document.getElementById('toggle-source-file');
    const urlToggle = document.getElementById('toggle-source-url');

    const fileInput = document.getElementById('collection-imagen-file');
    const urlInput = document.getElementById('collection-url-imagen');

    if (source === 'file') {
        fileGroup.style.display = 'block';
        urlGroup.style.display = 'none';
        
        fileToggle.classList.add('active');
        urlToggle.classList.remove('active');
        
        // Remove name attribute from URL to avoid validation if empty
        urlInput.removeAttribute('name');
        fileInput.setAttribute('name', 'imagen_file');
    } else {
        fileGroup.style.display = 'none';
        urlGroup.style.display = 'block';
        
        fileToggle.classList.remove('active');
        urlToggle.classList.add('active');
        
        // Remove name attribute from file input
        fileInput.removeAttribute('name');
        urlInput.setAttribute('name', 'url_imagen');
    }
}

/**
 * Lee el archivo cargado y dibuja una vista previa instantánea en el modal
 */
function handleFilePreview(event) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById('image-preview-container');
    const previewImg = document.getElementById('image-preview-img');

    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
            previewContainer.style.display = 'flex';
        };
        reader.readAsDataURL(file);
    } else {
        previewImg.src = '';
        previewContainer.style.display = 'none';
    }
}

/**
 * Prepara y abre el modal para crear una Nueva Colección
 */
function openCreateColeccionModal() {
    const form = document.getElementById('form-coleccion');
    const title = document.getElementById('modalColeccionTitle');
    const nameInput = document.getElementById('collection-name');
    const descInput = document.getElementById('collection-description');
    const urlInput = document.getElementById('collection-url-imagen');
    const methodContainer = document.getElementById('method-field-container');
    const previewContainer = document.getElementById('image-preview-container');
    const previewImg = document.getElementById('image-preview-img');

    // Resetear formulario
    form.reset();
    form.action = "/admin/colecciones";
    methodContainer.innerHTML = ""; // POST por defecto

    title.innerText = "Nueva Colección 🏷️";
    nameInput.value = "";
    descInput.value = "";
    urlInput.value = "";

    // Ocultar preview
    previewImg.src = "";
    previewContainer.style.display = "none";

    // Por defecto modo archivo
    setSourceMode('file');

    if (modalColeccionInstance) {
        modalColeccionInstance.show();
    }
}

/**
 * Prepara y abre el modal para editar una Colección existente
 * @param {number} id - ID de la colección
 * @param {string} nombre - Nombre actual
 * @param {string} descripcion - Descripción actual
 * @param {string} urlImagen - URL de la imagen actual
 */
function openEditColeccionModal(id, nombre, descripcion, urlImagen) {
    const form = document.getElementById('form-coleccion');
    const title = document.getElementById('modalColeccionTitle');
    const nameInput = document.getElementById('collection-name');
    const descInput = document.getElementById('collection-description');
    const urlInput = document.getElementById('collection-url-imagen');
    const methodContainer = document.getElementById('method-field-container');
    const previewContainer = document.getElementById('image-preview-container');
    const previewImg = document.getElementById('image-preview-img');

    // Resetear
    form.reset();
    form.action = `/admin/colecciones/${id}`;
    methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';

    title.innerText = "Editar Colección ✏️";
    nameInput.value = nombre;
    descInput.value = descripcion || '';

    // Manejar la imagen de portada previa
    if (urlImagen && urlImagen.trim() !== '') {
        previewImg.src = urlImagen;
        previewContainer.style.display = 'flex';
        
        // Si no empieza con http o pertenece a nuestro bucket, podemos considerarlo archivo
        // Pero para dar flexibilidad en edición: si es una URL externa, lo ponemos en modo URL
        const isExternal = urlImagen.startsWith('http') && !urlImagen.includes('r2.cloudflarestorage.com') && !urlImagen.includes('amazonaws.com') && !urlImagen.includes('colecciones/');
        
        if (isExternal) {
            urlInput.value = urlImagen;
            setSourceMode('url');
        } else {
            urlInput.value = '';
            setSourceMode('file');
        }
    } else {
        previewImg.src = '';
        previewContainer.style.display = 'none';
        setSourceMode('file');
    }

    if (modalColeccionInstance) {
        modalColeccionInstance.show();
    }
}
