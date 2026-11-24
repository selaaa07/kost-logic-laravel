@extends('layouts.user')

@section('title', 'Dashboard Penghuni')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0" style="color: #5b9bd5;">Halo, {{ $user->name }}!</h3>
        <p class="text-secondary mb-0">
            @if ($penghuni && $penghuni->kamar)
                Kamar {{ $penghuni->kamar->nomor_kamar }} &bull; Masuk sejak {{ \Carbon\Carbon::parse($penghuni->tanggal_masuk)->format('d M Y') }}
            @else
                Belum terdaftar di kamar
            @endif
        </p>
    </div>
</div>

{{-- Peringatan Merah Jika Ada Tagihan Belum Lunas --}}
@if ($adaTunggakan)
    <div class="alert alert-danger border-0 border-start border-5 border-danger shadow-sm mb-4">
        <strong>Peringatan:</strong> Anda memiliki tagihan kost yang belum dilunasi. Silakan cek tabel di bawah untuk melakukan pembayaran.
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="fw-bold mb-4">Riwayat Tagihan & Pembayaran</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Bulan Tagihan</th>
                        <th>Nominal</th>
                        <th>Tanggal Bayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pembayarans as $b)
                        <tr>
                            <td class="fw-semibold">{{ $b->bulan_tagihan }}</td>
                            <td>Rp {{ number_format($b->nominal, 0, ',', '.') }}</td>
                            <td>{{ $b->tanggal_bayar ? \Carbon\Carbon::parse($b->tanggal_bayar)->format('d M Y') : '-' }}</td>
                            <td>
                                @if ($b->status === 'lunas')
                                    <span class="badge bg-success px-3 py-2">Lunas</span>
                                @elseif ($b->status === 'menunggu')
                                    <span class="badge bg-warning text-dark px-3 py-2">Menunggu Konfirmasi</span>
                                @else
                                    <span class="badge bg-danger px-3 py-2">Belum Lunas</span>
                                    <div class="mt-1 small">
                                        @if ($b->bulan_tagihan === $bulanSekarang)
                                            @if ($sisaHari > 0)
                                                <span class="text-primary fw-semibold">
                                                    ⏳ Tenggat tgl {{ $hariTenggatValid }} ({{ $sisaHari }} hari lagi)
                                                </span>
                                            @elseif ($sisaHari === 0)
                                                <span class="text-warning fw-bold">
                                                    ⚠️ Jatuh tempo hari ini (tgl {{ $hariTenggatValid }})!
                                                </span>
                                            @else
                                                <span class="text-danger fw-bold">
                                                    🚨 Lewat tenggat {{ abs($sisaHari) }} hari (tenggat tgl {{ $hariTenggatValid }})
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-danger fw-bold">🚨 Menunggak bulan lalu</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($b->status === 'belum_lunas')
                                    <button type="button" class="btn btn-sm btn-primary" style="background-color: #5b9bd5; border:none;" data-bs-toggle="modal" data-bs-target="#modalBayar{{ $b->id }}">
                                        Bayar Kost
                                    </button>

                                    {{-- Modal Form Bayar 2 Metode --}}
                                    <div class="modal fade" id="modalBayar{{ $b->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header text-white" style="background-color: #5b9bd5;">
                                                    <h5 class="modal-title">Pembayaran {{ $b->bulan_tagihan }}</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('user.pembayaran.bayar', $b->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="alert alert-info small mb-3">
                                                            Jumlah Tagihan: <strong>Rp {{ number_format($b->nominal, 0, ',', '.') }}</strong>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small">Pilih Metode Pembayaran</label>
                                                            <select name="metode_bayar" class="form-select" id="pilihMetode{{ $b->id }}" onchange="toggleMetode{{ $b->id }}()" required>
                                                                <option value="transfer">Transfer Bank</option>
                                                                <option value="tunai">Uang Tunai (Cash)</option>
                                                            </select>
                                                        </div>

                                                        {{-- Area Transfer Bank --}}
                                                        <div id="areaTransfer{{ $b->id }}">
                                                            <div class="alert alert-secondary small p-2 mb-3">
                                                                Transfer ke: <strong>BCA 1234567890</strong> a.n Pemilik Kost
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold small">Upload Bukti Transfer</label>
                                                                <input type="file" name="bukti_bayar" id="inputBukti{{ $b->id }}" class="form-control" accept="image/*" required>
                                                            </div>
                                                        </div>

                                                        {{-- Area Tunai --}}
                                                        <div id="areaTunai{{ $b->id }}" style="display:none;">
                                                            <div class="alert alert-warning small mb-0">
                                                                Anda memilih pembayaran <strong>Tunai (Cash)</strong>. Tidak perlu upload bukti — klik <strong>Konfirmasi Bayar</strong> di bawah, lalu serahkan uang fisik langsung ke Admin Kost.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary btn-sm" style="background-color: #5b9bd5; border:none;">Konfirmasi Bayar</button>
                                                    </div>
                                                </form>

                                                <script>
                                                    function toggleMetode{{ $b->id }}() {
                                                        var metode = document.getElementById("pilihMetode{{ $b->id }}").value;
                                                        var areaTransfer = document.getElementById("areaTransfer{{ $b->id }}");
                                                        var areaTunai = document.getElementById("areaTunai{{ $b->id }}");
                                                        var inputBukti = document.getElementById("inputBukti{{ $b->id }}");

                                                        if (metode === "tunai") {
                                                            areaTransfer.style.display = "none";
                                                            areaTunai.style.display = "block";
                                                            inputBukti.removeAttribute("required");
                                                        } else {
                                                            areaTransfer.style.display = "block";
                                                            areaTunai.style.display = "none";
                                                            inputBukti.setAttribute("required", "required");
                                                        }
                                                    }
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($b->status === 'menunggu')
                                    <span class="text-secondary small fw-bold">Diproses Admin</span>
                                @elseif ($b->status === 'lunas')
                                    @php
                                        $urlKwitansi = \Illuminate\Support\Facades\Route::has('kwitansi.cetak')
                                            ? route('kwitansi.cetak', $b->id)
                                            : url('/kwitansi/' . $b->id);
                                    @endphp
                                    <a href="{{ $urlKwitansi }}" target="_blank" class="btn btn-sm btn-dark">Cetak Kwitansi</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-secondary">Belum ada riwayat tagihan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection