<?php declare(strict_types=1); ?>
<div class="modal fade app-confirm-modal" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionModalLabel" aria-hidden="true" data-confirm-modal>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <p class="section-kicker mb-1">Confirmacion requerida</p>
                    <h2 class="modal-title h5" id="confirmActionModalLabel" data-confirm-title>Eliminar registro</h2>
                </div>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0 text-muted" data-confirm-message>Esta accion no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger" type="button" data-confirm-submit>Eliminar</button>
            </div>
        </div>
    </div>
</div>
