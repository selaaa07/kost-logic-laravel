<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;

class KwitansiController extends Controller
{
    public function cetak($id)
    {
        $pembayaran = Pembayaran::with('penghuni.user', 'penghuni.kamar')->findOrFail($id);

        // Penghuni hanya boleh melihat kwitansi miliknya sendiri
        if (Auth::user()->role === 'penghuni' && $pembayaran->penghuni->user_id !== Auth::id()) {
            abort(403);
        }

        return view('kwitansi', compact('pembayaran'));
    }
}