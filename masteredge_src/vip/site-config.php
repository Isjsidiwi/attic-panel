<?php
// Site Configuration Manager
session_start();

// Simple password protection
$configPassword = 'admin123'; // Change this password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        if ($_POST['password'] === $configPassword) {
            $_SESSION['site_config_auth'] = true;
            header('Location: site-config.php');
            exit;
        } else {
            $error = 'Invalid password!';
        }
    }
    
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        if (!isset($_SESSION['site_config_auth'])) {
            die('Unauthorized');
        }
        
        $baseName = $_POST['base_name'] ?? '';
        $baseNameFull = $_POST['base_name_full'] ?? '';
        
        if (empty($baseName) || empty($baseNameFull)) {
            $updateError = 'Both fields are required!';
        } else {
            // Read Constants.php
            $constantsFile = __DIR__ . '/../app/Config/Constants.php';
            $content = file_get_contents($constantsFile);
            
            // Update BASE_NAME
            $content = preg_replace(
                "/define\('BASE_NAME',\s*'[^']*'\);/",
                "define('BASE_NAME', '" . addslashes($baseName) . "');",
                $content
            );
            
            // Update BASE_NAME_FULL
            $content = preg_replace(
                "/define\('BASE_NAME_FULL',\s*'[^']*'\);/",
                "define('BASE_NAME_FULL', '" . addslashes($baseNameFull) . "');",
                $content
            );
            
            if (file_put_contents($constantsFile, $content)) {
                $updateSuccess = true;
            } else {
                $updateError = 'Failed to update file. Check permissions.';
            }
        }
    }
    
    if (isset($_POST['action']) && $_POST['action'] === 'logout') {
        unset($_SESSION['site_config_auth']);
        header('Location: site-config.php');
        exit;
    }
}

// Get current values
$constantsFile = __DIR__ . '/../app/Config/Constants.php';
$currentBaseName = '';
$currentBaseNameFull = '';

if (file_exists($constantsFile)) {
    $content = file_get_contents($constantsFile);
    
    // Extract BASE_NAME
    if (preg_match("/define\('BASE_NAME',\s*'([^']*)'\);/", $content, $matches)) {
        $currentBaseName = stripslashes($matches[1]);
    }
    
    // Extract BASE_NAME_FULL
    if (preg_match("/define\('BASE_NAME_FULL',\s*'([^']*)'\);/", $content, $matches)) {
        $currentBaseNameFull = stripslashes($matches[1]);
    }
}

// Check if authenticated
$isAuthenticated = isset($_SESSION['site_config_auth']) && $_SESSION['site_config_auth'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Configuration</title>
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

        .config-container {
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

        .config-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #6366f1, #8b5cf6, transparent);
            animation: borderGlow 3s ease-in-out infinite;
        }

        @keyframes borderGlow {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        .config-header {
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

        .config-header h2 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }

        .config-header p {
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

        .btn-update {
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

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5);
        }

        .btn-logout {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #ef4444;
            padding: 8px 16px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.3);
            border-color: #ef4444;
            color: #ef4444;
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

        .current-value {
            background: rgba(99, 102, 241, 0.05);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 1rem;
        }

        .current-value strong {
            color: #6366f1;
            font-size: 0.85rem;
            display: block;
            margin-bottom: 5px;
        }

        .current-value span {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
        }

        small.text-muted {
            color: rgba(255, 255, 255, 0.5) !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="config-container">
            <!-- Header -->
            <div class="config-header">
                <div class="logo-icon">
                    <i class="fas fa-cog"></i>
                </div>
                <h2>Site Configuration</h2>
                <p>Update your site name and branding</p>
            </div>
            
            <!-- Content -->
            <div class="p-4 pb-5">
                <?php if (!$isAuthenticated): ?>
                    <!-- Login Form -->
                    <form method="POST">
                        <input type="hidden" name="action" value="login">
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger mb-3">
                                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Enter Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control with-icon" id="password" name="password" 
                                       placeholder="Enter configuration password" required autofocus>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-update">
                                <i class="fas fa-sign-in-alt me-2"></i>Access Configuration
                            </button>
                        </div>
                        
                        <div class="alert alert-info mt-4" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Default Password:</strong> admin123<br>
                            <small>Change password in site-config.php file (line 5)</small>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- Configuration Form -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-white mb-0">Current Settings</h5>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="logout">
                            <button type="submit" class="btn btn-logout btn-sm">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </button>
                        </form>
                    </div>
                    
                    <?php if (isset($updateSuccess)): ?>
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-check-circle me-2"></i>Configuration updated successfully!
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($updateError)): ?>
                        <div class="alert alert-danger mb-3">
                            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($updateError) ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Current Values Display -->
                    <div class="current-value">
                        <strong>CURRENT SITE NAME (SHORT):</strong>
                        <span><?= htmlspecialchars($currentBaseName) ?></span>
                    </div>
                    
                    <div class="current-value">
                        <strong>CURRENT SITE NAME (FULL):</strong>
                        <span><?= htmlspecialchars($currentBaseNameFull) ?></span>
                    </div>
                    
                    <hr style="border-color: rgba(99, 102, 241, 0.2); margin: 1.5rem 0;">
                    
                    <!-- Update Form -->
                    <form method="POST">
                        <input type="hidden" name="action" value="update">
                        
                        <div class="mb-3">
                            <label for="base_name" class="form-label">Site Name (Short)</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-tag"></i>
                                </span>
                                <input type="text" class="form-control with-icon" id="base_name" name="base_name" 
                                       placeholder="Enter short site name" value="<?= htmlspecialchars($currentBaseName) ?>" required>
                            </div>
                            <small class="text-muted">This will be BASE_NAME constant</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="base_name_full" class="form-label">Site Name (Full)</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-heading"></i>
                                </span>
                                <input type="text" class="form-control with-icon" id="base_name_full" name="base_name_full" 
                                       placeholder="Enter full site name" value="<?= htmlspecialchars($currentBaseNameFull) ?>" required>
                            </div>
                            <small class="text-muted">This will be BASE_NAME_FULL constant</small>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-update">
                                <i class="fas fa-save me-2"></i>Update Configuration
                            </button>
                        </div>
                    </form>
                    
                    <div class="alert alert-info mt-4" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>File Location:</strong> app/Config/Constants.php<br>
                        <small>Changes will be applied immediately across the entire site</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Security measures
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });

        document.addEventListener('keydown', function(e) {
            if (e.keyCode === 123 || 
                (e.ctrlKey && e.shiftKey && e.keyCode === 73) || 
                (e.ctrlKey && e.shiftKey && e.keyCode === 74) || 
                (e.ctrlKey && e.key === 'u') || 
                (e.ctrlKey && e.key === 's')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>

