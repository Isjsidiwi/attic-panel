<?php  if ($user->level == null) {  exit();  } ?>             

<?= $this->extend('Layout/Starter')?>

<?= $this->section('content')?>
<div class="row justify-content-center">
<div class="row">
    <div class="col-lg-12">
        <?= $this->include('Layout/msgStatus')?>
    </div>
    <div class="col-lg-6">
        <div class="card mb-3" style="background: rgba(20, 20, 20, 0.8); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 20px; backdrop-filter: blur(10px); position: relative; overflow: hidden; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 80px rgba(239, 68, 68, 0.1);">
            <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #ef4444, #dc2626, transparent); animation: borderGlow 3s ease-in-out infinite;"></div>
            <div class="card-header text-white p-3" style="background: linear-gradient(135deg, #ef4444, #dc2626); border-bottom: 1px solid rgba(239, 68, 68, 0.2); border-radius: 20px 20px 0 0;">
                <i class="fas fa-gift me-2"></i>Generate <?= $title?>
            </div>
            <div class="card-body" style="background: transparent; color: #e6eef8;">
                <?= form_open()?>
              <div class="row"><div class="form-group mb-3">
                    <label for="set_level" class="form-label" style="color: rgba(255, 255, 255, 0.8); font-weight: 500;">SET ACCOUNT LEVEL</label>
                    <?= form_dropdown(['class' => 'form-select', 'name' => 'set_level', 'id' => 'set_level'], $set_level, old('set_level')?: '')?>
                    <?php if ($validation->hasError('set_level')) :?>
                        <small id="help-set_level" class="text-danger"><?= $validation->getError('set_level')?></small>
                    <?php endif;?>
                </div>
                <div class="form-group mb-3">
                    <label for="used_limit" style="color: rgba(255, 255, 255, 0.8); font-weight: 500;">SET REFERRAL USED LIMIT </label>
                        <input type="number" class="form-control" name="used_limit" id="used_limit" min="1" max="100" value="1">
                    <?php if ($validation->hasError('used_limit')) :?>
                        <small id="help-used_limit" class="text-danger"><?= $validation->getError('used_limit')?></small>
                    <?php endif;?>
                   </div>

              <div class="form-group mb-3">
                    <label for="set_saldo" style="color: rgba(255, 255, 255, 0.8); font-weight: 500;">ENTER BALANCE</label>
                    <div class="input-group mt-2">
                        <span class="input-group-text" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #000; font-weight: 700; border: 1px solid rgba(251, 191, 36, 0.3);"><strong><?= $Naman?></strong></span>
                        <input type="number" class="form-control" name="set_saldo" id="set_saldo" minlength="1" maxlength="15" value="100">
                    <?php if ($validation->hasError('set_saldo')) :?>
                        <small id="help-set_saldo" class="text-danger"><?= $validation->getError('set_saldo')?></small>
                    <?php endif;?>
                </div>
                   </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-outline-primary">CREATE CODE</button>
                </div>
                <?= form_close()?>
            </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <?php if ($code) :?>
            <div class="card mb-3" style="background: rgba(20, 20, 20, 0.8); border: 1px solid rgba(34, 197, 94, 0.2); border-radius: 20px; backdrop-filter: blur(10px); position: relative; overflow: hidden; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 80px rgba(34, 197, 94, 0.1);">
                <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #22c55e, #16a34a, transparent); animation: borderGlow 3s ease-in-out infinite;"></div>
                <div class="card-header text-white p-3" style="background: linear-gradient(135deg, #22c55e, #16a34a); border-bottom: 1px solid rgba(34, 197, 94, 0.2); border-radius: 20px 20px 0 0;">
                    <i class="fas fa-history me-2"></i>History Generate - Total <?= $total_code?>
                </div>
                <div class="card-body" style="background: transparent; color: #e6eef8;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>REFERRAL HASHED</th>
                                    <th>REFERRAL CODE</th>
                                    <th>LEVEL</th>
                                    <th>BALANCE</th>
                                    <th>TOTAL USED</th>
                                    <th>TOTAL LIMIT</th>
                                    <th>CREATE BY</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($code as $c) :?>
                                    <tr>
                                        <td><?= $c['id_reff']?></td>
                                        <td><?= substr($c['code'], 2,10)?></td>
                                        <td><span class="text-success h5 blur"><strong><?= $c['Ucode']?></strong></span></td>
                      <td><span class="text-primary"><?php if ($c['set_level'] == 2):?> ADMIN<?php else:?>RESELLER<?php endif;?></span></td>
                                        <td> <?= $Naman?>  <?= $c['set_saldo']?></td>
                                        <td><?= $c['used_limit']?: '&mdash;'?></td>
                                        <td><?= $c['max_limit']?: '&mdash;'?></td>
                                        <td><?= $c['created_by']?></td>
                                    </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>
</div>

<?= $this->endSection()?>

<?= $this->section('css')?>
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

    .btn-outline-primary {
        border-color: rgba(99, 102, 241, 0.5);
        color: #6366f1;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background: rgba(99, 102, 241, 0.1);
        border-color: #6366f1;
        color: #6366f1;
        transform: translateY(-2px);
    }

    .table {
        color: #e6eef8 !important;
        background: transparent !important;
    }

    .table thead th {
        font-weight: 600;
        background: rgba(99, 102, 241, 0.1) !important;
        color: #e6eef8 !important;
        border-color: rgba(99, 102, 241, 0.2) !important;
        font-size: 0.85rem;
    }

    .table tbody tr {
        background: rgba(99, 102, 241, 0.02) !important;
        border-color: rgba(99, 102, 241, 0.1) !important;
    }

    .table tbody tr:hover {
        background: rgba(99, 102, 241, 0.08) !important;
    }

    .table tbody td {
        border-color: rgba(99, 102, 241, 0.1) !important;
        color: #e6eef8 !important;
    }

    .text-success {
        color: #22c55e !important;
    }

    .text-primary {
        color: #6366f1 !important;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .blur {
        filter: blur(0px);
        transition: filter 0.5s;
    }

    .blur-in {
        filter: blur(4px);
    }
</style>
<?= $this->endSection()?>

<?= $this->section('js')?>
<script>
  const ucodeTds = document.querySelectorAll('td span.text-success.h5.blur strong');

  ucodeTds.forEach((td) => {
      td.addEventListener('mouseout', () => {
      td.classList.add('blur-in');
    });

    td.addEventListener('mouseover', () => {
      td.classList.remove('blur-in');
    });
 });
</script>
<?= $this->endSection()?>
