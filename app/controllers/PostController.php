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
            $newId = (new PostService(db()))->duplicate($id);
            flash($newId === null ? 'error' : 'success', $newId === null ? 'No se encontro la publicacion.' : 'Publicacion duplicada.');
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

            (new ExportService($pdo))->register($id, (string) $post['format']);
            $posts->markExported($id);
            flash('success', 'Exportacion registrada.');
            redirect('/posts/edit?id=' . $id);
        } catch (Throwable) {
            flash('error', 'No fue posible registrar la exportacion.');
            redirect('/posts');
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
            $uploadResult = (new UploadService())->handle($_FILES['image'] ?? null);
            $templateId = valid_id($_POST['template_id'] ?? null);
            $template = $templateId === null ? null : $templates->find($templateId);

            if ($uploadResult['error'] !== null) {
                $errors['image'] = $uploadResult['error'];
            }

            if (
                $template !== null
                && $template['slug'] === 'nueva-funcionalidad'
                && ($uploadResult['path'] ?? null) === null
                && ($currentPost['image_path'] ?? null) === null
            ) {
                $errors['image'] = 'Esta plantilla requiere una captura o imagen.';
            }

            if ($errors !== []) {
                $this->form($id, $_POST, $errors);
                return;
            }

            $savedPost = $posts->save($_POST, $currentPost, $uploadResult['path']);

            if (($uploadResult['path'] ?? null) !== null && $currentPost !== null) {
                (new UploadService())->deleteIfUnused($currentPost['image_path'], $posts, (int) $currentPost['id']);
            }

            flash('success', 'Borrador guardado.');
            redirect('/posts/edit?id=' . (int) $savedPost['id']);
        } catch (Throwable) {
            flash('error', 'No fue posible guardar la publicacion.');
            $this->form($id, $_POST, ['general' => 'No fue posible guardar la publicacion.']);
        }
    }
}
