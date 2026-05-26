<?= $this->extend('Layout/Starter') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="row">
        <div class="col-lg-12">
            <?= $this->include('Layout/msgStatus') ?>
        </div>
        
        
   <div class="col-lg-6">
        <div class="card mb-3" style="background: rgba(20, 20, 20, 0.8); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px; backdrop-filter: blur(10px); position: relative; overflow: hidden; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 80px rgba(99, 102, 241, 0.1);">
            <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #6366f1, #8b5cf6, transparent); animation: borderGlow 3s ease-in-out infinite;"></div>
       <div class="card-header mb-3 h6 p-3 text-white" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); border-bottom: 1px solid rgba(251, 191, 36, 0.2); border-radius: 20px 20px 0 0;">
                <div class="row">
                    <div class="col pt-1"><strong><i class="fas fa-dollar-sign me-2"></i>CHANGE PRICE</strong>
                    </div>
                    <div class="col text-end">
                       <a class="btn btn-outline-light btn-sm" href="<?= site_url('keys/generate') ?>"><i class="fa-solid fa-file-plus fa-flip" style="--fa-animation-duration: 4s;"> </i></a>
                       <a class="btn btn-outline-light btn-sm" href="<?= site_url('keys/name-generate') ?>"><i class="fa-solid fa-file-pen fa-flip" style="--fa-animation-duration: 8s;"> </i></a>
                    </div>
                </div>
         </div>
            <div class="card-body">
                <ul class="list-group list-hover mb-3">
                    <?php foreach ($prices as $Naman): ?>
                <span class="border border-primary">
   <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-info">
                        5 HOURS PRICE  :- 
                        <span class="badge text-danger">
                          <?= $Naman['Currency'] ?> <?= $Naman['Hrs5'] ?>
                        </span>
                    </li>
                    </span>
                  <!--  <div><hr class="list-group-item-divider"></div>-->
                    <span class="border border-danger">
       <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-info">
                        1 DAY PRICE  :-
                        <span class="badge text-danger">
                      <?= $Naman['Currency'] ?>   <?= $Naman['Days1'] ?>
                       </span>
                    </li>
                    </span>
                 <!--    <div> <hr class= "list-group-divider"></div>-->
                     <span class="border border-warning">
     <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-info">
                       7 DAY PRICE   :-
                        <span class="badge text-danger">
                        <?= $Naman['Currency'] ?> <?= $Naman['Days7'] ?>
                        </span>
                    </li>
                    </span>
              <!--       <div><hr class= "list-group-divider"></div>-->
                     <span class="border border-danger">
 <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-info">
                        15 DAY PRICE  :-
                        <span class="badge text-danger">
                          <?= $Naman['Currency'] ?>  <?= $Naman['Days15'] ?>
                        </span>
                    </li>
                    </span>
                  <!--   <div><hr class= "list-group-divider"></div>-->
                     <span class="border border-primary">
<li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-info">
                        30 DAY PRICE  :-
                        <span class="badge text-danger">
                       <?= $Naman['Currency'] ?>   <?= $Naman['Days30'] ?>
                        </span>
                    </li>
                    </span>
                    <div></div>
               <!--      <div><hr class= "list-group-divider"></div>-->
                     <span class="border border-warning">
     <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-info">
                       60 DAY PRICE   :-
                        <span class="badge text-danger">
                       <?= $Naman['Currency'] ?>  <?= $Naman['Days60'] ?>
                        </span>
                    </li>
                    </span>
                       <?php endforeach; ?>
                    </ul>
                <?= form_open () ?>
        <input type="hidden" name="price_form" value="1">
                <div class="row">
                    <div class="form-group col-lg-6 mb-3">
                  <label for="time1" class="form-label">SELECT KEYS</label>      
     <?= form_dropdown(['class' => 'form-select', 'name' => 'time1', 'id' => 'time1'], $time1 ) ?>
             <?php if ($validation->hasError('time1')) : ?>
   <small id="help-time1" class="text-danger"><?= $validation->getError('time1') ?></small>
                        <?php endif; ?>
               </div>
                    <div class="form-group col-lg-6 mb-3">
                    <label for="new_value" class="form-label">ENTER NEW PRICE</label>
                      <?php foreach ($prices as $Naman): ?>
                    <div class="input-group">
                        <span class="input-group-text bg-warning"><strong><?= $Naman['Currency'] ?></strong></span>
                        <input type="number" name="new_value" id="new_value" class="form-control" placeholder="1" value="<?= old('new_value') ?: 1 ?>"min="1" max="30000" required>
                            </div>
                        <?php if ($validation->hasError('new_value')) : ?>
                            <small id="help-new_value" class="text-danger"><?= $validation->getError('new_value') ?></small>
                        <?php endif; ?>
                       </div>
                    <?php endforeach; ?>
            <!--    <div class="form-group">-->
                    <div class="form-group d-flex justify-content-center">
                    <button type="submit" value="update" class="btn btn-outline-danger btn-block"><strong>Update Price</strong></button>
                </div>
                <?= form_close() ?>
   
            </div>
        </div>
    </div>
     </div>
        
            <div class="col-lg-6">
        <div class="card mb-3" style="background: rgba(20, 20, 20, 0.8); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px; backdrop-filter: blur(10px); position: relative; overflow: hidden; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 80px rgba(99, 102, 241, 0.1);">
            <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #6366f1, #8b5cf6, transparent); animation: borderGlow 3s ease-in-out infinite;"></div>
            <div class="card-header h6 p-3 text-center text-white" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); border-bottom: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px 20px 0 0;"> <i class="fa-solid fa-indian-rupee-sign fa-flip" style="--fa-animation-duration: 2s; color: #fbbf24;"></i><strong> CHANGE CURRENCY </strong><i class="fa-solid fa-indian-rupee-sign fa-flip" style="--fa-animation-duration: 2s; color: #fbbf24;"></i></div>
            <div class="card-body" style="background: transparent; color: #e6eef8;">
                
                       <?php foreach ($prices as $Naman): ?>
                <!--  <span class="border border-danger"> -->
