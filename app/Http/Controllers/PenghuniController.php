<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenghuniController extends Controller
{
    public function index()
    {
        $penghunis = Penghuni::with(['user', 'kamar'])->latest()->get();
        return view('admin.penghuni.index', compact('penghunis'));
    }

    public function create()
    {
        $kamars = Kamar::where('status', 'kosong')->orderBy('nomor_kamar')->get();
        return view('admin.penghuni.create', compact('kamars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'no_hp'         => 'nullable|string|max:20',
            'kamar_id'      => 'required|exists:kamars,id',
            'tanggal_masuk' => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'penghuni',
                'no_hp'    => $request->no_hp,
            ]);

            Penghuni::create([
                'user_id'       => $user->id,
                'kamar_id'      => $request->kamar_id,
                'tanggal_masuk' => $request->tanggal_masuk,
                'status_aktif'  => true,
            ]);


            Kamar::where('id', $request->kamar_id)->update(['status' => 'terisi']);
        });

        return redirect()->route('admin.penghuni.index')
            ->with('sukses', 'Penghuni baru dan akun login berhasil dibuat!');
    }

    public function edit(Penghuni $penghuni)
    {
        $kamars = Kamar::where('status', 'kosong')
            ->orWhere('id', $penghuni->kamar_id)
            ->orderBy('nomor_kamar')
            ->get();

        return view('admin.penghuni.edit', compact('penghuni', 'kamars'));
    }

    public function update(Request $request, Penghuni $penghuni)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $penghuni->user_id,
            'no_hp'         => 'nullable|string|max:20',
            'kamar_id'      => 'required|exists:kamars,id',
            'tanggal_masuk' => 'required|date',
            'status_aktif'  => 'required|boolean',
            'password'      => 'nullable|min:6',
        ]);

        DB::transaction(function () use ($request, $penghuni) {
            $userData = [
                'name'  => $request->name,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $penghuni->user->update($userData);

            // Jika pindah kamar, kosongkan kamar lama
            if ($penghuni->kamar_id != $request->kamar_id) {
                Kamar::where('id', $penghuni->kamar_id)->update(['status' => 'kosong']);
            }

            // Jika status Nonaktif (0 / Keluar), kamar otomatis kosong; jika Aktif (1), kamar terisi
            Kamar::where('id', $request->kamar_id)->update([
                'status' => $request->boolean('status_aktif') ? 'terisi' : 'kosong',
            ]);

            $penghuni->update([
                'kamar_id'      => $request->kamar_id,
                'tanggal_masuk' => $request->tanggal_masuk,
                'status_aktif'  => $request->status_aktif,
            ]);
        });

        return redirect()->route('admin.penghuni.index')
            ->with('sukses', 'Data penghuni berhasil diperbarui!');
    }

    public function destroy(Penghuni $penghuni)
    {
        DB::transaction(function () use ($penghuni) {
            Kamar::where('id', $penghuni->kamar_id)->update(['status' => 'kosong']);

            $user = $penghuni->user;
            if ($user) {
                $user->delete();
            } else {
                $penghuni->delete();
            }
        });

        return redirect()->route('admin.penghuni.index')
            ->with('sukses', 'Penghuni berhasil dihapus dan status kamar kembali kosong.');
    }
}