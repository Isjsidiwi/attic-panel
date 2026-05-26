<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VIP Panel - Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #0a0a0a;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background */
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

        .install-container {
            background: rgba(20, 20, 20, 0.8);
            border-radius: 20px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 
                0 8px 32px 0 rgba(0, 0, 0, 0.37),
                0 0 80px rgba(99, 102, 241, 0.1);
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
            overflow: hidden;
            margin: 50px auto;
            max-width: 600px;
        }

        .install-container::before {
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

        .install-header {
            text-align: center;
            padding: 2.5rem 2rem 2rem;
            border-bottom: 1px solid rgba(99, 102, 241, 0.1);
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.5);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 30px rgba(99, 102, 241, 0.5); }
            50% { transform: scale(1.05); box-shadow: 0 0 40px rgba(99, 102, 241, 0.8); }
        }

        .install-header h2 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }

        .install-header p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.95rem;
        }

        .form-label {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .input-group-text {
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-right: none;
            color: #6366f1;
        }

        .form-control {
            background: rgba(20, 20, 20, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.3);
            color: #fff;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control.with-icon {
            border-left: none;
        }

        .form-control:focus {
            background: rgba(20, 20, 20, 0.8);
            border-color: #6366f1;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .btn-install {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            color: white;
            padding: 14px;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-install::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-install:hover::before {
            left: 100%;
        }

        .btn-install:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5);
        }

        .btn-test {
            background: rgba(34, 197, 94, 0.2);
            border: 1px solid rgba(34, 197, 94, 0.5);
            color: #22c55e;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-test:hover {
            background: rgba(34, 197, 94, 0.3);
            border-color: #22c55e;
            color: #22c55e;
        }

        .alert {
            border-radius: 12px;
            backdrop-filter: blur(5px);
            animation: slideIn 0.5s ease-out;
            border: none;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1) !important;
            border: 1px solid rgba(34, 197, 94, 0.3) !important;
            color: #22c55e !important;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1) !important;
            border: 1px solid rgba(239, 68, 68, 0.3) !important;
            color: #ef4444 !important;
        }

        .alert-info {
            background: rgba(99, 102, 241, 0.1) !important;
            border: 1px solid rgba(99, 102, 241, 0.3) !important;
            color: #6366f1 !important;
        }

        .steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .step::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: rgba(99, 102, 241, 0.2);
            z-index: -1;
        }

        .step:first-child::before {
            display: none;
        }

        .step-number {
            width: 30px;
            height: 30px;
            background: rgba(99, 102, 241, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #6366f1;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .step.active .step-number {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
        }

        .step-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
        }

        .step.active .step-label {
            color: #6366f1;
            font-weight: 600;
        }

        .spinner-border {
            display: none;
        }

        .loading .spinner-border {
            display: inline-block;
        }

        .loading .btn-text {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="install-container">
            <!-- Header -->
            <div class="install-header">
                <div class="logo-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <h2>VIP Panel Installer</h2>
                <p>Configure your database settings to get started</p>
            </div>
            
            <!-- Form -->
            <div class="p-4 pb-5">
                <!-- Steps -->
                <div class="steps">
                    <div class="step active">
                        <div class="step-number">1</div>
                        <div class="step-label">Database</div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-label">Verify</div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-label">Complete</div>
                    </div>
                </div>

                <!-- Messages -->
                <div id="message"></div>

                <!-- Installation Form -->
                <form id="installForm" method="POST">
                    <input type="hidden" name="action" value="install">

                    <!-- Database Host -->
                    <div class="mb-3">
                        <label for="db_host" class="form-label">Database Host</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-server"></i>
                            </span>
                            <input type="text" class="form-control with-icon" id="db_host" name="db_host" 
                                   placeholder="localhost" value="localhost" required>
                        </div>
                    </div>

                    <!-- Database Name -->
                    <div class="mb-3">
                        <label for="db_name" class="form-label">Database Name</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-database"></i>
                            </span>
                            <input type="text" class="form-control with-icon" id="db_name" name="db_name" 
                                   placeholder="your_database_name" required>
                        </div>
                    </div>

                    <!-- Database Username -->
                    <div class="mb-3">
                        <label for="db_username" class="form-label">Database Username</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" class="form-control with-icon" id="db_username" name="db_username" 
                                   placeholder="root" value="root" required>
                        </div>
                    </div>

                    <!-- Database Password -->
                    <div class="mb-3">
                        <label for="db_password" class="form-label">Database Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control with-icon" id="db_password" name="db_password" 
                                   placeholder="Enter database password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePwd">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Site URL -->
                    <div class="mb-3">
                        <label for="site_url" class="form-label">Site URL</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-globe"></i>
                            </span>
                            <input type="url" class="form-control with-icon" id="site_url" name="site_url" 
                                   placeholder="https://yourdomain.com" required>
                        </div>
                        <small class="text-muted" style="font-size: 0.85rem; opacity: 0.7;">Enter your website URL (without trailing slash)</small>
                    </div>

                    <!-- Test Connection Button -->
                    <div class="d-grid mb-3">
                        <button type="button" class="btn btn-test" id="testBtn">
                            <i class="fas fa-plug me-2"></i>Test Connection
                        </button>
                    </div>

                    <!-- Install Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-install">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            <span class="btn-text">
                                <i class="fas fa-download me-2"></i>Install & Configure
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Info -->
                <div class="alert alert-info mt-4" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Note:</strong> This will automatically:
                    <ul class="mb-0 mt-2" style="font-size: 0.9rem;">
                        <li>Update your .env file with database credentials</li>
                        <li>Configure site URL for proper routing</li>
                        <li>Import all database tables and structure</li>
                        <li>Create default admin account (Username: PRINCEAALYAN)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Password toggle
        document.getElementById('togglePwd').addEventListener('click', function() {
            const passwordField = document.getElementById('db_password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });

        // Test Connection
        document.getElementById('testBtn').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Testing...';

            const formData = new FormData();
            formData.append('action', 'test');
            formData.append('db_host', document.getElementById('db_host').value);
            formData.append('db_name', document.getElementById('db_name').value);
            formData.append('db_username', document.getElementById('db_username').value);
            formData.append('db_password', document.getElementById('db_password').value);

            fetch('install.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('message');
                if (data.success) {
                    messageDiv.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                } else {
                    messageDiv.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-times-circle me-2"></i>${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                }
                btn.disabled = false;
                btn.innerHTML = originalText;
            })
            .catch(error => {
                const messageDiv = document.getElementById('message');
                messageDiv.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-times-circle me-2"></i>Connection test failed!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        });

        // Form submission
        document.getElementById('installForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const btn = form.querySelector('.btn-install');
            btn.classList.add('loading');
            btn.disabled = true;

            const formData = new FormData(form);

            fetch('install.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    throw new Error('Invalid JSON response: ' + text);
                }
                
                const messageDiv = document.getElementById('message');
                if (data.success) {
                    // Update steps
                    document.querySelectorAll('.step').forEach(step => step.classList.add('active'));
                    
                    // Show success popup
                    Swal.fire({
                        title: 'Installation Complete!',
                        html: '<div style="text-align: center;"><i class="fas fa-check-circle" style="font-size: 4rem; color: #22c55e; margin-bottom: 1rem;"></i><p style="color: rgba(255,255,255,0.8); margin-top: 1rem;">Database configured successfully!<br>Redirecting to homepage...</p></div>',
                        icon: false,
                        background: 'rgba(20, 20, 20, 0.95)',
                        color: '#fff',
                        confirmButtonText: 'Go to Home',
                        confirmButtonColor: '#22c55e',
                        allowOutsideClick: false,
                        timer: 3000,
                        timerProgressBar: true,
                        showClass: {
                            popup: 'animate__animated animate__zoomIn'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__zoomOut'
                        }
                    }).then((result) => {
                        window.location.href = '/';
                    });
                } else {
                    messageDiv.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>Error!</strong> ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    btn.classList.remove('loading');
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Full error:', error);
                const messageDiv = document.getElementById('message');
                messageDiv.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-times-circle me-2"></i>
                        <strong>Error!</strong> ${error.message || 'Installation failed. Please try again.'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                btn.classList.remove('loading');
                btn.disabled = false;
            });
        });

        // Security measures - Disable right-click, F12, Ctrl+U, Ctrl+S, Ctrl+Shift+I
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });

        document.addEventListener('keydown', function(e) {
            // F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U, Ctrl+S
            if (e.keyCode === 123 || 
                (e.ctrlKey && e.shiftKey && e.keyCode === 73) || 
                (e.ctrlKey && e.shiftKey && e.keyCode === 74) || 
                (e.ctrlKey && e.key === 'u') || 
                (e.ctrlKey && e.key === 's')) {
                e.preventDefault();
                return false;
            }
        });

        // Disable developer tools
        (function() {
            var devtools = /./;
            devtools.toString = function() {
                this.opened = true;
            }
            console.log('%c', devtools);
        })();
    </script>
</body>
</html>

<?php
// PHP Backend for Installation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'test') {
        // Test database connection
        $host = $_POST['db_host'] ?? '';
        $name = $_POST['db_name'] ?? '';
        $username = $_POST['db_username'] ?? '';
        $password = $_POST['db_password'] ?? '';
        
        try {
            $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo json_encode([
                'success' => true,
                'message' => 'Database connection successful! ✓'
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    if ($action === 'install') {
        // Install and update .env file
        $host = $_POST['db_host'] ?? 'localhost';
        $name = $_POST['db_name'] ?? '';
        $username = $_POST['db_username'] ?? 'root';
        $password = $_POST['db_password'] ?? '';
        $siteUrl = $_POST['site_url'] ?? '';
        
        if (empty($name)) {
            echo json_encode([
                'success' => false,
                'message' => 'Database name is required!'
            ]);
            exit;
        }
        
        if (empty($siteUrl)) {
            echo json_encode([
                'success' => false,
                'message' => 'Site URL is required!'
            ]);
            exit;
        }
        
        // Remove trailing slash from URL
        $siteUrl = rtrim($siteUrl, '/');
        
        // Test connection first
        try {
            $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage()
            ]);
            exit;
        }
        
        // Import database structure
        try {
            // Create tables
            $pdo->exec("CREATE TABLE IF NOT EXISTS `files` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `path` varchar(255) NOT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci");
            
            $pdo->exec("CREATE TABLE IF NOT EXISTS `function_code` (
                `id_path` int(11) NOT NULL AUTO_INCREMENT,
                `NAMAN_SINGH` varchar(15) NOT NULL,
                `Online` varchar(5) NOT NULL,
                `Bullet` varchar(5) DEFAULT NULL,
                `Esp` varchar(5) DEFAULT NULL,
                `Aimbot` varchar(5) DEFAULT NULL,
                `Memory` varchar(5) DEFAULT NULL,
                `ModName` varchar(123) DEFAULT NULL,
                `Maintenance` varchar(250) DEFAULT NULL,
                `ftext` varchar(250) DEFAULT NULL,
                `status` varchar(50) DEFAULT NULL,
                `item` varchar(6) DEFAULT NULL,
                `SilentAim` varchar(6) DEFAULT NULL,
                `Setting` varchar(6) DEFAULT NULL,
                `Hrs5` varchar(15) DEFAULT NULL,
                `Days1` varchar(15) DEFAULT NULL,
                `Days7` varchar(15) DEFAULT NULL,
                `Days15` varchar(15) DEFAULT NULL,
                `Days30` varchar(15) DEFAULT NULL,
                `Days60` varchar(15) DEFAULT NULL,
                `Currency` varchar(15) DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id_path`),
                UNIQUE KEY `NAMAN_SINGH` (`NAMAN_SINGH`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci");
            
            $pdo->exec("CREATE TABLE IF NOT EXISTS `history` (
                `id_history` int(11) NOT NULL AUTO_INCREMENT,
                `keys_id` varchar(33) DEFAULT NULL,
                `user_do` varchar(33) DEFAULT NULL,
                `info` mediumtext NOT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id_history`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci");
            
            $pdo->exec("CREATE TABLE IF NOT EXISTS `keys_code` (
                `id_keys` int(11) NOT NULL AUTO_INCREMENT,
                `game` varchar(32) NOT NULL,
                `user_key` varchar(32) DEFAULT NULL,
                `duration` int(11) DEFAULT NULL,
                `expired_date` datetime DEFAULT NULL,
                `max_devices` int(11) DEFAULT NULL,
                `devices` mediumtext DEFAULT NULL,
                `status` tinyint(1) DEFAULT 1,
                `registrator` varchar(32) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                `key_reset_time` varchar(1) DEFAULT NULL,
                `key_reset_token` varchar(100) DEFAULT NULL,
                PRIMARY KEY (`id_keys`),
                UNIQUE KEY `user_key` (`user_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci");
            
            $pdo->exec("CREATE TABLE IF NOT EXISTS `referral_code` (
                `id_reff` int(11) NOT NULL AUTO_INCREMENT,
                `code` varchar(128) DEFAULT NULL,
                `Ucode` varchar(101) DEFAULT NULL,
                `set_saldo` int(11) DEFAULT NULL,
                `set_level` int(2) DEFAULT NULL,
                `used_limit` int(4) DEFAULT NULL,
                `max_limit` int(3) DEFAULT NULL,
                `used_by` varchar(66) DEFAULT NULL,
                `created_by` varchar(66) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id_reff`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci");
            
            $pdo->exec("CREATE TABLE IF NOT EXISTS `tokens` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `chave` varchar(255) NOT NULL,
                `Vendedor` varchar(255) NOT NULL,
                `StartDate` datetime DEFAULT '0000-00-00 00:00:00',
                `EndDate` datetime DEFAULT '0000-00-00 00:00:00',
                `UID` varchar(255) DEFAULT NULL,
                `UID2` varchar(255) DEFAULT NULL,
                `status` varchar(10) DEFAULT 'on',
                `devices` int(11) DEFAULT 1,
                `dias` int(11) DEFAULT 1,
                `diasp` varchar(255) DEFAULT NULL,
                `Expiry` int(11) DEFAULT 2,
                `version` varchar(255) DEFAULT NULL,
                `chon` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            
            $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
                `id_users` int(11) NOT NULL AUTO_INCREMENT,
                `fullname` varchar(155) DEFAULT NULL,
                `username` varchar(66) NOT NULL,
                `level` int(11) DEFAULT 2,
                `saldo` int(11) DEFAULT NULL,
                `status` tinyint(1) DEFAULT 1,
                `uplink` varchar(66) DEFAULT NULL,
                `password` varchar(155) NOT NULL,
                `image` varchar(100) NOT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                `loginDevices` varchar(150) DEFAULT NULL,
                `loginRsetTime` varchar(5) DEFAULT NULL,
                PRIMARY KEY (`id_users`),
                UNIQUE KEY `username` (`username`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci");
            
            // Check if data already exists before inserting
            $checkFunctionCode = $pdo->query("SELECT COUNT(*) FROM `function_code` WHERE `id_path` = 1")->fetchColumn();
            if ($checkFunctionCode == 0) {
                $pdo->exec("INSERT INTO `function_code` (`id_path`, `NAMAN_SINGH`, `Online`, `Bullet`, `Esp`, `Aimbot`, `Memory`, `ModName`, `Maintenance`, `ftext`, `status`, `item`, `SilentAim`, `Setting`, `Hrs5`, `Days1`, `Days7`, `Days15`, `Days30`, `Days60`, `Currency`) VALUES
                    (1, 'NAMAN_SINGH', 'false', 'false', 'false', 'false', 'false', 'AURA MODS NEW SAFE ', '', 'SEND FEEDBACK ', 'Safe', 'false', 'false', 'false', '9999', '30000', '1', '2', '4', '8', '$')");
            }
            
            // Check if admin user already exists before inserting
            $checkAdmin = $pdo->query("SELECT COUNT(*) FROM `users` WHERE `username` = 'PRINCEAALYAN'")->fetchColumn();
            if ($checkAdmin == 0) {
                $pdo->exec("INSERT INTO `users` (`id_users`, `fullname`, `username`, `level`, `saldo`, `status`, `uplink`, `password`, `image`, `created_at`, `updated_at`, `loginDevices`, `loginRsetTime`) VALUES
                    (1, 'PRINCEAALYAN', 'PRINCEAALYAN', 1, 2146331609, 1, 'PRINCEAALYAN', '\$2y\$08\$/CsSVgrGgCqVcievCuR2COPnlMIpRz6kA.hzItBD/xd1Cx0hj0kMK', 'photo_2025-10-08_22-25-22.jpg', NOW(), NOW(), 'WindowsNT10.0-Win64-x64', '0')");
            }
            
        } catch (PDOException $e) {
            // If error is not about existing tables, report it
            if (strpos($e->getMessage(), 'already exists') === false && strpos($e->getMessage(), 'Duplicate entry') === false) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Database setup failed: ' . $e->getMessage()
                ]);
                exit;
            }
        }
        
        // Update .env file
        $envFile = __DIR__ . '/app/.env';
        $envExample = __DIR__ . '/app/env';
        
        // Check if .env exists, if not copy from env
        if (!file_exists($envFile)) {
            if (file_exists($envExample)) {
                if (!copy($envExample, $envFile)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to create .env file. Check file permissions.'
                    ]);
                    exit;
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'env template file not found!'
                ]);
                exit;
            }
        }
        
        // Check if file is writable
        if (!is_writable($envFile)) {
            echo json_encode([
                'success' => false,
                'message' => '.env file is not writable. Please set permissions to 666 or 777.'
            ]);
            exit;
        }
        
        // Read .env content
        $envContent = file_get_contents($envFile);
        
        if ($envContent === false) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to read .env file.'
            ]);
            exit;
        }
        
        // Update database settings (with proper spacing)
        $envContent = preg_replace('/^database\.default\.hostname\s*=\s*.*$/m', "database.default.hostname = $host", $envContent);
        $envContent = preg_replace('/^database\.default\.database\s*=\s*.*$/m', "database.default.database = $name", $envContent);
        
        // Check if username line exists, if not add it after database line
        if (!preg_match('/^database\.default\.username\s*=/m', $envContent)) {
            $envContent = preg_replace('/^(database\.default\.database\s*=\s*.*)$/m', "$1\ndatabase.default.username = $username", $envContent);
        } else {
            $envContent = preg_replace('/^database\.default\.username\s*=\s*.*$/m', "database.default.username = $username", $envContent);
        }
        
        $envContent = preg_replace('/^database\.default\.password\s*=\s*.*$/m', "database.default.password = $password", $envContent);
        
        // Update base URL (with proper spacing)
        $envContent = preg_replace('/^app\.baseURL\s*=\s*.*$/m', "app.baseURL = $siteUrl/", $envContent);
        
        // Write updated content
        if (file_put_contents($envFile, $envContent) !== false) {
            // Create installation lock file
            file_put_contents(__DIR__ . '/app/.installed', date('Y-m-d H:i:s'));
            
            echo json_encode([
                'success' => true,
                'message' => 'Installation completed successfully! Redirecting...',
                'redirect' => '/'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update .env file. Check file permissions (chmod 666 .env).'
            ]);
        }
        exit;
    }
}

// Check if already installed
$alreadyInstalled = file_exists(__DIR__ . '/app/.installed');

// Handle reinstall request
if (isset($_GET['reinstall']) && $_GET['reinstall'] === 'confirm') {
    if (file_exists(__DIR__ . '/app/.installed')) {
        unlink(__DIR__ . '/app/.installed');
    }
    header('Location: install.php');
    exit;
}

if ($alreadyInstalled && !isset($_GET['force'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Already Installed</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            body {
                background: #0a0a0a;
                margin: 0;
                padding: 0;
            }
        </style>
    </head>
    <body>
        <script>
            Swal.fire({
                title: 'Already Installed!',
                html: '<div style="text-align: center;"><i class="fas fa-check-circle" style="font-size: 4rem; color: #22c55e; margin-bottom: 1rem;"></i><p style="color: rgba(255,255,255,0.8); margin-top: 1rem;">Installation has been completed previously.</p></div>',
                icon: false,
                background: 'rgba(20, 20, 20, 0.95)',
                color: '#fff',
                showCancelButton: true,
                confirmButtonText: 'Go to Home',
                cancelButtonText: 'Reinstall',
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#ef4444',
                allowOutsideClick: false,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/';
                } else if (result.isDismissed) {
                    // Ask for confirmation before reinstall
                    Swal.fire({
                        title: 'Confirm Reinstall?',
                        html: '<div style="text-align: center;"><i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #f59e0b; margin-bottom: 1rem;"></i><p style="color: rgba(255,255,255,0.8); margin-top: 1rem;">This will allow you to reinstall.<br><strong style="color: #ef4444;">Warning:</strong> Make sure to backup your data!</p></div>',
                        icon: false,
                        background: 'rgba(20, 20, 20, 0.95)',
                        color: '#fff',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Reinstall',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        allowOutsideClick: false
                    }).then((reinstallResult) => {
                        if (reinstallResult.isConfirmed) {
                            window.location.href = 'install.php?reinstall=confirm';
                        } else {
                            window.location.href = '/';
                        }
                    });
                }
            });
        </script>
    </body>
    </html>
    <?php
    exit;
}
?>

