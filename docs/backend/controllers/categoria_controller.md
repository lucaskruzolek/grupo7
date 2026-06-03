# Documentación Técnica del Controlador de Categorías (`CategoriaController.php`)

Este documento detalla el análisis técnico de los métodos implementados en el archivo [CategoriaController.php](file:///c:/Users/lucas/Herd/grupo7/app/Http/Controllers/CategoriaController.php). Este controlador es el responsable de gestionar el ciclo de vida de las Categorías Principales y Subcategorías del catálogo del sistema, administrando su almacenamiento y carga de archivos vectoriales (SVG) en Cloudflare R2 y controlando las restricciones transaccionales y de eliminación lógica (SoftDelete).

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
     * Lista todas las categorías con sus subcategorías hijas asociadas.
     */
    ```
*   **Propósito**: Consultar las categorías principales de la base de datos junto con sus subcategorías hijas para renderizarlas en el panel de administración.
*   **Flujo Lógico**:
    1. Ejecuta una consulta sobre el modelo `Categoria` filtrando las que poseen `parent_id` nulo (categorías raíz o principales).
    2. Utiliza la carga diligente (Eager Loading) mediante `with('children')` para precargar todas las subcategorías asociadas de un solo paso, previniendo el problema de consultas N+1 en la base de datos.
    3. Retorna la vista `backend.admin.categorias` inyectando la colección de categorías recuperada con `compact('categorias')`.
*   **Efectos Secundarios**: No produce modificaciones en la base de datos ni altera la sesión.
*   **Asincronismo y Dependencias**: Síncrono. Depende del modelo Eloquent [Categoria](file:///c:/Users/lucas/Herd/grupo7/app/Models/Categoria.php).

---

### 2. `create`

*   **PHPDoc**:
    ```php
    /**
     * Formulario de creación (Redirige al index ya que se gestiona por Modal).
     */
    ```
*   **Propósito**: Redirigir al listado principal de categorías debido a que la interfaz de creación se administra mediante un formulario modal dinámico en la misma página de índice.
*   **Flujo Lógico**:
    1. Retorna una redirección HTTP 302 a la ruta nombrada `admin.categorias.index`.
*   **Efectos Secundarios**: Redirección HTTP.
*   **Asincronismo y Dependencias**: Síncrono. Depende del sistema de enrutamiento de Laravel.

---

### 3. `store`

*   **PHPDoc**:
    ```php
    /**
     * Guarda una categoría o subcategoría en la base de datos con carga opcional de SVG a R2.
     */
    ```
*   **Propósito**: Validar y guardar un nuevo registro de categoría o subcategoría, gestionando el procesamiento y almacenamiento de su icono representativo (exclusivo para categorías principales) en el bucket de Cloudflare R2.
*   **Flujo Lógico**:
    1. Valida los parámetros del formulario (`Request`):
        *   `nombre`: Obligatorio, cadena de texto, máximo 100 caracteres.
        *   `parent_id`: Opcional (nulo), pero de existir debe ser un ID válido en la tabla `categorias`.
        *   `icono`: Opcional, tipo archivo, máximo 2048 KB (2MB).
    2. Inicializa la variable `$iconoUrl` en nulo.
    3. Verifica si se adjuntó un archivo de icono (`hasFile('icono')`) y si el campo `parent_id` está vacío (lo que confirma que es una categoría principal, no una subcategoría):
        *   Obtiene la referencia del archivo subido.
        *   Realiza una comprobación de seguridad obligando a que la extensión del archivo sea exactamente `'svg'` (ignora mayúsculas/minúsculas). Si es incorrecta, interrumpe el flujo y redirige de vuelta con los valores anteriores y un mensaje de error.
        *   Genera una ruta y nombre de archivo único para evitar colisiones: `icons/[slug-del-nombre]_[timestamp]_[uniqid].svg`.
        *   Sube el archivo al almacenamiento remoto configurado en el disco `'s3'` (conectado a Cloudflare R2) definiendo el nivel de visibilidad como `'public'`.
        *   Si la carga es exitosa, obtiene la URL pública del objeto almacenado asignándola a `$iconoUrl`.
    4. Crea el registro en la base de datos mediante `Categoria::create()` pasando el nombre, el id de categoría padre (si no existe se evalúa a nulo) y la URL del icono.
    5. Redirige a la ruta `admin.categorias.index` con un mensaje de éxito bajo la clave `exito`.
*   **Efectos Secundarios**:
    *   Inserta un nuevo registro en la tabla `categorias`.
    *   Almacena físicamente un archivo en Cloudflare R2.
*   **Asincronismo y Dependencias**: Síncrono. Depende de las fachadas `Storage` y `Str` de Laravel, del modelo `Categoria`, y de la conexión con el servicio externo de Cloudflare R2 (vía el controlador de S3).

---

### 4. `edit`

*   **PHPDoc**:
    ```php
    /**
     * Muestra el formulario para editar (Redirige al index ya que se gestiona por Modal).
     */
    ```
*   **Propósito**: Redirigir al listado principal ya que el formulario de edición de categorías se procesa dentro del modal administrado dinámicamente por JavaScript en el índice.
*   **Flujo Lógico**:
    1. Redirige HTTP 302 a la ruta `admin.categorias.index`.
*   **Efectos Secundarios**: Redirección HTTP.
*   **Asincronismo y Dependencias**: Síncrono. Depende de la resolución de rutas de Laravel.

---

### 5. `update`

*   **PHPDoc**:
    ```php
    /**
     * Actualiza la categoría.
     */
    ```
*   **Propósito**: Procesar la modificación de una categoría o subcategoría existente, actualizar su nombre o parentesco, y administrar la sustitución o eliminación del icono SVG en Cloudflare R2.
*   **Flujo Lógico**:
    1. Valida los datos recibidos con las mismas reglas que el método `store`.
    2. Inicializa la URL del icono con la que ya posee la categoría en base de datos.
    3. **Gestión de Carga de Nuevo Icono**:
        *   Si hay un nuevo archivo `'icono'` en la petición, valida manualmente que posea extensión SVG.
        *   Si la categoría ya poseía un icono previamente registrado, extrae la ruta del objeto en R2 mediante `parse_url()` y elimina el archivo existente del disco `'s3'`.
        *   Genera un nombre secuencial único y almacena el nuevo archivo en el almacenamiento externo, reasignando `$iconoUrl` con la nueva URL pública.
    4. **Conversión a Subcategoría**:
        *   Si el formulario contiene un `parent_id` (indicando que la categoría principal se ha convertido en subcategoría de otra):
            *   Como las subcategorías no admiten iconos, si la categoría poseía un icono registrado previamente, extrae su ruta y lo elimina físicamente de R2.
            *   Establece la variable `$iconoUrl` en nulo.
    5. Actualiza los valores de la categoría llamando al método `update()` del modelo.
    6. Redirige a `admin.categorias.index` con un mensaje flash de éxito.
*   **Efectos Secundarios**:
    *   Modifica el registro correspondiente en la tabla `categorias`.
    *   Puede eliminar archivos antiguos y subir archivos nuevos en Cloudflare R2.
*   **Asincronismo y Dependencias**: Síncrono. Depende de `Storage`, `Str`, el modelo `Categoria` y el almacenamiento en la nube R2.

---

### 6. `destroy`

*   **PHPDoc**:
    ```php
    /**
     * Eliminación lógica (SoftDelete). Restringe si hay productos asociados.
     */
    ```
*   **Propósito**: Dar de baja de manera lógica (marcado de fecha `deleted_at`) una categoría o subcategoría, verificando restricciones previas de integridad referencial para evitar inconsistencias en el catálogo de productos.
*   **Flujo Lógico**:
    1. **Restricción por Productos Directos**: Comprueba si existen productos asociados directamente a esta categoría usando `$categoria->productos()->exists()`. Si se encuentran, cancela el proceso y redirige de vuelta con un mensaje de error.
    2. **Restricción de Categorías Principales (Hijas y Productos)**:
        *   Si la categoría a eliminar es principal (`parent_id === null`), recorre todas sus subcategorías hijas (`$categoria->children`).
        *   Si alguna de las subcategorías posee productos asociados en el catálogo, interrumpe el flujo y retorna un error notificando cuál subcategoría bloquea la eliminación.
        *   Si ninguna subcategoría posee productos, procede a eliminar en cascada lógica todas las subcategorías hijas mediante `$categoria->children()->delete()`.
    3. **Limpieza del Almacenamiento**: Si la categoría eliminada posee un icono SVG registrado, analiza su URL para extraer la ruta relativa y lo elimina de manera permanente del bucket Cloudflare R2 para optimizar el espacio de almacenamiento.
    4. **Baja Lógica**: Aplica el método `$categoria->delete()` para establecer la marca de SoftDelete sobre la categoría actual.
    5. Redirige al listado principal con un mensaje flash de éxito indicando que la categoría fue dada de baja.
*   **Efectos Secundarios**:
    *   Aplica borrado lógico en la tabla `categorias` para el registro actual y, si aplica, para sus registros hijos en cascada.
    *   Elimina archivos del bucket de Cloudflare R2.
*   **Asincronismo y Dependencias**: Síncrono. Depende de las relaciones de Eloquent del modelo `Categoria`, de `Storage` para la eliminación de archivos, y del motor de base de datos para registrar la marca de SoftDelete.
