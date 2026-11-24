<?php

namespace App\Http\Controllers;

use App\Models\EstimasiServis;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $token    = $request->attributes->get('session_token');
        $riwayat  = EstimasiServis::where('session_token', $token)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('riwayat.index', compact('riwayat'));
    }

    public function show(Request $request, int $id)
    {
        $token    = $request->attributes->get('session_token');
        $estimasi = EstimasiServis::where('id', $id)
            ->where('session_token', $token)
            ->firstOrFail();

        return view('riwayat.detail', compact('estimasi'));
    }

    public function destroy(Request $request, int $id)
    {
        $token    = $request->attributes->get('session_token');
        $estimasi = EstimasiServis::where('id', $id)
            ->where('session_token', $token)
            ->firstOrFail();

        $estimasi->delete();

        return redirect()->route('riwayat.index')
            ->with('success', 'Catatan estimasi berhasil dihapus.');
    }
}
