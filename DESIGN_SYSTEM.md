# Design System

## Identidad visual

Finanzas App Studio usa automaticamente los archivos oficiales:

- `assets/images/branding/logo-finanzas-app.png`
- `assets/images/branding/favicon-finanzas-app.png`

El usuario no selecciona ni reemplaza el logo desde el editor.

## Paleta

Variables oficiales en `assets/css/app.css`:

- `--fa-navy: #0B2D7A`
- `--fa-blue: #1E73EA`
- `--fa-cyan: #19B8D1`
- `--fa-turquoise: #28C6A7`
- `--fa-green: #50D45A`
- `--fa-lime: #B7F34A`
- `--fa-white: #FFFFFF`
- `--fa-background: #F4F7FB`
- `--fa-surface: #FFFFFF`
- `--fa-text: #0F172A`
- `--fa-text-muted: #64748B`
- `--fa-border: #DCE4EE`

Gradientes reutilizables:

- `--fa-gradient-primary`
- `--fa-gradient-dark`
- `--fa-gradient-growth`

## Tipografia

Familia unica: Inter con fallback `"Segoe UI", Arial, sans-serif`.

Pesos permitidos:

- 400 regular
- 500 medium
- 600 semibold
- 700 bold
- 800 extra bold

## Espaciado

Escala base: `8, 16, 24, 32, 40, 48, 64, 80, 96`.

Reglas para publicaciones:

- Margen seguro cuadrado: minimo `64px`.
- Margen seguro historias: minimo `72px`.
- Separacion titulo/descripccion: `24px`.
- Separacion entre bloques principales: `32px`.
- Padding de tarjetas: `24px` a `32px`.
- Logo separado del borde al menos `40px`.

## Bordes y sombras

- Radio estandar: `16px`.
- Radio grande: `24px`.
- Radio de imagenes: `20px`.
- Sombra recomendada: `0 16px 40px rgba(15, 23, 42, 0.14)`.

## Plantillas

Las plantillas son estructuras bloqueadas. El usuario solo modifica plantilla, formato, titulo, subtitulo, descripcion, CTA, version y captura.

## Formatos

- Instagram cuadrado: `1080 x 1080`
- Instagram vertical: `1080 x 1350`
- Historia / Estado: `1080 x 1920`
- Facebook publicacion: `1200 x 630`
- Facebook historia: `1080 x 1920`
- LinkedIn publicacion: `1200 x 1200`
- WhatsApp estado: `1080 x 1920`

## Reglas futuras

- No introducir capas libres, drag and drop, rotacion ni coordenadas manuales.
- No agregar selectores libres de color o tipografia.
- Mantener recursos locales para evitar problemas de CORS.
- Toda salida de vistas debe escaparse.
