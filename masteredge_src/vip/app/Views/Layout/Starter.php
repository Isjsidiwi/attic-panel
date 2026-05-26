<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
 
    <!-- Add Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
 
    <link rel="apple-touch-icon" sizes="180x180" href="<?=base_url()?>/icon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?=base_url()?>/icon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url()?>/icon/favicon-16x16.png">
    <link rel="manifest" href="<?=base_url()?>/icon/site.webmanifest">
    <link rel="mask-icon" href="<?=base_url()?>/icon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#603cba">
    <meta name="theme-color" content="#ffffff">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title><?= BASE_NAME ?> - <?= isset($title) ? $title : 'Panel' ?></title>
    <?= $this->renderSection('css') ?>

    <?= link_tag('assets/css/natacode.css') ?>

    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

 <style>
    body {
        background: #0a0a0a !important;
        position: relative;
        overflow-x: hidden;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
            radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 90%, rgba(168, 85, 247, 0.1) 0%, transparent 50%);
        animation: gradientShift 10s ease infinite;
        z-index: 0;
        pointer-events: none;
    }

    @keyframes gradientShift {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    #content {
        position: relative;
        z-index: 1;
    }

    .telegram-icon {
        position: fixed;
        bottom: 15px;
        left: 2px;
        width: 55px;
        height: 55px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 5px 25px rgba(99, 102, 241, 0.5);
        transition: all 0.3s ease;
        z-index: 1000;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .telegram-icon:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 35px rgba(99, 102, 241, 0.7);
    }

    .telegram-icon a {
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .telegram-icon i {
        font-size: 24px;
    }

    footer {
        background: rgba(20, 20, 20, 0.95) !important;
        border-top: 1px solid rgba(99, 102, 241, 0.2) !important;
        backdrop-filter: blur(10px);
    }

    footer small {
        color: #6366f1 !important;
    }
</style>
</head>

<body>
    <div class="telegram-icon">
        <a href="https://telegram.me/aalyanmods" target="_blank">
            <i class="fab fa-telegram"></i>
        </a>
    </div>

    <!-- Start menu -->
    <?= $this->include('Layout/Header') ?>
    <!-- End of menu -->
    <main>
        <div class="container p-3 py-4 mb-3" id="content">
            <!-- Start content -->
            <?= $this->renderSection('content') ?>

            <!-- End of content -->
        </div>
    </main>
    <footer class="fixed-bottom bg-body border-top text-muted">
        <div class="container">
            <small class="text-danger">&copy; <?= date('Y') ?> - <?= BASE_NAME ?></small>
        </div>
    </footer>
    <script>
    $(document).ready(function() {
        $(window).on("contextmenu",function(e){
           return false;
        }); 
    }); 
     document.onkeydown = function (e) {
        e = e || window.event;//Get event
        if (e.ctrlKey) {
            var c = e.which || e.keyCode;//Get key code
            switch (c) {
                case 83://Block Ctrl+S
                case 87://Block Ctrl+W --Not work in Chrome
                case 73://Block Ctrl+I
                case 67: //Block Ctrl+C
                    e.preventDefault();     
                    e.stopPropagation();
                break;
            }
        }
    };
    </script>
    <script>
    $(document).ready(function() {
        $(window).on("contextmenu",function(e){
           return false;
        }); 
    }); 
     document.onkeydown = function (e) {
        e = e || window.event;//Get event
        if (e.ctrlKey) {
            var c = e.which || e.keyCode;//Get key code
            switch (c) {
                case 83://Block Ctrl+S
                case 87://Block Ctrl+W --Not work in Chrome
                case 73://Block Ctrl+I
                case 67: //Block Ctrl+C
                    e.preventDefault();     
                    e.stopPropagation();
                break;
            }
        }
    };
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.0/sweetalert2.all.min.js" integrity="sha512-0UUEaq/z58JSHpPgPv8bvdhHFRswZzxJUT9y+Kld5janc9EWgGEVGfWV1hXvIvAJ8MmsR5d4XV9lsuA90xXqUQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <?= script_tag('assets/js/natacode.js') ?>

    <?= $this->renderSection('js') ?>

</body>

</html>