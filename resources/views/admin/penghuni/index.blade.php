@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Data Penghuni Kost</h3>
        <a href="{{ route('admin.penghuni.create') }}" class="btn btn-primary">+ Tambah Penghuni</a>
    </div>

    @if(session('sukses'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Penghuni</th>
                        <th>Email Login</th>
                        <th>No. HP</th>
                        <th>Kamar</th>
                        <th>Tanggal Masuk</th>
                        <th>Status</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penghunis as $index => $p)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $p->user->name ?? '-' }}</td>
                            <td>{{ $p->user->email ?? '-' }}</td>
                            <td>{{ $p->user->no_hp ?? '-' }}</td>
                            <td><span class="badge bg-info text-dark">Kamar {{ $p->kamar->nomor_kamar ?? '-' }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_masuk)->format('d M Y') }}</td>
                            <td>
                                @if($p->status_aktif)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.penghuni.edit', $p->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('admin.penghuni.destroy', $p->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin hapus penghuni ini? Akun login dan riwayat tagihannya juga akan terhapus, serta kamar kembali kosong.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada data penghuni.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection