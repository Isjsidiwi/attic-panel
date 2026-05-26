<?php
?>
<?= $this->extend('Layout/Starter') ?>
<?= $this->section('content') ?>

<style>
/* Dark Theme File Card */
.file-card {
    background: rgba(20, 20, 20, 0.8);
    border-radius: 20px;
    border: 1px solid rgba(99, 102, 241, 0.2);
    box-shadow: 
        0 8px 32px 0 rgba(0, 0, 0, 0.37),
        0 0 80px rgba(99, 102, 241, 0.1);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    margin-bottom: 30px;
}

.file-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, 
        transparent,
        #6366f1,
        #8b5cf6,
        transparent
    );
    animation: borderGlow 3s ease-in-out infinite;
}

@keyframes borderGlow {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

.file-card:hover {
    transform: translateY(-5px);
    box-shadow: 
        0 15px 40px rgba(0, 0, 0, 0.5),
        0 0 100px rgba(99, 102, 241, 0.2);
}

.file-header {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    padding: 20px;
    border-radius: 20px 20px 0 0;
    font-weight: 600;
    letter-spacing: 1px;
}

.card-body {
    color: #e6eef8;
}

.list-group-item {
    background: rgba(99, 102, 241, 0.05) !important;
    border: 1px solid rgba(99, 102, 241, 0.2) !important;
    border-left: 4px solid #6366f1 !important;
    margin-bottom: 8px;
    border-radius: 12px !important;
    color: rgba(255, 255, 255, 0.9) !important;
    transition: all 0.3s ease;
}

.list-group-item:hover {
    background: rgba(99, 102, 241, 0.1) !important;
    transform: translateX(5px);
    border-color: rgba(99, 102, 241, 0.3) !important;
}

.text-danger {
    color: #ef4444 !important;
}

.text-muted {
    color: rgba(255, 255, 255, 0.5) !important;
}

.badge {
    background: rgba(99, 102, 241, 0.2) !important;
    color: #6366f1 !important;
    padding: 8px 15px;
    font-size: 0.9rem;
    border-radius: 8px;
    border: 1px solid rgba(99, 102, 241, 0.3);
}

.text-info {
    color: #6366f1 !important;
}

.download-btn {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white !important;
    border: none;
    padding: 12px 25px;
    border-radius: 12px;
    transition: all 0.3s ease;
    font-weight: 600;
    position: relative;
    overflow: hidden;
}

.download-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.download-btn:hover::before {
    left: 100%;
}

.download-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5);
}

.upload-section {
    background: rgba(20, 20, 20, 0.8);
    border-radius: 20px;
    border: 1px solid rgba(99, 102, 241, 0.2);
    box-shadow: 
        0 8px 32px 0 rgba(0, 0, 0, 0.37),
        0 0 80px rgba(99, 102, 241, 0.1);
    backdrop-filter: blur(10px);
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
}

.upload-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, 
        transparent,
        #fbbf24,
        #f59e0b,
        transparent
    );
    animation: borderGlow 3s ease-in-out infinite;
}

.upload-section .card-header {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #000;
    padding: 20px;
    border-radius: 20px 20px 0 0;
    font-weight: 700;
}

.upload-btn {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #000 !important;
    border: none;
    padding: 12px 25px;
    border-radius: 12px;
    transition: all 0.3s ease;
    font-weight: 700;
    position: relative;
    overflow: hidden;
}

.upload-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.upload-btn:hover::before {
    left: 100%;
}

.upload-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(251, 191, 36, 0.5);
}

.form-control {
    border-radius: 12px;
    padding: 12px 15px;
    border: 1px solid rgba(99, 102, 241, 0.3);
    background: rgba(20, 20, 20, 0.6);
    color: #fff;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #fbbf24;
    background: rgba(20, 20, 20, 0.8);
    color: #fff;
    box-shadow: 0 0 0 0.2rem rgba(251, 191, 36, 0.25);
}

.form-group label {
    color: #fbbf24;
    font-weight: 600;
    margin-bottom: 10px;
}

.text-warning {
    color: #fbbf24 !important;
}

.text-light {
    color: rgba(255, 255, 255, 0.7) !important;
}
</style>

<div class="row">
    <div class="col-lg-12">
        <?= $this->include('Layout/msgStatus') ?>
    </div>
</div>
    
<div class="col-lg-6">
    <div class="card mb-3 file-card">
        <div class="card-header h4 text-center text-white file-header">
            <i class="fas fa-folder-open me-2"></i>
            UPLOADED LIB 
        </div>
        <div class="card-body">
            <?php if (empty($files)): ?>
                <p class="text-center text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i><br>
                    No files uploaded yet.
                </p>
            <?php else: ?>
                <ul class="list-group list-hover mb-3">
                    <?php foreach ($files as $file): ?>
                        <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-danger">
                            <i class="fas fa-file me-2"></i>
                            FILE NAME :- 
                            <span class="badge text-info">
                                <?= $file['name'] ?>
                            </span>
                        </li>
                        <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-danger">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            FILE PATH :-
                            <span class="badge text-info">
                                <?= $file['path'] ?>
                            </span>
                        </li>
                        <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-danger">
                            <i class="fas fa-clock me-2"></i>
                            UPDATE TIME :-
                            <span class="badge text-info">
                                <?= $file['updated_at'] ?>
                            </span>
                        </li>
                        <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-danger">
                            <i class="fas fa-calendar-plus me-2"></i>
                            CREATE TIME :-
                            <span class="badge text-info">
                                <?= $file['created_at'] ?>
                            </span>
                        </li>
                        <div class="form-group my-3 text-center">
                            <a href="<?= base_url('file/' . $file['name']) ?>" class="btn download-btn" role="button">
                                <i class="fas fa-download me-2"></i> DOWNLOAD LIB
                            </a>
                        </div>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
    
<div class="col-lg-6">
    <div class="card mb-3 upload-section">
        <div class="card-header h4 text-center">
            <i class="fas fa-cloud-upload-alt me-2"></i>
            <strong>UPDATE LIB FILES</strong>
        </div>
        <div class="card-body">
            <?= form_open('/Files', ['enctype'=>'multipart/form-data']); ?>
            <input type="hidden" name="files_form" value="1">
            <div class="form-group mb-3 text-warning">
                <label for="image" class="mb-2">
                    <i class="fas fa-file-upload me-2"></i>CHOOSE LIB
                </label>
                <input type="file" name="file" id="file" class="form-control" aria-describedby="help-file" required> 
                <?php if ($validation->hasError('file')) : ?>
                    <small id="help-file" class="text-light">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        <?= $validation->getError('file') ?>
                    </small>
                <?php endif; ?>
            </div>
            <div class="form-group my-3 text-center">
                <button type="submit" class="btn upload-btn">
                    <i class="fas fa-cloud-upload-alt me-2"></i> UPLOAD LIB
                </button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
