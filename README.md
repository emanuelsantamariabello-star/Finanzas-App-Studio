# Finanzas App Studio

Herramienta local para crear publicaciones promocionales de Finanzas App Web. Esta primera fase entrega la base modular del proyecto en PHP 8, MySQL, Bootstrap 5 y JavaScript Vanilla, sin frameworks ni Composer.

## Requisitos

- Windows
- XAMPP con Apache y MySQL
- PHP 8
- Navegador moderno

## Ruta local

El proyecto debe estar ubicado en:

```text
C:\xampp\htdocs\finanzas_app_studio
```

La aplicacion se ejecuta desde:

```text
http://localhost/finanzas_app_studio/
```

## Configuracion

1. Copiar `.env.example.php` como `.env.php`.
2. Ajustar los valores de base de datos si tu XAMPP usa credenciales diferentes.
3. Mantener `.env.php` fuera de Git.

## Base de datos

Crear la base y tablas desde phpMyAdmin o la consola de MySQL:

```sql
SOURCE C:/xampp/htdocs/finanzas_app_studio/database/schema.sql;
SOURCE C:/xampp/htdocs/finanzas_app_studio/database/seeds/001_seed_templates.sql;
```

Tambien puedes ejecutar primero `database/migrations/001_create_initial_tables.sql` si la base `finanzas_app_studio` ya existe.

## Estructura

- `app/config`: configuracion central, bootstrap y PDO.
- `app/controllers`: controladores HTTP.
- `app/helpers`: utilidades para rutas, assets, flash, HTML, respuestas y validacion.
- `app/models`: modelos base del dominio.
- `app/services`: servicios de aplicacion.
- `app/views`: layout, parciales y vistas.
- `assets`: CSS, JavaScript e imagenes publicas.
- `database`: esquema, migraciones y seeds.
- `public/uploads`: capturas o recursos subidos en fases futuras.
- `public/exports`: imagenes exportadas en fases futuras.
- `routes`: definicion simple de rutas.
- `storage`: logs y temporales internos.

## Estado de la Fase 1

Incluye dashboard responsive inicial, layout reutilizable, navbar, sidebar, footer, helpers base, configuracion PDO, SQL inicial y documentacion.

No incluye editor visual, exportacion PNG, CRUD completo, login, usuarios, IA ni dashboard funcional avanzado.
