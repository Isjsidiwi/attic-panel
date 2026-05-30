const express = require('express');
const router = express.Router();
const auth = require('../../middleware/auth');
const { loadConfig } = require('../../config');
const multer = require('multer');
const fs = require('fs');
const path = require('path');
const githubFiles = require('../../services/githubFiles');

const requireOwner = auth.requireOwner;
const upload = multer({
  storage: multer.memoryStorage(),
  limits: { fileSize: Number(process.env.MAX_UPLOAD_BYTES || 100 * 1024 * 1024) }
});

function uploadDir() {
  return path.resolve(__dirname, '../../public/uploads');
}

function resolveLocalUpload(filename) {
  const dir = uploadDir();
  const target = path.resolve(dir, githubFiles.sanitizeFileName(filename));
  if (!target.startsWith(dir + path.sep) && target !== dir) {
    throw new Error('Nama file tidak valid.');
  }
  return target;
}

function listLocalFiles() {
  const dir = uploadDir();
  if (!fs.existsSync(dir)) return [];

  return fs
    .readdirSync(dir)
    .map((name) => {
      const stats = fs.statSync(path.join(dir, name));
      return {
        name,
        path: name,
        sha: '',
        sizeBytes: stats.size,
        size: `${(stats.size / 1024).toFixed(2)} KB`,
        date: stats.mtime,
        url: `/uploads/${encodeURIComponent(name)}`,
        storage: 'local'
      };
    })
    .sort((a, b) => b.date - a.date);
}

router.get('/', auth, requireOwner, async (req, res) => {
  const cfg = await loadConfig();
  let files = [];
  const githubConfigured = githubFiles.isConfigured();

  try {
    files = githubConfigured ? await githubFiles.listFiles() : listLocalFiles();
  } catch (err) {
    console.error('List files error:', err.message);
    res.flash('error', 'Gagal mengambil daftar file GitHub. Cek token, owner, repo, dan branch.');
    files = [];
  }

  res.render('files', {
    title: 'Manage Files',
    panel_name: cfg.panel_name,
    files,
    storageMode: githubConfigured ? 'GitHub repository' : 'Local fallback',
    githubConfig: githubFiles.getConfig()
  });
});

router.post('/upload', auth, requireOwner, upload.single('file'), async (req, res) => {
  try {
    if (!req.file) {
      res.flash('error', 'Tidak ada file yang diupload.');
      return res.redirect('/admin/files');
    }

    if (githubFiles.isConfigured()) {
      const uploaded = await githubFiles.uploadFile(req.file);
      res.flash('success', `File ${uploaded.name} berhasil diupload ke GitHub.`);
      return res.redirect('/admin/files');
    }

    if (process.env.VERCEL || process.env.AWS_REGION) {
      res.flash(
        'error',
        'Sistem read-only (Vercel) terdeteksi. Anda wajib mengatur GITHUB_TOKEN di Environment Variables untuk mengupload file.'
      );
      return res.redirect('/admin/files');
    }

    const dir = uploadDir();
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
    const fileName = githubFiles.sanitizeFileName(req.file.originalname);
    fs.writeFileSync(resolveLocalUpload(fileName), req.file.buffer);
    res.flash('success', `File ${fileName} berhasil diupload lokal.`);
  } catch (err) {
    console.error('Upload file error:', err.message);
    res.flash('error', `Gagal upload file: ${err.message}`);
  }
  res.redirect('/admin/files');
});

async function deleteManagedFile(req, res) {
  try {
    const targetPath = req.body.path || req.params.filename;
    const sha = req.body.sha || '';
    if (!targetPath) {
      res.flash('error', 'File tidak ditemukan.');
      return res.redirect('/admin/files');
    }

    if (githubFiles.isConfigured()) {
      await githubFiles.deleteFile(targetPath, sha);
      res.flash('success', 'File berhasil dihapus dari GitHub.');
      return res.redirect('/admin/files');
    }

    const filepath = resolveLocalUpload(targetPath);
    if (fs.existsSync(filepath)) {
      fs.unlinkSync(filepath);
      res.flash('success', `File ${githubFiles.sanitizeFileName(targetPath)} berhasil dihapus.`);
    } else {
      res.flash('error', 'File tidak ditemukan.');
    }
  } catch (err) {
    console.error('Delete file error:', err.message);
    res.flash('error', `Gagal hapus file: ${err.message}`);
  }
  res.redirect('/admin/files');
}

router.post('/delete', auth, requireOwner, deleteManagedFile);
router.post('/delete/:filename', auth, requireOwner, deleteManagedFile);

router.post('/rename', auth, requireOwner, async (req, res) => {
  try {
    const oldPath = req.body.path || req.body.old_name;
    const newName = githubFiles.sanitizeFileName(req.body.new_name);
    if (!oldPath || !newName) {
      res.flash('error', 'Nama file rename tidak valid.');
      return res.redirect('/admin/files');
    }

    if (githubFiles.isConfigured()) {
      await githubFiles.renameFile(oldPath, newName);
      res.flash('success', `File berhasil direname menjadi ${newName} di GitHub.`);
      return res.redirect('/admin/files');
    }

    const oldFile = resolveLocalUpload(oldPath);
    const newFile = resolveLocalUpload(newName);
    if (!fs.existsSync(oldFile)) {
      res.flash('error', 'File lama tidak ditemukan.');
      return res.redirect('/admin/files');
    }
    fs.renameSync(oldFile, newFile);
    res.flash('success', `File berhasil direname menjadi ${newName}.`);
  } catch (err) {
    console.error('Rename file error:', err.message);
    res.flash('error', `Gagal rename file: ${err.message}`);
  }
  res.redirect('/admin/files');
});

module.exports = router;
