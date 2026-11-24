<?php

namespace App\Http\Controllers;

use App\Models\EstimasiServis;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->attributes->get('session_token');
        $jumlahEstimasi = EstimasiServis::where('session_token', $token)->count();

        return view('dashboard', compact('jumlahEstimasi'));
    }
}
