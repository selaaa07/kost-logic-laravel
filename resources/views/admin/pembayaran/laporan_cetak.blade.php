<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Kost Logic - {{ $labelPeriode }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #212529;
        }
        .sheet {
            max-width: 900px;
            margin: 24px auto;
            background: #ffffff;
            padding: 36px;
            border-top: 6px solid #0d6efd;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .kop {
            border-bottom: 2px solid #212529;
            padding-bottom: 14px;
            margin-bottom: 24px;
        }
        .summary-box {
            background: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 12px 16px;
            border-radius: 4px;
        }
        @media print {
            body { background: #ffffff; }
            .sheet { box-shadow: none; margin: 0; padding: 16px; max-width: 100%; border-top: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="sheet">
        {{-- Tombol Cetak / Tutup (tidak ikut tercetak) --}}
        <div class="d-flex justify-content-end gap-2 mb-3 no-print">
            <button onclick="window.print()" class="btn btn-primary btn-sm px-3">
                🖨️ Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="btn btn-outline-secondary btn-sm">
                Tutup
            </button>
        </div>

        {{-- Kop Laporan --}}
        <div class="kop text-center">
            <h4 class="fw-bold mb-1">LAPORAN KEUANGAN PEMASUKAN KOST</h4>
            <p class="mb-0 fw-semibold text-primary">Sistem Informasi Kost Logic</p>
            <small class="text-muted">
                Periode: <strong>{{ $labelPeriode }}</strong>
                @if ($kamarTerpilih)
                    | Filter Khusus: <strong>Kamar {{ $kamarTerpilih->nomor_kamar }}</strong>
                @else
                    | Cakupan: <strong>Semua Kamar</strong>
                @endif
            </small>
        </div>

        {{-- Ringkasan Pemasukan --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="summary-box">
                    <div class="small text-muted">Total Pemasukan Lunas</div>
                    <div class="fs-5 fw-bold text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-box">
                    <div class="small text-muted">Via Transfer Bank</div>
                    <div class="fs-6 fw-bold">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-box">
                    <div class="small text-muted">Via Tunai (Cash)</div>
                    <div class="fs-6 fw-bold">Rp {{ number_format($totalTunai, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        {{-- Rekap Per Kamar (tampil jika Semua Kamar) --}}
        @if (!$kamarTerpilih && $rekapPerKamar->isNotEmpty())
            <h6 class="fw-bold mb-2">Rekap Pemasukan Per Kamar</h6>
            <table class="table table-bordered table-sm mb-4">
                <thead class="table-light">
                    <tr>
                        <th>Nomor Kamar</th>
                        <th class="text-center">Jumlah Transaksi Lunas</th>
                        <th class="text-end">Total Pemasukan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rekapPerKamar as $rekap)
                        <tr>
                            <td class="fw-semibold">Kamar {{ $rekap['nomor_kamar'] }}</td>
                            <td class="text-center">{{ $rekap['jumlah_transaksi'] }}x</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($rekap['total_nominal'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Rincian Transaksi Lunas --}}
        <h6 class="fw-bold mb-2">Rincian Transaksi Pembayaran Lunas</h6>
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center">No</th>
                    <th>Tanggal Bayar</th>
                    <th>Penghuni</th>
                    <th>Kamar</th>
                    <th>Bulan Tagihan</th>
                    <th>Metode</th>
                    <th class="text-end">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksis as $i => $t)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $t->tanggal_bayar ? \Carbon\Carbon::parse($t->tanggal_bayar)->format('d M Y') : '-' }}</td>
                        <td>{{ $t->penghuni->user->name ?? '-' }}</td>
                        <td>Kamar {{ $t->penghuni->kamar->nomor_kamar ?? '-' }}</td>
                        <td>{{ $t->bulan_tagihan }}</td>
                        <td>
                            @if ($t->metode_bayar === 'tunai')
                                Tunai
                            @elseif ($t->metode_bayar === 'transfer')
                                Transfer ({{ $t->nama_bank ?? '-' }})
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-end fw-semibold">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Tidak ada data pemasukan lunas pada periode/filter ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-secondary fw-bold">
                    <td colspan="6" class="text-end">TOTAL PEMASUKAN</td>
                    <td class="text-end">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Tanda Tangan --}}
        <div class="d-flex justify-content-end mt-5">
            <div class="text-center" style="width: 240px;">
                <p class="mb-5">Dicetak pada {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
                <div class="fw-bold text-decoration-underline mt-4">Admin Kost Logic</div>
            </div>
        </div>
    </div>
</body>
</html>