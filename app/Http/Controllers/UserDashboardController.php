<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * Daftar nama bulan bahasa Indonesia.
     */
    private array $daftarBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    /**
     * Halaman Dashboard Penghuni + Auto-Billing & Countdown Tenggat per tanggal_masuk.
     */
    public function index()
    {
        $user = Auth::user();
        $penghuni = $user->penghuni()->with('kamar')->first();

        $now = Carbon::now();
        $bulanSekarang = $this->daftarBulan[(int) $now->format('n')] . ' ' . $now->format('Y');

        $pembayarans = collect();
        $adaTunggakan = false;
        $hariTenggatValid = 1;
        $sisaHari = 0;

        if ($penghuni && $penghuni->kamar) {
            // 1. Auto-billing: terbitkan tagihan bulan ini otomatis jika bulan masuk <= bulan ini
            $akhirBulanIni = $now->copy()->endOfMonth()->toDateString();
            if ($penghuni->status_aktif && $penghuni->tanggal_masuk <= $akhirBulanIni) {
                $sudahAda = Pembayaran::where('penghuni_id', $penghuni->id)
                    ->where('bulan_tagihan', $bulanSekarang)
                    ->exists();

                if (!$sudahAda) {
                    Pembayaran::create([
                        'penghuni_id'   => $penghuni->id,
                        'bulan_tagihan' => $bulanSekarang,
                        'nominal'       => $penghuni->kamar->harga,
                        'status'        => 'belum_lunas',
                    ]);
                }
            }

            // 2. Hitung countdown tenggat sesuai tanggal_masuk penghuni
            $hariTenggat = (int) Carbon::parse($penghuni->tanggal_masuk)->format('d');
            $hariTenggatValid = min($hariTenggat, (int) $now->format('t'));
            $tglJatuhTempo = Carbon::createFromDate($now->year, $now->month, $hariTenggatValid)->startOfDay();
            $sisaHari = (int) $now->copy()->startOfDay()->diffInDays($tglJatuhTempo, false);

            // 3. Ambil riwayat pembayaran milik penghuni yang login
            $pembayarans = $penghuni->pembayarans()->latest()->get();
            $adaTunggakan = $pembayarans->where('status', 'belum_lunas')->isNotEmpty();
        }

        return view('user.dashboard', compact(
            'user',
            'penghuni',
            'pembayarans',
            'adaTunggakan',
            'bulanSekarang',
            'hariTenggatValid',
            'sisaHari'
        ));
    }

    /**
     * Proses pembayaran 2 metode:
     * - Transfer Bank (wajib upload bukti ke storage/app/public/bukti_bayar)
     * - Tunai / Cash (tanpa upload bukti, status menjadi 'menunggu')
     */
    public function bayar(Request $request, $id)
    {
        $penghuni = Auth::user()->penghuni;
        if (!$penghuni) {
            return redirect()->route('user.dashboard')->with('error', 'Data penghuni tidak ditemukan.');
        }

        $pembayaran = Pembayaran::where('id', $id)
            ->where('penghuni_id', $penghuni->id)
            ->firstOrFail();

        if ($pembayaran->status !== 'belum_lunas') {
            return redirect()->route('user.dashboard')->with('error', 'Tagihan ini sudah diproses atau lunas.');
        }

        $request->validate([
            'metode_bayar' => 'required|in:transfer,tunai',
            'bukti_bayar'  => 'required_if:metode_bayar,transfer|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'bukti_bayar.required_if' => 'Bukti transfer wajib diupload untuk metode Transfer.',
        ]);

        if ($request->metode_bayar === 'transfer') {
            $pathBukti = $request->file('bukti_bayar')->store('bukti_bayar', 'public');

            $pembayaran->update([
                'metode_bayar'  => 'transfer',
                'nama_bank'     => null,
                'bukti_bayar'   => $pathBukti,
                'status'        => 'menunggu',
                'tanggal_bayar' => Carbon::now()->toDateString(),
            ]);

            return redirect()->route('user.dashboard')
                ->with('sukses', 'Bukti transfer berhasil dikirim! Menunggu verifikasi Lunas dari Admin.');
        }

        // Metode Tunai (Cash)
        $pembayaran->update([
            'metode_bayar'  => 'tunai',
            'nama_bank'     => null,
            'bukti_bayar'   => null,
            'status'        => 'menunggu',
            'tanggal_bayar' => Carbon::now()->toDateString(),
        ]);

        return redirect()->route('user.dashboard')
            ->with('sukses', 'Metode Tunai dipilih! Silakan serahkan uang fisik ke Admin Kost agar dapat diverifikasi Lunas.');
    }
}