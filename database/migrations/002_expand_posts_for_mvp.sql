USE finanzas_app_studio;

ALTER TABLE posts
    MODIFY title VARCHAR(90) NOT NULL,
    ADD COLUMN subtitle VARCHAR(130) NULL AFTER title,
    ADD COLUMN description VARCHAR(320) NULL AFTER subtitle,
    ADD COLUMN cta_text VARCHAR(60) NULL AFTER description,
    ADD COLUMN version_label VARCHAR(30) NULL AFTER cta_text,
    ADD COLUMN format VARCHAR(40) NOT NULL DEFAULT 'instagram_square' AFTER version_label,
    ADD COLUMN image_path VARCHAR(255) NULL AFTER format,
    ADD COLUMN content_json JSON NULL AFTER status;

UPDATE posts
SET description = body_text
WHERE description IS NULL AND body_text IS NOT NULL;

UPDATE posts
SET image_path = screenshot_path
WHERE image_path IS NULL AND screenshot_path IS NOT NULL;

ALTER TABLE posts
    MODIFY status ENUM('draft', 'exported') NOT NULL DEFAULT 'draft',
    DROP COLUMN body_text,
    DROP COLUMN screenshot_path;
