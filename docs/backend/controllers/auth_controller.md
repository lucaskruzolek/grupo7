# Documentación Técnica del Controlador de Autenticación (`AuthController.php`)

Este documento detalla el análisis técnico de los métodos implementados en el archivo [AuthController.php](file:///c:/Users/lucas/Herd/grupo7/app/Http/Controllers/AuthController.php). Este controlador gestiona el ciclo de vida de la autenticación de usuarios, incluyendo el registro de nuevos clientes, el inicio de sesión con control de roles y el cierre seguro de la sesión.

---

## Índice de Métodos

1. [formularioLogin](#1-formulariologin)
2. [formularioRegistro](#2-formularioregistro)
3. [registrar](#3-registrar)
4. [autenticar](#4-autenticar)
5. [logout](#5-logout)

---

### 1. `formularioLogin`

*   **PHPDoc**:
    ```php
    /**
     * Muestra el formulario de login.
     */
    ```
*   **Propósito**: Renderizar la vista correspondiente al formulario de inicio de sesión de la aplicación.
*   **Flujo Lógico**:
    1. Retorna la vista `backend.usuarios.login`.
*   **Efectos Secundarios**: No produce efectos secundarios en la base de datos ni en la sesión actual.
*   **Asincronismo y Dependencias**: Síncrono. Depende del motor de plantillas de Blade para resolver y renderizar la vista.

---

### 2. `formularioRegistro`

*   **PHPDoc**:
    ```php
    /**
     * Muestra el formulario de registro.
     */
    ```
*   **Propósito**: Renderizar la vista correspondiente al formulario de creación/registro de nuevos usuarios en la plataforma.
*   **Flujo Lógico**:
    1. Retorna la vista `backend.usuarios.register`.
*   **Efectos Secundarios**: No produce efectos secundarios.
*   **Asincronismo y Dependencias**: Síncrono. Depende del motor de renderizado Blade.

---

### 3. `registrar`

*   **PHPDoc**:
    ```php
    /**
     * Procesa el formulario de registro: valida, crea el usuario y lo loguea.
     */
    ```
*   **Propósito**: Validar los datos provistos en el formulario de registro, crear una nueva instancia de usuario con el rol predeterminado de `'cliente'`, iniciar la sesión del usuario recién creado de manera automática y redirigir al inicio del sitio.
*   **Flujo Lógico**:
    1. Ejecuta la validación de los datos recibidos mediante `$request->validate()`:
        *   `nombre`: Obligatorio, cadena de caracteres, máximo 255 caracteres.
        *   `apellido`: Obligatorio, cadena de caracteres, máximo 255 caracteres.
        *   `email`: Obligatorio, formato de correo válido, único en la tabla `usuarios`.
        *   `password`: Obligatorio, mínimo 6 caracteres, debe coincidir con su confirmación (`confirmed`).
    2. Consulta u obtiene el rol de cliente de la base de datos usando `Rol::firstOrCreate(['nombre' => 'cliente'], ...)`. Si el rol no existe, se inserta en base de datos.
    3. Registra el nuevo registro en la tabla `usuarios` mediante `Usuario::create(...)`. Cabe destacar que el password se almacena procesado gracias a que el modelo `Usuario` tiene configurado el cast `hashed` para el atributo `password`.
    4. Loguea automáticamente al usuario en la aplicación con `Auth::login($usuario)`.
    5. Regenera el ID de la sesión PHP para evitar ataques de fijación de sesión usando `$request->session()->regenerate()`.
    6. Redirige al cliente a la ruta nombrada `'inicio'`.
*   **Efectos Secundarios**: 
    *   Inserta un nuevo registro en la tabla `usuarios`.
    *   Puede insertar un nuevo registro en la tabla `roles` si el rol `'cliente'` no existe aún.
    *   Modifica el estado de autenticación de la sesión HTTP actual y regenera el ID de sesión del usuario.
*   **Asincronismo y Dependencias**: Síncrono. Depende de los modelos Eloquent [Usuario](file:///c:/Users/lucas/Herd/grupo7/app/Models/Usuario.php) y [Rol](file:///c:/Users/lucas/Herd/grupo7/app/Models/Rol.php), de la fachada `Auth` de Laravel y de la base de datos.

---

### 4. `autenticar`

*   **PHPDoc**:
    ```php
    /**
     * Procesa el login: valida credenciales, inicia sesión y redirige según rol.
     */
    ```
*   **Propósito**: Verificar las credenciales de correo y contraseña proporcionadas por el usuario, iniciar su sesión en el servidor y redirigirlo a la sección administrativa o principal según el nivel de privilegios de su rol.
*   **Flujo Lógico**:
    1. Valida que el `email` y `password` estén presentes y que el correo posea un formato válido.
    2. Verifica si el usuario marcó la opción de recordar sesión leyendo el parámetro `remember` de la petición.
    3. Intenta realizar el inicio de sesión por credenciales a través del método `Auth::attempt()`.
    4. **En caso de éxito en la autenticación**:
        *   Regenera el ID de la sesión actual (`$request->session()->regenerate()`).
        *   Verifica a través de la relación Eloquent si el rol del usuario autenticado es `'admin'`.
        *   Si es administrador (`admin`), redirige al dashboard administrativo en `/admin/dashboard`.
        *   Si es cualquier otro rol (ej: `'cliente'`), redirige a la ruta nombrada `'inicio'`.
    5. **En caso de fallo en la autenticación**:
        *   Redirige de vuelta (`back()`) adjuntando un error bajo la clave `email` indicando `"Email o contraseña incorrectos"`.
        *   Mantiene en la redirección el campo `email` enviado por el usuario mediante `onlyInput('email')`.
*   **Efectos Secundarios**: Modifica la sesión HTTP actual del cliente (cambia el estado a autenticado y regenera el ID de sesión) en caso de éxito.
*   **Asincronismo y Dependencias**: Síncrono. Depende de la base de datos para validar las credenciales de la tabla `usuarios` y su relación con `roles` mediante Eloquent, además de la configuración de autenticación definida en `config/auth.php`.

---

### 5. `logout`

*   **PHPDoc**:
    ```php
    /**
     * Cierra la sesión del usuario autenticado.
     */
    ```
*   **Propósito**: Desautenticar al usuario actual, destruir la información contenida en su sesión y regenerar el token CSRF para prevenir reutilización de la sesión.
*   **Flujo Lógico**:
    1. Invoca el método `Auth::logout()` para limpiar la autenticación del usuario.
    2. Invalida la sesión actual en el servidor web mediante `$request->session()->invalidate()`.
    3. Genera un nuevo token CSRF en la sesión con `$request->session()->regenerateToken()`.
    4. Retorna una redirección hacia la ruta nombrada `'login'`.
*   **Efectos Secundarios**: Destruye la sesión de usuario activa en el almacenamiento del servidor e invalida la cookie de sesión del cliente.
*   **Asincronismo y Dependencias**: Síncrono. Depende directamente de la fachada `Auth` de Laravel y de las APIs de gestión de sesiones de la petición HTTP (`Request`).
