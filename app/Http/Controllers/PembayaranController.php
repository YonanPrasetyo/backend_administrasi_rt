<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Pembayaran;

class PembayaranController extends Controller
{
    public function index()
    {
        try {
            $pembayaran = Pembayaran::all();
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
            ]);

            $validated['total'] = $validated['jenis'] == 'iuran satpam' ? 100000 : 15000;

            $pembayaran = Pembayaran::create($validated);
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
}
