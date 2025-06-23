<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemesanan;

class PemilikController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:pemilik');
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

        return view('pemilik.laporan-transaksi.laporan-transaksi', compact(
            'pemesanans',
            'jumlahPerbulan',
            'jumlahPertahun',
            'bulan',
            'tahun'
        ));
    }
}
