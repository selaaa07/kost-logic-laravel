@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="row g-3">
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Kamar</div>
                    <h2 class="fw-bold mb-0 mt-1">{{ $totalKamar }}</h2>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded">
                    <i class="bi bi-door-closed fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Kamar Kosong</div>
                    <h2 class="fw-bold mb-0 mt-1 text-success">{{ $kamarKosong }}</h2>
                </div>
                <div class="bg-success bg-opacity-10 text-success p-3 rounded">
                    <i class="bi bi-door-open fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Penghuni</div>
                    <h2 class="fw-bold mb-0 mt-1 text-info">{{ $totalPenghuni }}</h2>
                </div>
                <div class="bg-info bg-opacity-10 text-info p-3 rounded">
                    <i class="bi bi-people fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Tagihan Belum Lunas</div>
                    <h2 class="fw-bold mb-0 mt-1 text-danger">{{ $tagihanBelumLunas }}</h2>
                </div>
                <div class="bg-danger bg-opacity-10 text-danger p-3 rounded">
                    <i class="bi bi-receipt fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        <h6 class="fw-bold mb-2">Selamat datang di Panel Admin Kost Logic</h6>
        <p class="text-muted mb-3">Gunakan menu di sebelah kiri untuk mengelola data kamar, penghuni, tagihan pembayaran, dan laporan komplain.</p>
        <a href="{{ route('kamar.index') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-door-open me-1"></i> Kelola Data Kamar
        </a>
    </div>
</div>
@endsection