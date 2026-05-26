<?= $this->extend('Layout/Starter') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="row">
        <div class="col-lg-12">
            <?= $this->include('Layout/msgStatus') ?>
        </div>
        <div class="col-lg-12">
            <div class="card mb-3" style="background: rgba(20, 20, 20, 0.8); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px; backdrop-filter: blur(10px); position: relative; overflow: hidden; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 80px rgba(99, 102, 241, 0.1);">
                <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #6366f1, #8b5cf6, transparent); animation: borderGlow 3s ease-in-out infinite;"></div>
                <div class="card-header h6 p-3 text-center text-white" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); border-bottom: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px 20px 0 0;">
                    <i class="fas fa-wallet me-2"></i><strong>ADD BALANCE</strong>
                </div>
                <div class="card-body" style="background: transparent; color: #e6eef8;">
                    <?= form_open () ?>
                    <div class="row">
                        <div class="form-group col-lg-6 mb-3">
                            <label for="user_id" class="form-label" style="color: rgba(255, 255, 255, 0.8); font-weight: 500;">SELECT USER</label>
                            <?php $select = array(); foreach ($users as $user) { $select[$user->id_users] = $user->username." :- ".$Naman .  $user->saldo; }?>
                            <?= form_dropdown(['class' => 'form-select', 'name' =>'user_id', 'id' =>'user_id'], $select)?>
                            <?php if ($validation->hasError('user_id')) : ?>
                                <small id="help-user_id" class="text-danger"><?= $validation->getError('user_id') ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="form-group col-lg-6 mb-3">
                            <label for="saldo" class="form-label" style="color: rgba(255, 255, 255, 0.8); font-weight: 500;">ENTER BALANCE</label>
                            <div class="input-group">
                                <span class="input-group-text" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #000; font-weight: 700; border: 1px solid rgba(251, 191, 36, 0.3);">
                                    <strong><?= $Naman ?></strong>
                                </span>
                                <input type="number" name="saldo" id="saldo" class="form-control" minlength="1" maxlength="10" value="1000">
                            </div>
                            <?php if ($validation->hasError('saldo')) : ?>
                                <small id="help-saldo" class="text-danger"><?= $validation->getError('saldo') ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group d-flex justify-content-center">
                        <button type="submit" value="update" class="btn btn-primary" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; padding: 12px 35px; border-radius: 12px; font-weight: 600; transition: all 0.3s ease; position: relative; overflow: hidden;">
                            <i class="fas fa-plus-circle me-2"></i><strong>ADD BALANCE</strong>
                        </button>
                    </div>
                    <?= form_close() ?>
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

    .form-select,
    .form-control {
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
        color: #e6eef8 !important;
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25) !important;
    }

    .form-select option {
        background: #1a1a1a;
        color: #e6eef8;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .input-group .form-control {
        border-left: none !important;
    }

    .btn-primary {
        position: relative;
        overflow: hidden;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5) !important;
    }

    .text-danger {
        color: #ef4444 !important;
        font-size: 0.85rem;
        margin-top: 5px;
        display: block;
    }
</style>
<?= $this->endSection() ?>
