<?= $this->extend('Layout/Starter') ?>
<?= $this->section('content') ?>

<!-- Add Preloader HTML -->
<div class="preloader">
    <div class="loader"></div>
</div>

<div class="row g-2">
    <div class="col-12">
        <?= $this->include('Layout/msgStatus') ?>
    </div>

    <style>
        /* Preloader CSS */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #0a0a0a;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(99, 102, 241, 0.2);
            border-top: 5px solid #6366f1;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .preloader-hide {
            display: none;
        }

        /* Animated Background */
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
        }

        @keyframes gradientShift {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        /* Main Content */
        .row {
            position: relative;
            z-index: 2;
        }

        /* Profile Card */
        .profile-card {
            background: rgba(20, 20, 20, 0.8);
            border-radius: 20px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 
                0 8px 32px 0 rgba(0, 0, 0, 0.37),
                0 0 80px rgba(99, 102, 241, 0.1);
            backdrop-filter: blur(10px);
            margin-bottom: 20px;
            transition: all 0.3s ease;
            animation: cardSlideIn 0.6s ease-out;
            position: relative;
            overflow: hidden;
        }

        .profile-card::before {
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

        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 15px 40px rgba(0, 0, 0, 0.5),
                0 0 100px rgba(99, 102, 241, 0.2);
        }

        @keyframes cardSlideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(99, 102, 241, 0.4);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
            margin: 15px auto;
            object-fit: cover;
            transition: all 0.3s ease;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3); }
            50% { box-shadow: 0 15px 40px rgba(99, 102, 241, 0.5); }
        }

        .profile-image:hover {
            transform: scale(1.05);
            border-color: #6366f1;
        }

        .card-header {
            border-radius: 20px 20px 0 0;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
            color: #fff !important;
            text-align: center;
            border-bottom: 1px solid rgba(99, 102, 241, 0.1);
        }

        .list-group-item {
            padding: 15px;
            margin-bottom: 8px;
            border-radius: 12px !important;
            background: rgba(99, 102, 241, 0.05) !important;
            border: 1px solid rgba(99, 102, 241, 0.2) !important;
            color: #e6eef8 !important;
            transition: all 0.3s ease;
        }

        .list-group-item:hover {
            background: rgba(99, 102, 241, 0.1) !important;
            transform: translateX(5px);
            border-color: rgba(99, 102, 241, 0.3) !important;
        }

        .user-name {
            font-size: 1.8rem;
            margin: 15px 0;
            color: #e6eef8;
            font-weight: 700;
        }

        .balance-box {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            padding: 20px;
            border-radius: 15px;
            margin: 15px 0;
        }

        .balance-box .text-muted {
            color: rgba(230, 238, 248, 0.7) !important;
            font-size: 0.9rem;
        }

        .balance-box .h4 {
            color: #6366f1 !important;
            font-size: 2rem;
            font-weight: 700;
        }

        .badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }

        .bg-info {
            background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
            color: #fff !important;
        }

        .bg-primary {
            background: rgba(99, 102, 241, 0.2) !important;
            color: #6366f1 !important;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .bg-danger {
            background: rgba(239, 68, 68, 0.2) !important;
            color: #ef4444 !important;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .bg-success {
            background: rgba(34, 197, 94, 0.2) !important;
            color: #22c55e !important;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .text-primary {
            color: #6366f1 !important;
        }

        .text-danger {
            color: #ef4444 !important;
        }

        .text-success {
            color: #22c55e !important;
        }

        .text-info {
            color: #6366f1 !important;
        }

        .text-white {
            color: #e6eef8 !important;
        }

        .card-body {
            color: #e6eef8;
        }

        /* Button Styling */
        .btn {
            transition: all 0.3s ease;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 15px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
            text-decoration: none;
        }

        .btn-outline-primary {
            border-color: rgba(99, 102, 241, 0.3) !important;
            color: #6366f1 !important;
        }

        .btn-outline-primary:hover {
            background: rgba(99, 102, 241, 0.1) !important;
            border-color: #6366f1 !important;
            color: #6366f1 !important;
        }

        .btn-outline-success {
            border-color: rgba(34, 197, 94, 0.3) !important;
            color: #22c55e !important;
        }

        .btn-outline-success:hover {
            background: rgba(34, 197, 94, 0.1) !important;
            border-color: #22c55e !important;
            color: #22c55e !important;
        }

        .btn-outline-info {
            border-color: rgba(99, 102, 241, 0.3) !important;
            color: #6366f1 !important;
        }

        .btn-outline-info:hover {
            background: rgba(99, 102, 241, 0.1) !important;
            border-color: #6366f1 !important;
            color: #6366f1 !important;
        }

        .btn-outline-warning {
            border-color: rgba(168, 85, 247, 0.3) !important;
            color: #8b5cf6 !important;
        }

        .btn-outline-warning:hover {
            background: rgba(168, 85, 247, 0.1) !important;
            border-color: #8b5cf6 !important;
            color: #8b5cf6 !important;
        }

        .btn-outline-secondary {
            border-color: rgba(148, 163, 184, 0.3) !important;
            color: #94a3b8 !important;
        }

        .btn-outline-secondary:hover {
            background: rgba(148, 163, 184, 0.1) !important;
            border-color: #94a3b8 !important;
            color: #94a3b8 !important;
        }

        .btn-outline-dark {
            border-color: rgba(100, 116, 139, 0.3) !important;
            color: #64748b !important;
        }

        .btn-outline-dark:hover {
            background: rgba(100, 116, 139, 0.1) !important;
            border-color: #64748b !important;
            color: #64748b !important;
        }

        .btn-outline-danger {
            border-color: rgba(239, 68, 68, 0.3) !important;
            color: #ef4444 !important;
        }

        .btn-outline-danger:hover {
            background: rgba(239, 68, 68, 0.1) !important;
            border-color: #ef4444 !important;
            color: #ef4444 !important;
        }

        @media (max-width: 768px) {
            .profile-image {
                width: 100px;
                height: 100px;
            }
            .user-name {
                font-size: 1.5rem;
            }
            .badge {
                font-size: 0.8rem;
            }
            .balance-box .h4 {
                font-size: 1.5rem;
            }
        }
    </style>

    <!-- Add JavaScript for Preloader -->
    <script>
        window.addEventListener('load', function() {
            const preloader = document.querySelector('.preloader');
            preloader.classList.add('preloader-hide');
        });
    </script>

    <!-- Profile Section -->
    <div class="col-12">
        <div class="profile-card">
            <div class="card-header text-center text-white">
                <i class="fas fa-user-circle"></i> PROFILE
            </div>
            <div class="card-body text-center">
                <?php if ($user->image): ?>
                    <img src="<?= base_url('/uploads/' . $user->image) ?>" 
                         alt="<?= esc($user->username) ?>" 
                         class="profile-image">
                <?php else: ?>
                    <div class="profile-image d-flex align-items-center justify-content-center bg-light">
                        <i class="fas fa-user fa-3x text-secondary"></i>
                    </div>
                <?php endif; ?>
                
                <h4 class="user-name"><?= getName($user) ?></h4>
                <div class="badge bg-info mb-2">
                    <?= getLevel($user->level) ?>
                </div>
                <div class="balance-box">
                    <div class="text-muted">Balance</div>
                    <div class="h4 mb-0">₹<?= number_format($user->saldo, 2) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Info -->
    <div class="col-12">
        <div class="profile-card">
            <div class="card-header text-white">
                <i class="fas fa-clock"></i> LOGIN INFO
            </div>
            <div class="card-body p-2">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-sign-in-alt text-primary"></i> Login</span>
                        <span class="badge bg-primary">
                            <?= $time::parse(session()->time_since)->humanize() ?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span><i class="fas fa-sign-out-alt text-danger"></i> Logout</span>
                        <span class="badge bg-danger">
                            <?= $time::now()->difference($time::parse(session()->time_login))->humanize() ?>
                        </span>
                    </li>
                </ul>
            </div>

        </div>
    </div>

        <!-- Quick Actions -->
        <div class="col-lg-6">
            <div class="card profile-card">
                <div class="card-header text-center text-white">
                    <i class="fas fa-bolt me-2"></i> QUICK ACTIONS
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="<?= site_url('keys') ?>" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-key me-1"></i> Keys
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= site_url('keys/generate') ?>" class="btn btn-outline-success w-100 mb-2">
                                <i class="fas fa-plus me-1"></i> Generate
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= site_url('settings') ?>" class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-cog me-1"></i> Settings
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= site_url('Profile') ?>" class="btn btn-outline-warning w-100 mb-2">
                                <i class="fas fa-user me-1"></i> Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Summary Card -->
        <div class="col-lg-6">
            <div class="card profile-card">
                <div class="card-header text-center text-white">
                    <i class="fas fa-chart-line me-2"></i> ACTIVITY SUMMARY
                </div>
                <div class="card-body">
                    <ul class="list-group list-hover">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-calendar-check me-2 text-success"></i>
                                <span>Last Active</span>
                            </div>
                            <span class="badge bg-success">Today</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-tasks me-2 text-info"></i>
                                <span>Account Status</span>
                            </div>
                            <span class="badge bg-info">Active</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Admin Panel (if admin) -->
        <?php if (($user->level == 1) || ($user->level == 2)) : ?>
        <div class="col-12">
            <div class="card profile-card">
                <div class="card-header text-center text-white">
                    <i class="fas fa-shield-alt me-2"></i> ADMIN PANEL
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-3 col-6">
                            <a href="<?= site_url('admin/manage-users') ?>" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-users me-1"></i> Users
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="<?= site_url('admin/Add-Balance') ?>" class="btn btn-outline-success w-100 mb-2">
                                <i class="fas fa-wallet me-1"></i> Balance
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="<?= site_url('admin/create-referral') ?>" class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-hand-holding-heart me-1"></i> Referral
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="<?= site_url('admin/Clear') ?>" class="btn btn-outline-warning w-100 mb-2">
                                <i class="fas fa-broom me-1"></i> Clear
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="<?= site_url('Server') ?>" class="btn btn-outline-secondary w-100 mb-2">
                                <i class="fas fa-server me-1"></i> Server
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="<?= site_url('Files') ?>" class="btn btn-outline-dark w-100 mb-2">
                                <i class="fas fa-file me-1"></i> Files
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="<?= site_url('Price') ?>" class="btn btn-outline-danger w-100 mb-2">
                                <i class="fas fa-rupee-sign me-1"></i> Price
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Additional Features -->
        <div class="col-12">
            <div class="card profile-card">
                <div class="card-header text-center text-white">
                    <i class="fas fa-tools me-2"></i> ADDITIONAL FEATURES
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-4 col-6">
                            <a href="<?= site_url('keys/name-generate') ?>" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-file-pen me-1"></i> Name Key
                            </a>
                        </div>
                        <div class="col-md-4 col-6">
                            <a href="<?= site_url('keys/Unused') ?>" class="btn btn-outline-success w-100 mb-2">
                                <i class="fas fa-clock me-1"></i> Unused Keys
                            </a>
                        </div>
                        <div class="col-md-4 col-6">
                            <a href="<?= site_url('keys/Expired') ?>" class="btn btn-outline-danger w-100 mb-2">
                                <i class="fas fa-exclamation-triangle me-1"></i> Expired Keys
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<?= $this->endSection() ?>