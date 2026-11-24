@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <h3 class="mb-4">Daftar Komplain Penghuni</h3>

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
                        <th>Tanggal Lapor</th>
                        <th>Kamar</th>
                        <th>Pelapor</th>
                        <th>Isi Keluhan</th>
                        <th>Bukti Foto</th>
                        <th>Status</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($komplains as $index => $k)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $k->created_at->format('d M Y, H:i') }}</td>
                            <td><span class="badge bg-info text-dark">Kamar {{ $k->kamar->nomor_kamar ?? '-' }}</span></td>
                            <td>{{ $k->user->name ?? '-' }}</td>
                            <td>{{ $k->keluhan }}</td>
                            <td class="text-center">
                                @if($k->foto)
                                    <a href="{{ asset('storage/' . $k->foto) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $k->foto) }}" alt="Bukti Komplain" width="80" class="img-thumbnail">
                                    </a>
                                @else
                                    <span class="text-muted small">Tidak ada foto</span>
                                @endif
                            </td>
                            <td>
                                @if($k->status == 'selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                @endif
                            </td>
                            <td>
                                @if($k->status == 'menunggu')
                                    <form action="{{ route('admin.komplain.selesai', $k->id) }}" method="POST"
                                          onsubmit="return confirm('Tandai keluhan ini sudah selesai ditangani?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">Tandai Selesai</button>
                                    </form>
                                @else
                                    <span class="text-muted small">Sudah ditangani</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada laporan komplain masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection