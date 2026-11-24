@extends('layouts.admin')

@section('title', 'Kelola Kamar')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Daftar Kamar Kost</h6>
        <a href="{{ route('kamar.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kamar
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nomor Kamar</th>
                        <th>Fasilitas</th>
                        <th>Harga / Bulan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kamars as $index => $kamar)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if ($kamar->foto)
                                    <img src="{{ asset('storage/' . $kamar->foto) }}"
                                         alt="Kamar {{ $kamar->nomor_kamar }}"
                                         class="rounded object-fit-cover"
                                         width="80" height="55">
                                @else
                                    <span class="badge bg-secondary">Tanpa Foto</span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $kamar->nomor_kamar }}</td>
                            <td>{{ $kamar->fasilitas }}</td>
                            <td>Rp {{ number_format($kamar->harga, 0, ',', '.') }}</td>
                            <td>
                                @if ($kamar->status == 'kosong')
                                    <span class="badge bg-success">Kosong</span>
                                @else
                                    <span class="badge bg-danger">Terisi</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('kamar.edit', $kamar->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('kamar.destroy', $kamar->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus kamar {{ $kamar->nomor_kamar }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data kamar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection