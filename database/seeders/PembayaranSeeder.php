<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;

class PembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $start = Carbon::create(2024, 1, 1);
        $end = Carbon::create(2025, 5, 1);

        while ($start <= $end) {
            foreach (range(1, 15) as $id) {
                DB::table('pembayaran')->insert([
                    'id_penghuni' => $id,
                    'id_rumah' => $id,
                    'tahun' => $start->year,
                    'bulan' => $start->month,
                    'jenis' => 'iuran satpam',
                    'total' => 100000,
                    'tanggal' => $start->copy()->day(5)->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('pembayaran')->insert([
                    'id_penghuni' => $id,
                    'id_rumah' => $id,
                    'tahun' => $start->year,
                    'bulan' => $start->month,
                    'jenis' => 'iuran kebersihan',
                    'total' => 15000,
                    'tanggal' => $start->copy()->day(5)->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $start->addMonth();
        }
    }
}
