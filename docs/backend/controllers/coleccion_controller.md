# Documentación Técnica del Controlador de Colecciones (`ColeccionController.php`)

Este documento detalla el análisis técnico de los métodos implementados en el archivo [ColeccionController.php](file:///c:/Users/lucas/Herd/grupo7/app/Http/Controllers/ColeccionController.php). Este controlador gestiona el ciclo de vida de las agrupaciones de productos en "Colecciones", administrando metadatos como el título y la descripción, y controlando la carga física de imágenes promocionales en el bucket Cloudflare R2 o su vinculación mediante URLs externas.

---

## Índice de Métodos

1. [index](#1-index)
2. [create](#2-create)
3. [store](#3-store)
4. [edit](#4-edit)
5. [update](#5-update)
6. [destroy](#6-destroy)

---

### 1. `index`

*   **PHPDoc**:
    ```php
    /**
     * Muestra el listado de todas las colecciones.
     */
    ```
*   **Propósito**: Consultar todas las colecciones de productos y computar dinámicamente la cantidad de productos vinculados a cada una de ellas para presentarlas en la vista de administración.
*   **Flujo Lógico**:
    1. Consulta todas las colecciones utilizando `Coleccion::withCount('productos')` para inyectar en cada objeto el atributo virtual `productos_count` mediante una sola subconsulta optimizada.
    2. Retorna la vista `backend.admin.colecciones` enviando el listado agrupado en la variable `colecciones`.
*   **Efectos Secundarios**: No produce modificaciones en la base de datos ni altera la sesión.
*   **Asincronismo y Dependencias**: Síncrono. Depende del modelo Eloquent [Coleccion](file:///c:/Users/lucas/Herd/grupo7/app/Models/Coleccion.php).

---

### 2. `create`

*   **PHPDoc**:
    ```php
    /**
     * Formulario para crear (Redirige al index administrado por Modal).
     */
    ```
*   **Propósito**: Redirigir al listado principal de colecciones debido a que el formulario para agregar nuevas colecciones se despliega a través de una interfaz de diálogo modal gestionada en el frontend.
*   **Flujo Lógico**:
    1. Retorna una redirección HTTP 302 a la ruta `admin.colecciones.index`.
*   **Efectos Secundarios**: Redirección HTTP.
*   **Asincronismo y Dependencias**: Síncrono. Depende del sistema de enrutamiento de Laravel.

---

### 3. `store`

*   **PHPDoc**:
    ```php
    /**
     * Guarda la colección en la base de datos con soporte opcional para subida de archivo a R2 o URL.
     */
    ```
*   **Propósito**: Validar y registrar una nueva colección en la base de datos, soportando tanto la carga de un archivo de imagen local a Cloudflare R2 como la asignación de una dirección URL de imagen remota.
*   **Flujo Lógico**:
    1. Valida los parámetros del formulario (`Request`):
        *   `nombre`: Obligatorio, cadena de texto, máximo 100 caracteres.
        *   `descripcion`: Opcional, cadena de texto.
        *   `url_imagen`: Opcional, debe cumplir el formato de una dirección URL válida.
        *   `imagen_file`: Opcional, tipo imagen (JPEG, PNG, WEBP, etc.), tamaño máximo de 5120 KB (5MB).
    2. Inicializa la variable `$urlImagen` con el valor del input `url_imagen` (que puede ser nulo o una URL externa).
    3. **Procesamiento de Archivo de Imagen**:
        *   Si se detecta la carga de un archivo local (`imagen_file`), se obtiene su referencia.
        *   Genera un nombre de archivo único utilizando el slug del nombre de la colección, un timestamp y un hash aleatorio, respetando la extensión original del archivo: `colecciones/[slug-nombre]_[timestamp]_[uniqid].[extension]`.
        *   Sube el archivo al almacenamiento en la nube en el disco `'s3'` (Cloudflare R2) con visibilidad pública.
        *   Si la subida es exitosa, se obtiene la URL de acceso del disco y se sobrescribe la variable `$urlImagen` con esta URL.
    4. Registra la nueva colección en la base de datos llamando a `Coleccion::create()`.
    5. Redirige a `admin.colecciones.index` adjuntando un mensaje flash de éxito en la clave `exito`.
*   **Efectos Secundarios**:
    *   Crea un nuevo registro en la tabla `colecciones`.
    *   Sube un archivo de imagen al bucket de Cloudflare R2 (si se cargó `imagen_file`).
*   **Asincronismo y Dependencias**: Síncrono. Depende de las fachadas `Storage` y `Str`, del modelo `Coleccion` y del servicio Cloudflare R2.

---

### 4. `edit`

*   **PHPDoc**:
    ```php
    /**
     * Formulario de edición (Redirige al index administrado por Modal).
     */
    ```
*   **Propósito**: Redirigir al listado principal ya que la edición de colecciones se gestiona de manera interactiva a través del formulario modal en el índice.
*   **Flujo Lógico**:
    1. Redirige HTTP 302 a la ruta `admin.colecciones.index`.
*   **Efectos Secundarios**: Redirección HTTP.
*   **Asincronismo y Dependencias**: Síncrono. Depende del enrutamiento de Laravel.

---

### 5. `update`

*   **PHPDoc**:
    ```php
    /**
     * Actualiza la colección seleccionada.
     */
    ```
*   **Propósito**: Modificar los metadatos de una colección existente en la base de datos y gestionar la actualización o reemplazo del archivo de imagen en Cloudflare R2 en caso de subirse uno nuevo.
*   **Flujo Lógico**:
    1. Valida los datos recibidos utilizando las mismas reglas que el método `store`.
    2. Inicializa la variable `$urlImagen` con el valor del input `url_imagen` (permitiendo actualizarla a una nueva URL remota o nula).
    3. **Reemplazo de Imagen en el Almacenamiento**:
        *   Si se recibe un nuevo archivo en `imagen_file`:
            *   Si la colección ya poseía una imagen previa (`url_imagen`), valida e intenta identificar si era una ruta de almacenamiento local analizando la URL. Si corresponde a un archivo en R2, procede a eliminar el archivo antiguo del disco `'s3'` para evitar archivos huérfanos.
            *   Genera un nuevo nombre único de archivo y lo sube al bucket de R2.
            *   Si la subida es correcta, reasigna `$urlImagen` con la nueva URL pública del archivo.
    4. Actualiza los campos de la colección mediante `$coleccion->update()`.
    5. Redirige al índice con un mensaje flash indicando que la colección fue modificada correctamente.
*   **Efectos Secundarios**:
    *   Actualiza el registro correspondiente en la tabla `colecciones`.
    *   Sube un nuevo archivo y puede eliminar el archivo antiguo en el almacenamiento R2.
*   **Asincronismo y Dependencias**: Síncrono. Depende de `Storage`, `Str`, el modelo `Coleccion` y la API de Cloudflare R2.

---

### 6. `destroy`

*   **PHPDoc**:
    ```php
    /**
     * Baja lógica de la colección (SoftDelete).
     */
    ```
*   **Propósito**: Marcar una colección como eliminada lógicamente en la base de datos y eliminar permanentemente su archivo de imagen asociado del almacenamiento en la nube R2.
*   **Flujo Lógico**:
    1. **Eliminación Física de la Imagen**:
        *   Verifica si la colección posee una URL de imagen (`url_imagen`).
        *   Si existe, extrae la ruta relativa del archivo dentro del bucket, limpiando cualquier prefijo de simulación como `'storage/'`.
        *   Comprueba la existencia del archivo en el disco `'s3'` y, si es verdadero, lo elimina de forma definitiva de Cloudflare R2.
    2. **Baja Lógica**:
        *   Llama al método `$coleccion->delete()` para activar el SoftDelete sobre el registro (marcando el campo `deleted_at`).
    3. Redirige a `admin.colecciones.index` con un mensaje flash de éxito.
*   **Efectos Secundarios**:
    *   Aplica el borrado lógico sobre el registro correspondiente en la tabla `colecciones`.
    *   Elimina de manera permanente el archivo físico de la imagen de Cloudflare R2.
*   **Asincronismo y Dependencias**: Síncrono. Depende del modelo `Coleccion`, de la fachada `Storage` y de la conexión con el almacenamiento remoto.
