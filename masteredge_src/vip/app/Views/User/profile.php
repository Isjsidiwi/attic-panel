<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <?= $this->include('Layout/msgStatus') ?>
            
            <style>
                .mt-4 img {
                    border-radius: 15px;
                    width: 120px;
                    height: 140px;
                    margin: 20px auto;
                    display: block;
                    border: 4px solid rgba(99, 102, 241, 0.5);
                    box-shadow: 0 0 30px rgba(99, 102, 241, 0.4);
                    animation: pulse 2s ease-in-out infinite;
                }

                @keyframes pulse {
                    0%, 100% { box-shadow: 0 0 30px rgba(99, 102, 241, 0.4); }
                    50% { box-shadow: 0 0 40px rgba(99, 102, 241, 0.6); }
                }

                .profile-card {
                    background: rgba(20, 20, 20, 0.8);
                    border-radius: 20px;
                    border: 1px solid rgba(99, 102, 241, 0.2);
                    box-shadow: 
                        0 8px 32px 0 rgba(0, 0, 0, 0.37),
                        0 0 80px rgba(99, 102, 241, 0.1);
                    backdrop-filter: blur(10px);
                    overflow: hidden;
                    position: relative;
                }

                .profile-card::before {
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
                
                .profile-header {
                    background: linear-gradient(135deg, #6366f1, #8b5cf6);
                    color: white;
                    padding: 20px;
                    text-align: center;
                    font-weight: 600;
                    letter-spacing: 1px;
                    border-bottom: 1px solid rgba(99, 102, 241, 0.2);
                }
                
                .upload-form {
                    padding: 30px;
                    background: transparent;
                }

                .file-upload-wrapper {
                    position: relative;
                    width: 100%;
                    margin: 15px 0;
                }

                .file-upload-label {
                    display: block;
                    padding: 20px;
                    background: rgba(99, 102, 241, 0.05);
                    border: 2px dashed rgba(99, 102, 241, 0.3);
                    border-radius: 12px;
                    text-align: center;
                    color: #6366f1;
                    font-weight: bold;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                .file-upload-label:hover {
                    background: rgba(99, 102, 241, 0.1);
                    border-color: #6366f1;
                    transform: translateY(-2px);
                }

                .file-upload-input {
                    opacity: 0;
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    cursor: pointer;
                }

                .file-name-display {
                    margin-top: 10px;
                    font-size: 0.9em;
                    color: rgba(255, 255, 255, 0.7);
                }
                
                .update-btn {
                    background: linear-gradient(135deg, #6366f1, #8b5cf6);
                    border: none;
                    border-radius: 12px;
                    padding: 12px 35px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    color: white;
                    position: relative;
                    overflow: hidden;
                }

                .update-btn::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                    transition: left 0.5s ease;
                }

                .update-btn:hover::before {
                    left: 100%;
                }
                
                .update-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.5);
                }

                .text-danger {
                    color: #ef4444 !important;
                }
            </style>

            <div class="profile-card">
                <div class="profile-header">
                    <i class="fas fa-user-circle me-2"></i>
                    CHANGE PROFILE IMAGE
                </div>

                <?php if ($user->image != ''): ?>
                    <div class="mt-4">
                        <img src="<?= base_url('/uploads/' . $user->image) ?>" 
                             alt="<?= $user->username ?>" 
                             class="img-thumbnail">
                    </div>
                <?php endif; ?>

                <div class="upload-form">
                    <?= form_open('/Profile', ['enctype' => 'multipart/form-data']) ?>
                    <input type="hidden" name="image_form" value="1">
                    
                    <div class="mb-4">
                        <div class="file-upload-wrapper">
                            <label class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt me-2"></i>
                                CHOOSE IMAGE
                                <input type="file" 
                                       name="image" 
                                       id="image" 
                                       class="file-upload-input" 
                                       accept="image/*"
                                       required>
                            </label>
                            <div id="file-name-display" class="file-name-display text-center"></div>
                        </div>
                        
                        <?php if ($validation->hasError('image')): ?>
                            <div class="text-danger mt-2">
                                <small id="help-image">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    <?= $validation->getError('image') ?>
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary update-btn">
                            <i class="fas fa-sync-alt me-2"></i>
                            UPDATE IMAGE
                        </button>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelector('.file-upload-input').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || 'No file selected';
    document.getElementById('file-name-display').textContent = fileName;
});
</script>

<?= $this->endSection() ?>