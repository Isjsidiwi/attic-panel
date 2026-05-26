<?= $this->extend('Layout/Starter') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="row">
        <div class="col-lg-12">
            <?= $this->include('Layout/msgStatus') ?>
        </div>
         
        <div class="col-lg-6">
            <div class="card mb-3" style="background: rgba(20, 20, 20, 0.8); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 20px; backdrop-filter: blur(10px); position: relative; overflow: hidden; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 80px rgba(239, 68, 68, 0.1);">
                <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #ef4444, #dc2626, transparent); animation: borderGlow 3s ease-in-out infinite;"></div>
                <div class="card-header h6 p-3 text-center text-white" style="background: linear-gradient(135deg, #ef4444, #dc2626); border-bottom: 1px solid rgba(239, 68, 68, 0.2); border-radius: 20px 20px 0 0;">
                    <i class="fa-solid fa-trash-can-xmark fa-flip" style="--fa-animation-duration: 2s; color: #fbbf24;"></i> 
                    DELETE ALL REFERRAL 
                    <i class="fa-solid fa-trash-can-xmark fa-flip" style="--fa-animation-duration: 2s; color: #fbbf24;"></i>
                </div>
                <div class="card-body">
                    <div class="card-group-item card-group-item-action d-flex justify-content-between align-items-center text-danger">
                        TOTAL REFERRAL :- 
                        <span class="badge h6 p-2 text-dark">
                            <strong><?= $total_code ?></strong>
                        </span>
                    </div>
                    <?= form_open(site_url('/admin/Clear-C')) ?>
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-dark">DELETE ALL REFERRAL</button>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    
        <div class="col-lg-6">
            <div class="card mb-3" style="background: rgba(20, 20, 20, 0.8); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px; backdrop-filter: blur(10px); position: relative; overflow: hidden; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 80px rgba(99, 102, 241, 0.1);">
                <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #6366f1, #8b5cf6, transparent); animation: borderGlow 3s ease-in-out infinite;"></div>
                <div class="card-header h6 p-3 text-center text-white" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); border-bottom: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px 20px 0 0;">
                    <i class="fa-solid fa-broom-wide fa-flip" style="--fa-animation-duration: 5s; color: #fbbf24;"></i> 
                    CLEAR HISTORY 
                    <i class="fa-solid fa-broom-wide fa-flip" style="--fa-animation-duration: 5s; color: #fbbf24;"></i>
                </div>
                <div class="card-body" style="background: transparent; color: #e6eef8;">
                    <div class="card-group-item card-group-item-action d-flex justify-content-between align-items-center text-warning">
                        TOTAL HISTORY :- 
                        <span class="badge h6 p-2 text-dark">
                            <strong><?= $total_his ?></strong>
                        </span>
                    </div>
                    <?= form_open(site_url('/admin/Clear-H')) ?>
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-danger">CLEAR ALL HISTORY</button>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    
        <div class="col-lg-6">
            <div class="card mb-3" style="background: rgba(20, 20, 20, 0.8); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 20px; backdrop-filter: blur(10px); position: relative; overflow: hidden; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 80px rgba(148, 163, 184, 0.1);">
                <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #94a3b8, #64748b, transparent); animation: borderGlow 3s ease-in-out infinite;"></div>
                <div class="card-header h6 p-3 text-center text-white" style="background: linear-gradient(135deg, #64748b, #475569); border-bottom: 1px solid rgba(148, 163, 184, 0.2); border-radius: 20px 20px 0 0;">
                    <i class="fa-solid fa-trash-can-list fa-flip" style="--fa-animation-duration: 8s; color: #22c55e;"></i> 
                    DELETE KEYS 
                    <i class="fa-solid fa-trash-can-list fa-flip" style="--fa-animation-duration: 8s; color: #22c55e;"></i>
                </div>
                <div class="card-body" style="background: transparent; color: #e6eef8;">
                    <div class="card-group-item card-group-item-action d-flex justify-content-between align-items-center" style="color: #e6eef8;">
                        TOTAL KEYS :-
                        <span class="badge h6 p-2 text-danger">
                            <strong><?= $total_keys ?></strong>
                        </span>
                    </div>
                    <?= form_open('') ?>
                        <div class="form-group col-md-12 mb-3">
                            <label for="menu" class="form-label" style="color: rgba(255, 255, 255, 0.8); font-weight: 500;">SELECT KEYS</label>
                            <?php
                            $sel_server = [
                                '' => 'CHOOSE ONE',
                                site_url('/admin/Clear-K') => 'DELETE ALL KEYS',
                                site_url('/keys/Unused') => 'DELETE UN-USED KEYS',
                                site_url('/keys/Expired') => 'DELETE EXPIRED KEYS',
                            ];
                            echo form_dropdown('menu', $sel_server, '', ['class' => 'form-select', 'id' => 'menu']);
                            ?>
                        </div>
                        <div class="form-group d-flex justify-content-center">
                            <button type="button" onclick="gotosite()" class="btn btn-outline-info">DELETE KEYS</button>
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

    .card-group-item {
        background: rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.2);
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .card-group-item:hover {
        background: rgba(99, 102, 241, 0.1);
        transform: translateX(5px);
    }

    .badge {
        background: rgba(239, 68, 68, 0.2) !important;
        color: #ef4444 !important;
        padding: 10px 15px;
        border-radius: 10px;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .text-warning {
        color: #fbbf24 !important;
    }

    .form-select {
        background: rgba(20, 20, 20, 0.6) !important;
        border: 1px solid rgba(99, 102, 241, 0.3) !important;
        color: #e6eef8 !important;
        border-radius: 12px;
        padding: 12px 15px;
    }

    .form-select:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25) !important;
    }

    .form-select option {
        background: #1a1a1a;
        color: #e6eef8;
    }

    .btn-outline-dark {
        border-color: rgba(239, 68, 68, 0.5);
        color: #ef4444;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-dark:hover {
        background: rgba(239, 68, 68, 0.1);
        border-color: #ef4444;
        color: #ef4444;
        transform: translateY(-2px);
    }

    .btn-outline-danger {
        border-color: rgba(239, 68, 68, 0.5);
        color: #ef4444;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-danger:hover {
        background: rgba(239, 68, 68, 0.1);
        border-color: #ef4444;
        color: #ef4444;
        transform: translateY(-2px);
    }

    .btn-outline-info {
        border-color: rgba(99, 102, 241, 0.5);
        color: #6366f1;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-info:hover {
        background: rgba(99, 102, 241, 0.1);
        border-color: #6366f1;
        color: #6366f1;
        transform: translateY(-2px);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
function gotosite() {
    window.location = document.getElementById("menu").value;
}
</script>
<?= $this->endSection() ?>