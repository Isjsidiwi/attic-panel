<?= $this->extend('Layout/Starter') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="row">
        <div class="col-lg-12">
            <?= $this->include('Layout/msgStatus') ?>
        </div>
        
        
   <div class="col-lg-6">
        <div class="card mb-3">
       <div class="card-header mb-3  h6 p-3 bg-warning text-dark">
                <div class="row">
                    <div class="col pt-1"><strong>CHANGE PRICE</strong>
                    </div>
                    <div class="col text-end">
                       <a class="btn btn-outline-danger btn-sm" href="<?= site_url('keys/generate') ?>"><i class="fa-solid fa-file-plus fa-flip" style="--fa-animation-duration: 4s; color: blue;"> </i></a>
                       <a class="btn btn-outline-danger btn-sm" href="<?= site_url('keys/name-generate') ?>"><i class="fa-solid fa-file-pen fa-flip" style="--fa-animation-duration: 8s; color: blue;"> </i></a>
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
        <div class="card mb-3">
            <div class="card-header h6 p-3 text-center bg-info text-white"> <i class="fa-solid fa-indian-rupee-sign fa-flip" style="--fa-animation-duration: 2s; color: #f5f901;"></i><strong> CHANGE CURRENCY </strong><i class="fa-solid fa-indian-rupee-sign fa-flip" style="--fa-animation-duration: 2s; color: #f5f901;"></i></div>
            <div class="card-body">
                
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

