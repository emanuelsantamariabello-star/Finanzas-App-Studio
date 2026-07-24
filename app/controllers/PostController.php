<?php

declare(strict_types=1);

final class PostController
{
    public function index(): void
    {
        try {
            $pdo = db();
            $templates = new TemplateService($pdo);
            $templateId = valid_id($_GET['template_id'] ?? null);

            view('posts/index', [
                'title' => 'Publicaciones',
                'posts' => (new PostService($pdo))->list($templateId),
                'templates' => $templates->all(),
                'selectedTemplateId' => $templateId,
                'databaseAvailable' => true,
            ]);
        } catch (Throwable) {
            view('posts/index', [
                'title' => 'Publicaciones',
                'posts' => [],
                'templates' => [],
                'selectedTemplateId' => null,
                'databaseAvailable' => false,
            ]);
        }
    }

    public function create(): void
    {
        $this->form();
    }

    public function edit(): void
    {
        $id = valid_id($_GET['id'] ?? null);

        if ($id === null) {
            abort_not_found();
            return;
        }

        $this->form($id);
    }

    public function store(): void
    {
        $this->persist();
    }

    public function update(): void
    {
        $id = valid_id($_POST['id'] ?? null);

        if ($id === null) {
            abort_not_found();
            return;
        }

        $this->persist($id);
    }

    public function duplicate(): void
    {
        if (!verify_csrf()) {
            flash('error', 'La sesion expiro. Intenta nuevamente.');
            redirect('/posts');
        }

        $id = valid_id($_POST['id'] ?? null);

        if ($id === null) {
            abort_not_found();
            return;
        }

        try {
            $pdo = db();
            $templates = new TemplateService($pdo);
            $templateId = valid_id($_POST['template_id'] ?? null);
            $format = (string) ($_POST['format'] ?? '');

            if ($templateId === null || $templates->find($templateId) === null || !isset(PostService::FORMATS[$format])) {
                flash('error', 'Selecciona una plantilla y formato validos para duplicar.');
                redirect('/posts');
            }

            $newId = (new PostService($pdo))->duplicate($id, $templateId, $format);

            if ($newId === null) {
                flash('error', 'No se encontro la publicacion.');
                redirect('/posts');
            }

            flash('success', 'Publicacion duplicada con variacion.');
            redirect('/posts/edit?id=' . $newId);
        } catch (Throwable) {
            flash('error', 'No fue posible duplicar la publicacion.');
        }

        redirect('/posts');
    }

    public function delete(): void
    {
        if (!verify_csrf()) {
            flash('error', 'La sesion expiro. Intenta nuevamente.');
            redirect('/posts');
        }

        $id = valid_id($_POST['id'] ?? null);

        if ($id === null) {
            abort_not_found();
            return;
        }

        try {
            $pdo = db();
            $posts = new PostService($pdo);
            $upload = new UploadService();
            $post = $posts->find($id);
            $posts->delete($id);

            if ($post !== null) {
                $upload->deleteIfUnused($post['image_path'], $posts);
            }

            flash('success', 'Publicacion eliminada.');
        } catch (Throwable) {
            flash('error', 'No fue posible eliminar la publicacion.');
        }

        redirect('/posts');
    }

    public function export(): void
    {
        $id = valid_id($_GET['id'] ?? null);

        if ($id === null) {
            abort_not_found();
            return;
        }

        try {
            $pdo = db();
            $posts = new PostService($pdo);
            $post = $posts->find($id);

            if ($post === null) {
                abort_not_found();
                return;
            }

            flash('error', 'Genera la exportacion desde el boton Exportar PNG del editor.');
            redirect('/posts/edit?id=' . $id);
        } catch (Throwable) {
            flash('error', 'No fue posible preparar la exportacion.');
            redirect('/posts');
        }
    }

