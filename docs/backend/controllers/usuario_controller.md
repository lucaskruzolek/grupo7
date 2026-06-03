# Documentación Técnica del Controlador de Usuarios (`UsuarioController.php`)

Este documento detalla el análisis técnico de los métodos implementados en el archivo [UsuarioController.php](file:///c:/Users/lucas/Herd/grupo7/app/Http/Controllers/UsuarioController.php). Este controlador es el encargado de administrar el abanico de usuarios registrados en el sistema, permitiendo su listado junto a sus roles asignados, el alta manual con encriptación segura de contraseñas y la baja lógica (SoftDelete).

---

## Índice de Métodos

1. [index](#1-index)
2. [create](#2-create)
3. [store](#3-store)
4. [destroy](#4-destroy)

---

### 1. `index`

*   **PHPDoc**:
    *(El método original no posee bloque de comentarios PHPDoc en el código fuente).*
    ```php
    /**
     * Muestra la lista de usuarios.
     * 
     * @return \Illuminate\View\View
     */
    ```
*   **Propósito**: Consultar y renderizar el listado general de usuarios de la base de datos precargando sus roles correspondientes para evitar consultas recurrentes (N+1).
*   **Flujo Lógico**:
    1. Ejecuta la consulta sobre el modelo `Usuario` cargando diligentemente su relación `rol` mediante `Usuario::with('rol')->get()`.
    2. Retorna la vista `usuarios.index` pasando la colección recuperada a través de `compact('usuarios')`.
*   **Efectos Secundarios**: No produce modificaciones en base de datos ni sesión.
*   **Asincronismo y Dependencias**: Síncrono. Depende del modelo Eloquent [Usuario](file:///c:/Users/lucas/Herd/grupo7/app/Models/Usuario.php).

---

### 2. `create`

*   **PHPDoc**:
    *(El método original no posee bloque de comentarios PHPDoc en el código fuente).*
    ```php
    /**
     * Muestra el formulario para crear un nuevo usuario.
     * 
     * @return \Illuminate\View\View
     */
    ```
*   **Propósito**: Renderizar la vista del formulario para el alta manual de usuarios administrativos o clientes en el sistema.
*   **Flujo Lógico**:
    1. Obtiene todos los roles disponibles de la tabla `roles` llamando a `Rol::all()`.
    2. Retorna la vista `usuarios.create` enviando la lista de roles (`roles`) para poblar el selector del formulario.
*   **Efectos Secundarios**: No produce efectos secundarios.
*   **Asincronismo y Dependencias**: Síncrono. Depende del modelo Eloquent [Rol](file:///c:/Users/lucas/Herd/grupo7/app/Models/Rol.php).

---

### 3. `store`

*   **PHPDoc**:
    *(El método original no posee bloque de comentarios PHPDoc en el código fuente).*
    ```php
    /**
     * Almacena un usuario recién creado en el almacenamiento.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    ```
*   **Propósito**: Validar los datos del formulario de creación, asegurar la encriptación de la contraseña provista mediante hashing criptográfico y crear de manera persistente el nuevo usuario con su rol asociado.
*   **Flujo Lógico**:
    1. Valida los parámetros del formulario de la petición (`Request`):
        *   `nombre`: Obligatorio, cadena de texto, máximo 100 caracteres.
        *   `email`: Obligatorio, formato de correo válido, único en la tabla `usuarios`.
        *   `password`: Obligatorio, mínimo 8 caracteres, coincidente con su confirmación (`confirmed`).
        *   `rol_id`: Obligatorio, debe corresponder a un ID existente en la tabla `roles`.
    2. Inserta el nuevo registro en la tabla `usuarios` invocando `Usuario::create()`. Durante este proceso, se aplica explícitamente el hashing sobre la contraseña provista usando el helper de encriptación `Hash::make($request->password)` para garantizar el almacenamiento seguro de credenciales.
    3. Redirige a la ruta nombrada `usuarios.index` con un mensaje flash de éxito.
*   **Efectos Secundarios**:
    *   Inserta un nuevo registro en la tabla `usuarios`.
*   **Asincronismo y Dependencias**: Síncrono. Depende del modelo `Usuario`, del componente de encriptación `Hash` de Laravel y de la conexión a la base de datos.

---

### 4. `destroy`

*   **PHPDoc**:
    *(El método original no posee bloque de comentarios PHPDoc en el código fuente).*
    ```php
    /**
     * Elimina el usuario especificado del almacenamiento de forma lógica.
     * 
     * @param \App\Models\Usuario $usuario
     * @return \Illuminate\Http\RedirectResponse
     */
    ```
*   **Propósito**: Dar de baja a un usuario de manera lógica en el sistema sin eliminar físicamente su fila de la base de datos.
*   **Flujo Lógico**:
    1. Invoca el método `delete()` sobre la instancia inyectada `$usuario` (la cual posee configurado soporte de `SoftDeletes` en su modelo).
    2. Redirige a la ruta nombrada `usuarios.index` adjuntando un mensaje flash en la clave `exito`.
*   **Efectos Secundarios**:
    *   Establece la fecha y hora de baja en la columna `deleted_at` del registro del usuario indicado.
*   **Asincronismo y Dependencias**: Síncrono. Depende de la configuración de SoftDelete sobre el modelo `Usuario`.
