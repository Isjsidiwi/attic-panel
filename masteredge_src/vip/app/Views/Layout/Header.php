<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.2/css/all.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<style>
/* Base Styles */
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: #0a0a0a !important;
}

/* Dark Theme Header */
.navbar {
    padding: 0.8rem 1.5rem;
    background: rgba(20, 20, 20, 0.95);
    position: relative;
    z-index: 1;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
    border-bottom: 1px solid rgba(99, 102, 241, 0.2);
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.navbar::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, 
        transparent,
        #6366f1,
        #8b5cf6,
        transparent
    );
    animation: borderGlow 3s ease-in-out infinite;
}

@keyframes borderGlow {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

.navbar:hover {
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3);
}

.navbar-brand {
    font-size: 1.25rem;
    font-weight: 600;
    color: #fff !important;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
    position: relative;
    z-index: 2;
}

.navbar-brand:hover {
    transform: translateY(-1px);
    text-shadow: 0 0 20px rgba(255, 255, 255, 0.8);
}

.navbar-brand i {
    color: #ffffff;
    font-size: 1.4rem;
    filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.5));
}

/* Menu Container */
.menu-container {
    position: fixed;
    top: 0;
    right: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    visibility: hidden;
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 9999;
    pointer-events: none;
    backdrop-filter: blur(10px);
}

.nav-open .menu-container {
    visibility: visible;
    opacity: 1;
    pointer-events: auto;
}

/* Enhanced Menu Styles */
.dineuron-menu {
    width: 250px;
    height: 100vh;
    position: absolute;
    right: -250px;
    top: 0;
    background: rgba(20, 20, 20, 0.95);
    transition: all 0.3s ease;
    overflow-y: auto;
    box-shadow: -2px 0 15px rgba(0, 0, 0, 0.5);
    border-left: 1px solid rgba(99, 102, 241, 0.2);
    backdrop-filter: blur(10px);
}

.nav-open .dineuron-menu {
    right: 0;
}

/* Profile Section */
.profile-container {
    text-align: center;
    padding: 20px 15px;
    background: rgba(99, 102, 241, 0.05);
    border-bottom: 1px solid rgba(99, 102, 241, 0.2);
}

.profile-container img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 3px solid rgba(99, 102, 241, 0.5);
    padding: 2px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.3); }
    50% { box-shadow: 0 0 30px rgba(99, 102, 241, 0.5); }
}

.profile-container img:hover {
    transform: scale(1.05);
    border-color: #6366f1;
}

.proName pi {
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    display: block;
    margin-top: 8px;
    text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
}

/* Navigation List */
.nav {
    padding: 15px 10px;
}

.nav li {
    list-style: none;
    margin-bottom: 3px;
    opacity: 0;
    transform: translateX(20px);
    transition: all 0.3s ease;
}

.nav-open .nav li {
    opacity: 1;
    transform: translateX(0);
}

.nav li a {
    color: #fff;
    font-size: 14px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    background: rgba(99, 102, 241, 0.05);
    margin: 5px 0;
    border: 1px solid rgba(99, 102, 241, 0.2);
}

.nav li a i:not(.dropdown-icon) {
    min-width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(99, 102, 241, 0.1);
    border-radius: 6px;
    margin-right: 10px;
    color: #6366f1;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 0 10px rgba(99, 102, 241, 0.2);
}

.nav li a:hover {
    background: rgba(99, 102, 241, 0.1);
    transform: translateX(5px) scale(1.02);
    box-shadow: 0 5px 15px rgba(99, 102, 241, 0.2);
}

.nav li a:hover i:not(.dropdown-icon) {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    box-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
}

/* Dropdown Menu */
.dropdown-menu {
    background: rgba(10, 10, 10, 0.95);
    border-radius: 8px;
    margin: 3px 0 3px 35px;
    padding: 5px;
    display: none;
    position: static;
    float: none;
    border: 1px solid rgba(99, 102, 241, 0.2);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}

.dropdown-menu li a {
    padding: 8px 10px;
    font-size: 13px;
    background: rgba(99, 102, 241, 0.03);
    margin: 4px 0;
}

.dropdown-icon {
    margin-left: auto;
    font-size: 12px;
    transition: transform 0.3s;
}

.dropdown-icon.open {
    transform: rotate(180deg);
}

/* Hamburger Button */
.nav-button {
    position: relative;
    z-index: 10000;
    padding: 8px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 8px;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(5px);
}

.nav-button:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-1px);
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
}

.nav-button #nav-icon3 {
    width: 22px;
    height: 16px;
    position: relative;
    transform: rotate(0deg);
    transition: .5s ease-in-out;
    cursor: pointer;
}

.nav-button #nav-icon3 span {
    display: block;
    position: absolute;
    height: 2px;
    width: 100%;
    background: #ffffff;
    border-radius: 9px;
    opacity: 1;
    transform: rotate(0deg);
    transition: .25s ease-in-out;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.nav-button #nav-icon3 span:nth-child(1) { top: 0px; }
.nav-button #nav-icon3 span:nth-child(2),
.nav-button #nav-icon3 span:nth-child(3) { top: 7px; }
.nav-button #nav-icon3 span:nth-child(4) { top: 14px; }

.nav-open #nav-icon3 span:nth-child(1) {
    top: 7px;
    width: 0%;
    left: 50%;
}

.nav-open #nav-icon3 span:nth-child(2) {
    transform: rotate(45deg);
}

.nav-open #nav-icon3 span:nth-child(3) {
    transform: rotate(-45deg);
}

.nav-open #nav-icon3 span:nth-child(4) {
    top: 7px;
    width: 0%;
    left: 50%;
}

