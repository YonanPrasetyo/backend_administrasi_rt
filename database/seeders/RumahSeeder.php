<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RumahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rumahData = [];

        for ($i = 1; $i <= 20; $i++) {
            $rumahData[] = [
                'nomor_rumah' => (string) $i,
                'status_rumah' => $i <= 15 ? 'dihuni' : 'tidak dihuni',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('rumah')->insert($rumahData);
    }
}
