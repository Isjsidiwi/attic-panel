const express = require('express');
const router = express.Router();
const auth = require('../../middleware/auth');
const { loadConfig, saveConfig } = require('../../config');

const requireOwner = auth.requireOwner;

// GET /admin/necro — Tampilkan halaman konfigurasi Necro MLBB
router.get('/', auth, requireOwner, async (req, res) => {
  const cfg = await loadConfig();
  res.render('necro', {
    title: 'Necro MLBB Config',
    panel_name: cfg.panel_name || 'ATTIC PANEL',
    cfg,
    success_msg: res.locals.success_msg || [],
    error_msg:   res.locals.error_msg   || []
  });
});

// POST /admin/necro — Simpan konfigurasi Necro MLBB
router.post('/', auth, requireOwner, async (req, res) => {
  const {
    necro_registrator,
    necro_custom_title,
    necro_tittle,
    necro_subtitle,
    necro_modname,
    necro_mod_status,
    necro_credit,
    necro_btdata,
    necro_btdata_safe,
    necro_vvipmodsgr,
    necro_VVIPMODS,
    necro_cantcrackkirito,
    necro_cantcrack,
    necro_ownmod,
    necro_xenoanticrack,
    necro_antiban_dns,
    necro_feature_mode,
    necro_login_type,
    necro_type,
    // Feature flags (checkbox: present = true, absent = false)
    necro_feat_bypass,
    necro_feat_drone_view,
    necro_feat_no_grass,
    necro_feat_maphack,
    necro_feat_esp,
    necro_feat_retri_hack,
    necro_feat_unlock_skin,
    necro_feat_skill_hack,
    necro_feat_skill_cd,
    necro_feat_floating_button
  } = req.body;

  await saveConfig({
    necro_registrator:        (necro_registrator        || 'Izumi').trim(),
    necro_custom_title:       (necro_custom_title        || 'ATHARYX NIH BOS').trim(),
    necro_tittle:             (necro_tittle              || 'KillCracked ').trim(),
    necro_subtitle:           (necro_subtitle            || 'Give Feedback else Keys off').trim(),
    necro_modname:            (necro_modname             || 'KillCracked ').trim(),
    necro_mod_status:         (necro_mod_status          || 'Safe').trim(),
    necro_credit:             (necro_credit              || 'Give Feedback else Keys off').trim(),
    necro_btdata:             (necro_btdata              || 'BattleData').trim(),
    necro_btdata_safe:        (necro_btdata_safe         || 'Safe').trim(),
    necro_vvipmodsgr:         (necro_vvipmodsgr          || 'QUh/k8wd+CfJxob7qKlIyMlHfxiauTXyjkN6258nbu0=').trim(),
    necro_VVIPMODS:           (necro_VVIPMODS            || 'OK').trim(),
    necro_cantcrackkirito:    (necro_cantcrackkirito     || 'namohindimoakomacacrackbrokettawaginmobuongdiyosdiyosanniyo').trim(),
    necro_cantcrack:          (necro_cantcrack           || '5DXuN61YKEgIhKNqa6PbJgf8DXm1Sft10sEEfFs9st8=').trim(),
    necro_ownmod:             (necro_ownmod              || 'Rtc2ximHdWn+2LoOACPNH/BZ6IHhch+/pLkvdoo9gRw=').trim(),
    necro_xenoanticrack:      (necro_xenoanticrack       || 'YDPsDmdATek4YTC2yX0zF914WTB/hLd1hWo+EkFAiUAkn9GAOcR7xcWMQ3n8jifrWZvZxsAZwf8hKd7pChs3cQ==').trim(),
    necro_antiban_dns:        (necro_antiban_dns         || 'false').trim(),
    necro_feature_mode:       (necro_feature_mode        || '1').trim(),
    necro_login_type:         (necro_login_type          || 'stay').trim(),
    necro_type:               (necro_type                || 'premium').trim(),
    // Feature flags: checkbox value = 'true', absent = 'false'
    necro_feat_bypass:         necro_feat_bypass         ? 'true' : 'false',
    necro_feat_drone_view:     necro_feat_drone_view     ? 'true' : 'false',
    necro_feat_no_grass:       necro_feat_no_grass       ? 'true' : 'false',
    necro_feat_maphack:        necro_feat_maphack        ? 'true' : 'false',
    necro_feat_esp:            necro_feat_esp            ? 'true' : 'false',
    necro_feat_retri_hack:     necro_feat_retri_hack     ? 'true' : 'false',
    necro_feat_unlock_skin:    necro_feat_unlock_skin    ? 'true' : 'false',
    necro_feat_skill_hack:     necro_feat_skill_hack     ? 'true' : 'false',
    necro_feat_skill_cd:       necro_feat_skill_cd       ? 'true' : 'false',
    necro_feat_floating_button: necro_feat_floating_button ? 'true' : 'false'
  });

  res.flash('success', 'Konfigurasi Necro MLBB berhasil disimpan.');
  res.redirect('/admin/necro');
});

module.exports = router;
