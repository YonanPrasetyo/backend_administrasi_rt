<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;

class PengeluaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $start = Carbon::create(2024, 1, 1);
        $end = Carbon::create(2025, 5, 1);

        $pengeluaranTambahan = [
            ['nama' => 'Perbaikan Jalan', 'min' => 1000000, 'max' => 5000000],
            ['nama' => 'Perbaikan Selokan', 'min' => 500000, 'max' => 3000000],
            ['nama' => 'Renovasi Pos Satpam', 'min' => 2000000, 'max' => 8000000],
            ['nama' => 'Pembelian Alat Kebersihan', 'min' => 300000, 'max' => 1000000],
            ['nama' => 'Pengaspalan Ulang Jalan RT', 'min' => 5000000, 'max' => 15000000],
        ];

        while ($start <= $end) {
            $tanggal = $start->copy()->day(3)->toDateString();

            // Gaji Satpam
            DB::table('pengeluaran')->insert([
                'tanggal' => $tanggal,
                'nama' => 'Gaji Satpam',
                'jumlah' => 1500000,
                'keterangan' => 'Pembayaran gaji satpam bulan ' . $start->format('F Y'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Token listrik pos satpam
            DB::table('pengeluaran')->insert([
                'tanggal' => $tanggal,
                'nama' => 'Token Listrik Pos Satpam',
                'jumlah' => 200000,
                'keterangan' => 'Pembelian token listrik untuk pos satpam bulan ' . $start->format('F Y'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Pengeluaran tambahan acak (tidak muncul tiap bulan)
            foreach ($pengeluaranTambahan as $item) {
                if (rand(1, 100) <= 10) { // 33%
                    DB::table('pengeluaran')->insert([
                        'tanggal' => $start->copy()->day(rand(10, 25))->toDateString(),
                        'nama' => $item['nama'],
                        'jumlah' => rand($item['min'], $item['max']),
                        'keterangan' => 'Pengeluaran untuk ' . strtolower($item['nama']) . ' bulan ' . $start->format('F Y'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $start->addMonth();
        }

    }
}
