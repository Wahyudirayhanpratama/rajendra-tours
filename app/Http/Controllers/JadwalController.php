<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    public function showCari()
    {
        return view('pelanggan.cari-jadwal');
    }
    // Tampilkan semua jadwal
    public function cari(Request $request)
    {
        $request->validate([
            'cityfrom' => 'required|string',
            'cityto' => 'required|string',
            'date' => 'required|date',
            'jumlah_penumpang' => 'required|integer|min:1|max:5',
        ]);

        $cityfrom = $request->input('cityfrom');
        $cityto = $request->input('cityto');
        $tanggal = $request->input('date');
        $jumlah_penumpang = $request->input('jumlah_penumpang');

        $singkatanKota = [
            'pekanbaru' => 'PKU',
            'padang' => 'PDG',
            'duri' => 'Duri',
        ];
        session([
            'cityfrom' => $cityfrom,
            'cityto' => $cityto,
            'tanggal' => $tanggal,
            'jumlah_penumpang' => $jumlah_penumpang,
        ]);

        $cityfromSingkat = $singkatanKota[strtolower($cityfrom)] ?? strtoupper($cityfrom);
        $citytoSingkat = $singkatanKota[strtolower($cityto)] ?? strtoupper($cityto);

        $jadwals = Jadwal::with('mobil')
            ->where('kota_asal', $cityfrom)
            ->where('kota_tujuan', $cityto)
            ->where('tanggal', $tanggal)
            ->whereHas('mobil', function ($query) use ($jumlah_penumpang) {
                $query->where('kapasitas', '>=', $jumlah_penumpang);
            })
            ->get();

        foreach ($jadwals as $jadwal) {
            $kursi_db = DB::table('penumpangs')
                ->join('pemesanans', 'penumpangs.pemesanan_id', '=', 'pemesanans.pemesanan_id')
                ->where('pemesanans.jadwal_id', $jadwal->jadwal_id)
                ->pluck('penumpangs.nomor_kursi')
                ->toArray();

            $kursi_terpakai = [];
            foreach ($kursi_db as $kursi) {
                $kursi_array = explode(',', $kursi);
                $kursi_terpakai = array_merge($kursi_terpakai, array_map('trim', $kursi_array));
            }

            $jadwal->kursi_terpakai = count($kursi_terpakai);
            $jadwal->kursi_tersisa = ($jadwal->mobil->kapasitas ?? 0) - count($kursi_terpakai);
        }

        return view('pelanggan.jadwal', compact('jadwals', 'cityfrom', 'cityto', 'tanggal', 'jumlah_penumpang', 'cityfromSingkat', 'citytoSingkat'));
    }
    public function jadwalKeberangkatan()
    {
        $jadwals = Jadwal::with('mobil')->get();
        return view('admin.jadwal-keberangkatan.jadwal-keberangkatan', compact('jadwals'));
    }

    public function createJadwal()
    {
        $mobils = Mobil::all();
        return view('admin.jadwal-keberangkatan.tambah-jadwal-keberangkatan', compact('mobils'));
    }

    public function storeJadwal(Request $request)
    {
        $request->validate([
            'mobil_id' => 'required',
            'kota_asal' => 'required',
            'kota_tujuan' => 'required',
            'tanggal' => 'required|date',
            'jam_berangkat' => 'required',
            'harga' => 'required|numeric',
        ]);

        Jadwal::create([
            'jadwal_id' => Str::uuid(),
            'mobil_id' => $request->mobil_id,
            'kota_asal' => $request->kota_asal,
            'kota_tujuan' => $request->kota_tujuan,
            'tanggal' => $request->tanggal,
            'jam_berangkat' => $request->jam_berangkat,
            'harga' => $request->harga,
        ]);

        return redirect()->route('jadwal-keberangkatan')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function editJadwal($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $mobils = Mobil::all();
        return view('admin.jadwal-keberangkatan.edit-jadwal-keberangkatan', compact('jadwal', 'mobils'));
    }

    public function updateJadwal(Request $request, $id)
    {
        $request->validate([
            'mobil_id' => 'required',
            'kota_asal' => 'required',
            'kota_tujuan' => 'required',
            'tanggal' => 'required|date',
            'jam_berangkat' => 'required',
            'harga' => 'required|numeric',
        ]);

        $jadwal = Jadwal::findOrFail($id);
        $jadwal->update($request->all());

        return redirect()->route('jadwal-keberangkatan')->with('success', 'Jadwal berhasil diupdate.');
    }

    public function deleteJadwal($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();
        return redirect()->route('jadwal-keberangkatan')->with('success', 'Jadwal berhasil dihapus.');
    }
}
