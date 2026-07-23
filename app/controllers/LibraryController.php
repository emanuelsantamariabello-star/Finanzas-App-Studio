<?php

declare(strict_types=1);

final class LibraryController
{
    public function index(): void
    {
        $upload = new UploadService();
        $files = $upload->libraryFiles();
        $databaseAvailable = true;

        try {
            $posts = new PostService(db());

            foreach ($files as $index => $file) {
                $files[$index]['use_count'] = $posts->imageUseCount((string) $file['path']);
            }
        } catch (Throwable) {
            $databaseAvailable = false;

            foreach ($files as $index => $file) {
                $files[$index]['use_count'] = null;
            }
        }

        view('library/index', [
            'title' => 'Biblioteca',
            'files' => $files,
            'databaseAvailable' => $databaseAvailable,
        ]);
    }

    public function store(): void
    {
        if (!verify_csrf()) {
            flash('error', 'La sesion expiro. Intenta nuevamente.');
            redirect('/library');
        }

        $result = (new UploadService())->handle($_FILES['image'] ?? null);

        if ($result['error'] !== null) {
            flash('error', $result['error']);
            redirect('/library');
        }

        if (($result['path'] ?? null) === null) {
            flash('error', 'Selecciona una imagen para cargar.');
            redirect('/library');
        }

        flash('success', 'Imagen agregada a la biblioteca.');
        redirect('/library');
    }

    public function delete(): void
    {
        if (!verify_csrf()) {
            flash('error', 'La sesion expiro. Intenta nuevamente.');
            redirect('/library');
        }

        $upload = new UploadService();
        $path = $_POST['path'] ?? null;
        $resolvedPath = $upload->resolveLibraryPath(is_string($path) ? $path : null);

        if ($resolvedPath === null) {
            flash('error', 'La imagen seleccionada no es valida.');
            redirect('/library');
        }

        try {
            if ((new PostService(db()))->imageUseCount($resolvedPath) > 0) {
                flash('error', 'No se puede eliminar una imagen usada en publicaciones.');
                redirect('/library');
            }
        } catch (Throwable) {
            flash('error', 'No fue posible validar el uso de la imagen.');
            redirect('/library');
        }

        $deleted = $upload->deleteLibraryFile($resolvedPath);
        flash($deleted ? 'success' : 'error', $deleted ? 'Imagen eliminada.' : 'No fue posible eliminar la imagen.');
        redirect('/library');
    }
}
