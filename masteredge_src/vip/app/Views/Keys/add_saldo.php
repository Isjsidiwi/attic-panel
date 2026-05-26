<?= $this->extend('Layout/Starter') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="row">
        <div class="col-lg-12">
            <?= $this->include('Layout/msgStatus') ?>
        </div>
                  <div class="col-lg-12">
        <div class="card mb-3">
            <div class="card-header h6 p-3 text-center bg-info text-white"><strong> ADD BALANCE  </strong></div>
            <div class="card-body">
                <?= form_open () ?>
                <div class="row">
                    <div class="form-group col-lg-6 mb-3">
                  <label for="user_id" class="form-label">SELECT USER</label>
                 
 <?php $select = array(); foreach ($users as $user) { $select[$user->id_users] = $user->username." :- ".$Naman .  $user->saldo; }?>
             <?= form_dropdown(['class' => 'form-select', 'name' =>'user_id', 'id' =>'user_id'], $select)?>
                             <?php if ($validation->hasError('user_id')) : ?>
            <small id="help-user_id" class="text-danger"><?= $validation->getError('user_id') ?></small>
                        <?php endif; ?>
                        </div>
                    <div class="form-group col-lg-6 mb-3">
                    <label for="saldo" class="form-label">ENTER BALANCE</label>
                        <input type="number" name="saldo" id="saldo" class="form-control" minlength="1" maxlength="10" value="1000">
                         <?php if ($validation->hasError('saldo')) : ?>
                            <small id="help-saldo" class="text-danger"><?= $validation->getError('saldo') ?></small>
                        <?php endif; ?>
                          </div>
                    </div>
                    <div class="form-group d-flex justify-content-center">
                    <button type="submit" value="update" class="btn btn-outline-danger"><strong> ADD BALANCE</strong></button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
   
     </div>
        </div>
<?= $this->endSection() ?>

