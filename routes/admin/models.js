const express = require('express');
const router = express.Router();
const { isAuthenticated } = require('../../middleware/auth');
const fs = require('fs');
const path = require('path');
const multer = require('multer');

// Setup multer for uploading 3d models
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    const dir = path.join(__dirname, '../../public/models');
    if (!fs.existsSync(dir)){
        fs.mkdirSync(dir, { recursive: true });
    }
    cb(null, dir);
  },
  filename: function (req, file, cb) {
    cb(null, Date.now() + '-' + file.originalname);
  }
});

const upload = multer({ 
    storage: storage,
    fileFilter: (req, file, cb) => {
        // Allow common 3d model formats
        if (file.originalname.match(/\.(gltf|glb|obj|fbx|stl)$/i)) {
            cb(null, true);
        } else {
            cb(new Error('Hanya file model 3D (gltf, glb, obj, fbx, stl) yang diperbolehkan!'));
        }
    }
});

router.use(isAuthenticated);

// GET /admin/models
router.get('/', (req, res) => {
  const modelsDir = path.join(__dirname, '../../public/models');
  let models = [];
  
  if (fs.existsSync(modelsDir)) {
    try {
      models = fs.readdirSync(modelsDir)
        .filter(file => !file.startsWith('.'))
        .map(file => {
          const stats = fs.statSync(path.join(modelsDir, file));
          return {
            name: file,
            size: (stats.size / (1024 * 1024)).toFixed(2) + ' MB',
            date: stats.mtime,
            path: `/models/${file}`
          };
        });
    } catch (e) {
      console.error("Error reading models directory:", e);
    }
  }

  res.render('models', {
    title: 'Model 3D',
    panel_name: process.env.PANEL_NAME || 'ATTIC PANEL',
    admin: req.user,
    models: models
  });
});

// POST /admin/models/upload
router.post('/upload', upload.single('modelFile'), (req, res) => {
    try {
        if (!req.file) {
            req.flash('error', 'Gagal mengupload file.');
            return res.redirect('/admin/models');
        }
        req.flash('success', 'Model 3D berhasil diupload.');
        res.redirect('/admin/models');
    } catch (e) {
        req.flash('error', e.message);
        res.redirect('/admin/models');
    }
});

// POST /admin/models/delete
router.post('/delete', (req, res) => {
    try {
        const { filename } = req.body;
        if (!filename) {
             req.flash('error', 'Nama file tidak valid.');
             return res.redirect('/admin/models');
        }
        
        const filePath = path.join(__dirname, '../../public/models', filename);
        if (fs.existsSync(filePath)) {
            fs.unlinkSync(filePath);
            req.flash('success', 'Model 3D berhasil dihapus.');
        } else {
            req.flash('error', 'File tidak ditemukan.');
        }
    } catch (e) {
        req.flash('error', 'Gagal menghapus file: ' + e.message);
    }
    res.redirect('/admin/models');
});


module.exports = router;
