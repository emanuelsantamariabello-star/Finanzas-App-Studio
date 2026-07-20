<?php

declare(strict_types=1);

final class DashboardController
{
    public function index(): void
    {
        $databaseAvailable = true;
        $recentPosts = [];
        view('dashboard/index', [
            'title' => 'Dashboard',
            'stats' => $this->stats($databaseAvailable, $recentPosts),
            'recentPosts' => $recentPosts,
            'databaseAvailable' => $databaseAvailable,
        ]);
    }

    public function notFound(): void
    {
        abort_not_found();
    }

    private function stats(bool &$databaseAvailable, array &$recentPosts): array
    {
        try {
            $pdo = db();
            $templates = new TemplateService($pdo);
            $posts = new PostService($pdo);
            $exports = new ExportService($pdo);
            $recentPosts = $posts->recent();

            return [
                ['label' => 'Plantillas activas', 'value' => (string) $templates->count(), 'tone' => 'primary'],
                ['label' => 'Publicaciones', 'value' => (string) $posts->count(), 'tone' => 'success'],
                ['label' => 'Borradores', 'value' => (string) $posts->count('draft'), 'tone' => 'warning'],
                ['label' => 'Exportaciones', 'value' => (string) $exports->count(), 'tone' => 'info'],
            ];
        } catch (Throwable) {
            $databaseAvailable = false;

            return [
                ['label' => 'Plantillas activas', 'value' => '-', 'tone' => 'primary'],
                ['label' => 'Publicaciones', 'value' => '-', 'tone' => 'success'],
                ['label' => 'Borradores', 'value' => '-', 'tone' => 'warning'],
                ['label' => 'Exportaciones', 'value' => '-', 'tone' => 'info'],
            ];
        }
    }
}
