USE finanzas_app_studio;

INSERT INTO templates (name, slug, description, canvas_width, canvas_height)
VALUES
    ('Nueva funcionalidad', 'nueva-funcionalidad', 'Plantilla para anunciar nuevas herramientas o modulos.', 1080, 1080),
    ('Consejo financiero', 'consejo-financiero', 'Plantilla para compartir recomendaciones breves de educacion financiera.', 1080, 1080),
    ('Actualizacion de version', 'actualizacion-de-version', 'Plantilla para comunicar mejoras y cambios de version.', 1080, 1080)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    canvas_width = VALUES(canvas_width),
    canvas_height = VALUES(canvas_height);
