<style>
    .custom-alert {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 16px 20px;
        margin-bottom: 20px;
        color: #fff;
        position: relative;
        overflow: hidden;
        animation: slideIn 0.5s ease-out;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
    }

    .custom-alert::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, var(--primary-color), var(--accent-color));
    }

    .custom-alert.danger::before {
        background: linear-gradient(to bottom, #ef4444, #dc2626);
    }

    .custom-alert.success::before {
        background: linear-gradient(to bottom, #10b981, #059669);
    }

    .custom-alert.warning::before {
        background: linear-gradient(to bottom, #f59e0b, #d97706);
    }

    .custom-alert.secondary::before {
        background: linear-gradient(to bottom, #6b7280, #4b5563);
    }

    .custom-alert .alert-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .custom-alert .alert-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .custom-alert .alert-message {
        flex-grow: 1;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .custom-alert .close-btn {
        background: none;
        border: none;
        color: rgba(255, 255, 255, 0.7);
        cursor: pointer;
        padding: 4px;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .custom-alert .close-btn:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        transform: rotate(90deg);
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

    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }

    .custom-alert.fade-out {
        animation: fadeOut 0.5s ease-out forwards;
    }
</style>

<?php if (session()->getFlashdata('msgDanger')) : ?>
    <div class="custom-alert danger" role="alert">
        <div class="alert-content">
            <div class="alert-icon">⚠️</div>
            <div class="alert-message"><?= session()->getFlashdata('msgDanger') ?></div>
            <button type="button" class="close-btn" onclick="this.parentElement.parentElement.classList.add('fade-out')">×</button>
        </div>
    </div>
<?php elseif (session()->getFlashdata('msgSuccess')) : ?>
    <div class="custom-alert success" role="alert">
        <div class="alert-content">
            <div class="alert-icon">✅</div>
            <div class="alert-message"><?= session()->getFlashdata('msgSuccess') ?></div>
            <button type="button" class="close-btn" onclick="this.parentElement.parentElement.classList.add('fade-out')">×</button>
        </div>
    </div>
<?php elseif (session()->getFlashdata('msgWarning')) : ?>
    <div class="custom-alert warning" role="alert">
        <div class="alert-content">
            <div class="alert-icon">⚠️</div>
            <div class="alert-message"><?= session()->getFlashdata('msgWarning') ?></div>
            <button type="button" class="close-btn" onclick="this.parentElement.parentElement.classList.add('fade-out')">×</button>
        </div>
    </div>
<?php else : ?>
    <?php if (session()->has('userid')) : ?>
        <?php if (isset($messages)) : ?>
            <div class="custom-alert <?= $messages[1] ?>" role="alert">
                <div class="alert-content">
                    <div class="alert-icon">
                        <?php
                        switch($messages[1]) {
                            case 'success':
                                echo '✅';
                                break;
                            case 'danger':
                                echo '⚠️';
                                break;
                            case 'warning':
                                echo '⚠️';
                                break;
                            default:
                                echo 'ℹ️';
                        }
                        ?>
                    </div>
                    <div class="alert-message"><?= $messages[0] ?></div>
                    <button type="button" class="close-btn" onclick="this.parentElement.parentElement.classList.add('fade-out')">×</button>
                </div>
            </div>
        <?php else : ?>
            <div class="custom-alert secondary" role="alert">
                <div class="alert-content">
                    <div class="alert-icon">👋</div>
                    <div class="alert-message">Welcome <?= getName($user) ?></div>
                    <button type="button" class="close-btn" onclick="this.parentElement.parentElement.classList.add('fade-out')">×</button>
                </div>
            </div>
        <?php endif; ?>
    <?php else : ?>
        <?php // <div class="alert alert-danger alert-dismissible fade show" role="alert"> ?>
            <?php // <strong>DM :</strong> ?> 
            <?php // <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> ?>
        <?php // </div> ?>
    <?php endif; ?>
<?php endif; ?>

<script>
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.custom-alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.classList.add('fade-out');
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 5000);
        });
    });
</script>