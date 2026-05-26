<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VIP PANEL - Register</title>
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
        }

        @keyframes gradientShift {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        .login-container {
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
        }

        .login-container::before {
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

        .login-header {
            text-align: center;
            padding: 2.5rem 2rem 2rem;
            border-bottom: 1px solid rgba(99, 102, 241, 0.1);
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.5);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 30px rgba(99, 102, 241, 0.5); }
            50% { transform: scale(1.05); box-shadow: 0 0 40px rgba(99, 102, 241, 0.8); }
        }

        .login-header h2 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }

        .login-header p {
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

        .btn-outline-secondary {
            background: rgba(99, 102, 241, 0.1);
            border-color: rgba(99, 102, 241, 0.3);
            color: #6366f1;
        }

        .btn-outline-secondary:hover {
            background: rgba(99, 102, 241, 0.2);
            border-color: #6366f1;
            color: #6366f1;
        }

        .form-check-input {
            background-color: rgba(20, 20, 20, 0.6);
            border-color: rgba(99, 102, 241, 0.3);
        }

        .form-check-input:checked {
            background-color: #6366f1;
            border-color: #6366f1;
        }

        .form-check-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        .btn-login {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            color: white;
            padding: 12px;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5);
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.3), transparent);
        }

        .divider span {
            background: rgba(20, 20, 20, 0.9);
            padding: 0 1rem;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
            position: relative;
            z-index: 1;
        }

        .register-link {
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .register-link:hover {
            color: #8b5cf6;
            text-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
        }

        .telegram-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
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
            font-size: 1.5rem;
        }

        .error-message {
            color: #f87171;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .alert-danger {
            background: rgba(248, 113, 113, 0.1);
            border: 1px solid rgba(248, 113, 113, 0.3);
            color: #f87171;
        }

        /* File upload styling */
        .file-upload-label {
            display: block;
            padding: 12px 15px;
            background: rgba(20, 20, 20, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-label:hover {
            background: rgba(20, 20, 20, 0.8);
            border-color: #6366f1;
        }

        .file-upload-label i {
            color: #6366f1;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100 py-4">
        <div class="row w-100">
            <div class="col-12 d-flex justify-content-center">
                <div class="login-container" style="width: 100%; max-width: 450px;">
                    <!-- Header -->
                    <div class="login-header">
                        <div class="logo-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h2>Create Account</h2>
                        <p>Join our VIP community today</p>
                    </div>
                    
                    <!-- Form -->
                    <div class="p-4 pb-5">
                        <!-- Status Messages -->
                        <div id="formMsg" class="mb-3">
                            <?= $this->include('Layout/msgStatus') ?>
                        </div>

                        <?= form_open_multipart('register', ['id' => 'registerForm']) ?>
                            <!-- Username Field -->
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control with-icon" id="username" name="username" 
                                           placeholder="Enter your username" value="<?= old('username') ?>" required>
                                </div>
                                <?php if ($validation->hasError('username')) : ?>
                                    <div class="error-message"><?= $validation->getError('username') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Password Field -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control with-icon" id="password" name="password" 
                                           placeholder="Enter your password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePwd">
                                        <i class="fas fa-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                                <?php if ($validation->hasError('password')) : ?>
                                    <div class="error-message"><?= $validation->getError('password') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Confirm Password Field -->
                            <div class="mb-3">
                                <label for="password2" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control with-icon" id="password2" name="password2" 
                                           placeholder="Confirm your password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePwd2">
                                        <i class="fas fa-eye" id="eyeIcon2"></i>
                                    </button>
                                </div>
                                <?php if ($validation->hasError('password2')) : ?>
                                    <div class="error-message"><?= $validation->getError('password2') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Referral Code Field -->
                            <div class="mb-3">
                                <label for="referral" class="form-label">Referral Code</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-gift"></i>
                                    </span>
                                    <input type="text" class="form-control with-icon" id="referral" name="referral" 
                                           placeholder="Enter referral code" value="<?= old('referral') ?>" required>
                                </div>
                                <?php if ($validation->hasError('referral')) : ?>
                                    <div class="error-message"><?= $validation->getError('referral') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Profile Image Field -->
                            <div class="mb-3">
                                <label for="image" class="form-label">Profile Image</label>
                                <label for="image" class="file-upload-label">
                                    <i class="fas fa-camera"></i>
                                    <span id="fileText">Choose an image</span>
                                </label>
                                <input type="file" class="d-none" id="image" name="image" accept="image/*" required>
                                <?php if ($validation->hasError('image')) : ?>
                                    <div class="error-message"><?= $validation->getError('image') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Stay Logged In -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="stay_login" id="stayLogin">
                                    <label class="form-check-label" for="stayLogin">
                                        Stay logged in
                                    </label>
                                </div>
                            </div>

                            <!-- Register Button -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-login">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                </button>
                            </div>

                            <!-- Divider -->
                            <div class="divider">
                                <span>OR</span>
                            </div>

                            <!-- Login Link -->
                            <div class="text-center">
                                <span style="color: rgba(255, 255, 255, 0.5);">Already have an account? </span>
                                <a href="<?= site_url('login') ?>" class="register-link">Sign In</a>
                            </div>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Telegram Icon -->
    <div class="telegram-icon">
        <a href="https://telegram.me/aalyanmods" target="_blank">
            <i class="fab fa-telegram"></i>
        </a>
    </div>
            
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password toggle functionality
        document.getElementById('togglePwd').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
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

        document.getElementById('togglePwd2').addEventListener('click', function() {
            const passwordField = document.getElementById('password2');
            const eyeIcon = document.getElementById('eyeIcon2');
            
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

        // File upload functionality
        document.getElementById('image').addEventListener('change', function() {
            const fileName = this.files[0]?.name;
            if (fileName) {
                document.getElementById('fileText').textContent = fileName;
            }
        });

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const password2 = document.getElementById('password2').value;
            const referral = document.getElementById('referral').value.trim();
            const msgDiv = document.getElementById('formMsg');

            // Clear previous messages
            msgDiv.innerHTML = '';

            // Client-side validation
            if (username.length < 4) {
                e.preventDefault();
                msgDiv.innerHTML = '<div class="alert alert-danger">Username must be at least 4 characters.</div>';
                document.getElementById('username').focus();
                return false;
            }
            
            if (password.length < 5) {
                e.preventDefault();
                msgDiv.innerHTML = '<div class="alert alert-danger">Password must be at least 5 characters.</div>';
                document.getElementById('password').focus();
                return false;
            }

            if (password !== password2) {
                e.preventDefault();
                msgDiv.innerHTML = '<div class="alert alert-danger">Passwords do not match.</div>';
                document.getElementById('password2').focus();
                return false;
            }

            if (referral.length < 5) {
                e.preventDefault();
                msgDiv.innerHTML = '<div class="alert alert-danger">Referral code must be at least 5 characters.</div>';
                document.getElementById('referral').focus();
                return false;
            }

            // If validation passes, let form submit
            return true;
        });

        // Security measures
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        document.addEventListener('keydown', function(e) {
            // Block F12
            if (e.keyCode === 123) {
                e.preventDefault();
                return false;
            }
            
            // Block Ctrl+U
            if (e.ctrlKey && e.key === 'u') {
                e.preventDefault();
                return false;
            }
            
            // Block Ctrl+S
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>
