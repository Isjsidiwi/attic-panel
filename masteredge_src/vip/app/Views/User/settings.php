<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>

<div class="dashboard-container">
    <div class="col-lg-12">
        <?= $this->include('Layout/msgStatus') ?>
    </div>

    <style>
        /* Main Container Styling */
        .dashboard-container {
            padding: 30px;
            background: transparent;
            min-height: 100vh;
        }

        /* Dark Glass Card Style */
        .glass-card {
            background: rgba(20, 20, 20, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 
                0 8px 32px 0 rgba(0, 0, 0, 0.37),
                0 0 80px rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            margin-bottom: 30px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .glass-card::before {
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

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 15px 40px rgba(0, 0, 0, 0.5),
                0 0 100px rgba(99, 102, 241, 0.2);
        }

        /* Card Header */
        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #e6eef8;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(99, 102, 241, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: #6366f1;
        }

        /* Form Elements */
        .form-floating {
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 12px;
            padding: 15px;
            border: 1px solid rgba(99, 102, 241, 0.3);
            background: rgba(20, 20, 20, 0.6);
            color: #fff;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #6366f1;
            background: rgba(20, 20, 20, 0.8);
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-floating label {
            padding-left: 15px;
            color: rgba(255, 255, 255, 0.7);
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: #6366f1;
        }

        /* Custom Button */
        .custom-btn {
            width: 100%;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .custom-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .custom-btn:hover::before {
            left: 100%;
        }

        .btn-gradient-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            color: white;
        }

        .btn-gradient-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            color: white;
        }

        .custom-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5);
        }

        /* Error Messages */
        .error-message {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .glass-card {
            animation: fadeIn 0.5s ease-out;
        }
    </style>

    <div class="row">
        <div class="col-lg-6">
            <div class="glass-card">
                <h3 class="card-title">
                    <i class="fas fa-lock-alt"></i>
                    Password Management
                </h3>
                <?= form_open() ?>
                    <input type="hidden" name="password_form" value="1">
                    
                    <div class="form-floating">
                        <input type="password" name="current" id="current" class="form-control" placeholder="Current Password">
                        <label for="current">Current Password</label>
                        <?php if ($validation->hasError('current')) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $validation->getError('current') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-floating">
                        <input type="password" name="password" id="password" class="form-control" placeholder="New Password">
                        <label for="password">New Password</label>
                        <?php if ($validation->hasError('password')) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $validation->getError('password') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-floating">
                        <input type="password" name="password2" id="password2" class="form-control" placeholder="Confirm Password">
                        <label for="password2">Confirm Password</label>
                        <?php if ($validation->hasError('password2')) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $validation->getError('password2') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="custom-btn btn-gradient-primary">
                        <i class="fas fa-shield-alt"></i>
                        Update Password
                    </button>
                <?= form_close() ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="glass-card">
                <h3 class="card-title">
                    <i class="fas fa-user-edit"></i>
                    Profile Information
                </h3>
                <?= form_open() ?>
                    <input type="hidden" name="fullname_form" value="1">
                    
                    <div class="form-floating">
                        <input type="text" name="fullname" id="fullname" class="form-control" 
                               placeholder="Full Name" value="<?= old('fullname') ?: ($user->fullname ?: '') ?>">
                        <label for="fullname">Full Name</label>
                        <?php if ($validation->hasError('fullname')) : ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $validation->getError('fullname') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="custom-btn btn-gradient-danger">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>