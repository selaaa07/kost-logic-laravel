<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\Penghuni;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranAdminController extends Controller
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
     * Auto-billing on-load: menerbitkan tagihan bulan berjalan secara otomatis
     * bagi penghuni aktif yang bulan masuknya <= bulan sekarang.
     */
    private function generateTagihanBulanIni(): string
    {
        $now = Carbon::now();
        $bulanSekarang = $this->daftarBulan[(int) $now->format('n')] . ' ' . $now->format('Y');
        $akhirBulanIni = $now->copy()->endOfMonth()->toDateString();

        $penghuniAktif = Penghuni::with('kamar')
            ->where('status_aktif', true)
            ->whereDate('tanggal_masuk', '<=', $akhirBulanIni)
            ->get();

        foreach ($penghuniAktif as $penghuni) {
            if (!$penghuni->kamar) {
                continue;
            }

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

        return $bulanSekarang;
    }

    /**
     * Halaman daftar pembayaran admin + filter bulan + form cetak laporan.
     */
    public function index(Request $request)
    {
        $bulanSekarang = $this->generateTagihanBulanIni();

        $filterBulan = $request->query('bulan', '');

        $query = Pembayaran::with(['penghuni.user', 'penghuni.kamar'])->latest();
        if ($filterBulan !== '') {
            $query->where('bulan_tagihan', $filterBulan);
        }
        $pembayarans = $query->get();

        // Daftar bulan unik untuk dropdown filter (urut kalender terbaru)
        $opsiBulan = Pembayaran::select('bulan_tagihan')
            ->groupBy('bulan_tagihan')
            ->orderByRaw("RIGHT(bulan_tagihan, 4) DESC, FIELD(SUBSTRING_INDEX(bulan_tagihan, ' ', 1), 'Desember','November','Oktober','September','Agustus','Juli','Juni','Mei','April','Maret','Februari','Januari') ASC")
            ->pluck('bulan_tagihan');

        // Data penghuni aktif untuk modal tambah tagihan manual
        $penghunis = Penghuni::with(['user', 'kamar'])
            ->where('status_aktif', true)
            ->get();

        // Data semua kamar untuk filter cetak laporan per kamar dalam 1 tahun
        $kamars = Kamar::orderBy('nomor_kamar', 'asc')->get();

        return view('admin.pembayaran.index', compact(
            'pembayarans',
            'opsiBulan',
            'filterBulan',
            'bulanSekarang',
            'penghunis',
            'kamars'
        ));
    }

    /**
     * Tambah tagihan manual dari modal Admin.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'penghuni_id'   => 'required|exists:penghunis,id',
            'bulan_tagihan' => 'required|string|max:50',
            'nominal'       => 'required|numeric|min:0',
            'status'        => 'required|in:belum_lunas,menunggu,lunas',
            'metode_bayar'  => 'nullable|in:transfer,tunai',
            'tanggal_bayar' => 'nullable|date',
            'bukti_bayar'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('bukti_bayar')) {
            $data['bukti_bayar'] = $request->file('bukti_bayar')->store('bukti_bayar', 'public');
        }

        if ($data['status'] === 'lunas' && empty($data['tanggal_bayar'])) {
            $data['tanggal_bayar'] = Carbon::now()->toDateString();
        }

        $data['nama_bank'] = null;

        Pembayaran::create($data);

        return redirect()->route('admin.pembayaran.index')
            ->with('sukses', 'Tagihan pembayaran berhasil ditambahkan.');
    }

    /**
     * Edit pembayaran (termasuk membatalkan verifikasi lunas & upload/ganti bukti transfer).
     */
    public function update(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $data = $request->validate([
            'bulan_tagihan' => 'required|string|max:50',
            'nominal'       => 'required|numeric|min:0',
            'tanggal_bayar' => 'nullable|date',
            'status'        => 'required|in:belum_lunas,menunggu,lunas',
            'metode_bayar'  => 'nullable|in:transfer,tunai',
            'bukti_bayar'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('bukti_bayar')) {
            if ($pembayaran->bukti_bayar && Storage::disk('public')->exists($pembayaran->bukti_bayar)) {
                Storage::disk('public')->delete($pembayaran->bukti_bayar);
            }
            $data['bukti_bayar'] = $request->file('bukti_bayar')->store('bukti_bayar', 'public');
        }

        // Pastikan nama_bank selalu bersih (tidak dipakai lagi)
        $data['nama_bank'] = null;

        $pembayaran->update($data);

        return redirect()->route('admin.pembayaran.index')
            ->with('sukses', 'Data pembayaran #' . $pembayaran->id . ' berhasil diperbarui.');
    }

    /**
     * Hapus data pembayaran beserta file bukti transfernya jika ada.
     */
    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        if ($pembayaran->bukti_bayar && Storage::disk('public')->exists($pembayaran->bukti_bayar)) {
            Storage::disk('public')->delete($pembayaran->bukti_bayar);
        }

        $pembayaran->delete();

        return redirect()->route('admin.pembayaran.index')
            ->with('sukses', 'Data pembayaran berhasil dihapus.');
    }

    /**
     * Verifikasi pembayaran menjadi lunas.
     */
    public function verifikasi($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $pembayaran->update([
            'status'        => 'lunas',
            'tanggal_bayar' => $pembayaran->tanggal_bayar ?? Carbon::now()->toDateString(),
        ]);

        return redirect()->route('admin.pembayaran.index')
            ->with('sukses', 'Pembayaran berhasil diverifikasi menjadi LUNAS.');
    }

    /**
     * Cetak Laporan Keuangan:
     * - Per 3 bulan terakhir / 6 bulan terakhir / 1 tahun penuh
     * - Atau filter khusus per kamar dalam 1 tahun
     */
    public function cetakLaporan(Request $request)
    {
        $periode = $request->query('periode', '1_tahun'); // 3_bulan, 6_bulan, 1_tahun
        $tahun   = (int) $request->query('tahun', date('Y'));
        $kamarId = $request->query('kamar_id', ''); // kosong = semua kamar

        $query = Pembayaran::with(['penghuni.user', 'penghuni.kamar'])
            ->where('status', 'lunas');

        // Jika memilih kamar tertentu, otomatis hitung dalam 1 tahun penuh
        $kamarTerpilih = null;
        if ($kamarId !== '') {
            $kamarTerpilih = Kamar::find($kamarId);
            $periode = '1_tahun';
            $query->whereHas('penghuni', function ($q) use ($kamarId) {
                $q->where('kamar_id', $kamarId);
            });
        }

        // Terapkan rentang tanggal berdasarkan periode
        $now = Carbon::now();
        if ($periode === '3_bulan') {
            $tanggalMulai = $now->copy()->subMonths(2)->startOfMonth();
            $tanggalAkhir = $now->copy()->endOfMonth();
            $labelPeriode = '3 Bulan Terakhir (' . $tanggalMulai->format('d M Y') . ' - ' . $tanggalAkhir->format('d M Y') . ')';
            $query->whereBetween('tanggal_bayar', [$tanggalMulai->toDateString(), $tanggalAkhir->toDateString()]);
        } elseif ($periode === '6_bulan') {
            $tanggalMulai = $now->copy()->subMonths(5)->startOfMonth();
            $tanggalAkhir = $now->copy()->endOfMonth();
            $labelPeriode = '6 Bulan Terakhir (' . $tanggalMulai->format('d M Y') . ' - ' . $tanggalAkhir->format('d M Y') . ')';
            $query->whereBetween('tanggal_bayar', [$tanggalMulai->toDateString(), $tanggalAkhir->toDateString()]);
        } else {
            $labelPeriode = 'Tahun ' . $tahun . ' (1 Tahun Penuh)';
            $query->whereYear('tanggal_bayar', $tahun);
        }

        $transaksis = $query->orderBy('tanggal_bayar', 'asc')->get();

        // Hitung ringkasan keuangan
        $totalPemasukan = $transaksis->sum('nominal');
        $totalTransfer  = $transaksis->where('metode_bayar', 'transfer')->sum('nominal');
        $totalTunai     = $transaksis->where('metode_bayar', 'tunai')->sum('nominal');

        // Rekap pemasukan per kamar
        $rekapPerKamar = $transaksis
            ->groupBy(fn ($item) => $item->penghuni->kamar->nomor_kamar ?? 'Tanpa Kamar')
            ->map(function ($items, $nomorKamar) {
                return [
                    'nomor_kamar'      => $nomorKamar,
                    'jumlah_transaksi' => $items->count(),
                    'total_nominal'    => $items->sum('nominal'),
                ];
            })
            ->sortKeys();

        return view('admin.pembayaran.laporan_cetak', compact(
            'transaksis',
            'labelPeriode',
            'kamarTerpilih',
            'totalPemasukan',
            'totalTransfer',
            'totalTunai',
            'rekapPerKamar',
            'tahun',
            'periode'
        ));
    }
}