@extends('layouts.user')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Laporan Kerusakan & Komplain Kamar</h3>

    @if(session('sukses'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Form Kirim Komplain -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <strong>Kirim Laporan Baru</strong>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Kamar Anda:</strong> 
                        <span class="badge bg-info text-dark">
                            Kamar {{ $penghuni->kamar->nomor_kamar ?? 'Belum terdaftar' }}
                        </span>
                    </p>
                    
                    <form action="{{ route('user.komplain.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Detail Keluhan / Kerusakan</label>
                            <textarea name="keluhan" rows="4" class="form-control @error('keluhan') is-invalid @enderror" 
                                      placeholder="Contoh: Keran kamar mandi bocor atau lampu kamar mati..." required>{{ old('keluhan') }}</textarea>
                            @error('keluhan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto Bukti Kerusakan (Opsional)</label>
                            <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept=".jpg,.jpeg,.png">
                            <small class="text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB.</small>
                            @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Kirim Laporan</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Riwayat Komplain User -->
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <strong>Riwayat Komplain Saya</strong>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keluhan</th>
                                <th>Bukti Foto</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($komplains as $item)
                                <tr>
                                    <td>{{ $item->created_at->format('d M Y, H:i') }}</td>
                                    <td>{{ $item->keluhan }}</td>
                                    <td class="text-center">
                                        @if($item->foto)
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#bukti{{ $item->id }}">
                                                Lihat bukti
                                            </button>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status == 'selesai')
                                            <span class="badge bg-success">Selesai</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Menunggu</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada riwayat komplain.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal foto bukti (di luar tabel supaya tampilannya tidak rusak) --}}
@foreach($komplains as $item)
    @if($item->foto)
        <div class="modal fade" id="bukti{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Bukti komplain</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <img src="{{ asset('storage/' . $item->foto) }}" class="img-fluid rounded" alt="Bukti">
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection