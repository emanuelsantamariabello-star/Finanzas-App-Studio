<?php

declare(strict_types=1);

final class TemplateController
{
    public function index(): void
    {
        try {
            view('templates/index', [
                'title' => 'Plantillas',
                'templates' => (new TemplateService(db()))->all(),
                'formats' => PostService::FORMATS,
                'templateMeta' => $this->meta(),
                'databaseAvailable' => true,
            ]);
        } catch (Throwable) {
            view('templates/index', [
                'title' => 'Plantillas',
                'templates' => [],
                'formats' => PostService::FORMATS,
                'templateMeta' => $this->meta(),
                'databaseAvailable' => false,
            ]);
        }
    }

    private function meta(): array
    {
        return [
            'nueva-funcionalidad' => [
                'goal' => 'Promocionar funciones nuevas con captura protagonista.',
                'fields' => ['Titulo', 'Subtitulo', 'Descripcion', 'CTA', 'Version opcional', 'Captura obligatoria'],
            ],
            'consejo-financiero' => [
                'goal' => 'Compartir consejos educativos cortos sin requerir captura.',
                'fields' => ['Titulo', 'Descripcion', 'CTA opcional'],
            ],
            'actualizacion-de-version' => [
                'goal' => 'Comunicar cambios de version con etiqueta destacada.',
                'fields' => ['Version', 'Titulo', 'Descripcion', 'CTA', 'Captura opcional'],
            ],
        ];
    }
}
