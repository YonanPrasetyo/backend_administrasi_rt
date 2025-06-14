<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Rumah;

class RumahController extends Controller
{
    public function index()
    {
        try {
            $rumah = Rumah::all();

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
            $rumah = Rumah::find($id);

            if (!$rumah) {
                return response()->json([
                    'message' => 'rumah tidak ditemukan',
                ],Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'successfully fetch data',
                'data' => $rumah
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
                'status_rumah' => 'required|in:dihuni,tidak dihuni',
            ]);

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
                'status_rumah' => 'required|in:dihuni,tidak dihuni',
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
}
