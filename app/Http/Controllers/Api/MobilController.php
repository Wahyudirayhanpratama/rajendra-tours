<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mobil;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MobilController extends Controller
{
    public function dataMobil(Request $request)
    {
        $mobils = Mobil::with(['jadwalsAktif'])->get(); // eager load jadwal aktif saja

        foreach ($mobils as $mobil) {
            // Ambil semua kursi yang sudah dipakai dari jadwal aktif
            $jadwalTerbaru = $mobil->jadwalsAktif->first();

            if ($jadwalTerbaru) {
                $kursi_db = DB::table('penumpangs')
                    ->join('pemesanans', 'penumpangs.pemesanan_id', '=', 'pemesanans.pemesanan_id')
                    ->where('pemesanans.jadwal_id', $jadwalTerbaru->jadwal_id)
                    ->where('pemesanans.status', '!=', 'Tiket dibatalkan') // ← tambahkan ini
                    ->whereNotNull('penumpangs.nomor_kursi')              // ← penting agar tidak ikut null
                    ->pluck('penumpangs.nomor_kursi')
                    ->toArray();

                $kursi_terpakai = [];
                foreach ($kursi_db as $kursi) {
                    $kursi_array = explode(',', $kursi);
                    $kursi_terpakai = array_merge($kursi_terpakai, array_map('trim', $kursi_array));
                }

                $mobil->kursi_terpakai = count($kursi_terpakai);
                $mobil->kursi_tersisa = $mobil->kapasitas - count($kursi_terpakai);
                $mobil->jadwal_terbaru = $jadwalTerbaru;
            } else {
                // Tidak ada jadwal aktif
                $mobil->kursi_terpakai = 0;
                $mobil->kursi_tersisa = $mobil->kapasitas;
                $mobil->jadwal_terbaru = null;
            }
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $mobils
                ]);
            }
        }
        return view('admin.data-mobil.data-mobil', compact('mobils'));
    }

    public function createMobil()
    {
        return view('admin.data-mobil.tambah-data-mobil');
    }

    public function storeMobil(Request $request)
    {
        $request->validate([
            'nama_mobil' => 'required',
            'nomor_polisi' => 'required|unique:mobils',
            'kapasitas' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $mobil = Mobil::create([
                'mobil_id' => Str::uuid(),
                'nama_mobil' => $request->nama_mobil,
                'nomor_polisi' => $request->nomor_polisi,
                'kapasitas' => $request->kapasitas,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mobil berhasil ditambahkan.',
                    'mobil' => $mobil
                ], 201);
            }

            return redirect()->route('data-mobil')->with('success', 'Mobil berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback(); // Jika ada yang gagal, rollback semua
            Log::error('Gagal menyimpan mobil', ['error' => $e->getMessage()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan data mobil.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withInput()->withErrors(['general_error' => 'Gagal menyimpan data mobil: ' . $e->getMessage()]);
        }
    }

    public function editMobil($id)
    {
        $mobil = Mobil::where('mobil_id', $id)->firstOrFail();
        return view('admin.data-mobil.edit-data-mobil', compact('mobil'));
    }

    public function updateMobil(Request $request, $id)
    {
        $request->validate([
            'nama_mobil' => 'required',
            'nomor_polisi' => 'required',
            'kapasitas' => 'required|numeric',
        ]);

        Mobil::where('mobil_id', $id)->update([
            'nama_mobil' => $request->nama_mobil,
            'nomor_polisi' => $request->nomor_polisi,
            'kapasitas' => $request->kapasitas,
        ]);
        $mobil = Mobil::where('mobil_id', $id)->first();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mobil berhasil diperbarui.',
                'mobil' => $mobil
            ], 200);
        }

        return redirect()->route('data-mobil')->with('success', 'Mobil berhasil diperbarui.');
    }

    public function deleteMobil(Request $request, $id)
    {
        $mobil = Mobil::where('mobil_id', $id)->delete();

        if (!$mobil) {
            return response()->json([
                'success' => false,
                'message' => 'Mobil tidak ditemukan.'
            ], 404);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mobil berhasil dihapus.',
                'mobil_id' => $id
            ], 200);
        }

        return redirect()->route('data-mobil')->with('success', 'Mobil berhasil dihapus.');
    }
}
