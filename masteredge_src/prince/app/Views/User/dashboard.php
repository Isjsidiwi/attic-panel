<?php

include('conn.php');
include('mail.php');
include('UserMail.php');

// For Credits
$sql = "SELECT * FROM credit where id=1";
$result = mysqli_query($conn, $sql);
$credit = mysqli_fetch_assoc($result);

// For Keys count
$sql = "SELECT COUNT(*) as id_keys FROM keys_code";
$result = mysqli_query($conn, $sql);
$keycount = mysqli_fetch_assoc($result);

// For Active Keys count
$sql = "SELECT COUNT(devices) as devices FROM keys_code";
$result = mysqli_query($conn, $sql);
$active = mysqli_fetch_assoc($result);

// For In-Active Keys Count
$sql = "SELECT COUNT(*) as devices FROM keys_code where devices IS NULL";
$result = mysqli_query($conn, $sql);
$inactive = mysqli_fetch_assoc($result);

// For Users Count
$sql = "SELECT COUNT(*) as id_users FROM users";
$result = mysqli_query($conn, $sql);
$users = mysqli_fetch_assoc($result);

$userid = session()->userid;
$sql = "SELECT `expiration_date` FROM `users` WHERE `id_users` = '".$userid."'";
$query = mysqli_query($conn, $sql);
$period = mysqli_fetch_assoc($query);

function HoursToDays($value)
{
    if($value == 1) {
       return "$value Hour";
    } else if($value >= 2 && $value < 24) {
       return "$value Hours";
    } else if($value == 24) {
       $darkespyt = $value/24;
       return "$darkespyt Day";
    } else if($value > 24) {
       $darkespyt = $value/24;
       return "$darkespyt Days";
    }
}

