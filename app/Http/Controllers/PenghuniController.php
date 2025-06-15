<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Penghuni;


class PenghuniController extends Controller
{
    public function index()
    {
        try {
            $penghuni = Penghuni::
            whereDoesntHave('penghuni_rumah') // Tidak punya penghuni_rumah
            ->orWhereHas('penghuni_rumah', function ($query) {
                $query->whereNull('tanggal_keluar'); // Punya, tapi belum keluar
            })
            ->with('penghuni_rumah.rumah')
            ->get()
            ->map(function ($penghuni) {
                return [
                    'id_penghuni' => $penghuni->id_penghuni,
                    'nama_lengkap' => $penghuni->nama_lengkap,
                    'status_penghuni' => $penghuni->status_penghuni,
                    'nomor_telepon' => $penghuni->nomor_telepon,
                    'status_nikah' => $penghuni->status_nikah,
                    'tanggal_masuk' => $penghuni->penghuni_rumah->tanggal_masuk ?? null,
                    'tanggal_keluar' => $penghuni->penghuni_rumah->tanggal_keluar ?? null,
                    'nomor_rumah' => $penghuni->penghuni_rumah->rumah->nomor_rumah ?? null
                ];
            });



            return response()->json([
                'message' => 'successfully fetch data',
                'data' => $penghuni
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
            $penghuni = Penghuni::find($id);

            if (!$penghuni) {
                return response()->json([
                    'message' => 'penghuni tidak ditemukan',
                ],Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'successfully fetch data',
                'data' => [
                    'id_penghuni' => $penghuni->id_penghuni,
                    'nama_lengkap' => $penghuni->nama_lengkap,
                    'foto_ktp_filename' => $penghuni->foto_ktp,
                    'foto_ktp_url' => $penghuni->foto_ktp_url,
                    'status_penghuni' => $penghuni->status_penghuni,
                    'nomor_telepon' => $penghuni->nomor_telepon,
                    'status_nikah' => $penghuni->status_nikah,
                    'created_at' => $penghuni->created_at,
                    'updated_at' => $penghuni->updated_at
                ]
            ],Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_lengkap' => 'required|string',
                'foto_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'status_penghuni' => 'required|in:kontrak,tetap',
                'nomor_telepon' => 'required|string',
                'status_nikah' => 'required|in:belum,sudah',
            ]);

            $foto_ktp = $request->file('foto_ktp');
            $foto_ktp_filename = 'ktp_' . str_replace(' ', '_', $request->nama_lengkap) . '_' . time() . '_' . uniqid() . '.' . $foto_ktp->getClientOriginalExtension();
            $path = $request->file('foto_ktp')->storeAs('ktp', $foto_ktp_filename, 'public');
            $validated['foto_ktp'] = $foto_ktp_filename;

            $penghuni = Penghuni::create($validated);

            return response()->json([
                'message' => 'successfully add data',
                'data' => [
                    'id_penghuni' => $penghuni->id_penghuni,
                    'nama_lengkap' => $penghuni->nama_lengkap,
                    'foto_ktp_filename' => $penghuni->foto_ktp,
                    'foto_ktp_url' => $penghuni->foto_ktp_url,
                    'status_penghuni' => $penghuni->status_penghuni,
                    'nomor_telepon' => $penghuni->nomor_telepon,
                    'status_nikah' => $penghuni->status_nikah,
                    'created_at' => $penghuni->created_at,
                    'updated_at' => $penghuni->updated_at
                ]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nama_lengkap' => 'required|string',
                'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'status_penghuni' => 'required|in:kontrak,tetap',
                'nomor_telepon' => 'required|string',
                'status_nikah' => 'required|in:belum,sudah',
            ]);

            $penghuni = Penghuni::find($id);
            if (!$penghuni) {
                return response()->json([
                    'message' => 'penghuni tidak ditemukan',
                ],Response::HTTP_NOT_FOUND);
            }

            if ($request->hasFile('foto_ktp')) {
                if($penghuni->foto_ktp && Storage::disk('public')->exists('ktp/' . $penghuni->foto_ktp)) {
                    Storage::disk('public')->delete('ktp/' . $penghuni->foto_ktp);
                }

                $foto_ktp = $request->file('foto_ktp');
                $foto_ktp_filename = 'ktp_' . str_replace(' ', '_', $request->nama_lengkap) . '_' . time() . '_' . uniqid() . '.' . $foto_ktp->getClientOriginalExtension();
                $path = $request->file('foto_ktp')->storeAs('ktp', $foto_ktp_filename, 'public');
                $validated['foto_ktp'] = $foto_ktp_filename;
            }else {
                $validated['foto_ktp'] = $penghuni->foto_ktp;
            }

            $penghuni->update($validated);

            return response()->json([
                'message' => 'successfully update data',
                'data' => [
                    'id_penghuni' => $penghuni->id_penghuni,
                    'nama_lengkap' => $penghuni->nama_lengkap,
                    'foto_ktp_filename' => $penghuni->foto_ktp,
                    'foto_ktp_url' => $penghuni->foto_ktp_url,
                    'status_penghuni' => $penghuni->status_penghuni,
                    'nomor_telepon' => $penghuni->nomor_telepon,
                    'status_nikah' => $penghuni->status_nikah,
                    'created_at' => $penghuni->created_at,
                    'updated_at' => $penghuni->updated_at
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $penghuni = Penghuni::find($id);
            if (!$penghuni) {
                return response()->json([
                    'message' => 'penghuni tidak ditemukan',
                ],Response::HTTP_NOT_FOUND);
            }

            if($penghuni->foto_ktp && Storage::disk('public')->exists('ktp/' . $penghuni->foto_ktp)) {
                Storage::disk('public')->delete('ktp/' . $penghuni->foto_ktp);
            }

            $penghuni->delete();

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
