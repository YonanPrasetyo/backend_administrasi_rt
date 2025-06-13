<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenghuniRumahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penghuniRumahData = [];

        for ($i = 1; $i <= 15; $i++) {
            $penghuniRumahData[] = [
                'id_penghuni' => $i,
                'id_rumah' => $i,
                'tanggal_masuk' => Carbon::now()->subMonths(rand(1, 24))->format('Y-m-d'),
                'tanggal_keluar' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert data ke database
        DB::table('penghuni_rumah')->insert($penghuniRumahData);
    }
}
