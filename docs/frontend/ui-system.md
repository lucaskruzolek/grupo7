# Sistema de Diseño: Temas, Superficies y Contenido

Este documento describe la arquitectura de componentes UI implementada en Pet Threads para estandarizar el diseño, facilitar el mantenimiento y permitir cambios globales de estilo sin afectar la estructura.

## Arquitectura de 3 Capas

El sistema se basa en una jerarquía de capas que separan el color, la elevación (forma) y el layout del contenido.

### 1. Capa de Color (Themes)
Define la paleta de colores semánticos. Los temas inyectan valores en variables CSS como `--color-surface`, `--color-text`, etc.

*   **Clases:** `theme-neutral` (por defecto), `theme-green`, `theme-coral`.
*   **Responsabilidad:** Definir "qué colores" se usan.
*   **Uso:** Se aplica generalmente en la sección padre o en el contenedor de la superficie.

### 2. Capa de Superficie (Surfaces)
Define la elevación, sombras, bordes y fondos. También provee el **alineamiento estructural** (Flexbox vertical y centrado) para que los elementos internos se distribuyan correctamente.

*   **`surface-flat`**: Plano sobre el fondo. Toma el color base del tema. Ideal para franjas de sección completa. No tiene flex por defecto (para permitir layouts libres en secciones).
*   **`surface-card`**: Elevado con sombra suave. Fondo sólido. Incluye alineamiento centrado y flex vertical.
*   **`surface-pill`**: Igual que la card, pero con esquinas muy redondeadas (`1rem`).

### 3. Capa de Contenido (Content)
Define el layout interno, alineación y escalado de los elementos (iconos, títulos, textos).

*   **`content-card`**: El contenedor principal de contenido.
    *   **Escalado:** Soporta la variable `--content-scale` (por defecto 1.2) para ajustar el tamaño de todo el bloque proporcionalmente.
*   **`content-icon`**: Contenedor para iconos. Puede contener un `<img>` o un `.icon-mask`.
*   **`content-title`**: Título del bloque (mayúsculas, Poppins, semibold).
*   **`content-text`**: Párrafos informativos con color secundario y algoritmo de justificación automática.

---

## Patrones de Uso Comunes

### Tarjeta Elevada (Ej: Tipos de Entrega)
Se combinan las tres capas para crear un elemento destacado.

```html
<div class="surface-card">
    <div class="content-card">
        <div class="content-icon">
            <img src="..." alt="...">
        </div>
        <h3 class="content-title">TÍTULO</h3>
        <p class="content-text">Descripción aquí...</p>
    </div>
</div>
```

### Franja Temática (Ej: Información Adicional)
Se usa una superficie plana para teñir el fondo de una sección completa.

```html
<section class="theme-coral surface-flat">
    <div class="container">
        <div class="content-card">
            <!-- contenido aquí -->
        </div>
    </div>
</section>
```

### Pill de Información (Ej: Importante)
Ideal para avisos rápidos dentro de una superficie.

```html
<div class="theme-green surface-pill">
    <div class="content-card">
        <h3 class="content-title">AVISO</h3>
        <p class="content-text">Texto del aviso.</p>
    </div>
</div>
```

---

## Mejores Prácticas

1.  **Independencia:** No apliques clases de color manuales (`text-main`, `bg-white`) si podés usar un tema.
2.  **Anidamiento:** Una `content-card` **siempre** debería estar dentro de una superficie para asegurar que el contraste de color sea el correcto.
3.  **Iconografía:** Para iconos que deben cambiar de color con el tema, usá la técnica `.icon-mask` con un SVG. Para iconos multicolor, usá `<img>` directo.
4.  **Botones:** Usá la clase `btn-theme` (si existe) para que los botones hereden automáticamente el color primario del tema aplicado a la superficie.
