# Documentación Técnica del Módulo de Gestión de Ventas (`ventas.js`)

Este documento detalla el análisis técnico de las funciones implementadas en el archivo [ventas.js](file:///c:/Users/lucas/Herd/grupo7/public/js/backend/ventas.js). Este módulo administra de forma interactiva el listado, filtrado client-side, visualización de detalles vía AJAX y la actualización de estados de pedidos y facturas en el panel de administración.

---

## Índice de Funciones

1. [selectOrder](#1-selectorder)
2. [loadOrderDetails](#2-loadorderdetails)
3. [renderOrderDetails](#3-renderorderdetails)
4. [renderOrderItems](#4-renderorderitems)
5. [renderOrderTimeline](#5-renderordertimeline)
6. [clearOrderDetails](#6-clearorderdetails)
7. [applyFilters](#7-applyfilters)
8. [filterOrders](#8-filterorders)

---

### 1. `selectOrder`

*   **JSDoc**:
    ```javascript
    /**
     * Selects an order row, highlights it visually, and loads its details.
     * @function selectOrder
     * @param {HTMLElement} row - The selected table row.
     */
    ```
*   **Propósito**: Resaltar visualmente el pedido activo en el listado izquierdo e iniciar la obtención de sus detalles.
*   **Flujo Lógico**:
    1. Obtiene todas las filas de la tabla `.orders-table tbody tr`.
    2. Remueve la clase visual `.row-active` de todas las filas para limpiar la selección anterior.
    3. Añade la clase `.row-active` a la fila cliqueada (`row`).
    4. Lee el ID del pedido desde el atributo de datos `data-order-id` de la fila.
    5. Invoca a `loadOrderDetails(orderId)` pasándole dicho ID.
*   **Efectos Secundarios**: Modifica clases CSS de elementos en el DOM y dispara la carga de datos del pedido.
*   **Asincronismo y Dependencias**: Síncrono. Depende de la existencia de la tabla en el DOM y de la función `loadOrderDetails()`.

---

### 2. `loadOrderDetails`

*   **JSDoc**:
    ```javascript
    /**
     * Handles the asynchronous AJAX loading of order details and manages spinner feedback.
     * @function loadOrderDetails
     * @param {number|string} id - The ID of the order to fetch.
     */
    ```
*   **Propósito**: Solicitar al servidor los detalles completos de una venta de forma asíncrona (AJAX) y gestionar los estados de carga en la UI.
*   **Flujo Lógico**:
    1. Llama a `clearOrderDetails()` para vaciar cualquier dato previo.
    2. Obtiene las referencias de `#detail-content` y `#detail-empty` en el DOM.
    3. Oculta el marcador de panel vacío (`#detail-empty`) y muestra `#detail-content` aplicando opacidad del 50% como feedback de carga.
    4. Realiza una petición `fetch` a la URL `/admin/ventas/${id}` con cabeceras que indican solicitud AJAX (`X-Requested-With: XMLHttpRequest`).
    5. En caso de respuesta exitosa, remueve la opacidad e inyecta la información delegando a `renderOrderDetails()`, `renderOrderItems()` y `renderOrderTimeline()`.
    6. Si el servidor responde con error (`success: false`) o la promesa falla (error de red), quita la opacidad, limpia la ficha llamando a `clearOrderDetails()` y muestra un cuadro de alerta.
*   **Efectos Secundarios**: Altera clases CSS de carga, modifica la variable global `activeOrder`, maneja el DOM y lanza `alert()`.
*   **Asincronismo y Dependencias**: Asíncrono (Promise). Depende de la API Fetch de red y de las funciones `clearOrderDetails()`, `renderOrderDetails()`, `renderOrderItems()` y `renderOrderTimeline()`.

---

### 3. `renderOrderDetails`

*   **JSDoc**:
    ```javascript
    /**
     * Renders general client information, payment method, and total of the order.
     * Configures actions for links and forms.
     * @function renderOrderDetails
     * @param {Object} order - The order data object.
     */
    ```
*   **Propósito**: Pintar la información de cabecera del pedido (cliente, método de pago, totales) en el panel de detalle y enlazar los formularios a la venta activa.
*   **Flujo Lógico**:
    1. Formatea el ID de pedido con padding de ceros a la izquierda y lo escribe en `#det-order-id`.
    2. Escribe el nombre completo y correo del cliente en `#det-cliente-nombre` y `#det-cliente-email`.
    3. Asigna la descripción del método de pago en `#det-forma-pago`.
    4. Formatea el total del pedido con formato monetario regional (`es-AR`) en `#det-total`.
    5. Actualiza el enlace del botón de descarga de facturas (`#det-btn-factura`) a la ruta `/admin/ventas/${order.id}/factura`.
    6. Modifica el atributo `action` del formulario de actualización de estado (`#det-form-estado`) a la ruta `/admin/ventas/${order.id}/estado`.
*   **Efectos Secundarios**: Modifica contenidos de texto y atributos de enlaces/formularios en el DOM.
*   **Asincronismo y Dependencias**: Síncrono. Depende de las propiedades del objeto de datos `order` y de la API del DOM.

---

### 4. `renderOrderItems`

*   **JSDoc**:
    ```javascript
    /**
     * Iterates and renders the purchased product items inside the details container.
     * @function renderOrderItems
     * @param {Array<Object>} details - Array of purchase detail items.
     */
    ```
*   **Propósito**: Mostrar detalladamente en una lista cada uno de los artículos comprados en la venta actual (con foto, talla, color, cantidad y precio).
*   **Flujo Lógico**:
    1. Obtiene el elemento `#det-items-container` y vacía su HTML interno.
    2. Itera a través del arreglo `details`.
    3. Por cada item, evalúa si posee una imagen de variante; si no la tiene, utiliza una miniatura genérica por defecto.
    4. Formatea el precio unitario del item con formato regional de Argentina.
    5. Concatena un bloque de marcado HTML en `itemsContainer.innerHTML` estructurando la tarjeta del artículo con su metadata correspondiente.
*   **Efectos Secundarios**: Modificaciones estructurales en el DOM de la lista de items.
*   **Asincronismo y Dependencias**: Síncrono. Depende del arreglo de items y del DOM.

---

### 5. `renderOrderTimeline`

*   **JSDoc**:
    ```javascript
    /**
     * Manages timeline classes (active, completed) and date texts.
     * @function renderOrderTimeline
     * @param {Object} order - The order data object.
     */
    ```
*   **Propósito**: Actualizar la línea de tiempo interactiva de estados del pedido (Carrito, Pago y Despacho) y configurar el botón administrativo para enviar/revertir despachos según el estado actual.
*   **Flujo Lógico**:
    1. Escribe la fecha de creación del carrito en `#tl-carrito-date`.
    2. Marca el paso de Pago (`#tl-pago-item`) como completado (`.completed`) y escribe su fecha en `#tl-pago-date`.
    3. Evalúa la propiedad `order.estado`:
        *   **Si es `DESPACHADO`**: Activa el paso de despacho como completado (`.completed`), escribe la fecha de envío en `#tl-despacho-date`, configura el input del formulario (`#det-input-estado`) a `CONFIRMADO` y actualiza el botón de envío a "Revertir despacho" (estilo secundario).
        *   **Si no es `DESPACHADO` (CONFIRMADO)**: Remueve la clase completado del paso de despacho, lo marca como activo, escribe "Pendiente de despacho" en `#tl-despacho-date`, establece el valor de input a `DESPACHADO` y configura el botón a "Marcar como Despachado" (estilo primario).
*   **Efectos Secundarios**: Modifica clases CSS de los items de la línea de tiempo, altera valores de formularios y cambia estilos del botón del estado.
*   **Asincronismo y Dependencias**: Síncrono. Depende de los estados del objeto `order` y del DOM.

---

### 6. `clearOrderDetails`

*   **JSDoc**:
    ```javascript
    /**
     * Resets the order details UI back to its initial empty placeholder state.
     * @function clearOrderDetails
     */
    ```
*   **Propósito**: Limpiar todas las variables y textos del panel derecho, restableciendo la pantalla a su estado vacío inicial (Empty State).
*   **Flujo Lógico**:
    1. Establece la variable global `activeOrder` a `null`.
    2. Añade la clase `.d-none` a `#detail-content` y remueve `.d-none` de `#detail-empty` para ocultar la ficha del pedido.
    3. Limpia todos los elementos dinámicos del panel inyectándoles valores neutros (`-`, `#`, `$0,00` o texto vacío) para evitar parpadeos con datos obsoletos en la próxima selección.
*   **Efectos Secundarios**: Muta `activeOrder` a `null` y limpia/oculta selectores DOM del detalle.
*   **Asincronismo y Dependencias**: Síncrono. Depende de la existencia de los selectores en el DOM.

---

### 7. `applyFilters`

*   **JSDoc**:
    ```javascript
    /**
     * Event handler that collects input filters and triggers local row filtering.
     * @function applyFilters
     */
    ```
*   **Propósito**: Recopilar los valores de los controles de filtro (búsqueda, estado y método de pago) en el DOM y disparar el algoritmo de filtrado.
*   **Flujo Lógico**:
    1. Lee los valores del buscador textual (`#search-pedido`), filtro de estado (`#filter-estado`) y filtro de método de pago (`#filter-pago`).
    2. Aplica conversión a minúsculas y remueve espacios en blanco a la cadena de búsqueda.
    3. Llama a `filterOrders(query, status, paymentMethod)` enviándole los parámetros limpios.
*   **Efectos Secundarios**: Ninguno directo en el DOM (los delega).
*   **Asincronismo y Dependencias**: Síncrono. Depende de los inputs del DOM y de la función `filterOrders()`.

---

### 8. `filterOrders`

*   **JSDoc**:
    ```javascript
    /**
     * Physically hides or shows order rows in the DOM table based on matching criteria.
     * @function filterOrders
     * @param {string} query - The textual search query.
     * @param {string} status - Selected status filter.
     * @param {string} paymentMethod - Selected payment method ID filter.
     */
    ```
*   **Propósito**: Filtrar las filas de la tabla de pedidos, ocultando las que no coincidan con los criterios y deseleccionando/limpiando la vista de detalles si el pedido seleccionado actualmente es filtrado.
*   **Flujo Lógico**:
    1. Obtiene todas las filas (`<tr>`) de la tabla de pedidos.
    2. Itera por cada fila extrayendo ID del pedido, nombre/correo del cliente, estado del pedido y ID de forma de pago.
    3. Evalúa las coincidencias:
        *   `matchSearch`: true si la query está vacía o si el ID, nombre o correo del cliente contienen el término de búsqueda.
        *   `matchStatus`: true si el estado seleccionado es "all" o coincide con el estado del pedido.
        *   `matchPayment`: true si la forma de pago seleccionada es "all" o coincide con la del pedido.
    4. Si se cumplen todas las condiciones, remueve la clase `.d-none` de la fila.
    5. Si alguna falla, añade `.d-none` para ocultar la fila. Adicionalmente, si la fila que se está ocultando poseía la clase `.row-active` (el pedido seleccionado actualmente), remueve la clase e invoca a `clearOrderDetails()` para ocultar la ficha del detalle.
*   **Efectos Secundarios**: Modifica la visibilidad de las filas en la tabla del DOM y limpia detalles si el seleccionado se oculta.
*   **Asincronismo y Dependencias**: Síncrono. Depende de `clearOrderDetails()`.
