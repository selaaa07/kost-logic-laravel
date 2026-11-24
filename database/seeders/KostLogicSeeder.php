<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kamar;
use App\Models\Penghuni;
use App\Models\Pembayaran;
use App\Models\Komplain;

class KostLogicSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        User::create([
            'name' => 'Admin Kost Logic',
            'email' => 'admin@kostlogic.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'no_hp' => '081200000000',
        ]);

        // 2. Data 10 Kamar
        $kamarData = [
            ['nomor_kamar' => '101', 'fasilitas' => 'Lantai 1 - Kipas Angin, Kasur, Lemari, Kamar Mandi Luar', 'harga' => 600000, 'status' => 'terisi'],
            ['nomor_kamar' => '102', 'fasilitas' => 'Lantai 1 - Kipas Angin, Kasur, Lemari, Kamar Mandi Luar', 'harga' => 600000, 'status' => 'terisi'],
            ['nomor_kamar' => '103', 'fasilitas' => 'Lantai 1 - Kipas Angin, Kasur, Lemari, Kamar Mandi Luar', 'harga' => 600000, 'status' => 'terisi'],
            ['nomor_kamar' => '104', 'fasilitas' => 'Lantai 1 - Kipas Angin, Kasur, Lemari, Kamar Mandi Luar', 'harga' => 600000, 'status' => 'kosong'],
            ['nomor_kamar' => '105', 'fasilitas' => 'Lantai 1 - Kipas Angin, Kasur, Lemari, Kamar Mandi Luar', 'harga' => 600000, 'status' => 'kosong'],
            ['nomor_kamar' => '201', 'fasilitas' => 'Lantai 2 - AC, Kasur, Lemari, Meja, Kamar Mandi Dalam', 'harga' => 1200000, 'status' => 'terisi'],
            ['nomor_kamar' => '202', 'fasilitas' => 'Lantai 2 - AC, Kasur, Lemari, Meja, Kamar Mandi Dalam', 'harga' => 1200000, 'status' => 'terisi'],
            ['nomor_kamar' => '203', 'fasilitas' => 'Lantai 2 - AC, Kasur, Lemari, Meja, Kamar Mandi Dalam', 'harga' => 1200000, 'status' => 'terisi'],
            ['nomor_kamar' => '204', 'fasilitas' => 'Lantai 2 - AC, Kasur, Lemari, Meja, Kamar Mandi Dalam', 'harga' => 1200000, 'status' => 'kosong'],
            ['nomor_kamar' => '205', 'fasilitas' => 'Lantai 2 - AC, Kasur, Lemari, Meja, Kamar Mandi Dalam', 'harga' => 1200000, 'status' => 'kosong'],
        ];

        $kamars = [];
        foreach ($kamarData as $item) {
            $kamars[$item['nomor_kamar']] = Kamar::create($item);
        }

        // 3. Data 6 Penghuni + Akun Login & Pembayaran Awal
        $penghuniData = [
            ['name' => 'Budi Santoso', 'email' => 'budi@kostlogic.com', 'no_hp' => '081234567890', 'kamar' => '101', 'tgl' => '2026-10-01', 'status_bayar' => 'lunas', 'metode' => 'transfer', 'bank' => null, 'tgl_bayar' => '2026-10-01', 'bukti' => 'bukti_bayar/bukti_1_budi.png'],
            ['name' => 'Andi Wijaya', 'email' => 'andi@kostlogic.com', 'no_hp' => '081298765432', 'kamar' => '102', 'tgl' => '2026-10-15', 'status_bayar' => 'belum_lunas', 'metode' => null, 'bank' => null, 'tgl_bayar' => null, 'bukti' => null],
            ['name' => 'Siti Aminah', 'email' => 'siti@kostlogic.com', 'no_hp' => '085712341234', 'kamar' => '103', 'tgl' => '2026-10-28', 'status_bayar' => 'lunas', 'metode' => 'transfer', 'bank' => null, 'tgl_bayar' => '2026-10-05', 'bukti' => 'bukti_bayar/bukti_3_siti.jpg'],
            ['name' => 'Rina Melati', 'email' => 'rina@kostlogic.com', 'no_hp' => '087811223344', 'kamar' => '201', 'tgl' => '2026-10-02', 'status_bayar' => 'lunas', 'metode' => 'tunai', 'bank' => null, 'tgl_bayar' => '2026-10-02', 'bukti' => null],
            ['name' => 'Doni Pratama', 'email' => 'doni@kostlogic.com', 'no_hp' => '081399887766', 'kamar' => '202', 'tgl' => '2026-10-28', 'status_bayar' => 'belum_lunas', 'metode' => null, 'bank' => null, 'tgl_bayar' => null, 'bukti' => null],
            ['name' => 'Fajar Hidayat', 'email' => 'fajar@kostlogic.com', 'no_hp' => '089644556677', 'kamar' => '203', 'tgl' => '2026-10-10', 'status_bayar' => 'lunas', 'metode' => 'transfer', 'bank' => null, 'tgl_bayar' => '2026-10-10', 'bukti' => 'bukti_bayar/bukti_6_fajar.jpg'],
        ];

        foreach ($penghuniData as $p) {
            $user = User::create([
                'name' => $p['name'],
                'email' => $p['email'],
                'password' => Hash::make('kost123'),
                'role' => 'penghuni',
                'no_hp' => $p['no_hp'],
            ]);

            $kamar = $kamars[$p['kamar']];

            $penghuni = Penghuni::create([
                'user_id' => $user->id,
                'kamar_id' => $kamar->id,
                'tanggal_masuk' => $p['tgl'],
                'status_aktif' => true,
            ]);

            Pembayaran::create([
                'penghuni_id' => $penghuni->id,
                'bulan_tagihan' => 'Oktober 2026',
                'nominal' => $kamar->harga,
                'status' => $p['status_bayar'],
                'metode_bayar' => $p['metode'],
                'nama_bank' => $p['bank'],
                'tanggal_bayar' => $p['tgl_bayar'],
                'bukti_bayar' => $p['bukti'] ?? null,
            ]);
        }

        // 4. Contoh 1 Data Komplain
        Komplain::create([
            'kamar_id' => $kamars['102']->id,
            'user_id' => User::where('email', 'andi@kostlogic.com')->first()->id,
            'keluhan' => 'Keran kamar mandi sedikit bocor',
            'status' => 'menunggu',
        ]);
    }
}