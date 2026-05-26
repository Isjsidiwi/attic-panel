<?php

include('conn.php');
include('mail.php');

// For Credits
$sql = "SELECT * FROM credit where id=1";
$result = mysqli_query($conn, $sql);
$credit = mysqli_fetch_assoc($result);

?>

<?= $this->extend('Layout/Starter') ?>
<?= $this->section('content') ?>

<style>
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

    .register-container {
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

    .register-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.3);
    }

    .register-header {
        text-align: center;
        margin-bottom: 40px;
        position: relative;
    }

    .register-header h3 {
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
    .form-group:nth-child(3) { animation-delay: 0.6s; }
    .form-group:nth-child(4) { animation-delay: 0.8s; }
    .form-group:nth-child(5) { animation-delay: 1.0s; }
    .form-group:nth-child(6) { animation-delay: 1.2s; }
    .form-group:nth-child(7) { animation-delay: 1.4s; }

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

    .btn-register {
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

    .btn-register::before {
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

    .btn-register:hover::before {
        left: 100%;
    }

    .btn-register:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(79, 70, 229, 0.3);
    }

    .login-link {
        color: var(--accent-color);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
        display: inline-block;
        margin-top: 20px;
    }

    .login-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--accent-color);
        transition: width 0.3s ease;
    }

    .login-link:hover::after {
        width: 100%;
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
        
        <div class="register-container">
            <div class="register-header">
                <h3>𝗥𝗘𝗚𝗜𝗦𝗧𝗘𝗥</h3>
            </div>

            <div class="box">
                <div class="col-lg-12">
                    <?= form_open() ?>
                    <div class="form-group">
                        <label for="email" class="form-label">𝑬𝒎𝒂𝒊𝒍 📧</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="𝚈𝒐𝒖𝒓 𝙴𝒎𝒂𝒊𝒍" minlength="13" maxlength="40" value="<?= old('email') ?>" required>
                        <?php if ($validation->hasError('email')) : ?>
                            <div class="error-message"><?= $validation->getError('email') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="username" class="form-label">𝑼𝒔𝒆𝒓𝒏𝒂𝒎𝒆 👤</label>
                        <input type="text" class="form-control" name="username" id="username" placeholder="𝚈𝒐𝒖𝒓 𝚄𝒔𝒆𝒓𝚗𝒂𝚖𝚎" minlength="4" maxlength="24" value="<?= old('username') ?>" required>
                        <?php if ($validation->hasError('username')) : ?>
                            <div class="error-message"><?= $validation->getError('username') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="fullname" class="form-label">𝑭𝒖𝒍𝒍𝒏𝒂𝒎𝒆 👤</label>
                        <input type="text" class="form-control" name="fullname" id="fullname" placeholder="𝚈𝒐𝒖𝒓 𝙵𝚞𝚕𝚕𝚗𝚊𝚖𝚎" minlength="4" maxlength="24" value="<?= old('fullname') ?>" required>
                        <?php if ($validation->hasError('fullname')) : ?>
                            <div class="error-message"><?= $validation->getError('fullname') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">𝑷𝒂𝒔𝒔𝒘𝒐𝒓𝒅 🔐</label>
                        <input type="password" class="form-control" name="password" id="password" placeholder="𝚈𝒐𝒖𝒓 𝙿𝒂𝒔𝒔𝒘𝒐𝒓𝒅" minlength="6" maxlength="24" required>
                        <?php if ($validation->hasError('password')) : ?>
                            <div class="error-message"><?= $validation->getError('password') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="password2" class="form-label">𝑪𝒐𝒏𝒇𝒊𝒓𝒎 𝑷𝒂𝒔𝒔𝒘𝒐𝒓𝒅 🔐</label>
                        <input type="password" class="form-control" name="password2" id="password2" placeholder="𝙲𝚘𝚗𝚏𝚒𝚛𝚖 𝙿𝚊𝚜𝚜𝚠𝚘𝚛𝚍" minlength="6" maxlength="24" required>
                        <?php if ($validation->hasError('password2')) : ?>
                            <div class="error-message"><?= $validation->getError('password2') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="referral" class="form-label">𝑹𝒆𝒇𝒆𝒓𝒓𝒂𝒍 𝑪𝒐𝒅𝒆 🎫</label>
                        <input type="text" class="form-control" name="referral" id="referral" placeholder="𝚈𝒐𝒖𝒓 𝚁𝒆𝒇𝒆𝒓𝒓𝒂𝒍 𝙲𝒐𝒅𝒆" value="<?= old('referral') ?>" maxlength="25" required>
                        <?php if ($validation->hasError('referral')) : ?>
                            <div class="error-message"><?= $validation->getError('referral') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="ip" class="form-label">𝑰𝑷 𝑨𝒅𝒅𝒓𝒆𝒔𝒔 🌐</label>
                        <input type="text" class="form-control" id="ip" placeholder="<?php echo $user_ip ?>" readonly>
                    </div>

                    <button type="submit" class="btn btn-register">
                        <i class="bi bi-box-arrow-in-right"></i> 𝐑𝐞𝐠𝐢𝐬𝐭𝐞𝐫
                    </button>

                    <div class="text-center mt-4">
                        <a href="<?= site_url('login') ?>" class="login-link">Already have an account? Login here</a>
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
    // Initialize Particles.js
    particlesJS('particles-js', {
        particles: {
            number: { value: 80, density: { enable: true, value_area: 800 } },
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