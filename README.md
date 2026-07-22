# Finanzas App Studio

Herramienta local para crear publicaciones promocionales de Finanzas App Web. El MVP permite crear borradores desde plantillas bloqueadas, cargar capturas, ver preview en tiempo real y exportar PNG.

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
- `assets`: CSS, JavaScript e imagenes publicas.
- `database`: esquema, migraciones y seeds.
- `public/uploads`: capturas o recursos subidos en fases futuras.
- `public/exports`: imagenes exportadas en fases futuras.
- `routes`: definicion simple de rutas.
- `storage`: logs y temporales internos.

## Estado de la Fase 1

Incluye dashboard responsive inicial, layout reutilizable, navbar, sidebar, footer, helpers base, configuracion PDO, SQL inicial y documentacion.

No incluye editor visual, exportacion PNG, CRUD completo, login, usuarios, IA ni dashboard funcional avanzado.

## MVP v0.2.0

Rutas disponibles:

- `GET /`
- `GET /posts`
- `GET /posts/create`
- `POST /posts/store`
- `GET /posts/edit?id={id}`
- `POST /posts/update`
- `POST /posts/duplicate`
- `POST /posts/delete`
- `GET /posts/export?id={id}`

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

La exportacion usa `html2canvas` desde CDN, espera fuentes e imagenes, guarda un PNG real en `public/exports`, registra la exportacion en MySQL y descarga el archivo guardado.

## Carga de imagenes

Las imagenes se guardan en `public/uploads`. Se validan MIME real, extension, errores de carga y tamaño maximo de 8 MB. No se usa el nombre original como nombre final.

## Limitaciones conocidas

- `html2canvas` se carga por CDN; para uso sin internet se recomienda vendorizarlo localmente en una fase posterior.
- No hay autenticacion ni usuarios en esta fase.
