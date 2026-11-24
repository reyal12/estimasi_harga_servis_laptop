<?php

namespace App\Http\Controllers;

use App\Http\Requests\EstimasiRequest;
use App\Models\EstimasiServis;
use App\Services\FuzzyApiService;
use Illuminate\Http\Request;

class EstimasiController extends Controller
{
    public function __construct(private FuzzyApiService $fuzzyApi) {}

    public function create()
    {
        return view('estimasi.form');
    }

    public function store(EstimasiRequest $request)
    {
        $token      = $request->attributes->get('session_token');
        $kerusakan  = (float) $request->input('tingkat_kerusakan');
        $usia       = (float) $request->input('usia_laptop');
        $kesulitan  = (float) $request->input('kesulitan_perbaikan');

        $result = $this->fuzzyApi->estimate($kerusakan, $usia, $kesulitan);

        if (! $result['success']) {
            return back()
                ->withInput()
                ->with('error', $result['error']);
        }

        $data = $result['data'];

        $estimasi = EstimasiServis::create([
            'session_token'       => $token,
            'tingkat_kerusakan'   => $kerusakan,
            'usia_laptop'         => $usia,
            'kesulitan_perbaikan' => $kesulitan,
            'estimasi_biaya'      => $data['estimasi_biaya'],
            'kategori_biaya'      => $data['kategori_biaya'],
            'detail_fuzzy'        => [
                'derajat_keanggotaan' => $data['derajat_keanggotaan'] ?? null,
                'aturan_aktif'        => $data['aturan_aktif'] ?? null,
            ],
            'versi_fuzzy'         => $data['versi_fuzzy'] ?? '1.0',
        ]);

        return redirect()->route('estimasi.hasil', $estimasi->id);
    }

    public function hasil(Request $request, int $id)
    {
        $token    = $request->attributes->get('session_token');
        $estimasi = EstimasiServis::where('id', $id)
            ->where('session_token', $token)
            ->firstOrFail();

        return view('estimasi.hasil', compact('estimasi'));
    }
}
