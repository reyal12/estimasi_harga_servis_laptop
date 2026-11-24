"""
fuzzy_engine.py
Implementasi Fuzzy Mamdani untuk estimasi biaya servis laptop.
Menggunakan NumPy agar langkah perhitungan transparan.
"""

import json
import os
import numpy as np

# ---------------------------------------------------------------------------
# Load parameter dari file konfigurasi
# ---------------------------------------------------------------------------
_CONFIG_PATH = os.path.join(os.path.dirname(__file__), "config", "fuzzy_params.json")

with open(_CONFIG_PATH, encoding="utf-8") as _f:
    _CONFIG = json.load(_f)

VERSI_FUZZY = _CONFIG["versi"]
_OUT_MIN    = _CONFIG["output_range"]["min"]
_OUT_MAX    = _CONFIG["output_range"]["max"]
_OUT_STEP   = _CONFIG["output_range"]["step"]
_VARIABLES  = _CONFIG["variables"]
_RULES      = _CONFIG["rules"]

# Universe of discourse untuk output (diskret, langkah Rp1.000)
_Z = np.arange(_OUT_MIN, _OUT_MAX + _OUT_STEP, _OUT_STEP, dtype=np.float64)


# ---------------------------------------------------------------------------
# Fungsi keanggotaan
# ---------------------------------------------------------------------------

def _trapesium(x: float, a: float, b: float, c: float, d: float) -> float:
    """Trapesium: naik a→b, plateau b→c, turun c→d."""
    if x <= a or x >= d:
        return 0.0
    if b <= x <= c:
        return 1.0
    if a < x < b:
        return (x - a) / (b - a)
    # c < x < d
    return (d - x) / (d - c)


def _segitiga(x: float, a: float, b: float, c: float) -> float:
    """Segitiga: naik a→b, turun b→c."""
    if x <= a or x >= c:
        return 0.0
    if x == b:
        return 1.0
    if a < x < b:
        return (x - a) / (b - a)
    return (c - x) / (c - b)


def _membership(x: float, set_def: dict) -> float:
    t = set_def["type"]
    p = set_def["params"]
    if t == "trapesium":
        return _trapesium(x, *p)
    if t == "segitiga":
        return _segitiga(x, *p)
    raise ValueError(f"Unknown membership type: {t}")


def _membership_output_array(set_def: dict) -> np.ndarray:
    """Hitung derajat keanggotaan output pada setiap titik _Z."""
    t = set_def["type"]
    p = set_def["params"]
    if t == "trapesium":
        a, b, c, d = p
        mu = np.where(_Z <= a, 0.0,
             np.where(_Z >= d, 0.0,
             np.where((_Z >= b) & (_Z <= c), 1.0,
             np.where(_Z < b, (_Z - a) / (b - a),
                              (d - _Z) / (d - c)))))
        return mu
    if t == "segitiga":
        a, b, c = p
        mu = np.where((_Z <= a) | (_Z >= c), 0.0,
             np.where(_Z < b, (_Z - a) / (b - a),
                              (c - _Z) / (c - b)))
        return mu
    raise ValueError(f"Unknown membership type: {t}")


# ---------------------------------------------------------------------------
# Fungsi utama
# ---------------------------------------------------------------------------

def hitung_estimasi(kerusakan: float, usia: float, kesulitan: float) -> dict:
    """
    Jalankan Fuzzy Mamdani dan kembalikan hasil.

    Returns dict dengan keys:
        success, estimasi_biaya, kategori_biaya, versi_fuzzy,
        derajat_keanggotaan, aturan_aktif
    """
    # 1. Fuzzifikasi
    dk_sets   = _VARIABLES["tingkat_kerusakan"]["sets"]
    ul_sets   = _VARIABLES["usia_laptop"]["sets"]
    kp_sets   = _VARIABLES["kesulitan_perbaikan"]["sets"]
    out_sets  = _VARIABLES["estimasi_biaya"]["sets"]

    mu_kerusakan  = {k: _membership(kerusakan, v)  for k, v in dk_sets.items()}
    mu_usia       = {k: _membership(usia, v)        for k, v in ul_sets.items()}
    mu_kesulitan  = {k: _membership(kesulitan, v)   for k, v in kp_sets.items()}

    derajat_keanggotaan = {
        "kerusakan": {k: round(v, 4) for k, v in mu_kerusakan.items()},
        "usia":      {k: round(v, 4) for k, v in mu_usia.items()},
        "kesulitan": {k: round(v, 4) for k, v in mu_kesulitan.items()},
    }

    # 2. Inferensi MIN + 3. Agregasi MAX (per himpunan output)
    aggregated = {k: np.zeros_like(_Z) for k in out_sets}
    aturan_aktif = []

    for rule in _RULES:
        alpha = min(
            mu_kerusakan[rule["kerusakan"]],
            mu_usia[rule["usia"]],
            mu_kesulitan[rule["kesulitan"]],
        )
        if alpha == 0.0:
            continue

        biaya_set = rule["biaya"]
        # Implikasi Mamdani: clipping pada alpha
        clipped = np.minimum(alpha, _membership_output_array(out_sets[biaya_set]))
        # Agregasi MAX
        aggregated[biaya_set] = np.maximum(aggregated[biaya_set], clipped)

        aturan_aktif.append({
            "no":    rule["no"],
            "alpha": round(alpha, 4),
            "biaya": biaya_set.capitalize(),
        })

    # Gabungkan semua himpunan output
    mu_total = np.zeros_like(_Z)
    for arr in aggregated.values():
        mu_total = np.maximum(mu_total, arr)

    # 4. Defuzzifikasi Centroid diskret
    sigma_mu = float(np.sum(mu_total))
    if sigma_mu == 0.0:
        raise ValueError("Sigma mu = 0: tidak ada aturan yang aktif.")

    z_star = float(np.sum(mu_total * _Z) / sigma_mu)

    # Pembulatan ke Rp1.000 terdekat
    estimasi_biaya = int(round(z_star / 1000) * 1000)

    # 5. Kategori: himpunan output dengan mu tertinggi pada z*
    idx = int(np.searchsorted(_Z, estimasi_biaya))
    idx = max(0, min(idx, len(_Z) - 1))

    mu_at_z = {}
    for k, arr in aggregated.items():
        mu_at_z[k] = float(arr[idx])

    max_mu  = max(mu_at_z.values())
    # Prioritas kategori (konservatif) bila seri: mahal > sedang > murah
    priority = ["mahal", "sedang", "murah"]
    kategori = next(
        k for k in priority if mu_at_z[k] == max_mu
    )

    return {
        "success":             True,
        "estimasi_biaya":      estimasi_biaya,
        "kategori_biaya":      kategori.capitalize(),
        "versi_fuzzy":         VERSI_FUZZY,
        "derajat_keanggotaan": derajat_keanggotaan,
        "aturan_aktif":        aturan_aktif,
    }