<li class="card-group-item card-group-item-action d-flex justify-content-between align-items-center text-danger"> CURRENT CURRENCY  :-  
                       <span class="badge h6 p-2 text-primary">
                           <strong><?= $Naman['Currency'] ?></strong>
                </span>
                    </li>
                 <!--   </span>-->
                 <?php endforeach; ?>
                
                <br></br>
                
                <?= form_open () ?>
        <input type="hidden" name="money_form" value="1">
                <div class="row">
                    <div class="form-group col-lg-12 mb-3">
                  <label for="time2" class="form-label text-warning">SELECT CURRENCY</label>
  <?= form_dropdown(['class' => 'form-select', 'name' => 'time2', 'id' => 'time2'], $time2 ) ?>
             <?php if ($validation->hasError('time2')) : ?>
   <small id="help-time2" class="text-danger"><?= $validation->getError('time2') ?></small>
                     <?php endif; ?>
                 </div>
            <!--    <div class="form-group">-->
                    <div class="form-group d-flex justify-content-center">
                    <button type="submit" value="update" class="btn btn-outline-dark btn-block"><strong>UPDATE CURRENCY</strong></button>
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

    .card-body {
        background: transparent !important;
        color: #e6eef8 !important;
    }

    .list-group {
        background: transparent !important;
    }

    .list-group-item {
        background: rgba(99, 102, 241, 0.05) !important;
        border: 1px solid rgba(99, 102, 241, 0.2) !important;
        margin-bottom: 8px;
        border-radius: 12px !important;
        color: #6366f1 !important;
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        background: rgba(99, 102, 241, 0.1) !important;
        transform: translateX(5px);
        border-color: rgba(99, 102, 241, 0.3) !important;
    }

    .text-info {
        color: #6366f1 !important;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .text-warning {
        color: #fbbf24 !important;
    }

    .text-primary {
        color: #6366f1 !important;
    }

    .badge {
        background: rgba(239, 68, 68, 0.2) !important;
        color: #ef4444 !important;
        padding: 8px 15px;
        font-size: 0.9rem;
        border-radius: 8px;
        border: 1px solid rgba(239, 68, 68, 0.3);
        font-weight: 600;
    }

    .badge.text-primary {
        background: rgba(99, 102, 241, 0.2) !important;
        color: #6366f1 !important;
        border: 1px solid rgba(99, 102, 241, 0.3);
    }

    .border-primary {
        border-color: rgba(99, 102, 241, 0.5) !important;
    }

    .border-danger {
        border-color: rgba(239, 68, 68, 0.5) !important;
    }

    .border-warning {
        border-color: rgba(251, 191, 36, 0.5) !important;
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

    .form-label {
        color: rgba(255, 255, 255, 0.8) !important;
        font-weight: 500;
    }

    .input-group-text {
        background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
        color: #000 !important;
        font-weight: 700;
        border: 1px solid rgba(251, 191, 36, 0.3) !important;
    }

    .btn-outline-danger {
        border-color: rgba(239, 68, 68, 0.5);
        color: #ef4444;
        background: transparent;
        transition: all 0.3s ease;
        font-weight: 600;
        position: relative;
        overflow: hidden;
    }

    .btn-outline-danger::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-outline-danger:hover::before {
        left: 100%;
    }

    .btn-outline-danger:hover {
        background: rgba(239, 68, 68, 0.1);
        border-color: #ef4444;
        color: #ef4444;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
    }

    .btn-outline-dark {
        border-color: rgba(99, 102, 241, 0.5);
        color: #6366f1;
        background: transparent;
        transition: all 0.3s ease;
        font-weight: 600;
        position: relative;
        overflow: hidden;
    }

    .btn-outline-dark::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn-outline-dark:hover::before {
        left: 100%;
    }

    .btn-outline-dark:hover {
        background: rgba(99, 102, 241, 0.1);
        border-color: #6366f1;
        color: #6366f1;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
    }

    .btn-outline-light {
        border-color: rgba(255, 255, 255, 0.3);
        color: white;
        transition: all 0.3s ease;
    }

    .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: white;
        color: white;
    }

    .card-group-item {
        background: rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.2);
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
        list-style: none;
    }

    .card-group-item:hover {
        background: rgba(99, 102, 241, 0.1);
        transform: translateX(5px);
    }

    small.text-danger {
        display: block;
        margin-top: 5px;
        font-size: 0.85rem;
    }
</style>
<?= $this->endSection() ?>

