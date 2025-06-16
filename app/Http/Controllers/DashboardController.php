<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        try {

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
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function laporan()
    {
        try {
            $tanggal_terawal_pembayaran = DB::table('pembayaran')->min('tanggal');
            $tanggal_terawal_pengeluaran = DB::table('pengeluaran')->min('tanggal');

            // Ambil tanggal awal paling kecil
            $tanggal_awal = Carbon::parse($tanggal_terawal_pembayaran)
                ->min(Carbon::parse($tanggal_terawal_pengeluaran))
                ->startOfMonth();

            $now = Carbon::now()->startOfMonth();

            // Query pembayaran - hanya untuk ringkasan
            $data_pembayaran_summary = Pembayaran::with('rumah')
                ->selectRaw('MONTH(tanggal) as bulan, YEAR(tanggal) as tahun, SUM(total) as total_pendapatan')
                ->groupBy(DB::raw('YEAR(tanggal)'), DB::raw('MONTH(tanggal)'))
                ->orderBy(DB::raw('YEAR(tanggal)'))
                ->orderBy(DB::raw('MONTH(tanggal)'))
                ->get();

            // Ambil semua detail pembayaran
            $detail_pembayaran = Pembayaran::with('rumah')
                ->get()
                ->groupBy(function ($item) {
                    $date = Carbon::parse($item->tanggal);
                    return $date->format('Y') . '-' . str_pad($date->format('m'), 2, '0', STR_PAD_LEFT);
                });

            // Format data pembayaran dengan detail
            $data_pembayaran = $data_pembayaran_summary->keyBy(function ($item) {
                return $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
            })->map(function ($item) use ($detail_pembayaran) {
                $key = $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
                return [
                    'bulan' => $item->bulan,
                    'tahun' => $item->tahun,
                    'total_pendapatan' => $item->total_pendapatan,
                    'data' => $detail_pembayaran->get($key, collect())->map(function ($item) {
                        return [
                            'id_pembayaran' => $item->id_pembayaran,
                            'tanggal' => $item->tanggal,
                            'total' => $item->total,
                            'jenis' => $item->jenis,
                            'rumah' => $item->rumah ? [
                                'nomor_rumah' => $item->rumah->nomor_rumah,
                            ] : null
                        ];
                    })->toArray()
                ];
            });

            // Query pengeluaran - hanya untuk ringkasan
            $data_pengeluaran_summary = DB::table('pengeluaran')
                ->selectRaw('MONTH(tanggal) as bulan, YEAR(tanggal) as tahun, SUM(jumlah) as total_pengeluaran')
                ->groupBy(DB::raw('YEAR(tanggal)'), DB::raw('MONTH(tanggal)'))
                ->orderBy(DB::raw('YEAR(tanggal)'))
                ->orderBy(DB::raw('MONTH(tanggal)'))
                ->get();

            // Ambil semua detail pengeluaran
            $detail_pengeluaran = DB::table('pengeluaran')
                ->select('*')
                ->get()
                ->groupBy(function ($item) {
                    $date = Carbon::parse($item->tanggal);
                    return $date->format('Y') . '-' . str_pad($date->format('m'), 2, '0', STR_PAD_LEFT);
                });

            // Format data pengeluaran dengan detail
            $data_pengeluaran = $data_pengeluaran_summary->keyBy(function ($item) {
                return $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
            })->map(function ($item) use ($detail_pengeluaran) {
                $key = $item->tahun . '-' . str_pad($item->bulan, 2, '0', STR_PAD_LEFT);
                return [
                    'bulan' => $item->bulan,
                    'tahun' => $item->tahun,
                    'total_pengeluaran' => $item->total_pengeluaran,
                    'data' => $detail_pengeluaran->get($key, collect())->toArray()
                ];
            });

            $result = [];
            $saldo_berjalan = 0;
            $periode = $tanggal_awal->diffInMonths($now) + 1;

            // Loop seperti biasa (dari lama ke baru)
            for ($i = 0; $i < $periode; $i++) {
                $date = $tanggal_awal->copy()->addMonths($i);
                $key = $date->format('Y-m');

                $pembayaran = $data_pembayaran->get($key);
                $pengeluaran = $data_pengeluaran->get($key);

                // Perbaikan akses data
                $total_pendapatan = (int) ($pembayaran['total_pendapatan'] ?? 0);
                $total_pengeluaran = (int) ($pengeluaran['total_pengeluaran'] ?? 0);
                $sisa_saldo = $total_pendapatan - $total_pengeluaran;
                $saldo_berjalan += $sisa_saldo;

                $result[] = [
                    'key' => $key,
                    'bulan' => (int) $date->format('m'),
                    'tahun' => (int) $date->format('Y'),
                    'total_pendapatan' => $total_pendapatan,
                    'total_pengeluaran' => $total_pengeluaran,
                    'sisa_saldo' => $sisa_saldo,
                    'saldo_berjalan' => $saldo_berjalan
                ];
            }

            // Balik urutan array dari baru ke lama
            $result = array_reverse($result);

            return response()->json([
                'pembayaran' => $data_pembayaran,
                'pengeluaran' => $data_pengeluaran,
                'per_bulan' => $result,
                'periode' => $periode
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