/* Animation Delays */
.nav li.delay-1 { transition-delay: 0.1s; }
.nav li.delay-2 { transition-delay: 0.2s; }
.nav li.delay-3 { transition-delay: 0.3s; }
.nav li.delay-4 { transition-delay: 0.4s; }
.nav li.delay-5 { transition-delay: 0.5s; }
.nav li.delay-6 { transition-delay: 0.6s; }
.nav li.delay-7 { transition-delay: 0.7s; }

</style>

</head>
<body>

<div class="head-main">
    <div class="navbar navbar-dark">
        <div class="container d-flex justify-content-between">
            <a class="navbar-brand" href="<?= site_url() ?>">
                <i class="bi bi-box text-danger px-2"></i>
                <?= BASE_NAME ?>
            </a>

            <button class="nav-button">
                <div id="nav-icon3">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>
        </div>
    </div>

    <?php if (session()->has('userid')) : ?>
    <div class="menu-container">
        <div class="dineuron-menu">
            <div class="profile-container">
                <img src="<?= base_url('uploads/'.getfile($user)) ?>" alt="Profile">
                <div class="proName">
                    <pi><?= getName($user) ?></pi>
                </div>
            </div>

            <ul class="nav flex-column">
                <li class="delay-1">
                    <a href="#" class="menu-link">
                        <i class="fa-regular fa-list-dropdown"></i>
                        <span>Keys</span>
                        <i class="fa-solid fa-chevron-down dropdown-icon"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?= site_url('keys')?>">
                            <i class="fa-solid fa-file-lines"></i>
                            <span>Keys List</span>
                        </a></li>
                        <li><a href="<?= site_url('keys/generate')?>">
                            <i class="fa-solid fa-file-plus"></i>
                            <span>Generate</span>
                        </a></li>
                        <li><a href="<?= site_url('keys/name-generate')?>">
                            <i class="fa-duotone fa-file-pen"></i>
                            <span>Name Key</span>
                        </a></li>
                        <?php if (($user->level == 1) || ($user->level ==2)) : ?>
                        <li><a href="<?= site_url('Price')?>">
                            <i class="fa-solid fa-indian-rupee-sign"></i>
                            <span>Price</span>
                        </a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <?php if (($user->level == 1) || ($user->level ==2)) : ?>
                <li class="delay-2">
                    <a href="#" class="menu-link">
                        <i class="fa-duotone fa-user-secret"></i>
                        <span>Users</span>
                        <i class="fa-solid fa-chevron-down dropdown-icon"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?= site_url('admin/manage-users')?>">
                            <i class="fa-regular fa-users-gear"></i>
                            <span>Manage</span>
                        </a></li>
                        <li><a href="<?= site_url('admin/Add-Balance')?>">
                            <i class="fa-duotone fa-wallet"></i>
                            <span>Balance</span>
                        </a></li>
                        <li><a href="<?= site_url('admin/create-referral')?>">
                            <i class="fa-regular fa-hand-holding-heart"></i>
                            <span>Referral</span>
                        </a></li>
                        <li><a href="<?= site_url('admin/Clear')?>">
                            <i class="fa-solid fa-broom"></i>
                            <span>Clear History</span>
                        </a></li>
                    </ul>
                </li>

                <li class="delay-3">
                    <a href="#" class="menu-link">
                        <i class="fa-solid fa-gamepad-modern"></i>
                        <span>Server</span>
                        <i class="fa-solid fa-chevron-down dropdown-icon"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?= site_url('Server')?>">
                            <i class="fa-solid fa-wrench"></i>
                            <span>Maintain</span>
                        </a></li>
                        <li><a href="<?= site_url('Files')?>">
                            <i class="fa-duotone fa-server"></i>
                            <span>Lib Files</span>
                        </a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <li class="delay-4">
                    <a href="<?= site_url('settings') ?>">
                        <i class="fa-solid fa-gear-complex-code"></i>
                        <span>Settings</span>
                    </a>
                </li>

                <?php if ($user->level == 1) : ?>
                <li class="delay-5">
                    <a href="<?= base_url('site-config.php') ?>" target="_blank">
                        <i class="fa-solid fa-cog"></i>
                        <span>Site Manager</span>
                    </a>
                </li>
                <?php endif; ?>

                <li class="delay-6">
                    <a href="<?= site_url('Profile') ?>">
                        <i class="fa-solid fa-id-badge"></i>
                        <span>Profile</span>
                    </a>
                </li>

                <li class="delay-7">
                    <a href="<?= site_url('logout') ?>">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Toggle dropdown menu
    $('.menu-link').click(function(e) {
        if($(this).siblings('.dropdown-menu').length) {
            e.preventDefault();
            $(this).siblings('.dropdown-menu').slideToggle(200);
            $(this).find('.dropdown-icon').toggleClass('open');
            
            // Close other dropdowns
            $('.dropdown-menu').not($(this).siblings('.dropdown-menu')).slideUp(200);
            $('.dropdown-icon').not($(this).find('.dropdown-icon')).removeClass('open');
        }
    });

    // Toggle menu visibility
    $('.nav-button').click(function(){
        $('body').toggleClass('nav-open');
    });

    // Close menu when clicking outside
    $('.menu-container').click(function(e){
        if(e.target === this) {
            $('body').removeClass('nav-open');
            // Close all dropdowns
            $('.dropdown-menu').slideUp(200);
            $('.dropdown-icon').removeClass('open');
        }
    });

    // Prevent menu from closing when clicking inside
    $('.dineuron-menu').click(function(e){
        e.stopPropagation();
    });
});
</script>

</body>
</html>