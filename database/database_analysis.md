# Análisis del Diseño de la Base de Datos

Este documento contiene un análisis técnico detallado del diseño actual de la base de datos del proyecto, basado en los archivos de migración ubicados en [database/migrations](file:///c:/Users/lucas/Herd/grupo7/database/migrations).

Se evalúan la posible sobre-ingeniería de tablas y las propuestas específicas de optimización.

---

## 1. Evaluación de Sobre-Ingeniería General

Al analizar el conjunto de migraciones, se identifican los siguientes puntos clave sobre la estructura:

### ⚠️ Redundancia de Tablas de Usuarios y Roles
- **Problema**: Existen dos pares de tablas dedicadas a la autenticación y roles de usuarios:
  1. Tabla `users` ([migración](file:///c:/Users/lucas/Herd/grupo7/database/migrations/2014_10_12_000000_create_users_table.php)) y su respectivo modelo `User`.
  2. Tabla `usuarios` ([migración](file:///c:/Users/lucas/Herd/grupo7/database/migrations/2026_05_15_002653_create_usuarios_table.php)) junto a la tabla `roles` ([migración](file:///c:/Users/lucas/Herd/grupo7/database/migrations/2014_10_11_002221_create_rols_table.php)) y sus respectivos modelos `Usuario` y `Rol`.
- **Análisis**: La tabla `users` es la por defecto creada al iniciar un proyecto Laravel. Sin embargo, el desarrollo del negocio se construyó sobre la tabla `usuarios` (en español y con relación a `roles`). Tener ambas tablas activas y ambos modelos genera duplicidad innecesaria, confusión en el desarrollo y un esquema sucio.
- **Recomendación**: Eliminar la tabla `users` y su migración original. Consolidar toda la lógica en `usuarios` y, si es necesario por convenciones de Laravel o dependencias (ej. Laravel Sanctum o Breeze), renombrar la tabla `usuarios` a `users` pero manteniendo los campos customizados como `rol_id`.

### 💡 Imagenes de Productos asociadas a Colores
La tabla `producto_imagenes` ([migración](file:///c:/Users/lucas/Herd/grupo7/database/migrations/2026_05_16_045706_create_producto_imagenes_table.php)) posee una relación nullable hacia `colores`. Esto es un excelente acierto de diseño: permite asociar imágenes específicas a colores particulares del producto (ej: mostrar la foto de la remera roja cuando el usuario selecciona "rojo" en la interfaz), sin obligar a duplicar imágenes en variaciones individuales. **No está sobre-diseñada; es eficiente y flexible.**

---

## 2. Propuesta 1: Unificación de Categorías y Subcategorías

Actualmente existen dos tablas:
- `categorias` ([migración](file:///c:/Users/lucas/Herd/grupo7/database/migrations/2026_05_16_045700_create_categorias_table.php))
- `subcategorias` ([migración](file:///c:/Users/lucas/Herd/grupo7/database/migrations/2026_05_16_045701_create_subcategorias_table.php))

### Evaluación de Unificación
La separación física en dos tablas para modelar una estructura jerárquica de categoría -> subcategoría es rígida y genera sobre-ingeniería en los siguientes aspectos:
1. **Rigidez en Profundidad**: Si el día de mañana se necesita un tercer nivel (ej. *Ropa -> Remeras -> Mangas Cortas*), el esquema actual obliga a crear una tercera tabla (`sub_subcategorias`) y refactorizar controladores, modelos y relaciones.
2. **Duplicidad de Código**: Se requieren dos modelos (`Categoria` y `Subcategoria`), dos controladores, y dos CRUDs administrativos prácticamente idénticos.

### Estructura Propuesta (Modelo de Lista de Adyacencia / Self-Referencing)
Se recomienda unificar ambas en una sola tabla `categorias`:

| Columna | Tipo | Atributos | Descripción |
| :--- | :--- | :--- | :--- |
| `id` | `bigint` | Primary Key, Auto-increment | Identificador único. |
| `parent_id` | `bigint` | Nullable, Foreign Key -> `categorias.id` | Apunta a la categoría padre. Si es `NULL`, es categoría raíz. |
| `nombre` | `string` | Máx. 100 caracteres | Nombre de la categoría. |
| `timestamps` | - | - | Campos `created_at` y `updated_at`. |
| `softDeletes` | - | - | Campo `deleted_at`. |

### Ventajas de la Unificación
- **Simplicidad**: Se elimina una tabla física entera. Un solo modelo `Categoria` con relaciones autoreferenciadas:
  ```php
  public function parent() {
      return $this->belongsTo(Categoria::class, 'parent_id');
  }

  public function children() {
      return $this->hasMany(Categoria::class, 'parent_id');
  }
  ```
- **Flexibilidad Infinita**: Permite tener N niveles de anidación sin cambiar la base de datos.
- **Limpieza en Producto**: El producto se relaciona directamente con una única categoría final (`categoria_id` en lugar de `subcategoria_id`), simplificando las consultas y evitando la redundancia lógica de tener un producto asociado a una subcategoría que internamente ya pertenece a una categoría.

---

## 3. Propuesta 2: Evaluación de Productos y Producto Variaciones

La disposición actual utiliza dos tablas principales:
- `productos` ([migración](file:///c:/Users/lucas/Herd/grupo7/database/migrations/2026_05_16_045702_rebuild_productos_table.php))
- `producto_variaciones` ([migración](file:///c:/Users/lucas/Herd/grupo7/database/migrations/2026_05_16_045705_create_producto_variaciones_table.php))

A continuación se evalúa la eficiencia de esta estructura frente a cada uno de los requerimientos de negocio planteados:

### Requerimiento 1: Llevar la cuenta del stock diferenciando por talle y por color
> *Ejemplo: 10 remeras en total: 5 verdes talle S, 2 verdes talle M, 3 rojas talle S.*

* **Evaluación**: La estructura actual de `producto_variaciones` es óptima y cumple al 100% de manera normalizada. Cada fila de variación une `producto_id`, `color_id` y `talle_id` con su propia columna `stock`.
* **Ejemplo de Datos Físicos**:
  - `producto_variaciones` (producto_id: 1 [Remera], color: Verde, talle: S, stock: 5)
  - `producto_variaciones` (producto_id: 1 [Remera], color: Verde, talle: M, stock: 2)
  - `producto_variaciones` (producto_id: 1 [Remera], color: Rojo, talle: S, stock: 3)
* **Eficiencia**: Para obtener el stock total de la remera, se calcula mediante un simple `SUM(stock)`. No hay redundancia de información descriptiva del producto (como el nombre o la descripción), lo cual ahorra espacio y optimiza las consultas.

### Requerimiento 2: Asignar un stock mínimo por producto diferenciando por talle y por color
* **Evaluación**: Actualmente, la tabla `producto_variaciones` **no cuenta con una columna para stock mínimo**.
* **Propuesta de Ajuste**: Para cumplir este requerimiento, se debe agregar el campo `stock_minimo` a la tabla `producto_variaciones`.
  - Colocarlo en la tabla `productos` sería incorrecto, ya que no permitiría diferenciar alertas de reposición por combinaciones específicas (por ejemplo, tal vez se venda mucho la Remera Verde S y requiera un mínimo de 5 unidades, mientras que la Remera Verde M requiera un mínimo de solo 2 unidades).
  - La migración de `producto_variaciones` debería modificarse para incluir:
    ```php
    $table->integer('stock_minimo')->default(0);
    ```

### Requerimiento 3: Mostrar el producto como unidad en la interfaz (listar talles/colores disponibles) y procesar transacciones a nivel de variante
> *Ejemplo: Mostrar "Remera" en el catálogo, pero al vender una remera verde S, descontar el stock únicamente de esa variante.*

* **Evaluación**: La relación Padre-Hijo (`productos` -> `producto_variaciones`) es el patrón estándar de la industria (también utilizado por plataformas como Shopify y WooCommerce) y el más eficiente para este caso:
  - **Unidad en la interfaz**: Para el catálogo principal, se consulta únicamente la tabla `productos`. Al abrir el detalle del producto, se cargan de forma diferida o mediante `eager loading` sus variaciones asociadas con sus talles y colores disponibles:
    ```php
    $producto = Producto::with(['variaciones.color', 'variaciones.talle'])->find($id);
    ```
  - **Tratamiento transaccional diferenciado**: En el carrito de compras y las ventas, el elemento de línea hace referencia directa al ID único de la variación (o su SKU). Al confirmar la compra, la base de datos realiza una actualización directa por ID de variación:
    ```sql
    UPDATE producto_variaciones SET stock = stock - 1 WHERE id = ?;
    ```
    Esto garantiza un bloqueo de fila rápido, evita bloqueos de tabla innecesarios y reduce el stock del producto específico sin alterar las demás variaciones.

---

## 4. Comparativa de Diseños Alternativos para Productos

Para justificar por qué la estructura actual (Padre-Hijo) es la más eficiente, la comparamos con otras alternativas de diseño comunes:

### A. Flat Table (Una sola tabla de productos con redundancia)
- **Concepto**: Cada variación es un producto independiente en la tabla `productos` (ej. Fila 1: "Remera Verde S", Fila 2: "Remera Verde M").
- **Desventaja**: Alto nivel de redundancia (duplicación de nombres, descripciones, categorías, imágenes principales). Agrupación compleja en el frontend para mostrar una sola remera (requiere `GROUP BY` o campos adicionales de agrupación).

### B. Atributos en formato JSON (Columna `variaciones` JSON en `productos`)
- **Concepto**: Una sola tabla `productos` con un campo JSON que contiene todas las combinaciones y sus stocks.
- **Desventaja**: Se pierde integridad referencial (no se pueden usar Foreign Keys con `colores` y `talles`). Las operaciones de actualización concurrente de stock son propensas a colisiones de datos y difíciles de bloquear selectivamente a nivel de fila de base de datos. Consultar variaciones con bajo stock se vuelve ineficiente.

### C. Patrón EAV (Entity-Attribute-Value)
- **Concepto**: Tablas hiper-flexibles de atributos y valores dinámicos.
- **Desventaja**: Altamente complejo de consultar. Requiere múltiples joins para obtener información básica de stock por color y talle, degradando severamente el rendimiento para una funcionalidad que es fija (talles y colores).

---

## Conclusiones y Plan de Acción Recomendado

1. **Unificar Categorías y Subcategorías**: Es una mejora altamente recomendada. Se simplifica el diseño físico de la base de datos y se gana flexibilidad.
2. **Conservar Estructura de Variaciones**: El esquema de `productos` y `producto_variaciones` es correcto y el más eficiente para los requerimientos planteados.
3. **Corregir Faltante de Stock Mínimo**: Añadir la columna `stock_minimo` a `producto_variaciones`.
4. **Depurar Tablas Huérfanas**: Eliminar la tabla y migración original de `users` generada por Laravel por defecto, delegando toda la autenticación a la tabla `usuarios`.
