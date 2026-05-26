<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>
<style>
    :root {
        --primary-color: #4f46e5;
        --secondary-color: #7c3aed;
        --accent-color: #06b6d4;
        --dark-color: #0f172a;
        --light-color: #ffffff;
        --glass-background: rgba(255, 255, 255, 0.05);
        --glass-border: 1px solid rgba(255, 255, 255, 0.1);
        --glass-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        --glass-blur: blur(20px);
    }
    body {
        background: var(--dark-color);
        font-family: 'Poppins', sans-serif;
        overflow-x: hidden;
        position: relative;
        min-height: 100vh;
    }
    #particles-js {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }
    .card {
        background: var(--glass-background);
        border-radius: 24px;
        box-shadow: var(--glass-shadow);
        border: var(--glass-border);
        backdrop-filter: var(--glass-blur);
        margin-top: 30px;
        transition: all 0.3s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px) scale(1.005);
        box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.3);
    }
    .card-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: var(--light-color);
        font-weight: 600;
        border-radius: 24px 24px 0 0;
        font-size: 1.5rem;
        letter-spacing: 1px;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .card-header .col-pt-1 {
        display: flex;
        align-items: center;
    }
    .card-header .col-pt-1 i {
        margin-right: 15px;
        font-size: 1.8rem;
    }
    .btn-secondary {
        background-color: rgba(255, 255, 255, 0.15) !important;
        border-color: rgba(255, 255, 255, 0.2) !important;
        color: var(--light-color) !important;
        transition: all 0.2s ease;
        border-radius: 16px;
        padding: 0.8rem 1.5rem;
    }
    .btn-secondary:hover {
        background-color: rgba(255, 255, 255, 0.25) !important;
        transform: translateY(-1px);
    }

    /* Dropdown Menu */
    .navbar-nav .dropdown-menu {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        padding: 10px 0;
        min-width: 220px;
    }
    .navbar-nav .dropdown-toggle {
        color: var(--light-color) !important;
        font-weight: 500;
        transition: color 0.2s;
    }
    .navbar-nav .dropdown-toggle:hover {
        color: var(--accent-color) !important;
    }
    .dropdown-menu .card-header {
        background: none;
        color: var(--dark-color);
        padding: 1rem 1.5rem;
        font-size: 1rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-radius: 12px 12px 0 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }
    .dropdown-item {
        color: var(--light-color);
        padding: 10px 20px;
        transition: background-color 0.2s, color 0.2s;
        border-radius: 10px;
        margin: 5px 10px;
    }
    .dropdown-item:hover {
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        color: var(--light-color);
        transform: translateX(5px);
    }
    .dropdown-item i {
        margin-right: 10px;
        font-size: 1.1em;
    }

    /* Table Styling */
    .table {
        color: var(--light-color);
        background: rgba(255,255,255,0.03);
        border-radius: 16px;
        overflow: hidden;
        border-collapse: collapse !important;
        border-spacing: 0 !important;
        --bs-table-bg: transparent !important;
        --bs-table-border-width: 0 !important;
    }
    .table th, .table td {
        border-color: transparent !important;
        vertical-align: middle;
        padding: 1rem;
        background-color: transparent !important;
    }
    .table thead th {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        font-weight: 600;
        color: var(--light-color);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none !important;
    }
    .table tbody tr:hover {
        background: rgba(255, 255, 255, 0.05);
        transform: scale(1.005);
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .table tbody tr {
        transition: all 0.2s ease;
        border: none !important;
    }
    .badge {
        padding: 0.5em 0.8em;
        border-radius: 15px;
        font-weight: 600;
        letter-spacing: 0.5px;
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    /* Key and Copy Button */
    .keyBlur.key-sensi {
        filter: blur(6px);
        user-select: none;
        transition: filter 0.2s;
    }
    .keyBlur {
        font-family: 'Fira Mono', monospace;
        font-size: 1.05em;
        letter-spacing: 1px;
        background: rgba(0,0,0,0.08);
        border-radius: 6px;
        padding: 3px 10px;
        margin-right: 5px;
        transition: filter 0.2s;
        color: var(--light-color);
    }
    .btn-copy {
        background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 0.9rem;
        margin-left: 5px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .btn-copy:hover {
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .copy-tooltip {
        display: none;
        position: absolute;
        top: -35px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--accent-color);
        color: #fff;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        z-index: 10;
        opacity: 0;
        transition: opacity 0.3s ease, top 0.3s ease;
        pointer-events: none;
        white-space: nowrap;
    }
    .copy-tooltip.show {
        display: block;
        opacity: 1;
        top: -45px;
    }

    /* Action Buttons */
    .btn-action {
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        margin: 0 4px;
        background: transparent;
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .btn-outline-danger { border-color: var(--primary-color); }
    .btn-outline-danger i { color: var(--primary-color) !important; }
    .btn-outline-danger:hover {
        background: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: #fff !important;
    }
    .btn-outline-danger:hover i { color: #fff !important; }

    .btn-outline-warning { border-color: #ffc107; }
    .btn-outline-warning i { color: #ffc107 !important; }
    .btn-outline-warning:hover {
        background: #ffc107 !important;
        border-color: #ffc107 !important;
        color: #fff !important;
    }
    .btn-outline-warning:hover i { color: #fff !important; }

    .btn-outline-info { border-color: var(--secondary-color); }
    .btn-outline-info i { color: var(--secondary-color) !important; }
    .btn-outline-info:hover {
        background: var(--secondary-color) !important;
        border-color: var(--secondary-color) !important;
        color: #fff !important;
    }
    .btn-outline-info:hover i { color: #fff !important; }

    .d-grid.gap-2.d-md-block {
        display: flex !important;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
        color: var(--light-color) !important;
        border-radius: 8px;
        border: none !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: var(--light-color) !important;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgba(255, 255, 255, 0.2) !important;
        color: var(--accent-color) !important;
        border: 1px solid var(--accent-color) !important;
    }
    .dataTables_filter input,
    .dataTables_length select {
        background: rgba(255,255,255,0.1);
        border: 1.5px solid rgba(255,255,255,0.15);
        color: var(--light-color);
        border-radius: 12px;
        padding: 0.85rem 1.2rem;
        transition: border 0.3s, background 0.3s, box-shadow 0.3s;
        font-size: 1rem;
    }
    .dataTables_filter input:focus,
    .dataTables_length select:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.4);
        background: rgba(255,255,255,0.15);
        color: var(--light-color);
    }
    .dataTables_info, .dataTables_length label, .dataTables_filter label {
        color: var(--light-color);
    }

</style>
<!-- Particles.js Container -->
<div id="particles-js"></div>

<div class="row justify-content-center">
    <div class="row">
        <div class="col-lg-12">
            <?= $this->include('Layout/msgStatus') ?>
        </div>
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="col-pt-1">
                        <i class="bi bi-key"></i> Keys Registered
                        <button class="btn btn-secondary btn-sm ms-3" id="blur-out" data-bs-toggle="tooltip" data-bs-placement="top" title="Eye Protect"><i class="bi bi-eye-slash"></i></button>
                    </div>
                    <div class="col text-end">
                        <div class="float-right">
                            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-file-arrow-down-fill"></i> Open Menu
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start" aria-labelledby="navbarDropdown">
                                        <li>
                                            <a class="dropdown-item" href="<?= site_url('keys/generate') ?>">
                                                <i class="bi bi-plus-circle" style="color: var(--primary-color);"></i> 𝐆𝐄𝐍𝐄𝐑𝐀𝐓𝐄 𝐊𝐄𝐘
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="<?= site_url('keys/deleteExp')  ?>">
                                                <i class="bi bi-eraser" style="color: var(--secondary-color);"></i> 𝐃𝐄𝐋𝐄𝐓𝐄 𝐄𝐗𝐏𝐈𝐑𝐄 𝐊𝐄𝐘
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="<?= site_url('keys/deleteUnused')  ?>">
                                                <i class="bi bi-box" style="color: var(--accent-color);"></i> 𝐃𝐄𝐋𝐄𝐓𝐄 𝐔𝐍-𝐔𝐒𝐄𝐃 𝐊𝐄𝐘
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($keylist) : ?>
                        <div class="table-responsive">
                            <table id="datatable" class="table table-hover text-center" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Game</th>
                                        <th>User Keys</th>
                                        <th>Devices</th>
                                        <th>Duration</th>
                                        <th>Expired</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTables will populate this tbody -->
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <p class="text-center">Nothing keys to show</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<?= link_tag("https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css") ?>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<?= script_tag("https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js") ?>
<?= script_tag("https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js") ?>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script>
    $(document).ready(function() {
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

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, "desc"]
            ],
            ajax: {
                url: "<?= site_url('keys/api') ?>",
                error: function (xhr, error, thrown) {
                    console.error("DataTables AJAX Error:", thrown);
                    console.error("Response Text:", xhr.responseText);
                    // You can add a user-friendly message here if needed
                    // $('#datatable').html('<p class="text-danger text-center">Failed to load data. Please try again later.</p>');
                }
            },
            columns: [
                { data: 'id', name: 'id_keys' },
                { data: 'game' },
                {
                    data: 'user_key',
                    render: function(data, type, row, meta) {
                        var is_valid = (row.status == 'Active') ? "text-success" : "text-danger";
                        return `
                            <div style="position:relative;display:inline-block;">
                                <span class="${is_valid} keyBlur key-sensi" id="key-${row.id}">${(row.user_key ? row.user_key : '&mdash;')}</span>
                                <button class="btn btn-copy btn-sm ms-1" onclick="copyKeyToClipboard('key-${row.id}', this)">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                                <span class="copy-tooltip" id="tooltip-key-${row.id}">Copied!</span>
                            </div>
                        `;
                    }
                },
                {
                    data: 'devices',
                    render: function(data, type, row, meta) {
                        var totalDevice = (row.devices ? row.devices : 0);
                        return `<span id="devMax-${row.user_key}">${totalDevice}/${row.max_devices}</span>`;
                    }
                },
                { data: 'duration', render: function(data, type, row, meta) { return row.duration; } },
                {
                    data: 'expired',
                    name: 'expired_date',
                    render: function(data, type, row, meta) {
                        return row.expired ? `<span class="badge text-dark">${row.expired}</span>` : '(not started yet)';
                    }
                },
                {
                    data: null,
                    render: function(data, type, row, meta) {
                        var btnReset = `<button class="btn btn-outline-danger btn-sm btn-action" onclick="resetUserKey('${row.user_key}')" data-bs-toggle="tooltip" data-bs-placement="left" title="Reset key?"><i class="bi bi-bootstrap-reboot"></i></button>`;
                        var btnalterOne = `<button class="btn btn-outline-warning btn-sm btn-action" onclick="resetUserKey1('${row.user_key}')" data-bs-toggle="tooltip" data-bs-placement="left" title="DELETE KEY?"><i class="bi bi-trash-fill"></i></button>`;
                        var btnEdits = `<a href="${window.location.origin}/keys/${row.id}" class="btn btn-outline-info btn-sm btn-action" data-bs-toggle="tooltip" data-bs-placement="left" title="Edit key information?"><i class="bi bi-person"></i></a>`;
                        return `<div class="d-grid gap-2 d-md-block">${btnReset} ${btnalterOne} ${btnEdits}</div>`;
                    }
                }
            ]
        });

        $("#blur-out").click(function() {
            if ($(".keyBlur").hasClass("key-sensi")) {
                $(".keyBlur").removeClass("key-sensi");
                $("#blur-out").html(`<i class="bi bi-eye"></i>`);
            } else {
                $(".keyBlur").addClass("key-sensi");
                $("#blur-out").html(`<i class="bi bi-eye-slash"></i>`);
            }
        });
    });

    function copyKeyToClipboard(elementId, btn) {
        var keyText = document.getElementById(elementId).innerText;
        navigator.clipboard.writeText(keyText).then(function() {
            var tooltip = document.getElementById('tooltip-' + elementId);
            tooltip.classList.add('show');
            setTimeout(function() {
                tooltip.classList.remove('show');
            }, 1200);
        });
    }

    function resetUserKey1(keys) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                Toast.fire({
                    icon: 'info',
                    title: 'Please wait...'
                })

                var base_url = window.location.origin;
                var api_url = `${base_url}/keys/resetAll`;
                $.getJSON(api_url, {
                        userkey: keys,
                        reset: 1
                    },
                    function(data, textStatus, jqXHR) {
                        if (textStatus == 'success') {
                            if (data.registered) {
                                if (data.reset) {
                                    $(`#devMax-${keys}`).html(`0/${data.devices_max}`);
                                    Swal.fire(
                                        'Reset!',
                                        'Your device key has been reset.',
                                        'success'
                                    )
                                } else {
                                    Swal.fire(
                                        'Failed!',
                                        data.devices_total ? "You don't have any access to this user." : "User key devices already reset.",
                                        data.devices_total ? 'error' : 'warning'
                                    )
                                }
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    "User key no longer exists.",
                                    'error'
                                )
                            }
                        }
                    }
                );
            }
        });
    }
    function resetUserKey(keys) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reset'
        }).then((result) => {
            if (result.isConfirmed) {
                Toast.fire({
                    icon: 'info',
                    title: 'Please wait...'
                })

                var base_url = window.location.origin;
                var api_url = `${base_url}/keys/reset`;
                $.getJSON(api_url, {
                        userkey: keys,
                        reset: 1
                    },
                    function(data, textStatus, jqXHR) {
                        if (textStatus == 'success') {
                            if (data.registered) {
                                if (data.reset) {
                                    $(`#devMax-${keys}`).html(`0/${data.devices_max}`);
                                    Swal.fire(
                                        'Reset!',
                                        'Your device key has been reset.',
                                        'success'
                                    )
                                } else {
                                    Swal.fire(
                                        'Failed!',
                                        data.devices_total ? "You don't have any access to this user." : "User key devices already reset.",
                                        data.devices_total ? 'error' : 'warning'
                                    )
                                }
                            } else {
                                Swal.fire(
                                    'Failed!',
                                    "User key no longer exists.",
                                    'error'
                                )
                            }
                        }
                    }
                );
            }
        });
    }
</script>
<?= $this->endSection() ?>