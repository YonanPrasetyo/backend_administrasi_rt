<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Pembayaran;
use App\Models\Rumah;

class PembayaranController extends Controller
{
    public function bulanConvert($bulan)
    {
        $bulanText = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        return $bulanText[$bulan];
    }
    public function index()
    {
        try {
            $pembayaran = Pembayaran::with('penghuni', 'rumah')->orderByDesc('created_at')->orderByDesc('bulan')->get()->map(function ($pembayaran) {
                return [
                    'id_pembayaran' => $pembayaran->id_pembayaran,
                    'tahun' => $pembayaran->tahun,
                    'bulan' => $this->bulanConvert($pembayaran->bulan),
                    'jenis' => $pembayaran->jenis,
                    'total' => $pembayaran->total,
                    'tanggal' => $pembayaran->tanggal,
                    'penghuni' => $pembayaran->penghuni->nama_lengkap,
                    'nomor_rumah' => $pembayaran->rumah->nomor_rumah
                ];
            });
            return response()->json([
                'message' => 'successfully fetch data',
                'data' => $pembayaran
            ],Response::HTTP_OK);
        }catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function pembayaran(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_penghuni' => 'required|exists:penghuni,id_penghuni',
                'id_rumah' => 'required|exists:rumah,id_rumah',
                'tahun' => 'required|integer',
                'bulan' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
                'jenis' => 'required|in:iuran satpam,iuran kebersihan',
                'tanggal' => 'required|date',
                'total_bulan' => 'required|integer',
            ]);

            $validated['total'] = $validated['jenis'] == 'iuran satpam' ? 100000 : 15000;

            $i = 0;
            do {
                $pembayaran = Pembayaran::create($validated);

                $validated['bulan']++;

                if ($validated['bulan'] > 12) {
                    $validated['tahun']++;
                    $validated['bulan'] = 1;
                }

                $i++;
            } while ($i < $validated['total_bulan']);

            return response()->json([
                'message' => 'successfully add data',
                'data' => $pembayaran
            ],Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $pembayaran = Pembayaran::find($id);
            if (!$pembayaran) {
                return response()->json([
                    'message' => 'pembayaran tidak ditemukan',
                ],Response::HTTP_NOT_FOUND);
            }

            $pembayaran->delete();

            return response()->json([
                'message' => 'successfully delete data',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function history($id)
    {
        try {
            $rumah = Rumah::where('id_rumah', $id)->first();

            if (!$rumah) {
                return response()->json([
                    'message' => 'rumah tidak ditemukan',
                ],Response::HTTP_NOT_FOUND);
            }

            $history = Pembayaran::where('id_rumah', $id)->orderByDesc('created_at')->get()->map(function ($pembayaran) {
                return [
                    'id_pembayaran' => $pembayaran->id_pembayaran,
                    'id_rumah' => $pembayaran->id_rumah,
                    'tahun' => $pembayaran->tahun,
                    'bulan' => $this->bulanConvert($pembayaran->bulan),
                    'jenis' => $pembayaran->jenis,
                    'total' => $pembayaran->total,
                    'tanggal' => $pembayaran->tanggal,
                ];
            });



            return response()->json([
                'message' => 'successfully fetch data',
                'data' => [
                    'rumah' => $rumah,
                    'history' => $history
                ]
            ],Response::HTTP_OK);
        }catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
