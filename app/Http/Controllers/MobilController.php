<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mobil;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MobilController extends Controller
{
    public function dataMobil()
    {
        $mobils = Mobil::all(); // ambil semua data mobil
        foreach ($mobils as $mobil) {
            // Ambil semua kursi yang sudah dipakai untuk mobil ini
            $kursi_db = DB::table('penumpangs')
                ->join('pemesanans', 'penumpangs.pemesanan_id', '=', 'pemesanans.pemesanan_id')
                ->join('jadwals', 'pemesanans.jadwal_id', '=', 'jadwals.jadwal_id')
                ->where('jadwals.mobil_id', $mobil->mobil_id)
                ->pluck('penumpangs.nomor_kursi')
                ->toArray();

            // Ubah jadi array kursi individu
            $kursi_terpakai = [];
            foreach ($kursi_db as $kursi) {
                $kursi_array = explode(',', $kursi);
                $kursi_terpakai = array_merge($kursi_terpakai, array_map('trim', $kursi_array));
            }

            // Tambahkan properti custom ke setiap mobil
            $mobil->kursi_terpakai = count($kursi_terpakai);
            $mobil->kursi_tersisa = $mobil->kapasitas - count($kursi_terpakai);
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

        Mobil::create([
            'mobil_id' => Str::uuid(),
            'nama_mobil' => $request->nama_mobil,
            'nomor_polisi' => $request->nomor_polisi,
            'kapasitas' => $request->kapasitas,
        ]);

        return redirect()->route('data-mobil')->with('success', 'Mobil berhasil ditambahkan.');
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

        return redirect()->route('data-mobil')->with('success', 'Mobil berhasil diperbarui.');
    }

    public function deleteMobil($id)
    {
        Mobil::where('mobil_id', $id)->delete();
        return redirect()->route('data-mobil')->with('success', 'Mobil berhasil dihapus.');
    }
}
