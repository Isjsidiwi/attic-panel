<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>

<style>
    /* Mobile App Style UI */
    :root {
        --primary: #4A90E2;
        --secondary: #F7F9FC;
        --success: #2ECC71;
        --danger: #E74C3C;
        --dark: #34495E;
        --light: #ECF0F1;
    }

    body {
        background: #f0f2f5;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell;
    }

    /* Mobile Container */
    .mobile-container {
        max-width: 480px;
        margin: 0 auto;
        padding: 16px;
        background: #fff;
        min-height: 100vh;
    }

    /* App Header */
    .app-header {
        display: flex;
        align-items: center;
        padding: 20px 15px;
        background: var(--primary);
        border-radius: 20px;
        margin-bottom: 20px;
        color: white;
        box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
    }

    .app-header h1 {
        font-size: 1.5rem;
        margin: 0;
        font-weight: 600;
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 20px;
        position: relative;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        color: var(--dark);
        font-weight: 500;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 15px;
        border: 2px solid #E8ECF2;
        border-radius: 15px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: var(--secondary);
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }

    /* Custom Select */
    .mobile-select {
        position: relative;
        background: var(--secondary);
        border-radius: 15px;
        border: 2px solid #E8ECF2;
    }

    .mobile-select select {
        width: 100%;
        padding: 15px;
        border: none;
        background: transparent;
        appearance: none;
        font-size: 1rem;
        color: var(--dark);
    }

    .mobile-select::after {
        content: '▼';
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary);
        pointer-events: none;
    }

    /* Error Messages */
    .error-msg {
        color: var(--danger);
        font-size: 0.8rem;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Action Buttons */
    .action-btn {
        width: 100%;
        padding: 16px;
        border: none;
        border-radius: 15px;
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: #357ABD;
        transform: translateY(-2px);
    }

    .btn-back {
        background: var(--light);
        color: var(--dark);
    }

    /* Card Sections */
    .info-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-active {
        background: rgba(46, 204, 113, 0.1);
        color: var(--success);
    }

    .status-banned {
        background: rgba(231, 76, 60, 0.1);
        color: var(--danger);
    }

    /* Animations */
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .info-card {
        animation: slideUp 0.3s ease-out forwards;
    }
</style>

<div class="mobile-container">
    <div class="app-header">
        <h1><i class="bi bi-person-circle me-2"></i> Edit Account</h1>
    </div>

    <?= $this->include('Layout/msgStatus') ?>

    <?= form_open() ?>
    <input type="hidden" name="user_id" value="<?= $target->id_users ?>">

    <div class="info-card">
        <div class="form-group">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" 
                   value="<?= old('username') ?: $target->username ?>">
            <?php if ($validation->hasError('username')) : ?>
                <div class="error-msg">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= $validation->getError('username') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="fullname" class="form-control" 
                   value="<?= old('fullname') ?: $target->fullname ?>">
            <?php if ($validation->hasError('fullname')) : ?>
                <div class="error-msg">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= $validation->getError('fullname') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="info-card">
        <div class="form-group">
            <label class="form-label">Role</label>
            <div class="mobile-select">
                <?php $sel_level = ['' => 'Select Role', '2' => 'Admin', '3' => 'Reseller']; ?>
                <?= form_dropdown('level', $sel_level, $target->level) ?>
            </div>
            <?php if ($validation->hasError('level')) : ?>
                <div class="error-msg">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= $validation->getError('level') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label">Status</label>
            <div class="mobile-select">
                <?php $sel_status = [
                    '' => 'Select Status',
                    '1' => 'Active',
                    '0' => 'Banned/Block'
                ]; ?>
                <?= form_dropdown('status', $sel_status, $target->status) ?>
            </div>
            <?php if ($validation->hasError('status')) : ?>
                <div class="error-msg">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= $validation->getError('status') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="info-card">
        <div class="form-group">
            <label class="form-label">Balance</label>
            <input type="number" name="saldo" class="form-control" 
                   value="<?= old('saldo') ?: $target->saldo ?>">
            <?php if ($validation->hasError('saldo')) : ?>
                <div class="error-msg">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= $validation->getError('saldo') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label">Uplink</label>
            <input type="text" name="uplink" class="form-control" disabled 
                   value="<?= old('uplink') ?: $target->uplink ?>">
        </div>
    </div>

    <button type="submit" class="action-btn btn-primary">
        <i class="bi bi-check2-circle"></i>
        Update Account
    </button>

    <a href="<?= site_url('admin/manage-users') ?>" class="action-btn btn-back">
        <i class="bi bi-arrow-left"></i>
        Back to Users
    </a>

    <?= form_close() ?>
</div>

<?= $this->endSection() ?>