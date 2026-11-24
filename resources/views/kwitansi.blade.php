@extends('layouts.user')

@section('title', 'Kwitansi')

@section('content')
    @if ($pembayaran->status !== 'lunas')
        <div class="alert alert-warning">
            Kwitansi belum tersedia karena tagihan ini belum lunas.
        </div>
    @else
        <div class="card mx-auto" style="max-width: 600px;">
            <div class="card-body p-4">
                <div class="text-center border-bottom pb-3 mb-3">
                    <h4 class="fw-bold mb-0">Kost Logic</h4>
                    <small class="text-muted">Kwitansi Pembayaran</small>
                </div>

                <table class="table table-borderless mb-3">
                    <tr>
                        <td style="width: 40%;">No. Kwitansi</td>
                        <td>: KW-{{ str_pad($pembayaran->id, 5, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td>Nama Penghuni</td>
                        <td>: {{ $pembayaran->penghuni->user->name }}</td>
                    </tr>
                    <tr>
                        <td>Kamar</td>
                        <td>: {{ $pembayaran->penghuni->kamar->nomor_kamar }}</td>
                    </tr>
                    <tr>
                        <td>Pembayaran Bulan</td>
                        <td>: {{ $pembayaran->bulan_tagihan }}</td>
                    </tr>
                    <tr>
                        <td>Metode Bayar</td>
                        <td>: {{ ucfirst($pembayaran->metode_bayar ?? '-') }}
                            @if ($pembayaran->nama_bank) ({{ $pembayaran->nama_bank }}) @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Tanggal Bayar</td>
                        <td>: {{ $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>: <span class="badge bg-success">LUNAS</span></td>
                    </tr>
                </table>

                <div class="bg-light rounded p-3 text-center mb-4">
                    <small class="text-muted">Jumlah dibayar</small>
                    <h3 class="fw-bold mb-0">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</h3>
                </div>

                <div class="text-center d-print-none">
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Cetak
                    </button>
                </div>
            </div>
        </div>
    @endif
@endsection