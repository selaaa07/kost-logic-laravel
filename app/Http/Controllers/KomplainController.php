<?php

namespace App\Http\Controllers;

use App\Models\Komplain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KomplainController extends Controller
{
    // --- SISI USER (PENGHUNI) ---
    public function indexUser()
    {
        $user = Auth::user();
        $penghuni = $user->penghuni;

        $komplains = Komplain::with('kamar')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('user.komplain', compact('penghuni', 'komplains'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'keluhan' => 'required|string|min:5|max:1000',
            'foto'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();
        $penghuni = $user->penghuni;

        if (!$penghuni) {
            return redirect()->route('user.komplain')
                ->with('error', 'Data kamar penghuni tidak ditemukan. Hubungi Admin.');
        }

        // Upload foto bukti ke storage/app/public/bukti_komplain
        $pathFoto = null;
        if ($request->hasFile('foto')) {
            $pathFoto = $request->file('foto')->store('bukti_komplain', 'public');
        }

        Komplain::create([
            'kamar_id' => $penghuni->kamar_id,
            'user_id'  => $user->id,
            'keluhan'  => $request->keluhan,
            'foto'     => $pathFoto,
            'status'   => 'menunggu',
        ]);

        // Wajib redirect agar tidak dobel saat browser di-refresh (F5)
        return redirect()->route('user.komplain')
            ->with('sukses', 'Laporan keluhan beserta bukti berhasil dikirim!');
    }

    // --- SISI ADMIN ---
    public function indexAdmin()
    {
        $komplains = Komplain::with(['kamar', 'user'])->latest()->get();
        return view('admin.komplain.index', compact('komplains'));
    }

    public function selesai($id)
    {
        $komplain = Komplain::findOrFail($id);
        $komplain->update(['status' => 'selesai']);

        return redirect()->route('admin.komplain.index')
            ->with('sukses', 'Status komplain berhasil diubah menjadi Selesai.');
    }
}