<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <?= $this->include('Layout/msgStatus') ?>
        <?php if (session()->getFlashdata('user_key')) : ?>
            <div class="alert alert-success" role="alert">
                Game : <?= session()->getFlashdata('game') ?> / <?= session()->getFlashdata('duration') ?> Hours<br>
                License :- 
                 <?php $keys = session()->getFlashdata('user_key');?>
        <strong class="key-sensi">
            <?php foreach ($keys as $key) :?>
                <?= $key?><br>
            <?php endforeach;?>
        </strong>
        <button class="btn btn-sm btn-outline-warning copy-btn" data-clipboard-text="<?php foreach ($keys as $key) :?><?= $key?><?= "\n"?><?php endforeach;?>"><i class="fa-solid fa-clipboard" style="color: #1100ff;"></i></button>
        <script>
            var keys = [<?php foreach ($keys as $key) :?>'<?= $key?>',<?php endforeach;?>];
        </script>
        <button class="btn btn-sm btn-outline-warning download-btn" onclick="downloadKeys('keys.txt')"><i class="fa-solid fa-file-arrow-down" style="color: #1100ff;"></i></button><br>
       
                Available for <?= session()->getFlashdata('max_devices') ?> Devices<br>
                <small>
                    <i>Duration will start when license login.</i><br>
                    <i class="bi bi-wallet"></i> Balance Reduce :
                    <span class="text-danger">-<?= session()->getFlashdata('fees') ?></span>
                    (Total left <?= $NAMAN ?> <?= $user->saldo ?>)
                </small>
            </div>
        <?php endif; ?>
        <div class="card" style="background: rgba(20, 20, 20, 0.8); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px; backdrop-filter: blur(10px); position: relative; overflow: hidden; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 80px rgba(99, 102, 241, 0.1);">
            <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #6366f1, #8b5cf6, transparent); animation: borderGlow 3s ease-in-out infinite;"></div>
            <div class="card-header mb-3 p3 text-white" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); border-bottom: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px 20px 0 0;">
                <div class="row">
                    <div class="col pt-1">
                        <i class="fas fa-key me-2"></i>CREATE LICENSE
                    </div>
                    <div class="col text-end">
                        <a class="btn btn-sm btn-outline-light" href="<?= site_url('keys') ?>"><i class="fa-solid fa-file-lines fa-fade" style="--fa-animation-duration: 5s; color: #11ff00;"></i> KEYS</a>
                            <a class="btn btn-outline-light btn-sm" href="<?= site_url('keys/name-generate') ?>"><i class="fa-solid fa-file-pen fa-flip" style="--fa-animation-duration: 8s; color: #00ffbb;"> </i></a>
                        <a class="btn btn-sm btn-outline-light" href="<?= site_url('Price') ?>"><i class="fa-solid fa-money-check-dollar-pen fa-flip" style="--fa-animation-duration: 3s; color: #eeff00;"></i> </a>
                    </div>
                </div>
            </div>
            <div class="card-body" style="background: transparent; color: #e6eef8;">
                <?= form_open() ?>

                <div class="row">
                    <div class="form-group col-lg-6 mb-3">
                        <label for="game" class="form-label" style="color: rgba(255, 255, 255, 0.8); font-weight: 500;">Games</label>
                        <?= form_dropdown(['class' => 'form-select', 'name' => 'game', 'id' => 'game'], $game, old('game') ?: '') ?>
                        <?php if ($validation->hasError('game')) : ?>
                            <small id="help-game" class="text-danger"><?= $validation->getError('game') ?></small>
                        <?php endif; ?>
                    </div>
       
        <div class="form-group col-lg-6 mb-3">
                    <label for="duration" class="form-label" style="color: rgba(255, 255, 255, 0.8); font-weight: 500;">Duration Of Key</label>
                    <?= form_dropdown(['class' => 'form-select', 'name' => 'duration', 'id' => 'duration'], $duration, old('duration') ?: '') ?>
                    <?php if ($validation->hasError('duration')) : ?>
                        <small id="help-duration" class="text-danger"><?= $validation->getError('duration') ?></small>
                    <?php endif; ?>
                        </div>    
                        
                    <div class="form-group col-lg-6 mb-3">
                   <label for="max_devices" class="form-label" style="color: rgba(255, 255, 255, 0.8); font-weight: 500;">Max Devices</label>
                   <input type="number" name="max_devices" id="max_devices" class="form-control" placeholder="1" value="<?= old('max_devices') ?: 1 ?>" min="1" max="100" required>
                        <?php if ($validation->hasError('game')) : ?>
                            <small id="help-max_devices" class="text-danger"><?= $validation->getError('max_devices') ?></small>
               <?php endif; ?>
                    </div>
               
                          <?php if (($user->level == 1) || ($user->level ==2)) { ?>
            <div class="form-group col-lg-6 mb-3">
                       <label for="bulk" class="form-label" style="color: rgba(255, 255, 255, 0.8); font-weight: 500;">ENTER BULK KEY NUMBER</label>
                      <input type="number" name="bulk" id="bulk" class="form-control" placeholder="1" value="<?= old('bulk') ?: 1 ?>" min="1" max="100" required>
                      <?php if ($validation->hasError('bulk')) : ?>
                 <small id="help-bulk" class="text-danger"><?= $validation->getError('bulk') ?></small>
                       <?php endif; ?>
                        </div>
             <?php } else { ?>
                   <div class="form-group col-lg-6 mb-3">
               <label for="bulk" class="form-label" style="color: rgba(255, 255, 255, 0.8); font-weight: 500;">ENTER BULK KEY NUMBER</label>
              <input type="number" name="bulk" id="bulk" class="form-control" placeholder="1" value="<?= old('bulk') ?: 1 ?>" min="1" max="10" required>
               <?php if ($validation->hasError('bulk')) : ?>
 <small id="help-bulk" class="text-danger"><?= $validation->getError('bulk') ?></small>
               <?php endif; ?>
                    </div>
                    <?php } ?>
                <div class="form-group mb-3">
                    <label for="estimation" class="form-label" style="color: rgba(255, 255, 255, 0.8); font-weight: 500;">TOTAL PRICES</label>
                    <div class="input-group mt-2">
                        <span class="input-group-text" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #000; font-weight: 700; border: 1px solid rgba(251, 191, 36, 0.3);"><strong><?= $NAMAN ?></strong></span>
                    <input type="text" id="estimation" class="form-control" placeholder="Your order will total" readonly>
                </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; padding: 12px 25px; border-radius: 12px; font-weight: 600; transition: all 0.3s ease; position: relative; overflow: hidden;">
                        <i class="fas fa-key me-2"></i>GENERATE KEY
                    </button>
                </div>
                 </div>
                <?= form_close() ?>
 
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

    .form-control[readonly] {
        background: rgba(99, 102, 241, 0.05) !important;
        border-color: rgba(99, 102, 241, 0.2) !important;
        color: #6366f1 !important;
        font-weight: 600;
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

    .alert-success {
        background: rgba(34, 197, 94, 0.1) !important;
        border: 1px solid rgba(34, 197, 94, 0.3) !important;
        color: #22c55e !important;
        border-radius: 12px;
        backdrop-filter: blur(5px);
    }

    .key-sensi {
        background: rgba(99, 102, 241, 0.1);
        padding: 10px;
        border-radius: 8px;
        border: 1px solid rgba(99, 102, 241, 0.3);
        display: inline-block;
        margin: 5px 0;
        color: #6366f1;
        font-weight: 600;
    }

    .btn-outline-warning {
        border-color: rgba(251, 191, 36, 0.5);
        color: #fbbf24;
        background: transparent;
        transition: all 0.3s ease;
    }

    .btn-outline-warning:hover {
        background: rgba(251, 191, 36, 0.1);
        border-color: #fbbf24;
        color: #fbbf24;
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

    .text-danger {
        color: #ef4444 !important;
    }

    small.text-danger {
        display: block;
        margin-top: 5px;
        font-size: 0.85rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        var price = JSON.parse('<?= $price ?>');
        getPrice(price);
        $("#max_devices, #bulk, #duration, #game").change(function() {
            getPrice(price);
        });
        function getPrice(price) {
            var price = price;
            var device = $("#max_devices").val();
            var durate = $("#duration").val();
            var bulk = $("#bulk").val();
            var gprice = price[durate];
            if (gprice != NaN) {
                var est278 = (device * gprice);
                var result = (est278 * bulk);
                $("#estimation").val(result);
            } else {
                $("#estimation").val('Estimation error');
            }
        }
    });
</script>

<script>
    $(document).ready(function() {
        new ClipboardJS('.copy-btn');
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.6/dist/clipboard.min.js"></script>


<script>
    function downloadKeys(filename) {
        var text = keys.join("\n");
        var element = document.createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
        element.setAttribute('download', filename);
        element.style.display = 'none';
        document.body.appendChild(element);
        element.click();
        document.body.removeChild(element);
    }
</script>

<?= $this->endSection() ?>