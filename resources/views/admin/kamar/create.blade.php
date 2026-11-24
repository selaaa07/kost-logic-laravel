@extends('layouts.admin')

@section('title', 'Tambah Kamar')

@section('content')
<div class="card border-0 shadow-sm col-lg-8">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0">Form Tambah Kamar Baru</h6>
    </div>

    <div class="card-body">
        <form action="{{ route('kamar.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Nomor Kamar</label>
                <input type="text" name="nomor_kamar" class="form-control" value="{{ old('nomor_kamar') }}" placeholder="Contoh: 101" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Fasilitas</label>
                <textarea name="fasilitas" rows="3" class="form-control" placeholder="Contoh: AC, Kasur, Lemari, Kamar Mandi Dalam, WiFi" required>{{ old('fasilitas') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Harga per Bulan (Rp)</label>
                <input type="number" name="harga" class="form-control" value="{{ old('harga') }}" placeholder="Contoh: 1200000" min="0" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select" required>
                    <option value="kosong" {{ old('status') == 'kosong' ? 'selected' : '' }}>Kosong</option>
                    <option value="terisi" {{ old('status') == 'terisi' ? 'selected' : '' }}>Terisi</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Foto Kamar (Opsional)</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
                <small class="text-muted">Format: JPG, JPEG, PNG, WEBP. Maksimal 2MB.</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Simpan
                </button>
                <a href="{{ route('kamar.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection