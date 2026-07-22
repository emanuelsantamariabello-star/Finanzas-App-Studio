<?php

declare(strict_types=1);

final class ExportController
{
    public function index(): void
    {
        try {
            $pdo = db();
            $templateId = valid_id($_GET['template_id'] ?? null);
            $format = (string) ($_GET['format'] ?? '');
            $format = isset(PostService::FORMATS[$format]) ? $format : null;

            view('exports/index', [
                'title' => 'Exportaciones',
                'exports' => (new ExportService($pdo))->list($templateId, $format),
                'templates' => (new TemplateService($pdo))->all(),
                'formats' => PostService::FORMATS,
                'selectedTemplateId' => $templateId,
                'selectedFormat' => $format,
                'databaseAvailable' => true,
            ]);
        } catch (Throwable) {
            view('exports/index', [
                'title' => 'Exportaciones',
                'exports' => [],
                'templates' => [],
                'formats' => PostService::FORMATS,
                'selectedTemplateId' => null,
                'selectedFormat' => null,
                'databaseAvailable' => false,
            ]);
        }
    }
}