    public function exportStore(): void
    {
        if (!verify_csrf()) {
            json_response(['ok' => false, 'message' => 'La sesion expiro. Intenta nuevamente.'], 419);
            return;
        }

        $id = valid_id($_POST['id'] ?? null);

        if ($id === null) {
            json_response(['ok' => false, 'message' => 'Publicacion no valida.'], 422);
            return;
        }

        try {
            $pdo = db();
            $posts = new PostService($pdo);
            $post = $posts->find($id);

            if ($post === null) {
                json_response(['ok' => false, 'message' => 'No se encontro la publicacion.'], 404);
                return;
            }

            $export = (new ExportService($pdo))->storeUploadedPng($post, $_FILES['png'] ?? null);
            $posts->markExported($id);

            json_response([
                'ok' => true,
                'message' => 'Exportacion guardada.',
                'download_url' => url($export['file_path']),
                'file_path' => $export['file_path'],
            ]);
        } catch (Throwable) {
            json_response(['ok' => false, 'message' => 'No fue posible guardar la exportacion.'], 500);
        }
    }

    private function form(?int $id = null, array $old = [], array $errors = []): void
    {
        try {
            $pdo = db();
            $templates = (new TemplateService($pdo))->all();
            $post = $id === null ? null : (new PostService($pdo))->find($id);

            if ($id !== null && $post === null) {
                abort_not_found();
                return;
            }

            view('posts/form', [
                'title' => $post === null ? 'Nueva publicacion' : 'Editar publicacion',
                'post' => $post,
                'old' => $old,
                'errors' => $errors,
                'templates' => $templates,
                'formats' => PostService::FORMATS,
                'mediaLibrary' => (new UploadService())->recentLibrary(),
            ]);
        } catch (Throwable) {
            flash('error', 'La base de datos no esta disponible.');
            redirect('/posts');
        }
    }

    private function persist(?int $id = null): void
    {
        if (!verify_csrf()) {
            flash('error', 'La sesion expiro. Intenta nuevamente.');
            redirect($id === null ? '/posts/create' : '/posts/edit?id=' . $id);
        }

        try {
            $pdo = db();
            $templates = new TemplateService($pdo);
            $posts = new PostService($pdo);
            $currentPost = $id === null ? null : $posts->find($id);

            if ($id !== null && $currentPost === null) {
                abort_not_found();
                return;
            }

            $errors = $posts->validate($_POST, $templates);
            $upload = new UploadService();
            $uploadResult = $upload->handle($_FILES['image'] ?? null);
            $libraryImagePath = $upload->resolveLibraryPath($_POST['library_image_path'] ?? null);
            $templateId = valid_id($_POST['template_id'] ?? null);
            $template = $templateId === null ? null : $templates->find($templateId);

            if ($uploadResult['error'] !== null) {
                $errors['image'] = $uploadResult['error'];
            }

            if (
                $template !== null
                && $template['slug'] === 'nueva-funcionalidad'
                && ($uploadResult['path'] ?? null) === null
                && $libraryImagePath === null
                && ($currentPost['image_path'] ?? null) === null
            ) {
                $errors['image'] = 'Esta plantilla requiere una captura o imagen.';
            }

            if ($errors !== []) {
                $this->form($id, $_POST, $errors);
                return;
            }

            $imagePath = $uploadResult['path'] ?? $libraryImagePath;
            $savedPost = $posts->save($_POST, $currentPost, $imagePath);

            if ($imagePath !== null && $currentPost !== null && $imagePath !== ($currentPost['image_path'] ?? null)) {
                $upload->deleteIfUnused($currentPost['image_path'], $posts, (int) $currentPost['id']);
            }

            flash('success', 'Borrador guardado.');
            redirect('/posts/edit?id=' . (int) $savedPost['id']);
        } catch (Throwable) {
            flash('error', 'No fue posible guardar la publicacion.');
            $this->form($id, $_POST, ['general' => 'No fue posible guardar la publicacion.']);
        }
    }
}
