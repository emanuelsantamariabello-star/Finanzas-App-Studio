<?php

declare(strict_types=1);

return [
    'GET' => [
        '/' => [DashboardController::class, 'index'],
        '/templates' => [TemplateController::class, 'index'],
        '/posts' => [PostController::class, 'index'],
        '/posts/create' => [PostController::class, 'create'],
        '/posts/edit' => [PostController::class, 'edit'],
        '/posts/export' => [PostController::class, 'export'],
        '/exports' => [ExportController::class, 'index'],
        '/library' => [LibraryController::class, 'index'],
        '/404' => [DashboardController::class, 'notFound'],
    ],
    'POST' => [
        '/posts/store' => [PostController::class, 'store'],
        '/posts/update' => [PostController::class, 'update'],
        '/posts/delete' => [PostController::class, 'delete'],
        '/posts/duplicate' => [PostController::class, 'duplicate'],
        '/posts/export' => [PostController::class, 'exportStore'],
        '/library/store' => [LibraryController::class, 'store'],
        '/library/delete' => [LibraryController::class, 'delete'],
    ],
];
