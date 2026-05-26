<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>

<!-- Enhanced Splash Screen -->
<div id="splash-screen" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: var(--dark-color); z-index: 9999; display: flex; justify-content: center; align-items: center; transition: all 0.8s ease-out;">
    <div class="splash-content" style="text-align: center; position: relative;">
        <!-- Animated Background Circles -->
        <div class="animated-circles">
            <div class="circle" style="position: absolute; width: 300px; height: 300px; border: 2px solid rgba(79, 70, 229, 0.1); border-radius: 50%; animation: rotate 10s linear infinite;"></div>
            <div class="circle" style="position: absolute; width: 250px; height: 250px; border: 2px solid rgba(124, 58, 237, 0.1); border-radius: 50%; animation: rotate 8s linear infinite reverse;"></div>
            <div class="circle" style="position: absolute; width: 200px; height: 200px; border: 2px solid rgba(6, 182, 212, 0.1); border-radius: 50%; animation: rotate 6s linear infinite;"></div>
        </div>

        <!-- Logo with enhanced animation -->
        <div class="splash-logo" style="width: 180px; height: 180px; margin: 0 auto 30px; position: relative; animation: float 3s ease-in-out infinite;">
            <img src="https://i.postimg.cc/fT3B6LyM/photo-2025-05-18-09-25-07.jpg" alt="Logo" style="width: 100%; height: 100%; object-fit: contain; filter: drop-shadow(0 0 20px rgba(79, 70, 229, 0.5));">
            <div class="logo-glow" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle, rgba(79, 70, 229, 0.2) 0%, transparent 70%); animation: pulse 2s infinite;"></div>
        </div>

        <!-- Enhanced Title -->
        <h1 style="font-size: 3.5em; margin-bottom: 20px; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: glow 1.5s infinite alternate; position: relative;">
            PRINCE VIP PANEL
            <div class="title-shine" style="position: absolute; top: 0; left: -100%; width: 50%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); animation: shine 3s infinite;"></div>
        </h1>

        <!-- Welcome Message -->
        <p style="color: rgba(255,255,255,0.8); font-size: 1.2em; margin-bottom: 30px; animation: fadeIn 1s ease-out;">
            Welcome to the Ultimate Gaming Experience
        </p>

        <!-- Enhanced Loading Bar -->
        <div class="loading-container" style="width: 300px; margin: 20px auto;">
            <div class="loading-bar" style="width: 100%; height: 4px; background: rgba(255, 255, 255, 0.1); border-radius: 2px; overflow: hidden; position: relative;">
                <div class="loading-progress" style="width: 0%; height: 100%; background: linear-gradient(90deg, var(--primary-color), var(--accent-color)); animation: loading 3s ease-in-out forwards;"></div>
            </div>
            <div class="loading-text" style="color: rgba(255,255,255,0.6); font-size: 0.9em; margin-top: 10px; animation: fadeIn 1s ease-out;">
                Loading your premium experience...
            </div>
        </div>

        <!-- Version Info -->
        <div class="version-info" style="position: absolute; bottom: -40px; left: 50%; transform: translateX(-50%); color: rgba(255,255,255,0.4); font-size: 0.8em;">
            Version 1.0.0
        </div>
    </div>
</div>

