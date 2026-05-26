<style>
    .alert {
        border-radius: 12px;
        border: 1px solid;
        backdrop-filter: blur(10px);
        animation: slideIn 0.5s ease-out;
        margin-bottom: 1rem;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        border-color: rgba(239, 68, 68, 0.3);
        color: #ef4444;
    }

    .alert-success {
        background: rgba(34, 197, 94, 0.1);
        border-color: rgba(34, 197, 94, 0.3);
        color: #22c55e;
    }

    .alert-warning {
        background: rgba(251, 191, 36, 0.1);
        border-color: rgba(251, 191, 36, 0.3);
        color: #fbbf24;
    }

    .alert-secondary {
        background: rgba(99, 102, 241, 0.1);
        border-color: rgba(99, 102, 241, 0.3);
        color: #6366f1;
    }

    .alert .text-info {
        color: #6366f1 !important;
    }

    .btn-close {
        filter: invert(1);
        opacity: 0.7;
    }

    .btn-close:hover {
        opacity: 1;
    }
</style>

<?php if (session()->getFlashdata('msgDanger')) : ?>
    <div class="alert alert-danger fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session()->getFlashdata('msgDanger') ?>
    </div>
<?php elseif (session()->getFlashdata('msgSuccess')) : ?>
    <div class="alert alert-success fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('msgSuccess') ?>
    </div>
<?php elseif (session()->getFlashdata('msgWarning')) : ?>
    <div class="alert alert-warning fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?= session()->getFlashdata('msgWarning') ?>
    </div>
<?php else : ?>
    <?php if (session()->has('userid')) : ?>
        <?php if (isset($messages)) : ?>
            <div class="alert alert-<?= $messages[1] ?> fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <?= $messages[0] ?>
            </div>
        <?php else : ?>
            <div class="alert alert-secondary text-info fade show" role="alert">
                <i class="fas fa-user-circle me-2"></i>
                Welcome <?= getName($user) ?>
            </div>
        <?php endif; ?>
    <?php else : ?>
        <div class="alert alert-secondary text-info alert-dismissible fade show" role="alert">
            <i class="fas fa-hand-wave me-2"></i>
            Welcome Stranger
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>
