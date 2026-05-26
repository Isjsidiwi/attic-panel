<style>
    :root {
        --primary-color: #4f46e5;
        --secondary-color: #7c3aed;
        --accent-color: #06b6d4;
        --dark-color: #0f172a;
        --light-color: #ffffff;
    }

    /* Navbar Styling */
    .navbar {
        background: rgba(15, 23, 42, 0.95) !important;
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1rem 0;
        transition: all 0.3s ease;
        z-index: 1040;
    }

    /* Brand Styling */
    .navbar-brand {
        font-size: 1.8rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .navbar-brand::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: -100%;
        width: 100%;
        height: 3px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        transition: all 0.3s ease;
    }

    .navbar-brand:hover::after {
        left: 0;
    }

    .navbar-brand:hover {
        transform: translateY(-3px);
        text-shadow: 0 0 25px rgba(79, 70, 229, 0.5);
    }

    /* Navigation Links */
    .nav-link {
        position: relative;
        padding: 0.7rem 1.2rem;
        margin: 0 0.4rem;
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        color: var(--light-color);
    }

    .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        opacity: 0.15;
        transition: all 0.3s ease;
    }

    .nav-link:hover::before {
        left: 0;
    }

    .nav-link:hover {
        color: var(--accent-color) !important;
        transform: translateY(-2px);
    }

    /* Dropdown Menu */
    .dropdown-menu {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        padding: 1rem 0;
        animation: dropdownFade 0.3s ease;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        z-index: 9999;
    }

    @keyframes dropdownFade {
        from {
            opacity: 0;
            transform: translateY(-15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Dropdown Items */
    .dropdown-item {
        color: var(--light-color);
        padding: 0.8rem 1.8rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border-radius: 10px;
        margin: 5px 10px;
    }

    .dropdown-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 0;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        opacity: 0.15;
        transition: all 0.3s ease;
    }

    .dropdown-item:hover {
        color: var(--accent-color);
        background: transparent;
        transform: translateX(8px);
    }

    .dropdown-item:hover::before {
        width: 100%;
    }

    .dropdown-item i {
        margin-right: 12px;
        transition: all 0.3s ease;
        color: var(--light-color);
    }

    .dropdown-item:hover i {
        transform: scale(1.2);
        color: var(--accent-color);
    }

    /* Dividers */
    .dropdown-divider {
        border-color: rgba(255, 255, 255, 0.15);
        margin: 0.8rem 0;
    }

    /* Mobile Menu Button */
    .navbar-toggler {
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 0.6rem;
        transition: all 0.3s ease;
        border-radius: 10px;
    }

    .navbar-toggler:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: rotate(90deg);
    }

    .navbar-toggler:focus {
        box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.3);
    }

    /* Nav Items Animation */
    .nav-item {
        animation: fadeInRight 0.5s ease forwards;
        opacity: 0;
    }

    .nav-item:nth-child(1) { animation-delay: 0.1s; }
    .nav-item:nth-child(2) { animation-delay: 0.2s; }
    .nav-item:nth-child(3) { animation-delay: 0.3s; }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Special Styling */
    .dropdown-item.text-muted {
        color: var(--accent-color) !important;
        font-weight: 600;
        letter-spacing: 1px;
        font-size: 1rem;
    }

    .dropdown-item.text-danger {
        color: var(--primary-color) !important;
    }

    .dropdown-item.text-danger:hover {
        color: #ff1a1a !important;
    }

    /* Active State */
    .nav-link.active {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: var(--light-color) !important;
        box-shadow: 0 5px 15px rgba(79, 70, 229, 0.2);
    }
</style>

<header>
    <nav class="navbar navbar-expand-md navbar-dark shadow-sm align-middle">
        <div class="container px-3">
            <a class="navbar-brand" href="<?= site_url() ?>">
                <i class="bi bi-box px-2"></i><?= BASE_NAME ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <?php if (session()->has('userid')) : ?>
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('keys') ?>">
                                <i class="bi bi-key"></i> Keys
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('keys/generate') ?>">
                                <i class="bi bi-plus-circle"></i> Generate
                            </a>
                        </li>
                    </ul>
                    <div class="float-right">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle pe-2"></i><?= getName($user) ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start" aria-labelledby="navbarDropdown">
                                    <li>
                                        <a class="dropdown-item" href="<?= site_url('settings') ?>">
                                            <i class="bi bi-gear"></i> Settings
                                        </a>
                                    </li>
                                   
                                    <?php if($user->level == 1) : ?>
                                        <li>
                                            <a class="dropdown-item" href="<?= site_url('ManageShortenerLinks') ?>">
                                                <i class="bi bi-link-45deg"></i> Manage Keygen Links
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    <?php if (($user->level == 1) || ($user->level ==2)) : ?>
                                        <li><a class="dropdown-item text-muted">Admin</a></li>
                                        
                                        <li>
                                            <a class="dropdown-item" href="<?= site_url('Server') ?>">
                                                <i class="bi bi-controller"></i> Online System
                                            </a>
                                        </li>
                                    
                                        <li>
                                            <a class="dropdown-item" href="<?= site_url('lib') ?>">
                                                <i class="bi bi-cloud-upload"></i> Online LIB
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php if($user->level == 1) : ?>
                                        <li>
                                            <a class="dropdown-item" href="<?= site_url('ManageHackingAttempt') ?>">
                                                <i class="bi bi-shield-check"></i> Manage Hacking Attempt
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a class="dropdown-item" href="<?= site_url('PrivateDashboard') ?>">
                                                <i class="bi bi-speedometer2"></i> Private Dashboard
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a class="dropdown-item" href="<?= site_url('admin/manage-users') ?>">
                                                <i class="bi bi-people"></i> Manage Users
                                            </a>
                                        </li>
                                        
                                        <li>
                                            <a class="dropdown-item" href="<?= site_url('admin/create-referral') ?>">
                                                <i class="bi bi-person-plus"></i> Create Referral
                                            </a>
                                        </li>
                                        
                                        <li><hr class="dropdown-divider"></li>
                                    <?php endif; ?>
                                    
                                    <li>
                                        <a class="dropdown-item" href="https://t.me/">
                                            <i class="bi bi-telegram"></i> Get Support
                                        </a>
                                    </li>
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    
                                    <li>
                                        <a class="dropdown-item text-danger" href="<?= site_url('logout') ?>">
                                            <i class="bi bi-box-arrow-in-left"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<script>
    // Add hover effect for dropdown
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(8px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    // Add animation for navbar brand
    const navbarBrand = document.querySelector('.navbar-brand');
    navbarBrand.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-3px)';
    });
    
    navbarBrand.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });

    // Add active state for current page
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.href === window.location.href) {
            link.classList.add('active');
        }
    });
</script>