$dateTime = strtotime($period['expiration_date']);
$getDateTime = date("F d, Y H:i:s", $dateTime);
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

    .dashboard-container {
        padding: 20px;
    }

    .card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        transition: all 0.3s ease;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.3);
    }

    .card-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: var(--light-color);
        font-weight: 600;
        border-bottom: none;
        padding: 1.5rem;
        font-size: 1.2rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .expiration-timer {
        font-family: 'Nova Mono', monospace;
        font-size: 2.5rem;
        text-align: center;
        color: var(--accent-color);
        text-shadow: 0 0 20px rgba(6, 182, 212, 0.5);
        animation: titlePulse 2s infinite;
    }

    @keyframes titlePulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .stats-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(79, 70, 229, 0.2);
    }

    .stats-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--accent-color);
        text-shadow: 0 0 10px rgba(6, 182, 212, 0.3);
    }

    .stats-label {
        color: var(--light-color);
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .table {
        color: var(--light-color);
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        overflow: hidden;
        border-collapse: collapse !important;
        border-spacing: 0 !important;
        margin-bottom: 0;
    }

    .table thead th {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: var(--light-color);
        font-weight: 600;
        border: none !important;
        padding: 1rem;
        outline: none !important;
        box-shadow: none !important;
    }

    .table td,
    .table th {
        border: none !important;
        padding: 1rem;
        vertical-align: middle;
        background-color: transparent !important;
        outline: none !important;
        box-shadow: none !important;
    }

    .table tbody tr {
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
    }

    .table tbody tr:nth-child(odd),
    .table tbody tr:nth-child(even) {
        background-color: transparent !important;
    }

    .table tbody tr:hover {
        background: rgba(255, 255, 255, 0.05) !important;
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: var(--light-color);
        border: none;
        border-radius: 16px;
        padding: 0.8rem 1.5rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(79, 70, 229, 0.3);
    }

    .btn-white {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn-white:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .chart-container {
        position: relative;
        margin: auto;
        height: 300px;
        padding: 1rem;
    }

    .telegram-float {
        position: fixed;
        bottom: 70px;
        right: 40px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        text-align: center;
        font-size: 30px;
        box-shadow: 0 15px 30px rgba(79, 70, 229, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        z-index: 1000;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .telegram-float:hover {
        transform: scale(1.1) translateY(-5px);
        box-shadow: 0 20px 40px rgba(79, 70, 229, 0.4);
    }
</style>

<!-- Particles.js Container -->
<div id="particles-js"></div>

<div class="dashboard-container">
    <div class="row">
        <div class="col-lg-12">
            <?= $this->include('Layout/msgStatus') ?>
        </div>

        <!-- Expiration Timer Card -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-clock"></i> 𝐄𝐱𝐩𝐫𝐚𝐭𝐢𝐨𝐧 𝐓𝐢𝐦𝐞𝐫
                </div>
                <div class="card-body">
                    <div class="expiration-timer" id="exp"></div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-graph-up"></i> 𝐒𝐭𝐚𝐭𝐬 𝐎𝐯𝐞𝐫𝐯𝐢𝐞��
                </div>
                <div class="card-body">
                    <div class="stats-card">
                        <div class="stats-value"><?php echo $keycount['id_keys']; ?></div>
                        <div class="stats-label">Total Keys</div>
                    </div>
                    <div class="stats-card">
                        <div class="stats-value"><?php echo $active['devices']; ?></div>
                        <div class="stats-label">Active Keys</div>
                    </div>
                    <div class="stats-card">
                        <div class="stats-value"><?php echo $inactive['devices']; ?></div>
                        <div class="stats-label">Inactive Keys</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-bar-chart"></i> 𝐊𝐞𝐲 𝐔𝐬𝐚𝐠𝐞 𝐀𝐧𝐚𝐥𝐲𝐬𝐢𝐬
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="usageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> 𝐇𝐢𝐬𝐭𝐨𝐫𝐲
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Info</th>
                                    <th>Key</th>
                                    <th>Days</th>
                                    <th>Devices</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $h) : ?>
                                    <?php $in = explode("|", $h->info) ?>
                                    <tr>
                                        <td><span class="badge">#3812<?= $h->id_history ?></span></td>
                                        <td><?= $in[0] ?></td>
                                        <td><span class="badge"><?= $in[1] ?>**</span></td>
                                        <td><span class="badge"><?= $in[2] ?> Days</span></td>
                                        <td><span class="badge"><?= $in[3] ?> Devices</span></td>
                                        <td><i class="badge"><?= $time::parse($h->created_at)->humanize() ?></i></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if (($user->level == 2) || ($user->level == 3)) : ?>
                        <div class="form-group mb-2 text-center">
                            <button type="button" class="btn" onclick="deleteAllUserKeys()">
                                <i class="bi bi-trash-fill"></i> Delete All History Records
                            </button>
                        </div>
                        <?php endif; ?>
                        <?php if($user->level == 1) : ?>
                        <div class="form-group mb-2 text-center">
                            <button type="button" class="btn btn-white" onclick="deleteAllUserKeyss()">
                                <i class="bi bi-trash-fill"></i> Delete All History Records
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Telegram Button -->
<a href="https://t.me/" class="telegram-float" target="_blank">
    <i class="fa-brands fa-telegram"></i>
</a>

<!-- Add Particles.js -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

    // Expiration Timer
    var countDownTimer = new Date("<?= $getDateTime ?>").getTime();
    var interval = setInterval(function() {
        var current = new Date().getTime();
        var diff = countDownTimer - current;
        var days = Math.floor(diff / (1000 * 60 * 60 * 24));
        var hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((diff % (1000 * 60)) / 1000);

        document.getElementById("exp").innerHTML = days + "d : " + hours + "h " +
            minutes + "m " + seconds + "s ";
        if (diff < 0) {
            clearInterval(interval);
            document.getElementById("exp").innerHTML = "EXPIRED";
        }
    }, 1000);

    // Chart.js Usage Chart
    var ctx = document.getElementById('usageChart').getContext('2d');
    var usageChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Active Keys', 'Inactive Keys'],
            datasets: [{
                data: [<?= $active['devices'] ?>, <?= $inactive['devices'] ?>],
                backgroundColor: [
                    'rgba(6, 182, 212, 0.7)',
                    'rgba(79, 70, 229, 0.7)'
                ],
                borderColor: [
                    'rgba(6, 182, 212, 1)',
                    'rgba(79, 70, 229, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: '#fff',
                        font: { size: 16 }
                    }
                }
            }
        }
    });
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css"/>

<?= $this->endSection() ?>