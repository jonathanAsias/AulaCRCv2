# theme_aulav2 (AulaV2)

Tema hijo de **Boost** (Moodle 4.5) con la portada del mockup Aula CRC. Independiente de `theme_aulacrc`: no hereda su chrome (GOV.CO kit, footer de columnas, login custom, etc.).

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
