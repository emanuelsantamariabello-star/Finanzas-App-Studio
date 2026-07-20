<?php

declare(strict_types=1);

final class DashboardController
{
    public function index(): void
    {
        view('dashboard/index', [
            'title' => 'Dashboard',
            'stats' => [
                ['label' => 'Plantillas base', 'value' => '3', 'tone' => 'primary'],
                ['label' => 'Publicaciones', 'value' => '0', 'tone' => 'success'],
                ['label' => 'Exportaciones', 'value' => '0', 'tone' => 'warning'],
            ],
        ]);
    }

    public function notFound(): void
    {
        abort_not_found();
    }
}
