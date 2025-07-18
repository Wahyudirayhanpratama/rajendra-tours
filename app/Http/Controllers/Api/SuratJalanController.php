<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Penumpang;
use Carbon\Carbon;

class SuratJalanController extends Controller
{
    // Menampilkan daftar unit yang berangkat hari ini
    public function index(Request $request)
    {
        $today = Carbon::today();

        // Ambil semua jadwal hari ini beserta data mobil dan pemesanan
        $jadwalHariIni = Jadwal::with(['mobil', 'pemesanans' => function ($query) {
            $query->where('status', 'lunas');
        }])->whereDate('tanggal', Carbon::today())
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $jadwalHariIni
            ]);
        }

        return view('admin.data-keberangkatan.surat-jalan', compact('jadwalHariIni'));
    }

    // Menampilkan halaman cetak surat jalan
    public function cetak(Request $request, $id)
    {
        $jadwal = Jadwal::with([
            'mobil',
            'pemesanans' => function ($query) {
                $query->where('status', 'lunas');
            },
            'pemesanans.penumpangs.user'
        ])->findOrFail($id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $jadwal
            ]);
        }

        return view('admin.data-keberangkatan.cetak-surat-jalan', compact(
            'jadwal',
        ));
    }
}
