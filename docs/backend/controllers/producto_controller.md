# Documentación Técnica del Controlador de Productos (`ProductoController.php`)

Este documento detalla el análisis técnico de los métodos implementados en el archivo [ProductoController.php](file:///c:/Users/lucas/Herd/grupo7/app/Http/Controllers/ProductoController.php). Este controlador gestiona toda la lógica del catálogo de productos y variantes físicas (combinaciones de talle y color), controlando la sincronización transaccional en la base de datos, el filtrado avanzado de la tienda virtual, la carga y optimización a formato WebP de imágenes en Cloudflare R2, y el ordenamiento secuencial de multimedia de productos.

---

## Índice de Métodos

1. [index](#1-index)
2. [adminIndex](#2-adminindex)
3. [create](#3-create)
4. [store](#4-store)
5. [updateGroup](#5-updategroup)
6. [getDetails](#6-getdetails)
7. [uploadImage](#7-uploadimage)
8. [deleteImage](#8-deleteimage)
9. [setCoverImage](#9-setcoverimage)
10. [show](#10-show)
11. [destroy](#11-destroy)

---

### 1. `index`

*   **PHPDoc**:
    ```php
    /**
     * Muestra el catálogo unificado para la tienda virtual.
     * Agrupa las filas por 'sku_base' para que el cliente vea un modelo único por tarjeta.
     */
    ```
*   **Propósito**: Consultar, filtrar y agrupar los productos por su SKU base para renderizar la tienda virtual del cliente (frontend), evitando duplicar variantes del mismo modelo en las tarjetas de exhibición.
*   **Flujo Lógico**:
    1. Construye una consulta Eloquent inicial agrupando los registros por campos comunes (`sku_base`, `nombre`, `precio`, `categoria_id`, `tipo_mascota`, `coleccion_id`) y calcula el color representativo por defecto con `MAX(sku_color)`. Carga diligentemente la `categoria` y su `imagenPortada`.
    2. **Filtro de Tipo de Mascota**: Si existe el parámetro `mascota`, filtra por el campo `tipo_mascota`.
    3. **Filtro de Categorías y Subcategorías**: Si existe el parámetro `categoria`:
        *   Consulta si el ID es padre de subcategorías hijas (`parent_id`).
        *   Si tiene hijas (ej: "Ropa"), filtra los productos usando un `whereIn` con todas las subcategorías hijas (ej: "Buzos", "Suéteres").
        *   Si no tiene hijas (es subcategoría directa), filtra exactamente por ese ID.
    4. **Filtro de Colección**: Si existe el parámetro `coleccion`, aplica un filtro directo por `coleccion_id`.
    5. Ejecuta la consulta de base de datos para obtener los productos filtrados.
    6. Obtiene todas las categorías y colecciones para poblar dinámicamente los menús de selección lateral (Sidebar).
    7. Retorna la vista `frontend.productos` con las variables de los productos, categorías y colecciones.
    8. *(Nota: La sección final del código contiene bloques de filtros adicionales para Talle, Color por relación y Rango de Precios aplicados sobre la consulta original, interpretando rangos separados por guion "5000-12000")*.
*   **Efectos Secundarios**: No produce modificaciones en la base de datos ni altera el estado de la sesión.
*   **Asincronismo y Dependencias**: Síncrono. Depende de los modelos `Producto`, `Categoria` y `Coleccion`.

---

### 2. `adminIndex`

*   **PHPDoc**:
    ```php
    /**
     * Muestra el listado de productos en el panel de administración.   
     */
    ```
*   **Propósito**: Obtener el inventario agrupado por SKU Base y formatear los datos de las variantes para su visualización y gestión consolidada en el panel administrativo.
*   **Flujo Lógico**:
    1. Consulta todos los productos activos de la base de datos junto con sus categorías, padres de categorías e imágenes ordenadas.
    2. Agrupa los productos devueltos utilizando la colección de colecciones en memoria por el campo `sku_base`.
    3. Itera sobre cada grupo de SKU Base para generar un arreglo unificado:
        *   Extrae el primer registro del grupo para obtener la información comercial general.
        *   Cuenta las variantes únicas de colores activos y de talles activos.
        *   Establece la miniatura del producto localizando la primera variante que tenga imágenes asociadas en orden de secuencia (`orden`); si no existe, asigna una imagen de respaldo predeterminada (`webp`).
        *   Construye una estructura con el SKU base, nombre base, tipo de mascota, detalles de categoría, cantidad de colores/talles y la miniatura.
    4. Ordena alfabéticamente la lista consolidada por el campo `nombre_base` usando la función `usort()`.
    5. Obtiene la estructura jerárquica de categorías (principales con hijas), colecciones, colores mapeados con sus códigos hexadecimales y SKU de tres letras, y un listado rígido de talles del sistema (`['-', 'XS', 'S', 'M', ...'3XL']`).
    6. Retorna la vista administrativa `backend.admin.productos`.
*   **Efectos Secundarios**: No altera base de datos ni sesión.
*   **Asincronismo y Dependencias**: Síncrono. Relaciona los modelos `Producto`, `Categoria`, `Coleccion` y `Color`.

---

### 3. `create`

*   **PHPDoc**:
    ```php
    /**
     * Formulario de creación de productos (Panel de Administración).
     */
    ```
*   **Propósito**: Renderizar la vista de creación cargando los datos iniciales necesarios para alimentar los selectores de la interfaz de usuario.
*   **Flujo Lógico**:
    1. Consulta todas las categorías, colecciones y colores de la base de datos.
    2. Retorna la vista `backend.admin.productos` con los listados correspondientes.
*   **Efectos Secundarios**: Ninguno.
*   **Asincronismo y Dependencias**: Síncrono. Depende de los modelos del sistema.

---

### 4. `store`

*   **PHPDoc**:
    ```php
    /**
     * Almacena un producto y sus variantes iniciales en la base de datos.
     */
    ```
*   **Propósito**: Crear y guardar de forma transaccional un grupo de variantes físicas (combinaciones de talles y colores) asociadas a un nuevo producto comercial.
*   **Flujo Lógico**:
    1. Valida los datos obligatorios del producto (nombre, SKU base, categoría, tipo de mascota, precio, stock mínimo) y la estructura del arreglo `variantes` (que requiere color_id, talle y stock por cada elemento).
    2. Inicia una transacción de base de datos con `DB::beginTransaction()`.
    3. Itera sobre cada variante provista:
        *   Consulta el nombre del color asociado.
        *   Construye los códigos únicos SKU: `sku_color` (SKU Base + Color) y `sku` de variante física (SKU Base + Color + Talle).
        *   Crea el registro de la variante en la tabla `productos` asignando el nombre estructurado: `"[Nombre Producto] - [Nombre Color] [Talle]"`.
    4. Confirma la transacción con `DB::commit()` y redirige al listado administrativo con un mensaje de éxito.
    5. **Gestión de Errores**: Si ocurre una excepción, deshace los cambios con `DB::rollBack()` y redirige al formulario reteniendo los valores anteriores e indicando el error.
*   **Efectos Secundarios**:
    *   Inserta múltiples filas en la tabla `productos`.
*   **Asincronismo y Dependencias**: Transaccional y síncrono. Depende de `DB` y de los modelos `Producto` y `Color`.

---

### 5. `updateGroup`

*   **PHPDoc**:
    ```php
    /**
     * Actualiza la información comercial básica y las variantes de stock de un grupo de productos.
     */
    ```
*   **Propósito**: Sincronizar en una sola operación transaccional los metadatos comunes de un producto (nombre base, descripción, tipo de mascota, precio, stock mínimo) y actualizar, restaurar o crear variantes físicas en base a un listado de control recibido en formato JSON.
*   **Flujo Lógico**:
    1. Valida el SKU Base del grupo y las especificaciones comerciales junto con el arreglo de variantes.
    2. Inicia una transacción SQL.
    3. Obtiene todas las variantes de la base de datos asociadas a dicho SKU Base, incluyendo las marcadas con SoftDelete (`withTrashed()`), indexándolas bajo la clave compuesta `"[color_id]_[talle]"`.
    4. **Procesamiento de Variantes Entrantes**:
        *   Si la combinación de color y talle ya existe en la base de datos:
            *   Si el registro estaba borrado lógicamente, lo restaura con `restore()`.
            *   Compara los valores del registro con los recibidos (nombre estructurado, descripción, tipo de mascota, precio, stock, stock mínimo) y ejecuta la actualización SQL inteligente únicamente sobre los atributos que sufrieron modificaciones.
        *   Si la combinación no existe:
            *   Crea una nueva variante física derivando la categoría y colección de las variantes existentes del SKU base.
    5. **Eliminación Lógica de Variantes Obsoletas**:
        *   Identifica las variantes existentes en la base de datos cuyas combinaciones no se incluyeron en la petición actual. Procede a aplicarles la eliminación lógica (`delete()`).
    6. **Sincronización Global**:
        *   Asegura que cualquier otra variante asociada al SKU Base actualice y sincronice sus campos comunes (nombre adaptado, descripción, tipo de mascota, precio y stock mínimo) para mantener la integridad visual del catálogo.
    7. Confirma la transacción con `DB::commit()` y devuelve una respuesta JSON de éxito.
    8. **Gestión de Errores**: Si ocurre un fallo, ejecuta un rollback y retorna una respuesta JSON de error con código HTTP 500.
*   **Efectos Secundarios**:
    *   Modifica, restaura, inserta o elimina lógicamente registros en la tabla `productos` correspondientes a las variantes del SKU Base.
*   **Asincronismo y Dependencias**: Transaccional. Depende de las transacciones del motor de base de datos y de la interacción con los modelos `Producto` y `Color`.

---

### 6. `getDetails`

*   **PHPDoc**:
    ```php
    /**
     * Devuelve los detalles de un producto específico en formato JSON (Carga bajo demanda).
     */
    ```
*   **Propósito**: Proveer los datos consolidados y estructurados de un producto y todas sus variantes para alimentar de forma dinámica los formularios de edición o pantallas de detalle en el frontend mediante llamadas AJAX.
*   **Flujo Lógico**:
    1. Consulta las variantes de productos asociadas a un `$sku_base` específico cargando sus relaciones (`categoria.parent`, `color`, `imagenes`).
    2. Si no hay registros, devuelve una respuesta JSON de error con código HTTP 404.
    3. Procesa las variantes obtenidas para estructurar:
        *   Colores activos únicos e información de códigos hexadecimales.
        *   Talles activos únicos en un arreglo plano.
        *   Un mapa asociativo multidimensional (`variantes`) indexado por el color en minúsculas y talle, indicando el SKU, stock y el ID de registro de la variante física.
        *   Un mapa multimedia (`colorMedia`) indexado por color que contiene la imagen de portada principal, y las galerías de miniaturas ordenadas numéricamente.
    4. Retorna una respuesta JSON estructurada con código HTTP 200 conteniendo los metadatos comerciales y los mapas procesados.
*   **Efectos Secundarios**: Ninguno.
*   **Asincronismo y Dependencias**: Síncrono. Depende de los modelos Eloquent de productos e imágenes.

---

### 7. `uploadImage`

*   **PHPDoc**:
    ```php
    /**
     * Sube y procesa una imagen para guardarla en Cloudflare R2 y registrarla.
     */
    ```
*   **Propósito**: Subir un archivo de imagen, optimizar su peso y tamaño convirtiéndolo al formato WebP, almacenarlo en Cloudflare R2 e insertar la referencia de la imagen asociada a un SKU de Color específico.
*   **Flujo Lógico**:
    1. Valida el parámetro `sku_color` y que el archivo `image` sea una imagen válida con tamaño máximo de 5MB.
    2. Invoca al servicio inyectado `ImageOptimizerService` para redimensionar la imagen a un ancho máximo de 1200 píxeles y convertirla a formato WebP con calidad del 80% en un directorio temporal local.
    3. Genera un nombre de archivo único para Cloudflare R2: `img/productos/[sku_color_minúsculas]_[timestamp]_[uniqid].webp`.
    4. Sube la imagen optimizada al bucket mediante el disco de almacenamiento `'s3'`.
    5. Elimina el archivo temporal generado localmente para liberar almacenamiento del servidor mediante `unlink()`.
    6. Calcula el orden de secuencia correlativo obteniendo el valor máximo de la columna `orden` registrado para dicho `sku_color` e incrementándolo en 1 (por defecto inicia en 1).
    7. Inserta el registro de la imagen en la tabla `producto_imagenes`.
    8. Retorna una respuesta JSON exitosa con los datos de la nueva imagen registrada (ID, URL, orden).
*   **Efectos Secundarios**:
    *   Crea un archivo temporal en el servidor y lo elimina.
    *   Almacena un archivo WebP optimizado en Cloudflare R2.
    *   Inserta una fila en la tabla `producto_imagenes`.
*   **Asincronismo y Dependencias**: Síncrono. Inyecta y depende de [ImageOptimizerService](file:///c:/Users/lucas/Herd/grupo7/app/Services/ImageOptimizerService.php), de la fachada `Storage` de Laravel, y del modelo [ProductoImagen](file:///c:/Users/lucas/Herd/grupo7/app/Models/ProductoImagen.php).

---

### 8. `deleteImage`

*   **PHPDoc**:
    ```php
    /**
     * Elimina una imagen del almacenamiento físico y de la base de datos.
     */
    ```
*   **Propósito**: Eliminar un archivo físico de imagen de Cloudflare R2 y su registro de la base de datos, reordenando de forma secuencial las imágenes restantes del producto.
*   **Flujo Lógico**:
    1. Localiza el registro de la imagen por su ID o arroja una excepción 404.
    2. Extrae la ruta relativa de la URL almacenada en base de datos.
    3. Si el archivo existe físicamente en el disco `'s3'` (R2), lo elimina.
    4. Borra el registro de la imagen de la base de datos con `$img->delete()`.
    5. Consulta las imágenes restantes asociadas a ese `sku_color` ordenadas de forma ascendente.
    6. Actualiza de manera secuencial la columna `orden` de las imágenes restantes para que comiencen en 1 y no queden saltos en la numeración (ej. 1, 2, 3...).
    7. Retorna una respuesta JSON indicando éxito.
*   **Efectos Secundarios**:
    *   Elimina de manera permanente un archivo de Cloudflare R2.
    *   Elimina un registro en `producto_imagenes` y reordena los restantes.
*   **Asincronismo y Dependencias**: Síncrono. Utiliza los modelos Eloquent de imágenes y la fachada de almacenamiento de archivos de Laravel.

---

### 9. `setCoverImage`

*   **PHPDoc**:
    ```php
    /**
     * Define una imagen como portada (orden = 1) y reordena el resto.
     */
    ```
*   **Propósito**: Marcar la imagen seleccionada con el orden prioritario 1 (portada) y reestructurar de manera transaccional el orden secuencial de los demás archivos asociados al mismo color del producto.
*   **Flujo Lógico**:
    1. Localiza el registro de la imagen de destino mediante su ID.
    2. Inicia una transacción de base de datos.
    3. Obtiene todas las imágenes pertenecientes al mismo `sku_color`.
    4. Actualiza la imagen seleccionada asignándole `orden = 1`.
    5. Itera sobre las demás imágenes del color para actualizarlas con un orden incremental continuo iniciando en `orden = 2`.
    6. Confirma la transacción y devuelve una respuesta JSON de éxito.
*   **Efectos Secundarios**:
    *   Actualiza el campo `orden` en múltiples registros de la tabla `producto_imagenes`.
*   **Asincronismo y Dependencias**: Transaccional. Depende del modelo Eloquent `ProductoImagen` y la fachada transaccional `DB`.

---

### 10. `show`

*   **PHPDoc**:
    ```php
    /**
     * Detalle de un producto en la tienda virtual.
     * Busca todas las variantes físicas que comparten el mismo 'sku_base' para renderizar los talles disponibles.
     */
    ```
*   **Propósito**: Renderizar la página de detalle comercial (tienda virtual) para un producto consultando todas las variantes disponibles con stock y las imágenes del color principal.
*   **Flujo Lógico**:
    1. Obtiene la primera variante que comparta el `$sku_base` para extraer sus metadatos descriptivos iniciales (cargando las relaciones `categoria` y `color`).
    2. Consulta y recupera todas las variantes físicas asociadas al mismo `$sku_base` que posean stock mayor a cero (`stock > 0`) para poblar el selector de talles disponibles.
    3. Obtiene la galería de imágenes del carrusel filtrando por el `sku_color` de la variante base y ordenándolas por su número de secuencia de forma ascendente.
    4. Renderiza la vista `productos.show` pasando el producto base, las variantes en stock y el listado de imágenes mediante `compact()`.
*   **Efectos Secundarios**: Ninguno.
*   **Asincronismo y Dependencias**: Síncrono. Depende de los modelos `Producto` y `ProductoImagen`.

---

### 11. `destroy`

*   **PHPDoc**:
    ```php
    /**
     * Eliminación de un modelo completo (Baja lógica de todas sus variantes).
     */
    ```
*   **Propósito**: Dar de baja de forma lógica todas las variantes físicas asociadas a un SKU base del catálogo.
*   **Flujo Lógico**:
    1. Ejecuta una consulta de eliminación lógica (`delete()`) sobre todos los registros en la tabla `productos` que coincidan con el `sku_base` suministrado.
    2. Redirige a la ruta `admin.productos.index` con un mensaje flash de éxito en la sesión.
*   **Efectos Secundarios**:
    *   Aplica SoftDelete (marca `deleted_at`) sobre múltiples registros en la tabla `productos`.
*   **Asincronismo y Dependencias**: Síncrono. Depende del modelo `Producto` configurado con soporte para borrado lógico de Eloquent.
