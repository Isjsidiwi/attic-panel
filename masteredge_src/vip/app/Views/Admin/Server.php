<?= $this->extend('Layout/Starter') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-lg-12">
        <?= $this->include('Layout/msgStatus') ?>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">
        <!-- Server Status Card -->
        <div class="col-lg-6">
            <div class="card mb-3" style="background: rgba(20, 20, 20, 0.8); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 20px; backdrop-filter: blur(10px); position: relative; overflow: hidden; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 80px rgba(239, 68, 68, 0.1);">
                <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #ef4444, #dc2626, transparent); animation: borderGlow 3s ease-in-out infinite;"></div>
                <div class="card-header h6 p-3 text-center text-white" style="background: linear-gradient(135deg, #ef4444, #dc2626); border-bottom: 1px solid rgba(239, 68, 68, 0.2); border-radius: 20px 20px 0 0;">
                    <i class="fas fa-server me-2"></i>
                    <strong>CHANGE SERVER STATUS</strong>
                </div>
                <div class="card-body" style="background: transparent; color: #e6eef8;">
                    <?php foreach ($server as $Naman): ?>
                        <li class="list-item d-flex justify-content-between align-items-center text-info">
                            CURRENT SERVER :- 
                            <span class="status-badge">
                                <strong><?php if ($Naman['Online'] == 'false'): ?>ON<?php else: ?>OFF<?php endif; ?></strong>
                            </span>
                        </li>
                        <li class="list-item d-flex justify-content-between align-items-center text-danger">
                            CURRENT NOTICE :- 
                            <span class="status-badge">
                                <strong><?= $Naman['Maintenance'] ?></strong>
                            </span>
                        </li>
                    <?php endforeach; ?>
                    
                    <div class="mt-4">
                        <?= form_open() ?>
                        <input type="hidden" name="online_form" value="1">
                        <div class="row">
                            <div class="form-group col-lg-6 mb-3">
                                <label for="server" class="form-label">SELECT ON/OFF</label>
                                <?php
                                $sel_server = ['' => 'CHOOSE SERVER', 'false' => 'SERVER ON', 'true' => 'SERVER OFF'];
                                echo form_dropdown(['class' => 'form-select', 'name' => 'server', 'id' => 'server'], $sel_server);
                                if ($validation->hasError('server')) : ?>
                                    <small class="text-danger"><?= $validation->getError('server') ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="form-group col-lg-6 mb-3">
                                <label for="value" class="form-label">MAINTENANCE NOTICE</label>
                                <input type="text" name="value" id="value" class="form-control" placeholder="ENTER MAINTENANCE" maxlength="1000">
                                <?php if ($validation->hasError('value')) : ?>
                                    <small class="text-danger"><?= $validation->getError('value') ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-center">
                            <button type="submit" class="custom-btn btn-outline-danger">
                                <i class="fas fa-sync-alt me-2"></i>
                                <strong>UPDATE SERVER</strong>
                            </button>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mod Name Card -->
        <div class="col-lg-6">
            <div class="card mb-3" style="background: rgba(20, 20, 20, 0.8); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px; backdrop-filter: blur(10px); position: relative; overflow: hidden; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 80px rgba(99, 102, 241, 0.1);">
                <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #6366f1, #8b5cf6, transparent); animation: borderGlow 3s ease-in-out infinite;"></div>
                <div class="card-header h6 p-3 text-center text-white" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); border-bottom: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px 20px 0 0;">
                    <i class="fas fa-user-shield me-2"></i>
                    <strong>CHANGE MOD NAME</strong>
                </div>
                <div class="card-body" style="background: transparent; color: #e6eef8;">
                    <?php foreach ($server as $Naman): ?>
                        <li class="list-item d-flex justify-content-between align-items-center text-primary">
                            CURRENT MOD NAME :- 
                            <span class="status-badge">
                                <strong><?= $Naman['ModName'] ?></strong>
                            </span>
                        </li>
                    <?php endforeach; ?>

                    <div class="mt-4">
                        <?= form_open() ?>
                        <input type="hidden" name="name_form" value="1">
                        <div class="row">
                            <div class="form-group col-lg-12 mb-3">
                                <label for="name" class="form-label">ENTER MOD NAME</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="NAMAN SINGH" maxlength="1000" required>
                                <?php if ($validation->hasError('name')) : ?>
                                    <small class="text-danger"><?= $validation->getError('name') ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-center">
                            <button type="submit" class="custom-btn btn-outline-dark">
                                <i class="fas fa-edit me-2"></i>
                                <strong>UPDATE MOD NAME</strong>
                            </button>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mod Status Card -->
        <div class="col-lg-6">
            <div class="card mb-3" style="background: rgba(20, 20, 20, 0.8); border: 1px solid rgba(251, 191, 36, 0.2); border-radius: 20px; backdrop-filter: blur(10px); position: relative; overflow: hidden; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 80px rgba(251, 191, 36, 0.1);">
                <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #fbbf24, #f59e0b, transparent); animation: borderGlow 3s ease-in-out infinite;"></div>
                <div class="card-header h6 p-3 text-center text-white" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); border-bottom: 1px solid rgba(251, 191, 36, 0.2); border-radius: 20px 20px 0 0;">
                    <i class="fas fa-cogs me-2"></i>
                    <strong>UPDATE MOD STATUS</strong>
                </div>
                <div class="card-body" style="background: transparent; color: #e6eef8;">
                    <?php foreach ($server as $Naman): ?>
                        <?php 
                        $status_items = [
                            'Bullet' => 'BULLET STATUS',
                            'Aimbot' => 'AIMBOT STATUS',
                            'Memory' => 'MEMORY STATUS',
                            'SilentAim' => 'SILENT-AIM STATUS',
                            'item' => 'ITEM STATUS',
                            'Setting' => 'SETTING STATUS',
                            'Esp' => 'ESP STATUS'
                        ];
                        
                        foreach ($status_items as $key => $label): ?>
                            <li class="list-item d-flex justify-content-between align-items-center text-info">
                                CURRENT <?= $label ?> :- 
                                <span class="status-badge">
                                    <strong><?php if ($Naman[$key] == 'false'): ?>ON<?php else: ?>OFF<?php endif; ?></strong>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>

                    <div class="mt-4">
                        <?= form_open() ?>
                        <input type="hidden" name="server_form" value="1">
                        <div class="row">
                            <?php 
                            $mod_options = ['false' => 'ON', 'true' => 'OFF'];
                            foreach ($status_items as $key => $label): ?>
                                <div class="form-group col-lg-3 mb-3">
                                    <label for="<?= $key ?>" class="form-label"><?= $label ?></label>
                                    <?= form_dropdown(['class' => 'form-select', 'name' => $key, 'id' => $key], $mod_options) ?>
                                    <?php if ($validation->hasError($key)) : ?>
                                        <small class="text-danger"><?= $validation->getError($key) ?></small>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-group d-flex justify-content-center">
                            <button type="submit" class="custom-btn btn-outline-info">
                                <i class="fas fa-save me-2"></i>
                                <strong>UPDATE MOD SERVER</strong>
                            </button>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating & Status Card -->
        <div class="col-lg-6">
            <div class="card mb-3" style="background: rgba(20, 20, 20, 0.8); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px; backdrop-filter: blur(10px); position: relative; overflow: hidden; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 80px rgba(99, 102, 241, 0.1);">
                <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #6366f1, #8b5cf6, transparent); animation: borderGlow 3s ease-in-out infinite;"></div>
                <div class="card-header h6 p-3 text-center text-white" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); border-bottom: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px 20px 0 0;">
                    <i class="fas fa-comment-alt me-2"></i>
                    <strong>CHANGE FLOATING & STATUS</strong>
                </div>
                <div class="card-body" style="background: transparent; color: #e6eef8;">
                    <?php foreach ($server as $Naman): ?>
                        <li class="list-item d-flex justify-content-between align-items-center text-success">
                            CURRENT STATUS :- 
                            <span class="status-badge">
                                <strong><?= $Naman['status'] ?></strong>
                            </span>
                        </li>
                        <li class="list-item d-flex justify-content-between align-items-center text-danger">
                            CURRENT FLOATING TEXT :- 
                            <span class="status-badge">
                                <strong><?= $Naman['ftext'] ?></strong>
                            </span>
                        </li>
                    <?php endforeach; ?>

                    <div class="mt-4">
                        <?= form_open() ?>
                        <input type="hidden" name="ftext_form" value="1">
                        <div class="row">
                            <div class="form-group col-lg-6 mb-3">
                                <label for="status" class="form-label">SELECT STATUS</label>
                                <?php
                                $sel_status = [
                                    '' => 'CHOOSE STATUS',
                                    'Safe' => 'SAFE',
                                    'Anti-Cheat Is High...!' => 'ANTI CHEAT HIGH'
                                ];
                                echo form_dropdown(['class' => 'form-select', 'name' => 'status', 'id' => 'status'], $sel_status);
                                if ($validation->hasError('status')) : ?>
                                    <small class="text-danger"><?= $validation->getError('status') ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="form-group col-lg-6 mb-3">
                                <label for="ftext" class="form-label">ENTER FLOATING TEXT</label>
                                <input type="text" name="ftext" id="ftext" class="form-control" placeholder="NAMAN SINGH" maxlength="250" required>
                                <?php if ($validation->hasError('ftext')) : ?>
                                    <small class="text-danger"><?= $validation->getError('ftext') ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-center">
                            <button type="submit" class="custom-btn btn-outline-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>UPDATE FLOATING & STATUS</strong>
                            </button>
                        </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    @keyframes borderGlow {
        0%, 100% { opacity: 0.5; }
        50% { opacity: 1; }
    }

    .list-item {
        padding: 12px;
        margin: 8px 0;
        border-radius: 12px;
        background: rgba(99, 102, 241, 0.05);
        list-style: none;
        border: 1px solid rgba(99, 102, 241, 0.2);
        transition: all 0.3s ease;
    }

    .list-item:hover {
        background: rgba(99, 102, 241, 0.1);
        transform: translateX(5px);
    }

    .status-badge {
        padding: 8px 15px;
        border-radius: 10px;
        background: rgba(99, 102, 241, 0.2);
        color: #6366f1;
        border: 1px solid rgba(99, 102, 241, 0.3);
        font-weight: 600;
    }

    .text-info {
        color: #6366f1 !important;
    }

    .text-success {
        color: #22c55e !important;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .text-primary {
        color: #6366f1 !important;
    }

    .form-select, .form-control {
        background: rgba(20, 20, 20, 0.6) !important;
        border: 1px solid rgba(99, 102, 241, 0.3) !important;
        color: #e6eef8 !important;
        border-radius: 12px;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }

    .form-select:focus,
    .form-control:focus {
        background: rgba(20, 20, 20, 0.8) !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25) !important;
    }

    .form-select option {
        background: #1a1a1a;
        color: #e6eef8;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .form-label {
        color: rgba(255, 255, 255, 0.8) !important;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .custom-btn {
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .custom-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .custom-btn:hover::before {
        left: 100%;
    }

    .custom-btn:hover {
        transform: translateY(-2px);
    }

    .btn-outline-danger {
        border-color: rgba(239, 68, 68, 0.5);
        color: #ef4444;
    }

    .btn-outline-danger:hover {
        background: rgba(239, 68, 68, 0.1);
        border-color: #ef4444;
        color: #ef4444;
        box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
    }

    .btn-outline-dark {
        border-color: rgba(99, 102, 241, 0.5);
        color: #6366f1;
    }

    .btn-outline-dark:hover {
        background: rgba(99, 102, 241, 0.1);
        border-color: #6366f1;
        color: #6366f1;
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
    }

    .btn-outline-info {
        border-color: rgba(99, 102, 241, 0.5);
        color: #6366f1;
    }

    .btn-outline-info:hover {
        background: rgba(99, 102, 241, 0.1);
        border-color: #6366f1;
        color: #6366f1;
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
    }

    .btn-outline-success {
        border-color: rgba(34, 197, 94, 0.5);
        color: #22c55e;
    }

    .btn-outline-success:hover {
        background: rgba(34, 197, 94, 0.1);
        border-color: #22c55e;
        color: #22c55e;
        box-shadow: 0 5px 15px rgba(34, 197, 94, 0.3);
    }

    small.text-danger {
        display: block;
        margin-top: 5px;
        font-size: 0.85rem;
    }
</style>
<?= $this->endSection() ?>
