@extends('layouts.admin')

@section('title', 'Riwayat Tagihan & Pembayaran')

@section('content')
{{-- 1. CARD CETAK LAPORAN KEUANGAN (3 BULAN / 6 BULAN / 1 TAHUN & FILTER PER KAMAR) --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-printer me-1 text-primary"></i> Cetak Laporan Keuangan
        </h6>
        <form action="{{ route('admin.pembayaran.laporan') }}" method="GET" target="_blank" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Periode Laporan</label>
                <select name="periode" id="selectPeriode" class="form-select form-select-sm">
                    <option value="3_bulan">Per 3 Bulan (3 Bulan Terakhir)</option>
                    <option value="6_bulan">Per 6 Bulan (6 Bulan Terakhir)</option>
                    <option value="1_tahun" selected>Per 1 Tahun Penuh</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Tahun</label>
                <input type="number" name="tahun" class="form-control form-control-sm" value="{{ date('Y') }}" min="2020" max="2099">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold mb-1">Filter Kamar (Opsional — 1 Tahun)</label>
                <select name="kamar_id" id="selectKamarLaporan" class="form-select form-select-sm" onchange="cekFilterKamar()">
                    <option value="">-- Semua Kamar --</option>
                    @foreach ($kamars as $k)
                        <option value="{{ $k->id }}">
                            Kamar {{ $k->nomor_kamar }} (Rp {{ number_format($k->harga, 0, ',', '.') }}/bln)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark btn-sm w-100">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Cetak Laporan
                </button>
            </div>
        </form>
        <small class="text-muted d-block mt-2">
            * Jika memilih <strong>Kamar tertentu</strong>, periode otomatis diset ke <strong>1 Tahun Penuh</strong> sesuai tahun yang dipilih.
        </small>
    </div>
</div>

{{-- 2. HEADER FILTER BULAN & TOMBOL TAMBAH TAGIHAN --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <form method="GET" action="{{ route('admin.pembayaran.index') }}" class="d-flex align-items-center gap-2">
        <select name="bulan" class="form-select form-select-sm" style="min-width: 180px;" onchange="this.form.submit()">
            <option value="">Semua Bulan</option>
            @foreach ($opsiBulan as $bln)
                <option value="{{ $bln }}" {{ $filterBulan === $bln ? 'selected' : '' }}>
                    {{ $bln }}
                </option>
            @endforeach
        </select>
        @if ($filterBulan !== '')
            <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        @endif
    </form>

    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahTagihan">
        <i class="bi bi-plus-circle me-1"></i> Tambah Tagihan Manual
    </button>
</div>

{{-- 3. TABEL RIWAYAT PEMBAYARAN --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Penghuni & Kamar</th>
                        <th>Bulan Tagihan</th>
                        <th>Nominal</th>
                        <th>Tanggal Bayar</th>
                        <th>Bukti / Metode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pembayarans as $p)
                        @php
                            $tglMasuk = $p->penghuni->tanggal_masuk ?? date('Y-m-01');
                            $hariTenggat = (int) \Carbon\Carbon::parse($tglMasuk)->format('d');
                            $hariTenggatValid = min($hariTenggat, (int) date('t'));
                            $tglJatuhTempo = \Carbon\Carbon::createFromDate(date('Y'), date('m'), $hariTenggatValid)->startOfDay();
                            $sisaHari = (int) \Carbon\Carbon::now()->startOfDay()->diffInDays($tglJatuhTempo, false);
                        @endphp
                        <tr>
                            <td class="fw-semibold">#{{ $p->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $p->penghuni->user->name ?? 'Penghuni Dihapus' }}</div>
                                <small class="text-muted">Kamar {{ $p->penghuni->kamar->nomor_kamar ?? '-' }}</small>
                            </td>
                            <td>{{ $p->bulan_tagihan }}</td>
                            <td class="fw-semibold">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                            <td>{{ $p->tanggal_bayar ? \Carbon\Carbon::parse($p->tanggal_bayar)->format('d M Y') : '-' }}</td>
                            <td>
                                @if ($p->metode_bayar === 'tunai')
                                    <span class="badge bg-warning text-dark">Tunai (Cash)</span>
                                @elseif ($p->bukti_bayar)
                                    <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalBukti{{ $p->id }}">
                                        <i class="bi bi-image me-1"></i> Lihat Bukti
                                    </button>

                                    {{-- Modal Lihat Bukti Transfer --}}
                                    <div class="modal fade" id="modalBukti{{ $p->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold">
                                                        Bukti Transfer - {{ $p->penghuni->user->name ?? '-' }} ({{ $p->bulan_tagihan }})
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <p class="small text-muted mb-2">
                                                        Nominal: <strong>Rp {{ number_format($p->nominal, 0, ',', '.') }}</strong>
                                                    </p>
                                                    <img src="{{ asset('storage/' . $p->bukti_bayar) }}" alt="Bukti Transfer" class="img-fluid rounded border">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">Belum ada data</span>
                                @endif
                            </td>
                            <td>
                                @if ($p->status === 'lunas')
                                    <span class="badge rounded-pill text-bg-success px-3 py-2">Lunas</span>
                                @elseif ($p->status === 'menunggu')
                                    <span class="badge rounded-pill text-bg-warning px-3 py-2">Menunggu Verifikasi</span>
                                @else
                                    <span class="badge rounded-pill text-bg-danger px-3 py-2">Belum Lunas</span>
                                    <div class="mt-1 small">
                                        @if ($p->bulan_tagihan === $bulanSekarang)
                                            @if ($sisaHari > 0)
                                                <span class="text-primary fw-semibold">⏳ Tenggat tgl {{ $hariTenggatValid }} ({{ $sisaHari }} hari lagi)</span>
                                            @elseif ($sisaHari === 0)
                                                <span class="text-warning fw-bold">⚠️ Jatuh tempo hari ini (tgl {{ $hariTenggatValid }})</span>
                                            @else
                                                <span class="text-danger fw-bold">🚨 Lewat {{ abs($sisaHari) }} hari (tenggat tgl {{ $hariTenggatValid }})</span>
                                            @endif
                                        @else
                                            <span class="text-danger fw-bold">🚨 Menunggak</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @if ($p->status !== 'lunas')
                                        <form action="{{ route('admin.pembayaran.verifikasi', $p->id) }}" method="POST" onsubmit="return confirm('Verifikasi pembayaran ini menjadi LUNAS?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-success btn-sm">
                                                <i class="bi bi-check-circle me-1"></i> Verifikasi Lunas
                                            </button>
                                        </form>
                                    @else
                                        @php
                                            $urlKwitansi = \Illuminate\Support\Facades\Route::has('kwitansi.cetak')
                                                ? route('kwitansi.cetak', $p->id)
                                                : url('/kwitansi/' . $p->id);
                                        @endphp
                                        <a href="{{ $urlKwitansi }}" target="_blank" class="btn btn-dark btn-sm">
                                            <i class="bi bi-printer me-1"></i> Cetak Kwitansi
                                        </a>
                                    @endif

                                    {{-- Tombol Edit --}}
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $p->id }}">
                                        Edit
                                    </button>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('admin.pembayaran.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data pembayaran #{{ $p->id }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </div>

                                {{-- Modal Edit Pembayaran (Batal Verifikasi / Upload Bukti Manual) --}}
                                <div class="modal fade" id="modalEdit{{ $p->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h6 class="modal-title fw-bold">Edit Pembayaran #{{ $p->id }} — {{ $p->penghuni->user->name ?? '-' }}</h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.pembayaran.update', $p->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Bulan Tagihan</label>
                                                        <input type="text" name="bulan_tagihan" class="form-control" value="{{ $p->bulan_tagihan }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Nominal (Rp)</label>
                                                        <input type="number" name="nominal" class="form-control" value="{{ (int) $p->nominal }}" min="0" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Tanggal Bayar</label>
                                                        <input type="date" name="tanggal_bayar" class="form-control" value="{{ $p->tanggal_bayar }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Metode Bayar</label>
                                                        <select name="metode_bayar" class="form-select">
                                                            <option value="" {{ empty($p->metode_bayar) ? 'selected' : '' }}>-- Belum Dipilih --</option>
                                                            <option value="transfer" {{ $p->metode_bayar === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                                            <option value="tunai" {{ $p->metode_bayar === 'tunai' ? 'selected' : '' }}>Tunai (Cash)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Status Pembayaran</label>
                                                        <select name="status" class="form-select" required>
                                                            <option value="belum_lunas" {{ $p->status === 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                                            <option value="menunggu" {{ $p->status === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                                            <option value="lunas" {{ $p->status === 'lunas' ? 'selected' : '' }}>Lunas</option>
                                                        </select>
                                                        <div class="form-text">Ubah ke Belum Lunas / Menunggu jika ingin membatalkan verifikasi Lunas.</div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Upload / Ganti Bukti Transfer (Opsional)</label>
                                                        @if ($p->bukti_bayar)
                                                            <div class="small text-muted mb-1">Saat ini sudah ada bukti tersimpan.</div>
                                                        @endif
                                                        <input type="file" name="bukti_bayar" class="form-control" accept="image/*">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Belum ada data tagihan pembayaran.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- 4. MODAL TAMBAH TAGIHAN MANUAL --}}
<div class="modal fade" id="modalTambahTagihan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title fw-bold">Tambah Tagihan Manual</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.pembayaran.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih Penghuni</label>
                        <select name="penghuni_id" id="pilihPenghuniModal" class="form-select" onchange="isiHargaOtomatis()" required>
                            <option value="">-- Pilih Penghuni --</option>
                            @foreach ($penghunis as $ph)
                                <option value="{{ $ph->id }}" data-harga="{{ $ph->kamar->harga ?? 0 }}">
                                    {{ $ph->user->name ?? '-' }} — Kamar {{ $ph->kamar->nomor_kamar ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Bulan Tagihan</label>
                        <input type="text" name="bulan_tagihan" class="form-control" value="{{ $bulanSekarang }}" placeholder="Contoh: November 2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nominal (Rp)</label>
                        <input type="number" name="nominal" id="inputNominalModal" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Status Awal</label>
                        <select name="status" class="form-select" required>
                            <option value="belum_lunas">Belum Lunas</option>
                            <option value="lunas">Lunas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Metode Bayar (Opsional)</label>
                        <select name="metode_bayar" class="form-select">
                            <option value="">-- Belum Dipilih --</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="tunai">Tunai (Cash)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Upload Bukti Transfer (Opsional)</label>
                        <input type="file" name="bukti_bayar" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Tagihan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function isiHargaOtomatis() {
        var select = document.getElementById('pilihPenghuniModal');
        var harga = select.options[select.selectedIndex].getAttribute('data-harga');
        if (harga) {
            document.getElementById('inputNominalModal').value = Math.round(harga);
        }
    }

    function cekFilterKamar() {
        var kamarId = document.getElementById('selectKamarLaporan').value;
        var selectPeriode = document.getElementById('selectPeriode');
        if (kamarId !== '') {
            selectPeriode.value = '1_tahun';
        }
    }
</script>
@endsection