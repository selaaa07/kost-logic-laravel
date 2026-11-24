@extends('layouts.user')

@section('title', 'Beranda')

@section('content')
    <h3 class="fw-bold mb-3">Kamar Kosong</h3>

    <div class="row g-3">
        @forelse ($kamars as $kamar)
            <div class="col-md-4">
                <div class="card h-100">
                    @if ($kamar->foto)
                        <img src="{{ asset('storage/' . $kamar->foto) }}" class="card-img-top" alt="Kamar {{ $kamar->nomor_kamar }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">Kamar {{ $kamar->nomor_kamar }}</h5>
                        <p class="card-text text-muted small">{{ $kamar->fasilitas }}</p>
                        <p class="fw-bold">Rp {{ number_format($kamar->harga, 0, ',', '.') }} / bulan</p>

                        <button type="button" class="btn btn-outline-primary btn-sm"
                                data-bs-toggle="modal" data-bs-target="#foto{{ $kamar->id }}">
                            <i class="bi bi-image me-1"></i>Lihat foto
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="foto{{ $kamar->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Kamar {{ $kamar->nomor_kamar }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            @if ($kamar->foto)
                                <img src="{{ asset('storage/' . $kamar->foto) }}" class="img-fluid rounded"
                                     alt="Foto kamar {{ $kamar->nomor_kamar }}">
                            @else
                                <p class="text-muted my-4">Foto kamar ini belum tersedia.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">Belum ada kamar kosong saat ini.</p>
        @endforelse
    </div>

    {{-- Card hubungi admin --}}
    <div class="card mt-5">
        <div class="card-body text-center py-4">
            <h5 class="card-title fw-bold">Tertarik dengan kost logic?</h5>
            <p class="card-text text-muted">sungg chat admin di bawah</p>
            <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-success">
                <i class="bi bi-whatsapp me-1"></i>ADMIN HERE!
            </a>
        </div>
    </div>

    {{-- Card lokasi --}}
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title fw-bold mb-3">Lokasi</h5>
            <iframe src="https://maps.google.com/maps?q=Cilegon&output=embed"
                    width="100%" height="350" style="border:0;" class="rounded"
                    allowfullscreen loading="lazy"></iframe>
        </div>
    </div>
@endsection