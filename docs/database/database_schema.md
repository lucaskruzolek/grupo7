# Mapa de Esquema Virtual de la Base de Datos

Este documento sirve como referencia rápida del esquema físico de la base de datos generado a partir de las migraciones. Evita tener que escanear los archivos de migración repetidamente durante el desarrollo.

---

## Diagrama de Relaciones (MER)

```mermaid
erDiagram
    roles ||--o{ usuarios : "tiene muchos"
    categorias ||--o{ categorias : "padre de (recursivo)"
    categorias ||--o{ productos : "clasifica"
    colecciones ||--o{ productos : "agrupa"
    colores ||--o{ productos : "colorea"
    productos ||--o{ producto_imagenes : "se vincula por sku_color"
    usuarios ||--o{ ventas : "realiza"
    formas_pago ||--o{ ventas : "aplica a"
    ventas ||--o{ venta_detalles : "contiene"
    productos ||--o{ venta_detalles : "vendido en"
    usuarios ||--o{ consultas : "realiza"

    roles {
        id bigint PK
        nombre varchar(50)
        descripcion varchar(255)
    }

    usuarios {
        id bigint PK
        nombre varchar(255)
        apellido varchar(255)
        email varchar(255)
        password varchar(255)
        rol_id bigint FK
    }

    categorias {
        id bigint PK
        parent_id bigint FK "nullable"
        nombre varchar(100)
        icono varchar(255) "nullable"
        pide_talle boolean "default: true"
        pide_color boolean "default: true"
    }

    colores {
        id bigint PK
        nombre varchar(50)
        hex_code varchar(9)
    }

    colecciones {
        id bigint PK
        nombre varchar(100)
        descripcion text "nullable"
        url_imagen varchar(255) "nullable"
    }

    productos {
        id bigint PK
        categoria_id bigint FK
        coleccion_id bigint FK "nullable"
        color_id bigint FK
        nombre varchar(150)
        descripcion text "nullable"
        tipo_mascota enum "perro, gato, ambos"
        sku_base varchar(50) "index"
        sku_color varchar(80) "index"
        sku varchar(50) "unique"
        talle varchar(10)
        stock int "default: 0"
        stock_minimo int "default: 0"
        precio decimal(10,2) "default: 0.00"
        favorito boolean "default: false"
        activo boolean "default: true"
    }

    producto_imagenes {
        id bigint PK
        sku_color varchar(80) "index"
        url varchar(255)
        orden smallint "default: 0 (1 = principal)"
    }

    formas_pago {
        id bigint PK
        descripcion varchar(100)
    }

    ventas {
        id bigint PK
        fecha_venta datetime "nullable"
        fecha_despacho datetime "nullable"
        usuario_id bigint FK "nullable"
        estado enum "CARRITO, CONFIRMADO, DESPACHADO"
        total decimal(10,2)
        forma_pago_id bigint FK "nullable"
    }

    venta_detalles {
        id bigint PK
        venta_id bigint FK
        producto_id bigint FK
        cantidad int
        precio_unitario decimal(10,2)
        subtotal decimal(10,2)
    }

    consultas {
        id bigint PK
        nombre varchar(255)
        email varchar(255)
        telefono varchar(255) "nullable"
        pedido varchar(255) "nullable, index"
        asunto enum "consulta, reclamo, devolucion, otro"
        mensaje text
        leido boolean "default: false"
        respondido boolean "default: false"
        usuario_id bigint FK "nullable"
    }
```

---

## Detalle de Tablas y Columnas

### 1. Tabla: `roles`
* **Propósito:** Roles de usuario del sistema (ej: `admin`).

| Columna | Tipo | Nulabilidad | Restricciones / Atributos | Descripción |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint` | `NOT NULL` | `PRIMARY KEY`, `AUTO_INCREMENT` | Identificador único. |
| `nombre` | `varchar(50)` | `NOT NULL` | `UNIQUE` | Nombre del rol. |
| `descripcion` | `varchar(255)` | `NULL` | | Detalle opcional del rol. |
| `created_at` | `timestamp` | `NULL` | | Marca de tiempo de creación. |
| `updated_at` | `timestamp` | `NULL` | | Marca de tiempo de actualización. |
| `deleted_at` | `timestamp` | `NULL` | | SoftDelete. |

---

### 2. Tabla: `usuarios`
* **Propósito:** Credenciales y perfiles de los usuarios de la plataforma.

| Columna | Tipo | Nulabilidad | Restricciones / Atributos | Descripción |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint` | `NOT NULL` | `PRIMARY KEY`, `AUTO_INCREMENT` | Identificador único. |
| `nombre` | `varchar(255)` | `NOT NULL` | | Nombre de pila. |
| `apellido` | `varchar(255)` | `NOT NULL` | | Apellido. |
| `email` | `varchar(255)` | `NOT NULL` | `UNIQUE` | Email de inicio de sesión. |
| `password` | `varchar(255)` | `NOT NULL` | | Contraseña hasheada. |
| `rol_id` | `bigint` | `NOT NULL` | `FOREIGN KEY` -> `roles(id)` | Relación con rol (`onDelete: restrict`). |
| `remember_token`| `varchar(100)` | `NULL` | | Token de sesión persistente. |
| `created_at` | `timestamp` | `NULL` | | Creación. |
| `updated_at` | `timestamp` | `NULL` | | Actualización. |
| `deleted_at` | `timestamp` | `NULL` | | SoftDelete. |

---

### 3. Tabla: `categorias`
* **Propósito:** Clasificación de los productos en estructura de árbol y parámetros de variación.

| Columna | Tipo | Nulabilidad | Restricciones / Atributos | Descripción |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint` | `NOT NULL` | `PRIMARY KEY`, `AUTO_INCREMENT` | Identificador único. |
| `parent_id` | `bigint` | `NULL` | `FOREIGN KEY` -> `categorias(id)`| Relación recursiva (`onDelete: cascade`). |
| `nombre` | `varchar(100)` | `NOT NULL` | | Nombre de la categoría. |
| `icono` | `varchar(255)` | `NULL` | | URL del archivo SVG (R2). |
| `pide_talle` | `boolean` | `NOT NULL` | `default: true` | Control de variaciones de talle. |
| `pide_color` | `boolean` | `NOT NULL` | `default: true` | Control de variaciones de color. |
| `created_at` | `timestamp` | `NULL` | | Creación. |
| `updated_at` | `timestamp` | `NULL` | | Actualización. |
| `deleted_at` | `timestamp` | `NULL` | | SoftDelete. |

---

### 4. Tabla: `colores`
* **Propósito:** Catálogo maestro de colores para los productos.

| Columna | Tipo | Nulabilidad | Restricciones / Atributos | Descripción |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint` | `NOT NULL` | `PRIMARY KEY`, `AUTO_INCREMENT` | Identificador único. |
| `nombre` | `varchar(50)` | `NOT NULL` | | Nombre del color (ej: "Azul"). |
| `hex_code` | `varchar(9)` | `NOT NULL` | | Código hexadecimal (ej: "#0000FF" o "#051CEBFF"). |
| `created_at` | `timestamp` | `NULL` | | Creación. |
| `updated_at` | `timestamp` | `NULL` | | Actualización. |

---

### 5. Tabla: `colecciones`
* **Propósito:** Agrupaciones estacionales o de campañas de productos.

| Columna | Tipo | Nulabilidad | Restricciones / Atributos | Descripción |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint` | `NOT NULL` | `PRIMARY KEY`, `AUTO_INCREMENT` | Identificador único. |
| `nombre` | `varchar(100)` | `NOT NULL` | | Nombre de la campaña. |
| `descripcion` | `text` | `NULL` | | Descripción amplia. |
| `url_imagen` | `varchar(255)` | `NULL` | | Portada visual cargada en R2/URL. |
| `created_at` | `timestamp` | `NULL` | | Creación. |
| `updated_at` | `timestamp` | `NULL` | | Actualización. |
| `deleted_at` | `timestamp` | `NULL` | | SoftDelete. |

---

### 6. Tabla: `productos`
* **Propósito:** Catálogo plano de productos y variantes de stock.
* **Notas de Diseño:** Utiliza un esquema plano (Flat Model) donde cada registro representa una variante única de SKU, pero comparten un `sku_base` para agruparse en la UI.

| Columna | Tipo | Nulabilidad | Restricciones / Atributos | Descripción |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint` | `NOT NULL` | `PRIMARY KEY`, `AUTO_INCREMENT` | Identificador único. |
| `categoria_id` | `bigint` | `NOT NULL` | `FOREIGN KEY` -> `categorias(id)`| Categoría del producto (`onDelete: restrict`). |
| `coleccion_id` | `bigint` | `NULL` | `FOREIGN KEY` -> `colecciones(id)`| Colección opcional (`onDelete: set null`). |
| `color_id` | `bigint` | `NOT NULL` | `FOREIGN KEY` -> `colores(id)`| Color de la variante (`onDelete: restrict`). |
| `nombre` | `varchar(150)` | `NOT NULL` | | Nombre de la variante. |
| `descripcion` | `text` | `NULL` | | Descripción detallada. |
| `tipo_mascota` | `enum` | `NOT NULL` | `default: ambos` | Opciones: `['perro', 'gato', 'ambos']`. |
| `sku_base` | `varchar(50)` | `NOT NULL` | `INDEX` | Modelo agrupador (ej: `BUZO-POLAR`). |
| `sku_color` | `varchar(80)` | `NOT NULL` | `INDEX` | Agrupador de color (ej: `BUZO-POLAR-ROJO`). |
| `sku` | `varchar(50)` | `NOT NULL` | `UNIQUE` | Variante única final (ej: `BUZO-POLAR-ROJO-S`). |
| `talle` | `varchar(10)` | `NOT NULL` | | Talle físico ('S', 'M', 'L', '-', etc.). |
| `stock` | `int` | `NOT NULL` | `default: 0` | Cantidad física en bodega. |
| `stock_minimo` | `int` | `NOT NULL` | `default: 0` | Umbral para alertas de reabastecimiento. |
| `precio` | `decimal(10,2)`| `NOT NULL` | `default: 0.00` | Precio de venta. |
| `favorito` | `boolean` | `NOT NULL` | `default: false` | Destacado en landing page. |
| `activo` | `boolean` | `NOT NULL` | `default: true` | Indica si el producto está visible al público. |
| `created_at` | `timestamp` | `NULL` | | Creación. |
| `updated_at` | `timestamp` | `NULL` | | Actualización. |
| `deleted_at` | `timestamp` | `NULL` | | SoftDelete. |

#### Índices Especiales en `productos`:
* **Clave Única (`variacion_unica`):** `UNIQUE(sku_base, color_id, talle)` impide colisiones lógicas de la misma variante de color y talle en un mismo modelo de producto.

---

### 7. Tabla: `producto_imagenes`
* **Propósito:** Galerías fotográficas de productos agrupadas a nivel de color (`sku_color`).

| Columna | Tipo | Nulabilidad | Restricciones / Atributos | Descripción |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint` | `NOT NULL` | `PRIMARY KEY`, `AUTO_INCREMENT` | Identificador único. |
| `sku_color` | `varchar(80)` | `NOT NULL` | `INDEX` | Enlace al grupo de color del producto. |
| `url` | `varchar(255)` | `NOT NULL` | | URL de la imagen en R2. |
| `orden` | `smallint` | `NOT NULL` | `default: 0` | El valor `1` indica que es la portada principal. |
| `created_at` | `timestamp` | `NULL` | | Creación. |
| `updated_at` | `timestamp` | `NULL` | | Actualización. |

---

### 8. Tabla: `formas_pago`
* **Propósito:** Catálogo maestro para definir los métodos de pago aceptados en la tienda.

| Columna | Tipo | Nulabilidad | Restricciones / Atributos | Descripción |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint` | `NOT NULL` | `PRIMARY KEY`, `AUTO_INCREMENT` | Identificador único del método. |
| `descripcion` | `varchar(100)`| `NOT NULL` | | Nombre o descripción corta (ej: "Tarjeta"). |
| `created_at` | `timestamp` | `NULL` | | Creación. |
| `updated_at` | `timestamp` | `NULL` | | Actualización. |

---

### 9. Tabla: `ventas`
* **Propósito:** Encabezados de venta o carritos de compra activos de los usuarios.

