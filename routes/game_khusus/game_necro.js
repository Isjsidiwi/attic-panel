const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const { validateAndRegisterKey } = require('../../services/gameAuth');
const { loadConfig } = require('../../config');

// Helper: format tanggal dari unix timestamp ke "DD-Mon-YYYY HH:MM"
const MON_SHORT = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
function fmtNecroDate(unix) {
  const d = new Date(Number(unix) * 1000);
  const dd   = String(d.getUTCDate()).padStart(2, '0');
  const mon  = MON_SHORT[d.getUTCMonth()];
  const yyyy = d.getUTCFullYear();
  const hh   = String(d.getUTCHours()).padStart(2, '0');
  const mm   = String(d.getUTCMinutes()).padStart(2, '0');
  return `${dd}-${mon}-${yyyy} ${hh}:${mm}`;
}

// Helper: format ISO tanggal dari unix ke "YYYY-MM-DD HH:MM:SS"
function fmtIso(unix) {
  return new Date(Number(unix) * 1000).toISOString().replace('T', ' ').slice(0, 19);
}

// Helper: format ISO offset "+00:00" dari unix
function fmtIsoOffset(unix) {
  return new Date(Number(unix) * 1000).toISOString().replace('Z', '+00:00').slice(0, 19) + '+00:00';
}

// Helper: current datetime "YYYY-MM-DD HH:MM:SS" UTC
function nowIso() {
  return new Date().toISOString().replace('T', ' ').slice(0, 19);
}

// Helper: current datetime "DD-Mon-YYYY HH:MM" UTC
function nowNecroDate() {
  const d = new Date();
  const dd   = String(d.getUTCDate()).padStart(2, '0');
  const mon  = MON_SHORT[d.getUTCMonth()];
  const yyyy = d.getUTCFullYear();
  const hh   = String(d.getUTCHours()).padStart(2, '0');
  const mm   = String(d.getUTCMinutes()).padStart(2, '0');
  return `${dd}-${mon}-${yyyy} ${hh}:${mm}`;
}

// Helper: parse boolean config value
function cfgBool(val, fallback = false) {
  if (val === undefined || val === null) return fallback;
  if (typeof val === 'boolean') return val;
  return String(val).toLowerCase() === 'true' || val === '1';
}

/**
 * POST /api/game/qy
 * MLBB (Necro) — endpoint dengan respon penuh yang dapat dikonfigurasi via admin panel.
 */
