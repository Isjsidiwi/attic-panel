<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="row">
        <div class="col-lg-12">
            <?= $this->include('Layout/msgStatus') ?>
        </div>
        <div class="col-lg-12">
            <div class="card shadow-lg border-0" style="background: rgba(20, 20, 20, 0.8); border: 1px solid rgba(99, 102, 241, 0.2) !important; border-radius: 20px; backdrop-filter: blur(10px); position: relative; overflow: hidden;">
                <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #6366f1, #8b5cf6, transparent); animation: borderGlow 3s ease-in-out infinite;"></div>
                <div class="card-header text-white" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); border-bottom: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px 20px 0 0;">
                    <div class="row">
                        <div class="col pt-1">
                            <h5 class="mb-0 text-white"><i class="bi bi-key-fill me-2"></i>REGISTERED KEYS</h5>
                        </div>
                        <div class="col text-end">
                            <a class="btn btn-outline-light btn-sm" href="<?= site_url('keys/generate') ?>">
                                <i class="fa-solid fa-file-plus fa-flip" style="--fa-animation-duration: 4s; color: #00ffbb;"></i>
                                <span class="ms-1 text-white">Generate</span>
                            </a>
                            <a class="btn btn-outline-light btn-sm" href="<?= site_url('keys/name-generate') ?>">
                                <i class="fa-solid fa-file-pen fa-flip" style="--fa-animation-duration: 8s; color: #00ffbb;"></i>
                                <span class="ms-1 text-white">Name Generate</span>
                            </a>
                           
                            <button class="btn btn-light btn-sm ms-1" id="blur-out" data-bs-toggle="tooltip" 
                                    data-bs-placement="top" title="Eye Protect">
                                <i class="bi bi-eye-slash"></i>
                                <span class="ms-1">Hide</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($keylist) : ?>
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-hover text-center align-middle" style="width:100%">
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
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <div class="text-center py-5">
                            <i class="bi bi-key-fill text-muted" style="font-size: 3rem;"></i>
                            <p class="mt-2" style="color: #e6eef8;">No keys available</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<?= link_tag("https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css") ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
    @keyframes borderGlow {
        0%, 100% { opacity: 0.5; }
        50% { opacity: 1; }
    }

    .card-body {
        background: transparent !important;
        color: #e6eef8 !important;
    }

    .reset-all-btn {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border: none;
        color: white !important;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
        position: relative;
        overflow: hidden;
    }

    .reset-all-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .reset-all-btn:hover::before {
        left: 100%;
    }

    .reset-all-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.5);
    }

    .reset-all-btn span {
        color: white !important;
    }

    .table {
        color: #e6eef8 !important;
        background: transparent !important;
    }

    .table thead th {
        font-weight: 600;
        background: rgba(99, 102, 241, 0.1) !important;
        color: #e6eef8 !important;
        border-color: rgba(99, 102, 241, 0.2) !important;
    }

    .table tbody tr {
        background: rgba(99, 102, 241, 0.02) !important;
        border-color: rgba(99, 102, 241, 0.1) !important;
    }

    .table tbody tr:hover {
        background: rgba(99, 102, 241, 0.08) !important;
    }

    .table tbody td {
        border-color: rgba(99, 102, 241, 0.1) !important;
        color: #e6eef8 !important;
    }

    .badge {
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 8px;
    }

    .bg-info {
        background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
        color: white !important;
    }

    .bg-primary {
        background: rgba(99, 102, 241, 0.9) !important;
        color: white !important;
    }

    .bg-warning {
        background: rgba(251, 191, 36, 0.9) !important;
        color: #000 !important;
    }

    .bg-secondary {
        background: rgba(148, 163, 184, 0.9) !important;
        color: white !important;
    }

    .key-sensi {
        filter: blur(4px);
        transition: all 0.3s ease;
    }

    .btn {
        transition: all 0.3s ease;
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
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn:hover::before {
        left: 100%;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .btn-outline-danger {
        border-color: rgba(239, 68, 68, 0.5);
        color: #ef4444;
    }

    .btn-outline-danger:hover {
        background: rgba(239, 68, 68, 0.1);
        border-color: #ef4444;
        color: #ef4444;
    }

    .btn-outline-primary {
        border-color: rgba(99, 102, 241, 0.5);
        color: #6366f1;
    }

    .btn-outline-primary:hover {
        background: rgba(99, 102, 241, 0.1);
        border-color: #6366f1;
        color: #6366f1;
    }

    .btn-outline-light {
        border-color: rgba(255, 255, 255, 0.3);
        color: white;
    }

    .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: white;
        color: white;
    }

    .btn-light {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.3);
        color: white;
    }

    .btn-light:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    /* DataTables Customization */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_processing,
    .dataTables_wrapper .dataTables_paginate {
        color: #e6eef8 !important;
    }

    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        background: rgba(20, 20, 20, 0.6);
        border: 1px solid rgba(99, 102, 241, 0.3);
        color: #e6eef8;
        border-radius: 8px;
        padding: 5px 10px;
    }

    .dataTables_wrapper .dataTables_length select:focus,
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #6366f1;
        outline: none;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #6366f1 !important;
        background: rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.2);
        border-radius: 8px;
        margin: 0 2px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgba(99, 102, 241, 0.1) !important;
        border-color: #6366f1 !important;
        color: #6366f1 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
        border-color: #6366f1 !important;
        color: white !important;
    }

    /* Sweet Alert Dark Theme */
    .swal2-popup {
        background: rgba(20, 20, 20, 0.95) !important;
        border: 1px solid rgba(99, 102, 241, 0.3);
        border-radius: 20px !important;
        backdrop-filter: blur(10px);
    }

    .swal2-title {
        color: #e6eef8 !important;
    }

    .swal2-html-container {
        color: #e6eef8 !important;
    }

    .swal2-confirm {
        background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
        border-radius: 12px !important;
        color: white !important;
        box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
    }

    .swal2-cancel {
        background: rgba(148, 163, 184, 0.2) !important;
        border: 1px solid rgba(148, 163, 184, 0.5);
        border-radius: 12px !important;
        color: #94a3b8 !important;
    }

    /* Custom text colors */
    .text-key {
        color: #e6eef8 !important;
    }

    .text-status {
        font-weight: 500;
    }

    .text-dark {
        color: #e6eef8 !important;
    }

    .bg-light {
        background: rgba(99, 102, 241, 0.1) !important;
    }

    .text-muted {
        color: rgba(255, 255, 255, 0.5) !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<?= script_tag("https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js") ?>
<?= script_tag("https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js") ?>
<script>
    $(document).ready(function() {
        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            order: [[0, "desc"]],
            ajax: "<?= site_url('keys/api') ?>",
            columns: [{
                    data: 'id',
                    name: 'id_keys'
                },
                {
                    data: 'game'
                },
                {
                    data: 'user_key',
                    render: function(data, type, row, meta) {
                        var is_valid = (row.status == 'Active') ? "text-success" : "text-danger";
                        return `<span class="${is_valid} keyBlur key-sensi text-key">${(row.user_key ? row.user_key : '&mdash;')}</span>`;
                    }
                },
                {
                    data: 'devices',
                    render: function(data, type, row, meta) {
                        var totalDevice = (row.devices ? row.devices : 0);
                        return `<span id="devMax-${row.user_key}" class="badge bg-info text-white">${totalDevice}/${row.max_devices}</span>`;
                    }
                },
                {
                    data: 'duration',
                    render: function(data, type, row, meta) {
                        return `<span class="badge bg-primary text-white">${row.duration}</span>`;
                    }
                },
                {
                    data: 'expired',
                    name: 'expired_date',
                    render: function(data, type, row, meta) {
                        return row.expired ? 
                            `<span class="badge bg-warning text-dark">${row.expired}</span>` : 
                            '<span class="badge bg-secondary text-white">(not started yet)</span>';
                    }
                },
                {
                    data: null,
                    render: function(data, type, row, meta) {
                        var btnReset = `<button class="btn btn-outline-danger btn-sm" onclick="resetUserKey('${row.user_key}')"
                            data-bs-toggle="tooltip" data-bs-placement="left" title="Reset key?">
                            <i class="bi bi-bootstrap-reboot"></i>
                            <span class="ms-1">Reset</span>
                        </button>`;
                       
                        var btnEdits = `<a href="<?= site_url('/keys/') ?>${row.id}" class="btn btn-outline-primary btn-sm"
                            data-bs-toggle="tooltip" data-bs-placement="left" title="Edit key information?">
                            <i class="bi bi-pen"></i>
                            <span class="ms-1">Edit</span>
                        </a>`;
                        
                        var btndelete = `<a href="<?= site_url('/keys/delete/') ?>${row.id}" 
                            class="btn btn-outline-danger btn-sm"
                            data-bs-toggle="tooltip" data-bs-placement="left" title="DELETE KEY?"
                            onclick="return confirm('Are you sure you want to delete this KEYS?')">
                            <i class="bi bi-trash"></i>
                            <span class="ms-1">Delete</span>
                        </a>`;

                        return `<div class="d-grid gap-2 d-md-block">${btnReset} ${btnEdits} ${btndelete}</div>`;
                    }
                }
            ]
        });

        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        $("#blur-out").click(function() {
            if ($(".keyBlur").hasClass("key-sensi")) {
                $(".keyBlur").removeClass("key-sensi");
                $("#blur-out").html(`<i class="bi bi-eye"></i> <span class="ms-1">Show</span>`);
            } else {
                $(".keyBlur").addClass("key-sensi");
                $("#blur-out").html(`<i class="bi bi-eye-slash"></i> <span class="ms-1">Hide</span>`);
            }
        });
    });

    function resetUserKey(keys) {
        Swal.fire({
            title: '<span class="text-dark">Reset This Key?</span>',
            html: `<div class="text-center">
                    <i class="bi bi-question-circle-fill text-warning" style="font-size: 3rem;"></i>
                    <p class="mt-3 text-dark">Are you sure you want to reset this key?</p>
                    <p class="text-danger">This action cannot be undone!</p>
                   </div>`,
            icon: false,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="bi bi-arrow-repeat"></i> Yes, reset it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Toast.fire({
                    icon: 'info',
                    title: 'Please wait...'
                })

                var api_url = `<?= site_url('/keys/reset') ?>`;
                
                $.getJSON(api_url, {
                    userkey: keys,
                    reset: 1
                },
                function(data, textStatus, jqXHR) {
                    if (textStatus == 'success') {
                        if (data.registered) {
                            if (data.reset) {
                                $(`#devMax-${keys}`).html(`0/${data.devices_max}`);
                                Swal.fire({
                                    title: '<span class="text-success">Success!</span>',
                                    html: `<div class="text-center">
                                            <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                                            <p class="mt-3 text-dark">Key has been reset successfully.</p>
                                           </div>`,
                                    icon: false,
                                    confirmButtonColor: '#28a745'
                                });
                            } else {
                                Swal.fire({
                                    title: '<span class="text-danger">Failed!</span>',
                                    html: `<div class="text-center">
                                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 3rem;"></i>
                                            <p class="mt-3 text-dark">${data.devices_total ? "You don't have any access to this user." : "User key devices already reset."}</p>
                                           </div>`,
                                    icon: false,
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        } else {
                            Swal.fire({
                                title: '<span class="text-danger">Error!</span>',
                                html: `<div class="text-center">
                                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 3rem;"></i>
                                        <p class="mt-3 text-dark">User key no longer exists.</p>
                                       </div>`,
                                icon: false,
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    }
                });
            }
        });
    }

    function resetAllKeys() {
        Swal.fire({
            title: '<span class="text-danger">Reset All Keys?</span>',
            html: `<div class="text-center">
                    <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 3rem;"></i>
                    <p class="mt-3 text-dark">This will reset all keys' devices count to 0.</p>
                    <p class="text-danger">This action cannot be undone!</p>
                   </div>`,
            icon: false,
            showCancelButton: true,
            confirmButtonColor: '#ff6b6b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-arrow-repeat"></i> Yes, reset all!',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.getJSON(`<?= site_url('/keys/reset-all') ?>`)
                    .then(response => {
                        if (!response.success) {
                            throw new Error(response.message || 'Reset failed');
                        }
                        return response;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(error.message);
                    });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $('#datatable').DataTable().ajax.reload();
                
                Swal.fire({
                    title: '<span class="text-success">Success!</span>',
                    html: `<div class="text-center">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                            <p class="mt-3 text-dark">All keys have been reset successfully.</p>
                           </div>`,
                    icon: false,
                    confirmButtonColor: '#28a745'
                });

                // Add animation to the reset button
                $('.reset-all-btn').addClass('animate__animated animate__rubberBand');
                setTimeout(() => {
                    $('.reset-all-btn').removeClass('animate__animated animate__rubberBand');
                }, 1000);
            }
        });
    }
</script>
<?= $this->endSection() ?>