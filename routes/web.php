<?php

declare(strict_types=1);

return [
    '/' => [DashboardController::class, 'index'],
    '/404' => [DashboardController::class, 'notFound'],
];