router.post('/game/qy', async (req, res) => {
  try {
    const userKey  = (req.body.user_key  || req.body.key    || '').trim();
    const serial   = (req.body.serial    || req.body.hwid   || '').trim();
    const resource = (req.body.resource  || '').trim();
    const version  = (req.body.version   || '1.5').trim();

    if (!userKey) {
      return res.status(200).json({ status: false, reason: 'Key diperlukan.', data: null });
    }

    // Validasi & registrasi key dari database
    const auth = await validateAndRegisterKey(userKey, serial);
    if (!auth.success) {
      return res.status(200).json({ status: false, reason: auth.reason, data: null });
    }

    const { key } = auth;

    // Load konfigurasi necro dari database
    const cfg = await loadConfig();

    // --- Nilai yang bisa dikustomisasi via admin ---
    const registrator   = cfg.necro_registrator   || 'Izumi';
    const customTitle   = cfg.necro_custom_title   || 'ATHARYX NIH BOS';
    const tittle        = cfg.necro_tittle         || 'KillCracked ';
    const subtitle      = cfg.necro_subtitle       || 'Give Feedback else Keys off';
    const modname       = cfg.necro_modname        || 'KillCracked ';
    const modStatus     = cfg.necro_mod_status     || 'Safe';
    const credit        = cfg.necro_credit         || 'Give Feedback else Keys off';
    const btData        = cfg.necro_btdata         || 'BattleData';
    const btDataSafe    = cfg.necro_btdata_safe    || 'Safe';
    const vvipmodsgr    = cfg.necro_vvipmodsgr     || 'QUh/k8wd+CfJxob7qKlIyMlHfxiauTXyjkN6258nbu0=';
    const VVIPMODS      = cfg.necro_VVIPMODS       || 'OK';
    const cantcrackStr  = cfg.necro_cantcrackkirito || 'namohindimoakomacacrackbrokettawaginmobuongdiyosdiyosanniyo';
    const cantcrack     = cfg.necro_cantcrack       || '5DXuN61YKEgIhKNqa6PbJgf8DXm1Sft10sEEfFs9st8=';
    const ownmod        = cfg.necro_ownmod          || 'Rtc2ximHdWn+2LoOACPNH/BZ6IHhch+/pLkvdoo9gRw=';
    const xenoanticrack = cfg.necro_xenoanticrack   || 'YDPsDmdATek4YTC2yX0zF914WTB/hLd1hWo+EkFAiUAkn9GAOcR7xcWMQ3n8jifrWZvZxsAZwf8hKd7pChs3cQ==';
    const antiban_dns   = cfg.necro_antiban_dns     || 'false';
    const featureMode   = cfg.necro_feature_mode    || '1';
    const loginType     = cfg.necro_login_type      || 'stay';
    const keyType       = cfg.necro_type            || 'premium';

    // Feature flags
    const feat_bypass         = cfgBool(cfg.necro_feat_bypass,         false);
    const feat_drone_view     = cfgBool(cfg.necro_feat_drone_view,     true);
    const feat_no_grass       = cfgBool(cfg.necro_feat_no_grass,       false);
    const feat_maphack        = cfgBool(cfg.necro_feat_maphack,        false);
    const feat_esp            = cfgBool(cfg.necro_feat_esp,            true);
    const feat_retri_hack     = cfgBool(cfg.necro_feat_retri_hack,     true);
    const feat_unlock_skin    = cfgBool(cfg.necro_feat_unlock_skin,    true);
    const feat_skill_hack     = cfgBool(cfg.necro_feat_skill_hack,     true);
    const feat_skill_cd       = cfgBool(cfg.necro_feat_skill_cd,       true);
    const feat_floating_button = cfgBool(cfg.necro_feat_floating_button, true);

    // Data dinamis dari key database
    const expiresAt     = Number(key.expires_at);
    const maxDevices    = Number(key.max_devices) || 100;
    let   serials       = [];
    try { serials = JSON.parse(key.device_serials || '[]'); } catch { serials = []; }
    const remainingSlot = Math.max(0, maxDevices - serials.length);
    const registration  = key.key_code;
    const nowTs         = nowIso();
    const nowDatte      = nowNecroDate();
    const expiredFmt    = fmtNecroDate(expiresAt);
    const expiredIso    = fmtIso(expiresAt);
    const expiredOffset = fmtIsoOffset(expiresAt);

    // Token: MD5 dari realString
    const realString = `MLBB-${registration}-${serial}-${resource}-Vm8Lk7Uj2JmsjCPVPVjrLa7zgfx3uz9E`;
    const token      = crypto.createHash('md5').update(realString).digest('hex');
    const rng        = Math.floor(Date.now() / 1000);

    // Set response headers sesuai spec
    res.set('Cache-Control', 'no-store, max-age=0, no-cache');
    res.set('Vary', 'Accept-Encoding');

    const featuresObj = {
      bypass:          feat_bypass,
      drone_view:      feat_drone_view,
      no_grass:        feat_no_grass,
      maphack:         feat_maphack,
      esp:             feat_esp,
      retri_hack:      feat_retri_hack,
      unlock_skin:     feat_unlock_skin,
      skill_hack:      feat_skill_hack,
      skill_cd:        feat_skill_cd,
      floating_button: feat_floating_button
    };

    res.status(200).json({
      status: true,
      data: {
        registrator:       registrator,
        custom_title:      customTitle,
        btData:            btData,
        vvipmodsgr:        vvipmodsgr,
        VVIPMODS:          VVIPMODS,
        Datte:             nowDatte,
        tittle:            tittle,
        title:             tittle,
        subtitle:          subtitle,
        btdata:            btDataSafe,
        instance:          'Instance',
        expired:           expiredFmt,
        real:              realString,
        token:             token,
        token_mode:        'old_resource_md5',
        modname:           modname,
        mod_status:        modStatus,
        credit:            credit,
        ESP:               false,
        Item:              false,
        AIM:               false,
        SilentAim:         false,
        BulletTrack:       false,
        Floating:          false,
        Memory:            false,
        Setting:           false,
        EXP:               expiredIso,
        device:            maxDevices,
        MOD_NAME:          modname,
        MOD_STATUS:        modStatus,
        FLOTING_TEST:      credit,
        BHATIA_EXP:        expiredIso,
        BHATIA_SLOT:       maxDevices,
        EXPR:              expiredIso,
        cantcrackkirito:   cantcrackStr,
        cantcrack:         cantcrack,
        ownmod:            ownmod,
        rng:               rng,
        ts:                nowTs,
        SLOT:              maxDevices,
        xenoanticrack:     xenoanticrack,
        REGISTRATION:      registration,
        succeded:          true,
        succeeded:         true,
        expiredAt:         `${expiredIso.slice(0, 10)}T${expiredIso.slice(11)}`,
        registeredAs:      registrator,
        maxDevices:        maxDevices,
        remainingSlot:     remainingSlot,
        feature_mode:      featureMode,
        login_type:        loginType,
        game:              'MLBB',
        user_key:          userKey,
        key:               userKey,
        serial:            serial,
        hwid:              serial,
        version:           version,
        resource:          resource,
        codebase:          '',
        antiban_dns:       antiban_dns,
        valid:             true,
        panel_enabled:     true,
        type:              keyType,
        expires_at:        expiredOffset,
        reason:            '',
        features:          featuresObj,
        esp:               feat_esp,
        ESPPlayer:         feat_esp,
        skill_hack:        feat_skill_hack,
        SkillHack:         feat_skill_hack,
        AutoSkillTable:    feat_skill_hack,
        retri_hack:        feat_retri_hack,
        RetriHack:         feat_retri_hack,
        unlock_skin:       feat_unlock_skin,
        UnlockSkin:        feat_unlock_skin,
        skill_cd:          feat_skill_cd,
        SkillCD:           feat_skill_cd,
        drone_view:        feat_drone_view,
        DroneView:         feat_drone_view,
        SetFieldOfView:    feat_drone_view,
        floating_button:   feat_floating_button,
        FloatingButton:    feat_floating_button,
        showFloatingButton: feat_floating_button,
        bypass:            feat_bypass,
        Bypass:            feat_bypass,
        no_grass:          feat_no_grass,
        NoGrass:           feat_no_grass,
        maphack:           feat_maphack,
        MapHack:           feat_maphack
      },
      antiban_dns:       antiban_dns,
      valid:             true,
      panel_enabled:     true,
      type:              keyType,
      expires_at:        expiredOffset,
      reason:            '',
      features:          featuresObj,
      esp:               feat_esp,
      ESP:               feat_esp,
      ESPPlayer:         feat_esp,
      skill_hack:        feat_skill_hack,
      SkillHack:         feat_skill_hack,
      AutoSkillTable:    feat_skill_hack,
      retri_hack:        feat_retri_hack,
      RetriHack:         feat_retri_hack,
      unlock_skin:       feat_unlock_skin,
      UnlockSkin:        feat_unlock_skin,
      skill_cd:          feat_skill_cd,
      SkillCD:           feat_skill_cd,
      drone_view:        feat_drone_view,
      DroneView:         feat_drone_view,
      SetFieldOfView:    feat_drone_view,
      floating_button:   feat_floating_button,
      FloatingButton:    feat_floating_button,
      showFloatingButton: feat_floating_button,
      bypass:            feat_bypass,
      Bypass:            feat_bypass,
      no_grass:          feat_no_grass,
      NoGrass:           feat_no_grass,
      maphack:           feat_maphack,
      MapHack:           feat_maphack
    });
  } catch (err) {
    console.error('[-] Necro (QY) Error:', err.message);
    res.status(500).json({ status: false, reason: 'Internal Server Error', data: null });
  }
});

module.exports = router;
