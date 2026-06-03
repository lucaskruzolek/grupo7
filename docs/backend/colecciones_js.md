# Documentación Técnica del Módulo de Gestión de Colecciones (`colecciones.js`)

Este documento detalla el análisis técnico de las funciones implementadas en el archivo [colecciones.js](file:///c:/Users/lucas/Herd/grupo7/public/js/backend/colecciones.js). Este módulo se encarga de la lógica interactiva del modal de creación y edición de colecciones, gestionando la alternancia entre la subida de archivos locales y URLs externas, así como la previsualización inmediata de imágenes.

---

## Índice de Funciones

1. [setSourceMode](#1-setsourcemode)
2. [handleFilePreview](#2-handlefilepreview)
3. [openCreateColeccionModal](#3-opencreatecoleccionmodal)
4. [openEditColeccionModal](#4-openeditcoleccionmodal)

---

### Inicialización y Eventos Globales

Al cargarse el DOM (`DOMContentLoaded`), el módulo realiza las siguientes tareas de inicialización:
*   Instancia el componente de Bootstrap `Modal` sobre el elemento `#modalColeccion` y lo asigna a la variable global `modalColeccionInstance`.
*   Asigna el escuchador del evento `change` en el input de imagen local `#collection-imagen-file` vinculando la función `handleFilePreview` para generar vistas previas instantáneas.

---

### 1. `setSourceMode`

*   **JSDoc**:
    ```javascript
    /**
     * Alterna entre la carga de archivo local y el ingreso de una URL externa.
     * 
     * @function setSourceMode
     * @param {string} source - El origen de la imagen ('file' o 'url').
     * @returns {void}
     */
    ```
*   **Propósito**: Ajustar la visualización del formulario en el modal según el método que elija el administrador para proveer la imagen de la colección.
*   **Flujo Lógico**:
    1. Actualiza la variable global `currentSource` con el valor del parámetro `source`.
    2. Obtiene las referencias de los grupos de entradas (`#group-source-file`, `#group-source-url`), botones de alternancia (`#toggle-source-file`, `#toggle-source-url`) e inputs de carga (`#collection-imagen-file`, `#collection-url-imagen`).
    3. Si el parámetro `source` es `'file'`:
        *   Muestra el grupo del campo de archivo local (`display = 'block'`) y oculta el grupo de URL externa (`display = 'none'`).
        *   Agrega la clase CSS `.active` al botón de alternancia de archivo y la remueve del botón de URL.
        *   Remueve el atributo `name` del input de URL para evitar validaciones en el servidor en caso de estar vacío, y agrega el atributo `name="imagen_file"` al input del archivo local.
    4. Si el parámetro `source` es cualquier otro valor (típicamente `'url'`):
        *   Oculta el grupo del campo de archivo local (`display = 'none'`) y muestra el de URL externa (`display = 'block'`).
        *   Agrega la clase CSS `.active` al botón de alternancia de URL y la remueve del botón de archivo.
        *   Remueve el atributo `name` del input de archivo local y agrega el atributo `name="url_imagen"` al input de URL externa.
*   **Efectos Secundarios**: Modifica la variable de estado global `currentSource` e interactúa directamente con el DOM (estilos inline `display`, clases interactivas y atributos `name`).
*   **Asincronismo y Dependencias**: Síncrono. Depende de la variable de estado `currentSource` y de la API de manipulación del DOM del navegador.

---

### 2. `handleFilePreview`

*   **JSDoc**:
    ```javascript
    /**
     * Lee el archivo cargado y dibuja una vista previa instantánea en el modal.
     * 
     * @function handleFilePreview
     * @param {Event} event - El evento de cambio (change) del selector de archivos.
     * @returns {void}
     */
    ```
*   **Propósito**: Proporcionar retroalimentación visual inmediata al usuario mostrando en pantalla la imagen local seleccionada antes de enviar el formulario.
*   **Flujo Lógico**:
    1. Extrae el primer elemento de la lista de archivos seleccionados (`event.target.files[0]`).
    2. Localiza en el DOM el contenedor de la vista previa (`#image-preview-container`) y el elemento imagen correspondiente (`#image-preview-img`).
    3. Si existe un archivo válido:
        *   Instancia un nuevo lector de archivos `FileReader`.
        *   Define el callback de finalización `onload` para asignar los datos leídos en Base64 a la propiedad `src` del elemento imagen y forzar la visualización del contenedor (`display = 'flex'`).
        *   Inicia la lectura asíncrona del archivo local en formato de datos Base64 llamando al método `.readAsDataURL(file)`.
    4. Si no se seleccionó ningún archivo o la selección está vacía:
        *   Limpia la propiedad `src` de la imagen de vista previa y oculta el contenedor (`display = 'none'`).
*   **Efectos Secundarios**: Altera directamente la visualización y las propiedades en el DOM de la interfaz de usuario.
*   **Asincronismo y Dependencias**: Asíncrono no bloqueante (ejecuta lógica en diferido mediante el callback `onload`). Depende de la Web API nativa `FileReader` y de la API del DOM.

---

### 3. `openCreateColeccionModal`

*   **JSDoc**:
    ```javascript
    /**
     * Prepara y abre el modal para crear una Nueva Colección.
     * 
     * @function openCreateColeccionModal
     * @returns {void}
     */
    ```
*   **Propósito**: Inicializar la estructura del modal de colecciones en su estado de creación limpio antes de desplegarlo.
*   **Flujo Lógico**:
    1. Obtiene las referencias a los elementos clave del formulario (`#form-coleccion`, `#modalColeccionTitle`, inputs y contenedores multimedia).
    2. Ejecuta `.reset()` en el elemento formulario para borrar entradas de interacciones previas.
    3. Apunta el atributo `action` del formulario a la ruta de almacenamiento `/admin/colecciones` (método POST por defecto).
    4. Vacía el contenedor `#method-field-container` garantizando que no existan directivas ocultas de simulación de métodos (como `_method`).
    5. Actualiza el título del modal en pantalla a "Nueva Colección 🏷️".
    6. Inicializa con cadenas vacías los valores de los inputs de nombre, descripción y URL de imagen externa.
    7. Limpia el origen de la vista previa de la imagen y oculta su contenedor en el DOM.
    8. Ejecuta la función `setSourceMode('file')` para forzar el modo de subida local por defecto.
    9. Valida la existencia de la variable global `modalColeccionInstance` e invoca a `.show()` para abrir la interfaz modal.
*   **Efectos Secundarios**: Reestablece campos de entrada, altera atributos y estilos de visualización en el DOM. Muestra el modal en pantalla.
*   **Asincronismo y Dependencias**: Síncrono. Depende de la función `setSourceMode()`, del objeto global `modalColeccionInstance` y de la clase `bootstrap.Modal`.

---

### 4. `openEditColeccionModal`

*   **JSDoc**:
    ```javascript
    /**
     * Prepara y abre el modal para editar una Colección existente.
     * 
     * @function openEditColeccionModal
     * @param {number} id - ID único de la colección a editar.
     * @param {string} nombre - Nombre comercial actual de la colección.
     * @param {string} descripcion - Descripción textual actual de la colección.
     * @param {string} urlImagen - Dirección de la imagen de portada registrada.
     * @returns {void}
     */
    ```
*   **Propósito**: Rellenar los campos del modal de colecciones con los datos actuales de la entidad seleccionada y adaptarlo para enviar una solicitud de actualización.
*   **Flujo Lógico**:
    1. Obtiene las referencias a los inputs y al contenedor de simulación de método del formulario.
    2. Limpia el formulario con `.reset()`.
    3. Configura el atributo `action` del formulario apuntando a la ruta de actualización `/admin/colecciones/{id}`.
    4. Inserta dentro de `#method-field-container` el input oculto que Laravel requiere para procesar el método PUT: `<input type="hidden" name="_method" value="PUT">`.
    5. Modifica el título visual en el encabezado del modal a "Editar Colección ✏️".
    6. Asigna los valores actuales de `nombre` y `descripcion` (este último cayendo a cadena vacía en caso de ser nulo) en los inputs correspondientes del DOM.
    7. Evalúa la presencia de la portada previa en `urlImagen`:
        *   Si la URL de la imagen existe y no es vacía:
            *   Asigna la URL al elemento de imagen del visor de previsualización y hace visible su contenedor (`display = 'flex'`).
            *   Determina si es una URL externa evaluando si comienza con `'http'` y no contiene cadenas indicativas de almacenamiento interno/bucket (como `'r2.cloudflarestorage.com'`, `'amazonaws.com'` o `'colecciones/'`).
            *   Si se evalúa como externa: asigna la URL al input `#collection-url-imagen` y cambia al modo URL ejecutando `setSourceMode('url')`.
            *   Si se evalúa como interna/archivo local previo: limpia el input de URL externa y cambia al modo de archivo local mediante `setSourceMode('file')`.
        *   Si no tiene imagen de portada asignada:
            *   Limpia la dirección del visor, oculta el contenedor y cambia al modo de archivo local por defecto (`setSourceMode('file')`).
    8. Valida e invoca el método `.show()` sobre la instancia global `modalColeccionInstance` para desplegar el modal.
*   **Efectos Secundarios**: Inyecta elementos HTML (input hidden `_method`), altera propiedades de inputs, fuentes de imagen y clases del DOM. Despliega la ventana modal.
*   **Asincronismo y Dependencias**: Síncrono. Depende de la función `setSourceMode()`, del objeto global `modalColeccionInstance` y de la biblioteca externa de Bootstrap.
