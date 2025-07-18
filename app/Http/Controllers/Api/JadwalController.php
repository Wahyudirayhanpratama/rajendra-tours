<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{
    public function showCari()
    {
        return view('pelanggan.cari-jadwal');
    }
    // Tampilkan semua jadwal
    public function cari(Request $request, $tanggal = null)
    {
        $cityfrom = $request->input('cityfrom') ?? session('cityfrom');
        $cityto = $request->input('cityto') ?? session('cityto');
        $jumlah_penumpang = $request->input('jumlah_penumpang') ?? session('jumlah_penumpang');
        $tanggal = $tanggal ?? $request->input('date') ?? session('tanggal');

        // Jika pencarian baru (via form), lakukan validasi dan simpan ke session
        if ($request->has('cityfrom') && $request->has('cityto')) {
            $request->validate([
                'cityfrom' => 'required|string',
                'cityto' => 'required|string',
                'date' => 'required|date',
                'jumlah_penumpang' => 'required|integer|min:1|max:5',
            ]);

            session([
                'cityfrom' => $cityfrom,
                'cityto' => $cityto,
                'tanggal' => $tanggal,
                'jumlah_penumpang' => $jumlah_penumpang,
            ]);
        }

        // Jika masih ada data yang kosong, redirect ke halaman pencarian
        if (!$cityfrom || !$cityto || !$tanggal || !$jumlah_penumpang) {
            return redirect()->route('form.pencarian')->with('error', 'Silakan isi form pencarian terlebih dahulu.');
        }

        $singkatanKota = [
            'pekanbaru' => 'PKU',
            'padang' => 'PDG',
            'duri' => 'Duri',
        ];

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
                ->where('pemesanans.status', '!=', 'Tiket dibatalkan') // hanya hitung tiket aktif
                ->whereNotNull('penumpangs.nomor_kursi')               // pastikan kursi sudah dipilih
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

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal ditemukan',
                'data' => $jadwals
            ]);
        }

        $tanggalCarbon = Carbon::parse($tanggal);
        $prevDate = $tanggalCarbon->copy()->subDay()->format('Y-m-d');
        $nextDate = $tanggalCarbon->copy()->addDay()->format('Y-m-d');

        return view('pelanggan.jadwal', compact(
            'jadwals',
            'cityfrom',
            'cityto',
            'tanggal',
            'jumlah_penumpang',
            'cityfromSingkat',
            'citytoSingkat',
            'prevDate',
            'nextDate'
        ));
    }
    public function jadwalKeberangkatan(Request $request)
    {
        $jadwals = Jadwal::with('mobil')->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $jadwals
            ]);
        }

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

        DB::beginTransaction();

        try {
            $jadwal = Jadwal::create([
                'jadwal_id' => Str::uuid(),
                'mobil_id' => $request->mobil_id,
                'kota_asal' => $request->kota_asal,
                'kota_tujuan' => $request->kota_tujuan,
                'tanggal' => $request->tanggal,
                'jam_berangkat' => $request->jam_berangkat,
                'harga' => $request->harga,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal berhasil ditambahkan.',
                    'jadwal' => $jadwal
                ], 201);
            }
            return redirect()->route('jadwal-keberangkatan')->with('success', 'Jadwal berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback(); // Jika ada yang gagal, rollback semua
            Log::error('Gagal menyimpan jadwal', ['error' => $e->getMessage()]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan jadwal keberangkatan.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->withInput()->withErrors(['general_error' => 'Gagal menyimpan jadwal keberangkatan: ' . $e->getMessage()]);
        }
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

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diperbarui.',
                'mobil' => $jadwal
            ], 200);
        }

        return redirect()->route('jadwal-keberangkatan')->with('success', 'Jadwal berhasil diupdate.');
    }

    public function deleteJadwal(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();

        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan.'
            ], 404);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dihapus.',
                'jadwal_id' => $id
            ], 200);
        }

        return redirect()->route('jadwal-keberangkatan')->with('success', 'Jadwal berhasil dihapus.');
    }
}
