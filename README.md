# theme_aulav2 (AulaV2)

Tema hijo de **Boost** (Moodle 4.5) con la portada del mockup Aula CRC. Independiente de `theme_aulacrc`.

## 1.0.3

- Hook-safe: depende de AulaCRC ≥ 4.5.73 en el mismo Moodle (el hook ya no rompe AulaV2).
- Portada con **categorías y cursos reales**: audiencias, temas, colecciones, ruta y recomendados.

## 1.0.2

- La franja de navegación del home muestra el menú primario de Moodle (Página principal, Administración del sitio, etc.), con menú móvil, usuario e idioma.
- Se mantiene la cabecera mockup (GOV.CO + logo) y el CTA “Ir a la sede CRC”.

## 1.0.1

- Cabecera del mockup en portada: barra GOV.CO, logo CRC centrado, menú (Inicio / Catálogo / Ir a la sede CRC).
- La portada ya no usa la navbar Boost ni el layout drawers estándar.
- Tipografía Nunito Sans por defecto; SCSS alineado al frame Figma.

## Instalación

1. Copia esta carpeta a `moodle/theme/aulav2/`.
2. Ve a **Administración del sitio → Notificaciones** para instalar el plugin.
3. **Apariencia → Temas → Selector de temas** → elige **AulaV2**.
4. Purga cachés si la portada no refleja estilos.

## Ajustes de marca

**Apariencia → AulaV2**:

| Pestaña | Qué configura |
|---------|----------------|
| **General** | Logos, favicon, colores Boost (primario/secundario), navbar, fondo, tipografía (sistema o Google Fonts) |
| **Colores del home** | Paleta del mockup (GOV.CO, navy, ink, hero, bordes) |
| **Avanzado** | SCSS inicial / extra |

## Portada

En `pagelayout-frontpage` se renderiza el home del mockup (hero, audiencias, colecciones, temas, ruta, recomendados, FAQ y footer institucional). El `main_content` de Moodle se emite en un contenedor oculto para cumplir el contrato del renderer.

## Estructura

```
aulav2/
  config.php, lib.php, settings.php, version.php
  layout/drawers.php, layout/login.php
  templates/{drawers,frontpage,footer}.mustache
  scss/aulav2/_home.scss
  javascript/faq.js
  pix/…
  lang/{en,es}/theme_aulav2.php
```
