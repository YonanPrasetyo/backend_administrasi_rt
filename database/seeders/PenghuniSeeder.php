<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenghuniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penghuniData = [
            [
                'nama_lengkap' => 'Ahmad Wijaya',
                'foto_ktp' => 'ktp_ahmad_wijaya.jpg',
                'status_penghuni' => 'tetap',
                'nomor_telepon' => '081234567890',
                'status_nikah' => 'sudah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Siti Nurhaliza',
                'foto_ktp' => 'ktp_siti_nurhaliza.jpg',
                'status_penghuni' => 'tetap',
                'nomor_telepon' => '081234567891',
                'status_nikah' => 'belum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Budi Santoso',
                'foto_ktp' => 'ktp_budi_santoso.jpg',
                'status_penghuni' => 'tetap',
                'nomor_telepon' => '081234567892',
                'status_nikah' => 'sudah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Rina Kartika',
                'foto_ktp' => 'ktp_rina_kartika.jpg',
                'status_penghuni' => 'tetap',
                'nomor_telepon' => '081234567893',
                'status_nikah' => 'belum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Doni Pratama',
                'foto_ktp' => 'ktp_doni_pratama.jpg',
                'status_penghuni' => 'tetap',
                'nomor_telepon' => '081234567894',
                'status_nikah' => 'sudah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Maya Sari',
                'foto_ktp' => 'ktp_maya_sari.jpg',
                'status_penghuni' => 'tetap',
                'nomor_telepon' => '081234567895',
                'status_nikah' => 'belum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Andi Kurniawan',
                'foto_ktp' => 'ktp_andi_kurniawan.jpg',
                'status_penghuni' => 'tetap',
                'nomor_telepon' => '081234567896',
                'status_nikah' => 'sudah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Dewi Lestari',
                'foto_ktp' => 'ktp_dewi_lestari.jpg',
                'status_penghuni' => 'tetap',
                'nomor_telepon' => '081234567897',
                'status_nikah' => 'belum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Rudi Hermawan',
                'foto_ktp' => 'ktp_rudi_hermawan.jpg',
                'status_penghuni' => 'tetap',
                'nomor_telepon' => '081234567898',
                'status_nikah' => 'sudah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Sari Indah',
                'foto_ktp' => 'ktp_sari_indah.jpg',
                'status_penghuni' => 'tetap',
                'nomor_telepon' => '081234567899',
                'status_nikah' => 'belum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Agus Setiawan',
                'foto_ktp' => 'ktp_agus_setiawan.jpg',
                'status_penghuni' => 'tetap',
                'nomor_telepon' => '081234567800',
                'status_nikah' => 'sudah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Lina Marlina',
                'foto_ktp' => 'ktp_lina_marlina.jpg',
                'status_penghuni' => 'tetap',
                'nomor_telepon' => '081234567801',
                'status_nikah' => 'belum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Hendra Gunawan',
                'foto_ktp' => 'ktp_hendra_gunawan.jpg',
                'status_penghuni' => 'tetap',
                'nomor_telepon' => '081234567802',
                'status_nikah' => 'sudah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Fitri Ramadhani',
                'foto_ktp' => 'ktp_fitri_ramadhani.jpg',
                'status_penghuni' => 'tetap',
                'nomor_telepon' => '081234567803',
                'status_nikah' => 'belum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_lengkap' => 'Yudi Prasetyo',
                'foto_ktp' => 'ktp_yudi_prasetyo.jpg',
                'status_penghuni' => 'tetap',
                'nomor_telepon' => '081234567804',
                'status_nikah' => 'sudah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert data ke database
        DB::table('penghuni')->insert($penghuniData);
    }
}
