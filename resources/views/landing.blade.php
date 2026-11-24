@extends('layouts.user')

@section('title', 'Beranda')

@section('content')
    <h3 class="fw-bold mb-3">Kamar Kosong</h3>

    <div class="row g-3">
        @forelse ($kamars as $kamar)
            <div class="col-md-4">
                <div class="card h-100">
                    @if ($kamar->foto)
                        <img src="{{ asset('storage/' . $kamar->foto) }}"
                             class="card-img-top"
                             style="height: 220px; object-fit: cover;"
                             alt="Kamar {{ $kamar->nomor_kamar }}">
                    @else
                        <div class="bg-light text-muted d-flex align-items-center justify-content-center"
                             style="height: 220px;">
                            <small>Foto belum tersedia</small>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">Kamar {{ $kamar->nomor_kamar }}</h5>
                        <p class="card-text text-muted small">{{ $kamar->fasilitas }}</p>
                        <p class="fw-bold mb-0">Rp {{ number_format($kamar->harga, 0, ',', '.') }} / bulan</p>
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