<style>
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @keyframes shine {
        0% { left: -100%; }
        20% { left: 100%; }
        100% { left: 100%; }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
        100% { transform: scale(1); opacity: 0.5; }
    }

    @keyframes glow {
        from { text-shadow: 0 0 5px rgba(79, 70, 229, 0.7), 0 0 10px rgba(79, 70, 229, 0.5); }
        to { text-shadow: 0 0 20px rgba(79, 70, 229, 1), 0 0 30px rgba(79, 70, 229, 0.8); }
    }

    @keyframes loading {
        0% { width: 0%; }
        100% { width: 100%; }
    }

    /* Hide main content initially */
    .row.justify-content-center {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.8s ease-out;
    }

    /* Show main content after splash screen */
    .row.justify-content-center.show {
        opacity: 1;
        transform: translateY(0);
    }

    :root {
        --primary-color: #4f46e5;
        --secondary-color: #7c3aed;
        --accent-color: #06b6d4;
        --dark-color: #0f172a;
        --light-color: #ffffff;
    }

    body {
        background: var(--dark-color);
        min-height: 100vh;
        font-family: 'Poppins', sans-serif;
        overflow-x: hidden;
        position: relative;
    }

    #particles-js {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }

    .login-container {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        padding: 40px;
        margin-top: 20px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        overflow: hidden;
        transform: translateY(0);
        transition: all 0.3s ease;
    }

    .login-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.3);
    }

    .login-header {
        text-align: center;
        margin-bottom: 40px;
        position: relative;
    }

    .login-header h3 {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        animation: titlePulse 2s ease-in-out infinite;
    }

    @keyframes titlePulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .form-group {
        position: relative;
        margin-bottom: 30px;
        animation: slideUp 0.5s ease-out forwards;
        opacity: 0;
    }

    .form-group:nth-child(1) { animation-delay: 0.2s; }
    .form-group:nth-child(2) { animation-delay: 0.4s; }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-control {
        background: rgba(255, 255, 255, 0.05);
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 16px;
        color: var(--light-color);
        font-size: 1rem;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--primary-color);
        box-shadow: 0 0 25px rgba(79, 70, 229, 0.2);
        transform: translateY(-2px);
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .form-label {
        color: var(--light-color);
        font-size: 0.9rem;
        margin-bottom: 10px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 600;
    }

    .btn-login {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: var(--light-color);
        border: none;
        border-radius: 16px;
        padding: 16px 32px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 2px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        width: 100%;
    }

    .btn-login::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.2),
            transparent
        );
        transition: 0.5s;
    }

    .btn-login:hover::before {
        left: 100%;
    }

    .btn-login:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(79, 70, 229, 0.3);
    }

    .register-link {
        color: var(--accent-color);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
        display: inline-block;
        margin-top: 20px;
    }

    .register-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--accent-color);
        transition: width 0.3s ease;
    }

    .register-link:hover::after {
        width: 100%;
    }

    .form-check {
        margin: 20px 0;
    }

    .form-check-input {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.2);
        width: 1.2em;
        height: 1.2em;
        margin-top: 0.2em;
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .form-check-label {
        color: var(--light-color);
        font-size: 0.9rem;
        margin-left: 8px;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.85rem;
        margin-top: 8px;
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .creator-info {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
    }

    .creator-info h3 {
        margin: 0;
        font-size: 1.2rem;
        color: var(--light-color);
    }

    .creator-info a {
        color: var(--accent-color);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .creator-info a:hover {
        color: var(--primary-color);
        text-shadow: 0 0 10px rgba(6, 182, 212, 0.5);
    }

    .footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        background: rgba(15, 23, 42, 0.9);
        backdrop-filter: blur(10px);
        color: var(--light-color);
        text-align: center;
        padding: 15px;
        font-size: 0.9rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    #time {
        color: var(--accent-color);
        font-weight: 600;
        font-family: 'Courier New', monospace;
    }
</style>

<!-- Particles.js Container -->
<div id="particles-js"></div>

<div class="row justify-content-center pt-5">
    <div class="col-lg-4">
        <?= $this->include('Layout/msgStatus') ?>
        
        <div class="creator-info">
            <h3>
                <span>𝐓𝐡𝐢𝐬 𝐏𝐚𝐧𝐞𝐥 𝐂𝐫𝐞𝐚𝐭𝐞𝐝 𝐁𝐲 :</span>
                <a href="https://telegram.me/" class="ms-2">@YourTelegram</a>
            </h3>
        </div>
        
        <div class="login-container">
            <div class="login-header">
                <h3>𝗟𝗢𝗚𝗜𝗡</h3>
            </div>

            <div class="box">
                <div class="col-lg-12">
                    <?= form_open() ?>
                    <div class="form-group">
                        <label for="username" class="form-label">𝑼𝒔𝒆𝒓𝒏𝒂𝒎𝒆 👤</label>
                        <input type="text" class="form-control" name="username" id="username" placeholder="𝚈𝒐𝒖𝒓 𝚄𝒔𝒆𝒓𝚗𝒂𝚖𝚎" required minlength="4">
                        <?php if ($validation->hasError('username')) : ?>
                            <div class="error-message"><?= $validation->getError('username') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">𝑷𝒂𝒔𝒔𝒘𝒐𝒓𝒅 🔐</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="𝚈𝒐𝒖𝒓 𝙿𝒂𝒔𝒔𝒘𝒐𝒓𝒅" required minlength="6">
                        <?php if ($validation->hasError('password')) : ?>
                            <div class="error-message"><?= $validation->getError('password') ?></div>
                        <?php endif; ?>
                    </div>

                    <input type="hidden" name="ip" value="<?php echo $_SERVER['HTTP_USER_AGENT']; ?>">

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="stay_log" id="stay_log" value="yes">
                        <label class="form-check-label" for="stay_log" data-bs-toggle="tooltip" data-bs-placement="top" title="Keep session more than 30 minutes">
                            Stay logged in
                        </label>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right"></i> 𝐋𝐨𝐠𝐢𝐧
                    </button>

                    <div class="text-center mt-4">
                        <a href="<?= site_url('register') ?>" class="register-link">Don't have an account? Register here</a>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>

        <p class="text-center mt-4">
            <small class="px-4 py-2 rounded" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px);">
                𝐁𝐔𝐘 𝐏𝐀𝐍𝐄𝐋 𝐃𝐌 :-
                <a href="https://telegram.me/" class="text-danger">@YourTelegram</a>
            </small>
        </p>
    </div>
