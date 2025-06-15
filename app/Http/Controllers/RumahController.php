<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Rumah;
use App\Models\PenghuniRumah;

class RumahController extends Controller
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
        return $bulanText[(int)$bulan] ?? null;
    }

    public function statusLunas($satpam, $kebersihan, $status_rumah)
    {
        if ($status_rumah == "tidak dihuni") {
            return "tidak aktif";
        }

        $bulan = date('n');
        $tahun = date('Y');

        $bulan_satpam = $satpam->bulan ?? null;
        $tahun_satpam = $satpam->tahun ?? null;
        $bulan_kebersihan = $kebersihan->bulan ?? null;
        $tahun_kebersihan = $kebersihan->tahun ?? null;

        if ($bulan_satpam == null || $tahun_satpam == null || $bulan_kebersihan == null || $tahun_kebersihan == null) {
            return "belum lunas";
        }

        // satpam lunas
        if (($bulan_satpam >= $bulan && $tahun_satpam >= $tahun) || $tahun_satpam > $tahun) {
            // kebersihan lunas
            if (($bulan_kebersihan >= $bulan && $tahun_kebersihan >= $tahun) || $tahun_kebersihan > $tahun) {
                return "lunas";
            }
        }

        return "belum lunas";
    }

    public function index()
    {
        try {
            $rumah = Rumah::with([
                                'penghuni_rumah',
                                'pembayaran' => function ($query) {
                                    $query->orderByDesc('tahun')
                                          ->orderByDesc('bulan');
                                }
                            ])
                            ->get()
                            ->map(function ($rumah) {

                $pembayaran_terakhir = $rumah->pembayaran
                ->groupBy('jenis')
                ->map(function ($group) {
                    return $group->first();
                });

                $pembayaranSatpam = $pembayaran_terakhir->get('iuran satpam');
                $pembayaranKebersihan = $pembayaran_terakhir->get('iuran kebersihan');

                $status_lunas = $this->statusLunas($pembayaranSatpam, $pembayaranKebersihan, $rumah->status_rumah);

                return [
                    'id_rumah' => $rumah->id_rumah,
                    'nomor_rumah' => $rumah->nomor_rumah,
                    'status_rumah' => $rumah->status_rumah,
                    'jumlah_penghuni_rumah' => $rumah->penghuni_rumah->count(),

                    'status_lunas' => $status_lunas,
                    'bulan_terakhir_satpam' => $this->bulanConvert($pembayaranSatpam->bulan ?? null),
                    'tahun_terakhir_satpam' => $pembayaranSatpam->tahun ?? null,

                    'bulan_terakhir_kebersihan' => $this->bulanConvert($pembayaranKebersihan->bulan ?? null),
                    'tahun_terakhir_kebersihan' => $pembayaranKebersihan->tahun ?? null,
                ];
            });


            return response()->json([
                'message' => 'successfully fetch data',
                'data' => $rumah
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
            $rumah = Rumah::with([
                            'penghuni_rumah.penghuni',
                            'pembayaran' => function ($query) {
                                $query->orderByDesc('tahun')
                                      ->orderByDesc('bulan');
                                }
                            ])
                        ->find($id);

            $pembayaran_terakhir = $rumah->pembayaran
            ->groupBy('jenis')
            ->map(function ($group) {
                return $group->first();
            });

            $pembayaranSatpam = $pembayaran_terakhir->get('iuran satpam');
            $pembayaranKebersihan = $pembayaran_terakhir->get('iuran kebersihan');

            $status_lunas = $this->statusLunas($pembayaranSatpam, $pembayaranKebersihan, $rumah->status_rumah);

            if (!$rumah) {
                return response()->json([
                    'message' => 'rumah tidak ditemukan',
                ],Response::HTTP_NOT_FOUND);
            }

            $penghuniRumah = $rumah->penghuni_rumah->map(function ($pr) {
                return [
                    'id_penghuni_rumah' => $pr->id_penghuni_rumah,
                    'id_penghuni' => $pr->id_penghuni,
                    'tanggal_masuk' => $pr->tanggal_masuk,
                    'tanggal_keluar' => $pr->tanggal_keluar,
                    'nama_lengkap' => $pr->penghuni->nama_lengkap,
                    'status_penghuni' => $pr->penghuni->status_penghuni,
                ];
            });

            $data = [
                'id_rumah' => $rumah->id_rumah,
                'nomor_rumah' => $rumah->nomor_rumah,
                'status_rumah' => $rumah->status_rumah,
                'penghuni_rumah' => $penghuniRumah,
                'status_lunas' => $status_lunas,

                'bulan_terakhir_satpam' => $this->bulanConvert($pembayaranSatpam->bulan ?? null),
                'tahun_terakhir_satpam' => $pembayaranSatpam->tahun ?? null,

                'bulan_terakhir_kebersihan' => $this->bulanConvert($pembayaranKebersihan->bulan ?? null),
                'tahun_terakhir_kebersihan' => $pembayaranKebersihan->tahun ?? null,
            ];

            return response()->json([
                'message' => 'successfully fetch data',
                'data' => $data
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
                'nomor_rumah' => 'required|string',
            ]);

            $validated['status_rumah'] = 'tidak dihuni';

            $rumah = Rumah::create($validated);

            return response()->json([
                'message' => 'successfully add data',
                'data' => $rumah
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
                'nomor_rumah' => 'required|string',
            ]);

            $rumah = Rumah::find($id);
            if (!$rumah) {
                return response()->json([
                    'message' => 'rumah tidak ditemukan',
                ],Response::HTTP_NOT_FOUND);
            }

            $rumah->update($validated);

            return response()->json([
                'message' => 'successfully update data',
                'data' => $rumah
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
            $rumah = Rumah::find($id);
            if (!$rumah) {
                return response()->json([
                    'message' => 'rumah tidak ditemukan',
                ],Response::HTTP_NOT_FOUND);
            }

            $rumah->delete();

            return response()->json([
                'message' => 'successfully delete data',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function add_penghuni(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'id_penghuni' => 'required|exists:penghuni,id_penghuni',
            ]);

            $rumah = Rumah::find($id);
            if (!$rumah) {
                return response()->json([
                    'message' => 'rumah tidak ditemukan',
                ],Response::HTTP_NOT_FOUND);
            }

            if ($rumah->status_rumah == 'tidak dihuni') {
                $rumah->update([
                    'status_rumah' => 'dihuni'
                ]);
            }

            PenghuniRumah::create([
                'id_rumah' => $id,
                'id_penghuni' => $validated['id_penghuni'],
                'tanggal_masuk' => now(),
            ]);

            return response()->json([
                'message' => 'successfully add data',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function remove_penghuni(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'id_penghuni' => 'required|exists:penghuni,id_penghuni',
            ]);

            $rumah = Rumah::find($id);
            if (!$rumah) {
                return response()->json([
                    'message' => 'rumah tidak ditemukan',
                ],Response::HTTP_NOT_FOUND);
            }

            PenghuniRumah::where('id_rumah', $id)
                            ->where('id_penghuni', $validated['id_penghuni'])
                            ->update(['tanggal_keluar' => now()]);

            return response()->json([
                'message' => 'successfully update data',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
