# Finanzas App Studio

Herramienta local para crear publicaciones promocionales de Finanzas App Web. El estado actual del MVP permite crear borradores desde plantillas bloqueadas, reutilizar imagenes desde una biblioteca local, ver preview en tiempo real, exportar PNG individuales y generar packs multiformato.

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

Si ya tienes la Fase 1 aplicada, ejecuta:

```sql
SOURCE C:/xampp/htdocs/finanzas_app_studio/database/migrations/002_expand_posts_for_mvp.sql;
```

## Estructura

- `app/config`: configuracion central, bootstrap y PDO.
- `app/controllers`: controladores HTTP.
- `app/helpers`: utilidades para rutas, assets, flash, HTML, respuestas y validacion.
- `app/models`: modelos base del dominio.
- `app/services`: servicios de aplicacion.
- `app/views`: layout, parciales y vistas.
- `assets`: CSS, JavaScript, librerias vendor locales e imagenes publicas.
- `database`: esquema, migraciones y seeds.
- `public/uploads`: capturas y recursos cargados por el usuario.
- `public/exports`: PNG exportados guardados en servidor.
- `routes`: definicion simple de rutas.
- `storage`: logs y temporales internos.

## Estado actual del MVP

Rutas disponibles:

- `GET /`
- `GET /templates`
- `GET /posts`
- `GET /posts/create`
- `POST /posts/store`
- `GET /posts/edit?id={id}`
- `POST /posts/update`
- `POST /posts/duplicate`
- `POST /posts/delete`
- `GET /posts/export?id={id}`
- `POST /posts/export`
- `POST /posts/export-pack`
- `GET /exports`
- `GET /library`
- `POST /library/store`
- `POST /library/tags`
- `POST /library/delete`

Plantillas disponibles:

- Nueva funcionalidad
- Consejo financiero
- Actualizacion de version

Formatos disponibles:

- Instagram cuadrado: `1080 x 1080`
- Instagram vertical: `1080 x 1350`
- Historia / Estado: `1080 x 1920`
- Facebook publicacion: `1200 x 630`
- Facebook historia: `1080 x 1920`
- LinkedIn publicacion: `1200 x 1200`
- WhatsApp estado: `1080 x 1920`

## Funcionalidades implementadas

- Dashboard base operativo con layout reutilizable.
- Modulo de Publicaciones con listado, filtros, creacion, edicion, duplicado y eliminacion.
- Duplicado con variacion para cambiar plantilla y formato conservando contenido e imagen.
- Preview en tiempo real dentro del editor.
- Previsualizacion rapida en listados de Publicaciones y Exportaciones.
- Exportacion PNG individual y pack de exportacion multiformato.
- Modulo de Plantillas como catalogo funcional.
- Modulo de Exportaciones con filtros, miniaturas y descarga de archivos.
- Modulo de Biblioteca para almacenar, buscar, etiquetar, reutilizar y eliminar imagenes locales.
- Modal global de confirmacion para acciones de eliminacion.
- Sidebar colapsable y ajustes UX globales del sistema.

## Integraciones locales

- `html2canvas` se sirve localmente desde `assets/vendor/html2canvas.min.js`.
- Las exportaciones se guardan en disco y se registran en MySQL.
- La biblioteca local reutiliza archivos guardados en `public/uploads`.

## Carga de imagenes

Las imagenes se guardan en `public/uploads`. Se validan MIME real, extension, errores de carga y tamano maximo de 8 MB. No se usa el nombre original como nombre final.

## Control de versiones y entorno

- `.env.php` debe permanecer fuera de Git.
- El repositorio incluye `.gitattributes` para reducir ruido por conversiones `LF/CRLF` en Windows.

## Limitaciones conocidas

- No hay autenticacion ni gestion de usuarios en esta fase.
- No hay integracion con IA ni publicacion directa en redes sociales.
