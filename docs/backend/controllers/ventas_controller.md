# Documentación Técnica del Controlador de Ventas (`VentaController.php`)

Este documento detalla el análisis técnico de los métodos implementados en el archivo [VentaController.php](file:///c:/Users/lucas/Herd/grupo7/app/Http/Controllers/VentaController.php). Este controlador administra toda la lógica del carrito de compras del cliente (adición, actualización de cantidad, eliminación y checkout seguro y atómico mediante transacciones de base de datos) y la visualización y gestión administrativa de pedidos.

---

## Índice de Métodos

1. [verCarrito](#1-vercarrito)
2. [agregarAlCarrito](#2-agregaralcarrito)
3. [actualizarCantidad](#3-actualizarcantidad)
4. [eliminarDelCarrito](#4-eliminardelcarrito)
5. [checkout](#5-checkout)
6. [adminIndex](#6-adminindex)
7. [adminShow](#7-adminshow)
8. [actualizarEstado](#8-actualizarestado)

---

### 1. `verCarrito`

*   **PHPDoc**:
    ```php
    /**
     * Muestra el carrito activo del usuario autenticado.
     */
    ```
*   **Propósito**: Consultar y mostrar el carrito de compras actual (`CARRITO`) del cliente autenticado con sus detalles de items, variantes de productos (con color e imágenes) y la lista de formas de pago disponibles.
*   **Flujo Lógico**:
    1. Obtiene el ID del usuario autenticado actual.
    2. Realiza una consulta Eloquent en el modelo `Venta` aplicando el scope `carrito()` y cargando diligentemente las relaciones (`detalles.producto.color`, `detalles.producto.imagenes`).
    3. Obtiene todas las formas de pago de la tabla `formas_pago`.
    4. Renderiza la vista `frontend.carrito` pasando las variables `$carrito` y `$formasPago`.
*   **Efectos Secundarios**: Ninguno. Es una consulta de lectura.
*   **Asincronismo y Dependencias**: Síncrono. Depende de los modelos `Venta` y `FormaPago`.

---

### 2. `agregarAlCarrito`

*   **PHPDoc**:
    ```php
    /**
     * Agrega una variante de producto al carrito.
     */
    ```
*   **Propósito**: Añadir unidades de una variante física específica de un producto al carrito de compras del usuario autenticado, verificando la disponibilidad física de inventario.
*   **Flujo Lógico**:
    1. Valida que los parámetros `producto_id` exista en la tabla `productos` y `cantidad` sea un entero mayor o igual a 1.
    2. Recupera la variante física del producto. Si no existe, lanza una excepción 404.
    3. Compara el stock disponible de la variante con la cantidad solicitada. Si el stock es insuficiente, interrumpe y devuelve un error.
    4. Busca el registro de cabecera de la venta en estado `CARRITO` para el usuario autenticado. Si no existe, crea uno nuevo inicializando `total = 0.00`.
    5. Comprueba si la variante de producto ya existe en el detalle del carrito:
        *   **Si ya existe**: Valida que la suma de la cantidad existente y la nueva no exceda el stock físico. Si no lo excede, incrementa la cantidad y actualiza el subtotal (`cantidad * precio_unitario`).
        *   **Si no existe**: Crea un nuevo registro en la tabla `venta_detalles` con el precio unitario actual de la variante y calcula el subtotal.
    6. Invoca el método auxiliar `recalcularTotal()` para recalcular la suma total de la cabecera.
    7. Retorna una redirección exitosa al carrito de compras (o respuesta JSON de éxito en caso de solicitudes AJAX).
*   **Efectos Secundarios**:
    *   Inserta o actualiza un registro en `ventas`.
    *   Inserta o actualiza un registro en `venta_detalles`.
*   **Asincronismo y Dependencias**: Síncrono. Depende de los modelos `Venta`, `VentaDetalle` y `Producto`.

---

### 3. `actualizarCantidad`

*   **PHPDoc**:
    ```php
    /**
     * Actualiza la cantidad de un item del carrito.
     */
    ```
*   **Propósito**: Modificar el número de unidades solicitadas de un artículo específico dentro de un carrito de compras activo, validando nuevamente el stock disponible.
*   **Flujo Lógico**:
    1. Valida que el parámetro `cantidad` sea un entero positivo mayor o igual a 1.
    2. Localiza el item detallado en la tabla `venta_detalles` por su ID o lanza una excepción 404.
    3. Verifica que la venta asociada al detalle pertenezca al usuario autenticado y que se encuentre en estado `CARRITO`.
    4. Consulta la variante física del producto y valida que su stock en inventario sea suficiente para cubrir la nueva cantidad deseada.
    5. Actualiza la cantidad y recalcula el subtotal del registro `venta_detalles`.
    6. Llama a `recalcularTotal()` para sincronizar el total general en la cabecera `ventas`.
    7. Redirige a la vista del carrito.
*   **Efectos Secundarios**:
    *   Actualiza el campo `cantidad` y `subtotal` en `venta_detalles`.
    *   Actualiza el campo `total` en la tabla `ventas`.
*   **Asincronismo y Dependencias**: Síncrono. Depende de los modelos `VentaDetalle`, `Venta` y `Producto`.

---

### 4. `eliminarDelCarrito`

*   **PHPDoc**:
    ```php
    /**
     * Elimina un item del carrito.
     */
    ```
*   **Propósito**: Retirar de forma permanente un artículo del carrito de compras activo de un cliente y reajustar el total general.
*   **Flujo Lógico**:
    1. Localiza el item de detalle en la tabla `venta_detalles` por su ID.
    2. Valida que el carrito pertenezca al usuario autenticado y esté en estado `CARRITO`.
    3. Elimina físicamente el registro del detalle con `$detalle->delete()`.
    4. Invoca `recalcularTotal()` para reajustar la cabecera del carrito.
    5. Redirige de regreso a la página del carrito de compras.
*   **Efectos Secundarios**:
    *   Elimina un registro en la tabla `venta_detalles`.
    *   Actualiza el campo `total` de la venta.
*   **Asincronismo y Dependencias**: Síncrono. Depende de los modelos `VentaDetalle` y `Venta`.

---

### 5. `checkout`

*   **PHPDoc**:
    ```php
    /**
     * Procesa la compra (Checkout). Transición de CARRITO a CONFIRMADO.
     */
    ```
*   **Propósito**: Finalizar y confirmar de forma segura una compra mediante el cobro del carrito de compras, la asignación de la forma de pago, la fijación de la fecha de la venta y la deducción atómica del stock físico en el inventario.
*   **Flujo Lógico**:
    1. Valida que el parámetro `forma_pago_id` sea obligatorio y corresponda a un registro válido en `formas_pago`.
    2. Ejecuta un bloque transaccional utilizando `DB::transaction()` para garantizar la atomicidad de la operación:
        *   Obtiene la cabecera del carrito activo (`CARRITO`) del cliente. Lanza una excepción si el carrito está vacío.
        *   Itera sobre cada uno de los items detallados en el carrito:
            *   Realiza una consulta a la variante física (`Producto`) aplicando un bloqueo de escritura (`lockForUpdate()`). Esto impide que otras peticiones concurrentes puedan comprar o modificar el stock del producto en el mismo instante.
            *   Valida si el stock disponible en la base de datos es suficiente para abastecer la cantidad solicitada. Si no lo es, interrumpe la transacción arrojando una excepción (haciendo rollback automático de todo el proceso).
            *   Resta la cantidad comprada del stock físico del producto mediante `decrement('stock', cantidad)`.
        *   Actualiza el registro de la cabecera `ventas` cambiando el `estado` a `'CONFIRMADO'`, asignando la fecha de venta actual (`fecha_venta = now()`), y asociando la forma de pago elegida.
    3. Si la transacción se completa con éxito, redirige al usuario a la página de inicio con un mensaje de éxito.
    4. **Gestión de Errores**: Si ocurre un error o excepción de stock, el bloque transaccional revierte todo cambio en la base de datos (rollback) y redirige al usuario conservando los valores e informando el problema.
*   **Efectos Secundarios**:
    *   Modifica el stock en múltiples filas de la tabla `productos`.
    *   Actualiza el estado, fecha y forma de pago en la tabla `ventas`.
*   **Asincronismo y Dependencias**: Transaccional. Depende de la fachada `DB` y de los modelos `Venta`, `Producto` y `FormaPago`.

---

### 6. `adminIndex`

*   **PHPDoc**:
    ```php
    /**
     * Listado administrativo de pedidos.
     */
    ```
*   **Propósito**: Cargar y listar todas las ventas confirmadas y despachadas de la plataforma en orden cronológico inverso para el panel de administración.
*   **Flujo Lógico**:
    1. Consulta las ventas usando el scope `ventas()` (filtrando estados `CONFIRMADO` y `DESPACHADO`).
    2. Carga diligentemente las relaciones (`usuario`, `formaPago`).
    3. Ordena los pedidos por fecha de venta de forma descendente y los pagina de a 15 elementos.
    4. Retorna la vista `backend.admin.ventas` con el listado paginado.
*   **Efectos Secundarios**: Ninguno.
*   **Asincronismo y Dependencias**: Síncrono. Depende de los modelos `Venta`.

---

### 7. `adminShow`

*   **PHPDoc**:
    ```php
    /**
     * Detalle administrativo de un pedido.
     */
    ```
*   **Propósito**: Visualizar la información comercial y el desglose de productos completo de un pedido finalizado en el panel administrativo.
*   **Flujo Lógico**:
    1. Busca la venta filtrada por el scope `ventas()` cargando sus relaciones de cliente, forma de pago y los items de detalle con sus variantes y colores.
    2. Si no se encuentra, arroja un error 404.
    3. Retorna la vista `backend.admin.venta_detalle` pasando la variable `$venta`.
*   **Efectos Secundarios**: Ninguno.
*   **Asincronismo y Dependencias**: Síncrono. Depende del modelo `Venta`.

---

### 8. `actualizarEstado`

*   **PHPDoc**:
    ```php
    /**
     * Actualiza el estado de un pedido (por parte del administrador).
     */
    ```
*   **Propósito**: Permitir al personal administrativo cambiar el estado de procesamiento del pedido (ej: marcar como `DESPACHADO` una venta ya confirmada).
*   **Flujo Lógico**:
    1. Valida que el parámetro `estado` sea obligatorio y contenga uno de los valores permitidos (`CONFIRMADO`, `DESPACHADO`).
    2. Localiza el pedido correspondiente en las ventas confirmadas.
    3. Actualiza el campo `estado` del pedido en base de datos.
    4. Redirige de regreso a la pantalla de detalle del pedido con un mensaje de éxito.
*   **Efectos Secundarios**:
    *   Actualiza el campo `estado` en la tabla `ventas`.
*   **Asincronismo y Dependencias**: Síncrono. Depende de los modelos `Venta`.
