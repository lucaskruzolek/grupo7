# Documentación Técnica del Módulo de Gestión de Categorías (`categorias.js`)

Este documento detalla el análisis técnico de las funciones implementadas en el archivo [categorias.js](file:///c:/Users/lucas/Herd/grupo7/public/js/backend/categorias.js). Este módulo controla el comportamiento dinámico del modal de creación y edición de categorías, permitiendo la adaptación en tiempo real del formulario según el tipo de categoría seleccionado (Principal o Subcategoría) y gestionando las restricciones de los campos del formulario.

---

## Índice de Funciones

1. [handleTypeChange](#1-handletypechange)
2. [openCreateParentModal](#2-opencreateparentmodal)
3. [openCreateSubcategoryModal](#3-opencreatesubcategorymodal)
4. [openEditModal](#4-openeditmodal)

---

### Inicialización y Eventos Globales

Al cargarse el DOM (`DOMContentLoaded`), el módulo realiza la siguiente tarea de inicialización:
*   Instancia el componente de Bootstrap `Modal` sobre el elemento `#modalCategory` y lo asigna a la variable global `modalCategoryInstance`.

Adicionalmente, se registra un escuchador del evento `submit` en el formulario `#form-category`:
*   **Propósito**: Habilitar de manera temporal los selectores bloqueados antes de que la petición HTTP sea enviada al servidor.
*   **Flujo Lógico**:
    1. Intercepta la acción de envío del formulario.
    2. Establece la propiedad `.disabled = false` en los inputs `#category-type` y `#category-parent`.
    3. Esto previene que el navegador omita los valores de estos campos en la carga útil (payload) HTTP POST, comportamiento estándar para elementos deshabilitados.
*   **Efectos Secundarios**: Altera el estado de habilitación de elementos del DOM durante el ciclo de envío.

---

### 1. `handleTypeChange`

*   **JSDoc**:
    ```javascript
    /**
     * Adapta dinámicamente los campos y atributos del formulario según el tipo de categoría.
     * 
     * @function handleTypeChange
     * @returns {void}
     */
    ```
*   **Propósito**: Mostrar u ocultar controles específicos (como el selector de categoría padre o el cargador del icono representativo) y reconfigurar sus atributos `name` basándose en el tipo de categoría.
*   **Flujo Lógico**:
    1. Lee el valor seleccionado actualmente en el selector de tipo de categoría (`#category-type`).
    2. Localiza en el DOM las referencias de los grupos de control (`#parent-select-group`, `#icon-file-group`) y del input selector de la categoría padre (`#category-parent`).
    3. Si el tipo es `'parent'` (Categoría Principal):
        *   Oculta el contenedor del selector de categoría padre (`display = 'none'`).
        *   Remueve el atributo `name` del selector `#category-parent` para evitar el envío del valor `parent_id` (o nulo) en el formulario de la petición HTTP.
        *   Muestra el cargador de iconos de archivo local (`display = 'block'`).
    4. Si el tipo es de cualquier otro valor (típicamente `'child'` o Subcategoría):
        *   Muestra el contenedor del selector de categoría padre (`display = 'block'`).
        *   Agrega el atributo `name="parent_id"` al selector `#category-parent` para que viaje correctamente en el envío HTTP.
        *   Oculta el cargador de iconos de archivo local (`display = 'none'`).
*   **Efectos Secundarios**: Modifica propiedades de visualización y atributos de los inputs del formulario directamente en el DOM.
*   **Asincronismo y Dependencias**: Síncrono. Depende de la API de manipulación del DOM.

---

### 2. `openCreateParentModal`

*   **JSDoc**:
    ```javascript
    /**
     * Prepara y abre el modal en el modo de creación de una nueva Categoría Principal.
     * 
     * @function openCreateParentModal
     * @returns {void}
     */
    ```
*   **Propósito**: Configurar y reestablecer los estados por defecto del formulario en el modal para crear una categoría de nivel superior.
*   **Flujo Lógico**:
    1. Obtiene las referencias a los controles principales del formulario, etiquetas de bloqueo y previsualización.
    2. Invoca el método `.reset()` en el elemento formulario para vaciar todos los campos de interacciones previas.
    3. Configura el atributo `action` del formulario apuntando al endpoint de creación (`"{{ route('admin.categorias.store') }}"`).
    4. Limpia el contenedor `#method-field-container` para asegurar el uso del método HTTP POST estándar.
    5. Cambia el título del modal en pantalla a `"Nueva categoría principal"`.
    6. Restablece el valor del input del nombre a una cadena vacía.
    7. Configura el selector de tipo en `"parent"`, lo deshabilita (`disabled = true`) y muestra el candado visual de bloqueo (`#type-lock-icon`).
    8. Habilita el selector de categoría padre (`disabled = false`), oculta el candado del padre y el texto de ayuda explicativo.
    9. Configura el input de carga del icono: lo define como obligatorio (`required = true`), muestra el asterisco indicador de requerimiento (`display = 'inline'`) y oculta con prioridad el contenedor de previsualización del icono.
    10. Llama a la función `handleTypeChange()` para ocultar los campos correspondientes al tipo principal.
    11. Muestra el modal llamando al método `.show()` de la instancia global `modalCategoryInstance`.
*   **Efectos Secundarios**: Modifica múltiples atributos en el DOM (`action`, `value`, `disabled`, `required`, estilos inline) e interactúa con el modal de Bootstrap.
*   **Asincronismo y Dependencias**: Síncrono. Depende de `handleTypeChange()`, de la variable global `modalCategoryInstance` y de la biblioteca de Bootstrap.

---

### 3. `openCreateSubcategoryModal`

*   **JSDoc**:
    ```javascript
    /**
     * Prepara y abre el modal en el modo de creación de una nueva Subcategoría.
     * 
     * @function openCreateSubcategoryModal
     * @param {number} parentId - ID de la categoría padre a vincular.
     * @param {string} parentName - Nombre de la categoría padre.
     * @returns {void}
     */
    ```
*   **Propósito**: Configurar y bloquear los estados del formulario en el modal para la creación de una subcategoría dependiente de un elemento específico.
*   **Flujo Lógico**:
    1. Obtiene las referencias del formulario y sus componentes.
    2. Resetea el formulario con `.reset()`.
    3. Asigna la ruta de creación (`"{{ route('admin.categorias.store') }}"`) al atributo `action`.
    4. Limpia el campo de método del contenedor oculto.
    5. Actualiza el título del modal a `"Nueva subcategoría para {parentName}"` para guiar visualmente al administrador.
    6. Inicializa el input de nombre como vacío.
    7. Asigna el tipo `"child"` al selector de tipo, lo deshabilita y activa su candado indicador.
    8. Escribe el valor `parentId` en el selector de categoría padre (`#category-parent`), lo deshabilita y visualiza el candado para impedir que el usuario altere el padre preseleccionado. Oculta el texto de ayuda.
    9. Configura el input de icono: lo define como no obligatorio (`required = false`), oculta el asterisco indicador y oculta el contenedor de la vista previa del icono.
    10. Invoca a `handleTypeChange()` para adaptar la interfaz.
    11. Despliega el modal a través de `modalCategoryInstance.show()`.
*   **Efectos Secundarios**: Modifica propiedades de inputs (`value`, `disabled`, `required`, `action`), altera textos del DOM e interactúa con el modal.
*   **Asincronismo y Dependencias**: Síncrono. Depende de `handleTypeChange()`, `modalCategoryInstance` y del DOM del navegador.

---

### 4. `openEditModal`

*   **JSDoc**:
    ```javascript
    /**
     * Prepara y abre el modal en modo edición para una categoría existente.
     * 
     * @function openEditModal
     * @param {number} id - ID de la categoría a editar.
     * @param {string} name - Nombre actual de la categoría.
     * @param {number|null} parentId - ID de la categoría padre, o null si es una categoría principal.
     * @param {string|null} iconoUrl - Dirección URL o ruta local del icono representativo (solo si es categoría principal).
     * @returns {void}
     */
    ```
*   **Propósito**: Configurar los campos del formulario con los valores actuales de la categoría seleccionada y adecuarlo para realizar una actualización parcial.
*   **Flujo Lógico**:
    1. Localiza los inputs, contenedores de simulación de método y previsualizaciones.
    2. Ejecuta `.reset()` sobre el formulario.
    3. Modifica la ruta de envío `action` apuntando a `/admin/categorias/{id}`.
    4. Inyecta en el contenedor de método el input oculto necesario para simular PUT: `<input type="hidden" name="_method" value="PUT">`.
    5. Cambia el título del modal a `"Editar categoría"`.
    6. Asigna el valor `name` al input de nombre.
    7. Configura el input del icono como no obligatorio (`required = false`) y oculta el asterisco (el usuario puede conservar el icono existente sin tener que subir uno nuevo).
    8. Evalúa el valor de `parentId` para bifurcar la configuración:
        *   **Si es una Categoría Principal (`parentId === null`)**:
            *   Asigna el tipo `"parent"` al selector de tipo, lo deshabilita y muestra su candado.
            *   Habilita el selector de categoría padre, ocultando su candado y su texto de ayuda.
            *   Si se provee `iconoUrl` válido: asigna la ruta al visor de previsualización y lo muestra (`display = 'flex'`). En caso contrario, lo oculta.
        *   **Si es una Subcategoría (`parentId !== null`)**:
            *   Asigna el tipo `"child"` al selector de tipo, lo deshabilita y muestra su candado.
            *   Asigna `parentId` al selector de categoría padre, lo deshabilita y muestra su candado.
            *   Muestra el texto explicativo de ayuda `#parent-helper-text` que alerta al usuario de que no se puede cambiar la categoría padre en edición.
            *   Oculta el contenedor de la previsualización de icono.
    9. Invoca a `handleTypeChange()` para reestructurar la visibilidad de los grupos de entradas.
    10. Abre el modal mediante el objeto global `modalCategoryInstance`.
*   **Efectos Secundarios**: Inyecta elementos en el DOM (campo oculto `_method`), altera propiedades de habilitación, obligatoriedad y fuentes de imagen en la interfaz, y muestra el modal.
*   **Asincronismo y Dependencias**: Síncrono. Depende de `handleTypeChange()`, de la instancia global `modalCategoryInstance` y del DOM.
