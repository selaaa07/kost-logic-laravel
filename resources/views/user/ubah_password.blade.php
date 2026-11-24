@extends('layouts.user')

@section('title', 'Ubah Password')

@section('content')
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-body p-4">
            <h4 class="fw-bold mb-3">Ubah Password</h4>

            <form action="{{ route('user.ubah-password.update') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="password_lama" class="form-label">Password lama</label>
                    <input type="password" name="password_lama" id="password_lama" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password_baru" class="form-label">Password baru</label>
                    <input type="password" name="password_baru" id="password_baru" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password_baru_confirmation" class="form-label">Konfirmasi password baru</label>
                    <input type="password" name="password_baru_confirmation" id="password_baru_confirmation" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Simpan password</button>
            </form>
        </div>
    </div>
@endsection