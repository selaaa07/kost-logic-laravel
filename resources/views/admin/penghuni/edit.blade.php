@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h3>Edit Data Penghuni</h3>
    </div>

    <div class="card shadow-sm col-md-8">
        <div class="card-body">
            <form action="{{ route('admin.penghuni.update', $penghuni->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $penghuni->user->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Login</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $penghuni->user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Reset Password (Opsional)</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak diubah">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nomor HP / WhatsApp</label>
                    <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', $penghuni->user->no_hp) }}">
                    @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kamar</label>
                        <select name="kamar_id" class="form-select @error('kamar_id') is-invalid @enderror" required>
                            @foreach($kamars as $kamar)
                                <option value="{{ $kamar->id }}" {{ old('kamar_id', $penghuni->kamar_id) == $kamar->id ? 'selected' : '' }}>
                                    Kamar {{ $kamar->nomor_kamar }}
                                </option>
                            @endforeach
                        </select>
                        @error('kamar_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control @error('tanggal_masuk') is-invalid @enderror" value="{{ old('tanggal_masuk', $penghuni->tanggal_masuk) }}" required>
                        @error('tanggal_masuk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status Penghuni</label>
                        <select name="status_aktif" class="form-select" required>
                            <option value="1" {{ old('status_aktif', $penghuni->status_aktif) == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status_aktif', $penghuni->status_aktif) == 0 ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Update Data</button>
                    <a href="{{ route('admin.penghuni.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection