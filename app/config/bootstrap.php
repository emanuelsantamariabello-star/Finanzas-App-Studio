<?php

declare(strict_types=1);

date_default_timezone_set('America/Bogota');

require_once APP_BASE_PATH . '/app/config/config.php';
require_once APP_BASE_PATH . '/app/helpers/assets.php';
require_once APP_BASE_PATH . '/app/helpers/routes.php';
require_once APP_BASE_PATH . '/app/helpers/flash.php';
require_once APP_BASE_PATH . '/app/helpers/html.php';
require_once APP_BASE_PATH . '/app/helpers/responses.php';
require_once APP_BASE_PATH . '/app/helpers/validation.php';
require_once APP_BASE_PATH . '/app/helpers/csrf.php';
require_once APP_BASE_PATH . '/app/config/database.php';
require_once APP_BASE_PATH . '/app/models/Template.php';
require_once APP_BASE_PATH . '/app/models/Post.php';
require_once APP_BASE_PATH . '/app/models/Export.php';
require_once APP_BASE_PATH . '/app/services/TemplateService.php';
require_once APP_BASE_PATH . '/app/services/PostService.php';
require_once APP_BASE_PATH . '/app/services/UploadService.php';
require_once APP_BASE_PATH . '/app/services/ExportService.php';
require_once APP_BASE_PATH . '/app/controllers/DashboardController.php';
require_once APP_BASE_PATH . '/app/controllers/PostController.php';
