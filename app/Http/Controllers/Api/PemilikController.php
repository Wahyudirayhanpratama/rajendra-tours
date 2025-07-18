<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\Log;

class PemilikController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:pemilik');
    }
    public function laporanTransaksi(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $pemesanans = Pemesanan::with(['penumpang', 'jadwal'])
            ->whereYear('created_at', $tahun)
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->get();

        $jumlahPerbulan = $pemesanans->count();
        $jumlahPertahun = Pemesanan::whereYear('created_at', $tahun)->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan transaksi berhasil diambil.',
            'data' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'jumlah_pemesanan_bulan_ini' => $jumlahPerbulan,
                'jumlah_pemesanan_tahun_ini' => $jumlahPertahun,
                'pemesanans' => $pemesanans,
            ]
        ]);

        // return view('pemilik.laporan-transaksi.laporan-transaksi', compact(
        //     'pemesanans',
        //     'jumlahPerbulan',
        //     'jumlahPertahun',
        //     'bulan',
        //     'tahun'
        // ));
    }
}
