<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penghuni;
use App\Models\Rumah;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now()->startOfMonth();
        $startDate = $now->copy()->subMonths(11);

        $pendapatan_awal = DB::table('pembayaran')
            ->where('tanggal', '<', $startDate)
            ->sum('total');

        $pengeluaran_awal = DB::table('pengeluaran')
            ->where('tanggal', '<', $startDate)
            ->sum('jumlah');

        $saldo_awal = $pendapatan_awal - $pengeluaran_awal;

        $data_pembayaran = DB::table('pembayaran')
            ->selectRaw('MONTH(tanggal) as bulan, YEAR(tanggal) as tahun, SUM(total) as total_pendapatan')
            ->where('tanggal', '>=', $startDate)
            ->groupBy(DB::raw('YEAR(tanggal)'), DB::raw('MONTH(tanggal)'))
            ->orderBy(DB::raw('YEAR(tanggal)'))
            ->orderBy(DB::raw('MONTH(tanggal)'))
            ->get()
            ->keyBy(function ($item) {
                return $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
            });

        $data_pengeluaran = DB::table('pengeluaran')
            ->selectRaw('MONTH(tanggal) as bulan, YEAR(tanggal) as tahun, SUM(jumlah) as total_pengeluaran')
            ->where('tanggal', '>=', $startDate)
            ->groupBy(DB::raw('YEAR(tanggal)'), DB::raw('MONTH(tanggal)'))
            ->orderBy(DB::raw('YEAR(tanggal)'))
            ->orderBy(DB::raw('MONTH(tanggal)'))
            ->get()
            ->keyBy(function ($item) {
                return $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
            });

        // Gabungkan pembayaran dan pengeluaran
        $result = [];
        $saldo_berjalan = $saldo_awal;
        for ($i = 0; $i < 12; $i++) {
            $date = $startDate->copy()->addMonths($i);
            $key = $date->format('Y-m');

            $pembayaran = $data_pembayaran->get($key);
            $pengeluaran = $data_pengeluaran->get($key);

            $total_pembayaran = $pembayaran->total_pendapatan ?? 0;
            $total_pengeluaran = $pengeluaran->total_pengeluaran ?? 0;
            $saldo_berjalan += $total_pembayaran - $total_pengeluaran;

            $result[] = [
                'bulan' => (int) $date->format('m'),
                'tahun' => (int) $date->format('Y'),
                'total_pendapatan' => $pembayaran->total_pendapatan ?? 0,
                'total_pengeluaran' => $pengeluaran->total_pengeluaran ?? 0,
                'saldo' => $saldo_berjalan
            ];
        }

        $penghuni = Penghuni::whereDoesntHave('penghuni_rumah')
            ->orWhereHas('penghuni_rumah', function ($query) {
                $query->whereNull('tanggal_keluar');
            })
            ->with('penghuni_rumah.rumah')
            ->count();

        $rumah = Rumah::count();
        $total_pembayaran = Pembayaran::sum('total');
        $total_pengeluaran = Pengeluaran::sum('jumlah');
        $total_saldo = $total_pembayaran - $total_pengeluaran;

        return response()->json([
            'penghuni' => $penghuni,
            'rumah' => $rumah,
            'pembayaran_bulan_ini' => end($result)['total_pendapatan'],
            'total_saldo' => $total_saldo,
            'per_bulan' => $result
        ]);
    }
}
