# Changelog

## No liberado

- Agrega modulo de Plantillas como catalogo funcional.
- Agrega modulo de Exportaciones con filtros, miniaturas y descarga de archivos.
- Guarda exportaciones PNG reales en `public/exports` y registra su ruta en MySQL.
- Agrega formatos para Facebook, LinkedIn y WhatsApp.
- Agrega menu lateral colapsable y mejoras UX globales.
- Corrige el layout de Facebook publicacion y el comportamiento visual del menu lateral.
- Agrega biblioteca local de imagenes con seleccion desde editor, almacenamiento, busqueda y etiquetas simples.
- Agrega modal global de confirmacion para eliminaciones y corrige la restauracion de botones al cancelar.
- Agrega duplicado de publicaciones con variacion de plantilla y formato.
- Agrega previsualizacion rapida en los listados de Publicaciones y Exportaciones.
- Agrega pack de exportacion multiformato desde una misma publicacion.
- Vendoriza `html2canvas` para uso local sin dependencia de CDN.
- Agrega normalizacion basica de finales de linea mediante `.gitattributes`.

## v0.2.0 - MVP generador de publicaciones

- Agrega gestion de publicaciones: listado, creacion, edicion, duplicado y eliminacion.
- Agrega preview en tiempo real con JavaScript Vanilla.
- Agrega exportacion PNG con `html2canvas`.
- Agrega migracion incremental para ampliar `posts`.
- Agrega carga segura de imagenes en `public/uploads`.
- Actualiza dashboard con datos reales desde MySQL.
- Documenta el sistema visual oficial en `DESIGN_SYSTEM.md`.

## v0.1.0 - Base profesional

- Crea estructura modular inicial.
- Agrega configuracion central, PDO, rutas simples y layout base.
- Agrega esquema SQL, migracion inicial y seed de plantillas.
