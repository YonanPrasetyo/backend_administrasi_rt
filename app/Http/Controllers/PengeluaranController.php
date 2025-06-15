<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Pengeluaran;

class PengeluaranController extends Controller
{
    public function index()
    {
        try {
            $pengeluaran = Pengeluaran::orderByDesc('tanggal')->get();
            return response()->json([
                'message' => 'successfully fetch data',
                'data' => $pengeluaran
            ],Response::HTTP_OK);
        }catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $pengeluaran = Pengeluaran::find($id);

            if (!$pengeluaran) {
                return response()->json([
                    'message' => 'pengeluaran tidak ditemukan',
                ],Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'successfully fetch data',
                'data' => $pengeluaran
            ],Response::HTTP_OK);
        }catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'nama' => 'required|string',
                'jumlah' => 'required|integer',
                'keterangan' => 'nullable|string',
            ]);

            $pengeluaran = Pengeluaran::create($validated);
            return response()->json([
                'message' => 'successfully add data',
                'data' => $pengeluaran
            ],Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'nama' => 'required|string',
                'jumlah' => 'required|integer',
                'keterangan' => 'nullable|string',
            ]);

            $pengeluaran = Pengeluaran::find($id);
            if (!$pengeluaran) {
                return response()->json([
                    'message' => 'pengeluaran tidak ditemukan',
                ],Response::HTTP_NOT_FOUND);
            }

            $pengeluaran->update($validated);

            return response()->json([
                'message' => 'successfully update data',
                'data' => $pengeluaran
            ],Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $pengeluaran = Pengeluaran::find($id);
            if (!$pengeluaran) {
                return response()->json([
                    'message' => 'pengeluaran tidak ditemukan',
                ],Response::HTTP_NOT_FOUND);
            }

            $pengeluaran->delete();

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
