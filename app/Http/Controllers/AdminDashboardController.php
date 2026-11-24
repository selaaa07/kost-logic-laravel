<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\Pembayaran;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalKamar = Kamar::count();
        $kamarKosong = Kamar::where('status', 'kosong')->count();
        $totalPenghuni = Penghuni::count();
        $tagihanBelumLunas = Pembayaran::where('status', 'belum_lunas')->count();

        return view('admin.dashboard', compact(
            'totalKamar',
            'kamarKosong',
            'totalPenghuni',
            'tagihanBelumLunas'
        ));
    }
}