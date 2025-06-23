<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemesanan;
use Carbon\Carbon;

class TiketController extends Controller
{
    public function index()
    {
        $userId = Auth::guard('pelanggan')->user()->user_id;

        $pemesanans = Pemesanan::with(['jadwal', 'penumpangs'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        // Ambil data dari session
        $cityfrom = session('cityfrom');
        $cityto = session('cityto');

        $singkatanKota = [
            'pekanbaru' => 'PKU',
            'padang' => 'PDG',
            'duri' => 'DURI',
        ];

        // Tiket aktif: tanggal keberangkatan hari ini atau yang akan datang
        $pemesanansAktif = Pemesanan::with(['jadwal', 'tiket'])
            ->where('user_id', $userId)
            ->whereHas('jadwal', function ($query) {
                $query->whereDate('tanggal', '>=', Carbon::today());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $cityfromSingkat = $singkatanKota[strtolower($cityfrom)] ?? strtoupper($cityfrom);
        $citytoSingkat = $singkatanKota[strtolower($cityto)] ?? strtoupper($cityto);

        return view('pelanggan.tiket', compact(
            'userId',
            'pemesanans',
            'cityfrom',
            'cityto',
            'cityfromSingkat',
            'citytoSingkat',
            'pemesanansAktif'
        ));
    }
    public function show(Request $request, $id)
    {
        $pemesanan = Pemesanan::with(['jadwal', 'tiket'])->where('pemesanan_id', $id)->firstOrFail();
        return view('pelanggan.detail-tiket', compact('pemesanan'));
    }
}