| Columna | Tipo | Nulabilidad | Restricciones / Atributos | Descripción |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint` | `NOT NULL` | `PRIMARY KEY`, `AUTO_INCREMENT` | Identificador único de la venta/carrito. |
| `fecha_venta` | `datetime` | `NULL` | | Momento en el que se confirma la compra (checkout). |
| `fecha_despacho`| `datetime` | `NULL` | | Momento en el que se despacha el pedido. |
| `usuario_id` | `bigint` | `NULL` | `FOREIGN KEY` -> `usuarios(id)` | Cliente propietario (`onDelete: set null`). |
| `estado` | `enum` | `NOT NULL` | `default: CARRITO` | Opciones: `['CARRITO', 'CONFIRMADO', 'DESPACHADO']`. |
| `total` | `decimal(10,2)`| `NOT NULL` | `default: 0.00` | Monto total acumulado de la venta. |
| `forma_pago_id`| `bigint` | `NULL` | `FOREIGN KEY` -> `formas_pago(id)`| Método de pago elegido (`onDelete: restrict`). |
| `created_at` | `timestamp` | `NULL` | | Creación (inicio del carrito). |
| `updated_at` | `timestamp` | `NULL` | | Actualización. |
| `deleted_at` | `timestamp` | `NULL` | | SoftDelete. |

---

### 10. Tabla: `venta_detalles`
* **Propósito:** Desglose de productos (variantes) incluidos en cada venta o carrito.

| Columna | Tipo | Nulabilidad | Restricciones / Atributos | Descripción |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint` | `NOT NULL` | `PRIMARY KEY`, `AUTO_INCREMENT` | Identificador único del item de venta. |
| `venta_id` | `bigint` | `NOT NULL` | `FOREIGN KEY` -> `ventas(id)` | Cabecera a la que pertenece (`onDelete: cascade`). |
| `producto_id` | `bigint` | `NOT NULL` | `FOREIGN KEY` -> `productos(id)` | Variante física del producto (`onDelete: restrict`). |
| `cantidad` | `int` | `NOT NULL` | `default: 1` | Cantidad de unidades añadidas. |
| `precio_unitario`| `decimal(10,2)`| `NOT NULL` | | Precio unitario del producto al momento de añadirlo. |
| `subtotal` | `decimal(10,2)`| `NOT NULL` | | Subtotal acumulado (`cantidad * precio_unitario`). |
| `created_at` | `timestamp` | `NULL` | | Creación. |
| `updated_at` | `timestamp` | `NULL` | | Actualización. |

---

### 11. Tabla: `consultas`
* **Propósito:** Registro de consultas, reclamos o mensajes enviados por los usuarios/clientes a través del formulario de contacto.

| Columna | Tipo | Nulabilidad | Restricciones / Atributos | Descripción |
| :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint` | `NOT NULL` | `PRIMARY KEY`, `AUTO_INCREMENT` | Identificador único de la consulta. |
| `nombre` | `varchar(255)` | `NOT NULL` | | Nombre del remitente. |
| `email` | `varchar(255)` | `NOT NULL` | | Email del remitente. |
| `telefono` | `varchar(255)` | `NULL` | | Teléfono opcional de contacto. |
| `pedido` | `varchar(255)` | `NULL` | `INDEX` | Número de pedido asociado opcional. |
| `asunto` | `enum` | `NOT NULL` | | Opciones: `['consulta', 'reclamo', 'devolucion', 'otro']`. |
| `mensaje` | `text` | `NOT NULL` | | Cuerpo del mensaje de la consulta. |
| `leido` | `boolean` | `NOT NULL` | `default: false` | Estado de lectura por parte del administrador. |
| `respondido` | `boolean` | `NOT NULL` | `default: false` | Estado de respuesta de la consulta. |
| `usuario_id` | `bigint` | `NULL` | `FOREIGN KEY` -> `usuarios(id)` | Usuario autenticado propietario (`onDelete: set null`). |
| `created_at` | `timestamp` | `NULL` | | Creación. |
| `updated_at` | `timestamp` | `NULL` | | Actualización. |
| `deleted_at` | `timestamp` | `NULL` | | SoftDelete. |