</div>

<div class="footer">
    | Current Time: <span id="time"></span>
</div>

<!-- Add Particles.js -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

<script>
    // Enhanced splash screen handling
    document.addEventListener('DOMContentLoaded', function() {
        const splashScreen = document.getElementById('splash-screen');
        const mainContent = document.querySelector('.row.justify-content-center');
        
        // Hide splash screen and show main content after 3 seconds
        setTimeout(() => {
            splashScreen.style.opacity = '0';
            splashScreen.style.transform = 'scale(0.95)';
            mainContent.classList.add('show');
            
            // Remove splash screen from DOM after fade out
            setTimeout(() => {
                splashScreen.remove();
            }, 800);
        }, 3000);

        // Add particle effect to splash screen
        particlesJS('particles-js', {
            particles: {
                number: { value: 50, density: { enable: true, value_area: 800 } },
                color: { value: '#ffffff' },
                shape: { type: 'circle' },
                opacity: {
                    value: 0.5,
                    random: false,
                    anim: { enable: false }
                },
                size: {
                    value: 3,
                    random: true,
                    anim: { enable: false }
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: '#ffffff',
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: 'none',
                    random: false,
                    straight: false,
                    out_mode: 'out',
                    bounce: false
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: { enable: true, mode: 'repulse' },
                    onclick: { enable: true, mode: 'push' },
                    resize: true
                }
            },
            retina_detect: true
        });
    });

    // Update time
    function updateTime() {
        const now = new Date();
        document.getElementById("time").textContent = now.toLocaleTimeString();
    }
    updateTime();
    setInterval(updateTime, 1000);

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Add input animation
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.borderColor = '#4f46e5';
        });
        
        input.addEventListener('blur', function() {
            this.style.transform = 'translateY(0)';
            this.style.borderColor = 'rgba(255, 255, 255, 0.1)';
        });
    });
</script>

<?= $this->endSection() ?>