
let modalCategoryInstance = null;

document.addEventListener('DOMContentLoaded', function () {
    modalCategoryInstance = new bootstrap.Modal(document.getElementById('modalCategory'));
});

/**
 * Modifica el formulario según la selección de Tipo de Categoría
 */
function handleTypeChange() {
    const type = document.getElementById('category-type').value;
    const parentGroup = document.getElementById('parent-select-group');
    const iconGroup = document.getElementById('icon-file-group');
    const parentSelect = document.getElementById('category-parent');

    if (type === 'parent') {
        parentGroup.style.display = 'none';
        parentSelect.removeAttribute('name'); // No envía parent_id
        iconGroup.style.display = 'block'; // Muestra el cargador de icono
    } else {
        parentGroup.style.display = 'block';
        parentSelect.setAttribute('name', 'parent_id'); // Envía parent_id
        iconGroup.style.display = 'none'; // Oculta el cargador de icono
    }
}

/**
 * Prepara el modal para crear una Categoría Principal
 */
function openCreateParentModal() {
    const form = document.getElementById('form-category');
    const title = document.getElementById('modalCategoryTitle');
    const typeSelect = document.getElementById('category-type');
    const nameInput = document.getElementById('category-name');
    const parentSelect = document.getElementById('category-parent');
    const methodContainer = document.getElementById('method-field-container');
    const lockIcon = document.getElementById('type-lock-icon');
    const parentLockIcon = document.getElementById('parent-lock-icon');
    const parentHelperText = document.getElementById('parent-helper-text');
    const iconInput = document.getElementById('category-icono');
    const iconRequiredAsterisk = document.getElementById('icon-required-asterisk');
    const previewContainer = document.getElementById('icon-preview-container');

    // Resetear formulario
    form.reset();
    form.action = "{{ route('admin.categorias.store') }}";
    methodContainer.innerHTML = ""; // Método POST

    title.innerText = "Nueva categoría principal";
    nameInput.value = "";

    // Configurar tipo principal y bloquearlo
    typeSelect.value = "parent";
    typeSelect.disabled = true;
    lockIcon.style.display = "block";

    // Habilitar selección de padre por defecto
    parentSelect.disabled = false;
    parentLockIcon.style.display = "none";
    parentHelperText.style.display = "none";

    // Requerir icono en la creación inicial
    iconInput.required = true;
    iconRequiredAsterisk.style.display = "inline";
    previewContainer.style.setProperty("display", "none", "important");

    handleTypeChange();
    modalCategoryInstance.show();
}

/**
 * Prepara el modal para crear una Subcategoría vinculada a un padre
 */
function openCreateSubcategoryModal(parentId, parentName) {
    const form = document.getElementById('form-category');
    const title = document.getElementById('modalCategoryTitle');
    const typeSelect = document.getElementById('category-type');
    const nameInput = document.getElementById('category-name');
    const parentSelect = document.getElementById('category-parent');
    const methodContainer = document.getElementById('method-field-container');
    const lockIcon = document.getElementById('type-lock-icon');
    const parentLockIcon = document.getElementById('parent-lock-icon');
    const parentHelperText = document.getElementById('parent-helper-text');
    const iconInput = document.getElementById('category-icono');
    const iconRequiredAsterisk = document.getElementById('icon-required-asterisk');
    const previewContainer = document.getElementById('icon-preview-container');

    // Resetear formulario
    form.reset();
    form.action = "{{ route('admin.categorias.store') }}";
    methodContainer.innerHTML = ""; // Método POST

    title.innerText = `Nueva subcategoría para ${parentName}`;
    nameInput.value = "";

    // Configurar tipo subcategoría y bloquearlo
    typeSelect.value = "child";
    typeSelect.disabled = true;
    lockIcon.style.display = "block";

    // Fijar y deshabilitar el selector de categoría padre
    parentSelect.value = parentId;
    parentSelect.disabled = true;
    parentLockIcon.style.display = "block";
    parentHelperText.style.display = "none";

    // No requiere icono
    iconInput.required = false;
    iconRequiredAsterisk.style.display = "none";
    previewContainer.style.setProperty("display", "none", "important");

    handleTypeChange();
    modalCategoryInstance.show();
}

/**
 * Prepara el modal para editar una categoría existente (padre o hija)
 */
function openEditModal(id, name, parentId, iconoUrl) {
    const form = document.getElementById('form-category');
    const title = document.getElementById('modalCategoryTitle');
    const typeSelect = document.getElementById('category-type');
    const nameInput = document.getElementById('category-name');
    const parentSelect = document.getElementById('category-parent');
    const methodContainer = document.getElementById('method-field-container');
    const lockIcon = document.getElementById('type-lock-icon');
    const parentLockIcon = document.getElementById('parent-lock-icon');
    const parentHelperText = document.getElementById('parent-helper-text');
    const iconInput = document.getElementById('category-icono');
    const iconRequiredAsterisk = document.getElementById('icon-required-asterisk');
    const previewContainer = document.getElementById('icon-preview-container');
    const previewImg = document.getElementById('icon-preview-img');

    // Resetear
    form.reset();
    form.action = `/admin/categorias/${id}`;
    methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';

    title.innerText = "Editar categoría";
    nameInput.value = name;

    // No es obligatorio subir icono en edición
    iconInput.required = false;
    iconRequiredAsterisk.style.display = "none";

    if (parentId === null) {
        // Es categoría principal
        typeSelect.value = "parent";
        typeSelect.disabled = true;
        lockIcon.style.display = "block";

        parentSelect.disabled = false;
        parentLockIcon.style.display = "none";
        parentHelperText.style.display = "none";

        // Mostrar vista previa del icono si existe
        if (iconoUrl && iconoUrl.trim() !== '') {
            previewImg.src = iconoUrl;
            previewContainer.style.setProperty("display", "flex", "important");
        } else {
            previewContainer.style.setProperty("display", "none", "important");
        }
    } else {
        // Es subcategoría
        typeSelect.value = "child";
        typeSelect.disabled = true;
        lockIcon.style.display = "block";

        parentSelect.value = parentId;
        parentSelect.disabled = true;
        parentLockIcon.style.display = "block";
        parentHelperText.style.display = "block"; // Alerta que el padre no se puede modificar

        previewContainer.style.setProperty("display", "none", "important");
    }

    handleTypeChange();
    modalCategoryInstance.show();
}

// Antes de enviar el formulario, habilitamos temporalmente los campos bloqueados para que viajen en la petición
document.getElementById('form-category').addEventListener('submit', function () {
    document.getElementById('category-type').disabled = false;
    document.getElementById('category-parent').disabled = false;
});