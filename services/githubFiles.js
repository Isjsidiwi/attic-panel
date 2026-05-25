const axios = require('axios');
const path = require('path');

function getConfig() {
  return {
    token: process.env.GITHUB_TOKEN || process.env.GH_TOKEN || '',
    owner: process.env.GITHUB_OWNER || process.env.VERCEL_GIT_REPO_OWNER || 'Isjsidiwi',
    repo: process.env.GITHUB_REPO || process.env.VERCEL_GIT_REPO_SLUG || 'attic-panel',
    branch: process.env.GITHUB_BRANCH || 'main',
    basePath: (process.env.GITHUB_UPLOAD_PATH || 'public/uploads').replace(/^\/+|\/+$/g, '')
  };
}

function isConfigured() {
  const cfg = getConfig();
  return Boolean(cfg.token && cfg.owner && cfg.repo);
}

function sanitizeFileName(name) {
  const base = path.basename(String(name || 'file'));
  const cleaned = base.replace(/[<>:"/\\|?*\x00-\x1F]/g, '_').replace(/\s+/g, ' ').trim();
  return cleaned || `file-${Date.now()}`;
}

function repoPathFor(fileName) {
  const cfg = getConfig();
  return [cfg.basePath, sanitizeFileName(fileName)].filter(Boolean).join('/');
}

function normalizeManagedPath(repoPath) {
  const cfg = getConfig();
  const clean = String(repoPath || '').replace(/^\/+|\/+$/g, '');
  if (!clean || (cfg.basePath && clean !== cfg.basePath && !clean.startsWith(`${cfg.basePath}/`))) {
    throw new Error('Path file di luar folder upload GitHub.');
  }
  return clean;
}

function rawUrl(repoPath) {
  const cfg = getConfig();
  if (process.env.GITHUB_PUBLIC_BASE_URL) {
    return `${process.env.GITHUB_PUBLIC_BASE_URL.replace(/\/+$/g, '')}/${repoPath}`;
  }
  return `https://raw.githubusercontent.com/${cfg.owner}/${cfg.repo}/${cfg.branch}/${repoPath}`;
}

function api() {
  const cfg = getConfig();
  return axios.create({
    baseURL: `https://api.github.com/repos/${cfg.owner}/${cfg.repo}`,
    headers: {
      Authorization: `Bearer ${cfg.token}`,
      Accept: 'application/vnd.github+json',
      'X-GitHub-Api-Version': '2022-11-28'
    }
  });
}

async function getContent(repoPath) {
  const cfg = getConfig();
  try {
    const res = await api().get(`/contents/${repoPath}`, { params: { ref: cfg.branch } });
    return res.data;
  } catch (err) {
    if (err.response && err.response.status === 404) return null;
    throw err;
  }
}

async function listFiles() {
  const cfg = getConfig();
  const res = await api().get(`/contents/${cfg.basePath}`, { params: { ref: cfg.branch } }).catch(err => {
    if (err.response && err.response.status === 404) return { data: [] };
    throw err;
  });

  return (Array.isArray(res.data) ? res.data : [])
    .filter(item => item.type === 'file')
    .map(item => ({
      name: item.name,
      path: item.path,
      sha: item.sha,
      sizeBytes: item.size,
      size: `${(Number(item.size || 0) / 1024).toFixed(2)} KB`,
      date: null,
      url: item.download_url || rawUrl(item.path),
      htmlUrl: item.html_url,
      storage: 'github'
    }))
    .sort((a, b) => a.name.localeCompare(b.name));
}

async function uploadFile(file) {
  const cfg = getConfig();
  const fileName = sanitizeFileName(file.originalname || file.name);
  const repoPath = repoPathFor(fileName);
  const existing = await getContent(repoPath);

  const body = {
    message: `Upload ${fileName}`,
    content: Buffer.from(file.buffer).toString('base64'),
    branch: cfg.branch
  };

  if (existing && existing.sha) body.sha = existing.sha;

  await api().put(`/contents/${repoPath}`, body);
  return { name: fileName, path: repoPath, url: rawUrl(repoPath) };
}

async function deleteFile(repoPath, sha) {
  const cfg = getConfig();
  const targetPath = normalizeManagedPath(repoPath);
  const content = sha ? { sha } : await getContent(targetPath);
  if (!content || !content.sha) throw new Error('File GitHub tidak ditemukan.');

  await api().delete(`/contents/${targetPath}`, {
    data: {
      message: `Delete ${path.basename(targetPath)}`,
      sha: content.sha,
      branch: cfg.branch
    }
  });
}

async function renameFile(oldPath, newName) {
  const cfg = getConfig();
  const sourcePath = normalizeManagedPath(oldPath);
  const targetName = sanitizeFileName(newName);
  const targetPath = repoPathFor(targetName);

  if (sourcePath === targetPath) return { name: targetName, path: targetPath, url: rawUrl(targetPath) };

  const source = await getContent(sourcePath);
  if (!source || !source.content || !source.sha) throw new Error('File GitHub tidak ditemukan.');

  await api().put(`/contents/${targetPath}`, {
    message: `Rename ${path.basename(sourcePath)} to ${targetName}`,
    content: source.content.replace(/\n/g, ''),
    branch: cfg.branch
  });

  await api().delete(`/contents/${sourcePath}`, {
    data: {
      message: `Remove old ${path.basename(sourcePath)}`,
      sha: source.sha,
      branch: cfg.branch
    }
  });

  return { name: targetName, path: targetPath, url: rawUrl(targetPath) };
}

module.exports = {
  getConfig,
  isConfigured,
  sanitizeFileName,
  repoPathFor,
  rawUrl,
  listFiles,
  uploadFile,
  deleteFile,
  renameFile
};
