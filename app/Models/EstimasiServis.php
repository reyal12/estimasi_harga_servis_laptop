<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimasiServis extends Model
{
    protected $table = 'estimasi_servis';

    protected $fillable = [
        'session_token',
        'tingkat_kerusakan',
        'usia_laptop',
        'kesulitan_perbaikan',
        'estimasi_biaya',
        'kategori_biaya',
        'detail_fuzzy',
        'versi_fuzzy',
    ];

    protected $casts = [
        'tingkat_kerusakan' => 'float',
        'usia_laptop' => 'float',
        'kesulitan_perbaikan' => 'float',
        'estimasi_biaya' => 'integer',
        'detail_fuzzy' => 'array',
    ];

    /**
     * Format estimasi_biaya as Indonesian Rupiah.
     */
    public function getEstimasiBiayaFormattedAttribute(): string
    {
        return 'Rp' . number_format($this->estimasi_biaya, 0, ',', '.');
    }
}
