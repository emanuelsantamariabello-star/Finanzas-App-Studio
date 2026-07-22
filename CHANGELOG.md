# Changelog

## No liberado

- Guarda las exportaciones PNG reales en `public/exports`.
- Registra en MySQL la ruta del archivo exportado guardado en servidor.

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
