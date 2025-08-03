<?php

namespace App\Http\Controllers;

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
                    ->where('pemesanans.status', '!=', 'Tiket dibatalkan')
                    ->whereNotNull('penumpangs.nomor_kursi')
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
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $gambarName = null;

        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar');
            $gambarName = time() . '_' . Str::random(3) . '.' . $gambar->getClientOriginalExtension();
            $gambar->move(public_path('uploads/mobil'), $gambarName);
        }

        DB::beginTransaction();

        try {
            Mobil::create([
                'mobil_id' => Str::uuid(),
                'nama_mobil' => $request->nama_mobil,
                'nomor_polisi' => $request->nomor_polisi,
                'kapasitas' => $request->kapasitas,
                'gambar' => $gambarName,
            ]);

            DB::commit();

            return redirect()->route('data-mobil')->with('success', 'Mobil berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback(); // Jika ada yang gagal, rollback semua
            Log::error('Gagal menyimpan mobil', ['error' => $e->getMessage()]);

            return back()->withInput()->withErrors(['general_error' => 'Gagal menyimpan data mobil: ' . $e->getMessage()]);
        }
        // dd($request->all());
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
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $mobil = Mobil::where('mobil_id', $id)->first();
        $namaFile = $mobil->gambar;
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada dan file-nya eksis
            $pathLama = public_path('uploads/mobil/' . $mobil->gambar);
            if ($mobil->gambar && file_exists($pathLama)) {
                unlink($pathLama);
            }

            // Upload gambar baru
            $file = $request->file('gambar');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/mobil'), $namaFile);
        }

        Mobil::where('mobil_id', $id)->update([
            'nama_mobil' => $request->nama_mobil,
            'nomor_polisi' => $request->nomor_polisi,
            'kapasitas' => $request->kapasitas,
            'gambar' => $namaFile,
        ]);
        return redirect()->route('data-mobil')->with('success', 'Mobil berhasil diperbarui.');
    }

    public function deleteMobil($id)
    {
        $mobil = Mobil::where('mobil_id', $id)->first();

        // Hapus gambar jika ada dan file-nya masih ada di folder
        if ($mobil && $mobil->gambar) {
            $pathGambar = public_path('uploads/mobil/' . $mobil->gambar);
            if (file_exists($pathGambar)) {
                unlink($pathGambar);
            }
        }
        Mobil::where('mobil_id', $id)->delete();
        return redirect()->route('data-mobil')->with('success', 'Mobil berhasil dihapus.');
    }
}
