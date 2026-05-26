<?= $this->extend('Layout/Starter') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="row">
        <div class="col-lg-12">
            <?= $this->include('Layout/msgStatus') ?>
        </div>
            
        <div class="col-lg-12">
            <div class="card shadow-sm" style="background: rgba(20, 20, 20, 0.8); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px; backdrop-filter: blur(10px); position: relative; overflow: hidden; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37), 0 0 80px rgba(99, 102, 241, 0.1);">
                <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #6366f1, #8b5cf6, transparent); animation: borderGlow 3s ease-in-out infinite;"></div>
                <div class="card-header text-white" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); border-bottom: 1px solid rgba(99, 102, 241, 0.2); border-radius: 20px 20px 0 0;">
                    <div class="row">
                        <div class="col pt-1">
                            <i class="fas fa-users me-2"></i>Manage <?= $title ?>
                        </div>
                        <div class="col text-end">
                            <a class="btn btn-outline-light btn-sm" href="<?= site_url('admin/create-referral') ?>">
                                <i class="fa-regular fa-hand-holding-heart fa-bounce" style="color: #00eeff;"></i>
                            </a>
                            <a class="btn btn-outline-light btn-sm" href="<?= site_url('keys/generate') ?>">
                                <i class="fa-solid fa-file-plus fa-flip" style="--fa-animation-duration: 4s; color: #00ffbb;"></i>
                            </a>
                            <a class="btn btn-outline-light btn-sm" href="<?= site_url('keys/name-generate') ?>">
                                <i class="fa-solid fa-file-pen fa-flip" style="--fa-animation-duration: 8s; color: #00ffbb;"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered table-hover text-center" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="row">#</th>
                                    <th>Username</th>
                                    <th>Profile</th>
                                    <th>Fullname</th>
                                    <th>Level</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Uplink</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<?= link_tag("https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css") ?>
<style>
    @keyframes borderGlow {
        0%, 100% { opacity: 0.5; }
        50% { opacity: 1; }
    }

    .card-body {
        background: transparent !important;
        color: #e6eef8 !important;
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

    .text-success {
        color: #22c55e !important;
    }

    .text-danger {
        color: #ef4444 !important;
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

    /* DataTables Dark Theme */
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

    .img-fluid {
        border: 2px solid rgba(99, 102, 241, 0.5) !important;
        box-shadow: 0 0 15px rgba(99, 102, 241, 0.3) !important;
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
        ajax: {
            url: "<?= site_url('admin/api/users') ?>"
        },
        columns: [
            { data: 'id' },
            { data: 'username' },
            { 
                data: null,
                render: function(data, type, row) {
                    return `<img src="<?= site_url('/uploads/') ?>${data.image}" 
                            class="img-fluid rounded" 
                            style="width: 100px; height: 80px; object-fit: cover; margin-top: 6px; position: relative; margin-left: -2px; box-shadow: 0px 0px 2px #ff0505;">`;
                }
            },
            { data: 'fullname' },
            { data: 'level' },
            { 
                data: 'saldo',
                render: function(data) {
                    return '<?= $Naman ?> ' + data;
                }
            },
            { 
                data: 'status',
                render: function(data, type, row) {
                    return data == 1 
                        ? '<span class="text-success">Active</span>'
                        : '<span class="text-danger">Banned</span>';
                }
            },
            { data: 'uplink' },
            { 
                data: null,
                render: function(data, type, row) {
                    return `
                        <div class="d-grid gap-2 d-md-block">
                            <a href="<?= site_url('/admin/user/delete/') ?>${row.id}" 
                               class="btn btn-outline-danger btn-sm"
                               data-bs-toggle="tooltip" 
                               data-bs-placement="left" 
                               title="DELETE USER?"
                               onclick="return confirm('Are you sure you want to delete this USER?')">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                            <a href="<?= site_url('/admin/user/') ?>${row.id}" 
                               class="btn btn-outline-primary btn-sm"
                               data-bs-toggle="tooltip" 
                               data-bs-placement="left" 
                               title="Edit User information?">
                                <i class="bi bi-pen"></i> EDIT
                            </a>
                        </div>`;
                }
            }
        ]
    });
});
</script>
<?= $this->endSection() ?>