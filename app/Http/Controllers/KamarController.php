<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KamarController extends Controller
{
    public function index()
    {
        $kamars = Kamar::orderBy('nomor_kamar', 'asc')->get();
        return view('admin.kamar.index', compact('kamars'));
    }

    public function create()
    {
        return view('admin.kamar.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor_kamar' => 'required|string|max:20|unique:kamars,nomor_kamar',
            'fasilitas'   => 'required|string',
            'harga'       => 'required|numeric|min:0',
            'status'      => 'required|in:kosong,terisi',
            'foto'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto_kamar', 'public');
        }

        Kamar::create($data);

        return redirect()->route('kamar.index')->with('sukses', 'Data kamar berhasil ditambahkan.');
    }

    public function edit(Kamar $kamar)
    {
        return view('admin.kamar.edit', compact('kamar'));
    }

    public function update(Request $request, Kamar $kamar)
    {
        $data = $request->validate([
            'nomor_kamar' => 'required|string|max:20|unique:kamars,nomor_kamar,' . $kamar->id,
            'fasilitas'   => 'required|string',
            'harga'       => 'required|numeric|min:0',
            'status'      => 'required|in:kosong,terisi',
            'foto'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($kamar->foto && Storage::disk('public')->exists($kamar->foto)) {
                Storage::disk('public')->delete($kamar->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto_kamar', 'public');
        }

        $kamar->update($data);

        return redirect()->route('kamar.index')->with('sukses', 'Data kamar berhasil diperbarui.');
    }

    public function destroy(Kamar $kamar)
    {
        if ($kamar->foto && Storage::disk('public')->exists($kamar->foto)) {
            Storage::disk('public')->delete($kamar->foto);
        }

        $kamar->delete();

        return redirect()->route('kamar.index')->with('sukses', 'Data kamar berhasil dihapus.');
    }
}