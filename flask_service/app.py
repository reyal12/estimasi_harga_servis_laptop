"""
app.py – Flask microservice untuk estimasi biaya servis laptop (Fuzzy Mamdani).
Jalankan: python app.py  (atau gunicorn app:app di produksi)
"""

import os
from functools import wraps

from flask import Flask, jsonify, request

from fuzzy_engine import hitung_estimasi

app = Flask(__name__)

API_KEY = os.environ.get("FLASK_API_KEY", "estimasi-servis-secret-key-2024")
HOST    = os.environ.get("FLASK_HOST", "127.0.0.1")
PORT    = int(os.environ.get("FLASK_PORT", 5000))


# ---------------------------------------------------------------------------
# Auth decorator
# ---------------------------------------------------------------------------

def require_api_key(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        key = request.headers.get("X-API-Key", "")
        if key != API_KEY:
            return jsonify({
                "success": False,
                "error": {
                    "code":    "UNAUTHORIZED",
                    "message": "API key tidak valid.",
                },
            }), 401
        return f(*args, **kwargs)
    return decorated


# ---------------------------------------------------------------------------
# Endpoints
# ---------------------------------------------------------------------------

@app.route("/api/v1/health", methods=["GET"])
@require_api_key
def health():
    return jsonify({"status": "ok", "service": "fuzzy-estimasi-servis-laptop"}), 200


@app.route("/api/v1/estimate", methods=["POST"])
@require_api_key
def estimate():
    data = request.get_json(silent=True)
    if data is None:
        return jsonify({
            "success": False,
            "error": {"code": "BAD_REQUEST", "message": "Body harus JSON."},
        }), 400

    # Validasi input
    errors = {}
    kerusakan_raw  = data.get("tingkat_kerusakan")
    usia_raw       = data.get("usia_laptop")
    kesulitan_raw  = data.get("kesulitan_perbaikan")

    def _to_float(val, field, min_val, max_val):
        try:
            v = float(val)
            if not (min_val <= v <= max_val):
                errors[field] = f"Harus antara {min_val} dan {max_val}."
            return v
        except (TypeError, ValueError):
            errors[field] = "Harus berupa angka."
            return None

    kerusakan  = _to_float(kerusakan_raw,  "tingkat_kerusakan",   0, 100)
    usia       = _to_float(usia_raw,        "usia_laptop",          0,  10)
    kesulitan  = _to_float(kesulitan_raw,   "kesulitan_perbaikan",  0,  10)

    if errors:
        return jsonify({
            "success": False,
            "error": {
                "code":    "VALIDATION_ERROR",
                "message": "Input tidak valid.",
                "fields":  errors,
            },
        }), 422

    try:
        result = hitung_estimasi(kerusakan, usia, kesulitan)
        return jsonify(result), 200
    except ValueError as exc:
        app.logger.error("Fuzzy engine ValueError: %s", exc)
        return jsonify({
            "success": False,
            "error": {"code": "CALCULATION_ERROR", "message": str(exc)},
        }), 500
    except Exception as exc:  # noqa: BLE001
        app.logger.exception("Unexpected error in fuzzy engine")
        return jsonify({
            "success": False,
            "error": {"code": "INTERNAL_ERROR", "message": "Terjadi kesalahan internal."},
        }), 500


# ---------------------------------------------------------------------------
# Entry point
# ---------------------------------------------------------------------------

if __name__ == "__main__":
    app.run(host=HOST, port=PORT, debug=False)
